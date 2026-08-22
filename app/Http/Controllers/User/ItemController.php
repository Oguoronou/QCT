<?php

namespace App\Http\Controllers\User;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Support\BlobStorage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\ItemClaimedNotification;
use App\Notifications\OwnershipClaimedNotification;
use App\Notifications\ClaimValidatedNotification;
use App\Notifications\OwnershipValidatedNotification;

class ItemController extends Controller
{
    // Constantes pour les dossiers de stockage
    const ITEM_IMAGES_FOLDER = 'uploads/items';
    const DECLARATION_PHOTOS_FOLDER = 'uploads/declarations';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vérifie que l'utilisateur est propriétaire de l'item
     */
    private function authorizeItem(Item $item): bool
    {
        return $item->user_id === Auth::id();
    }

    /**
     * Crée ou met à jour la déclaration de dépôt au commissariat pour un item.
     * Ne touche pas declared_at lors d'une correction ultérieure.
     */
    private function upsertPoliceDeclaration(Item $item, Request $request): void
    {
        $declaration = ItemPoliceDeclaration::firstOrNew(['item_id' => $item->id]);
        $declaration->commissariat_id = $request->commissariat_id;
        $declaration->declared_by_user_id = Auth::id();
        $declaration->declaration_number = $request->declaration_number;

        if ($request->hasFile('receipt_photo')) {
            BlobStorage::delete($declaration->receipt_photo);

            $declaration->receipt_photo = BlobStorage::store($request->file('receipt_photo'), self::DECLARATION_PHOTOS_FOLDER);
        }

        if (!$declaration->exists) {
            $declaration->declared_at = now();
        }

        $declaration->save();
    }

    public function index()
    {
        try {
            $user_id = Auth::user()->id ?? 1;

            $items = Item::where("user_id", $user_id)->paginate(10);
            if (isset($_GET["lost_found_status"])) {
                $lost_found_status = $_GET["lost_found_status"];
                $items = Item::where("user_id", $user_id)->where("lost_found_status", $lost_found_status)->paginate(10);
            }

            return view('my_items', ["items" => $items]);
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la récupération des objets.");
            return redirect()->back();
        }
    }

    private function showAddItemForm(string $type)
    {
        $categories = Category::all();

        return view('add_item', compact('categories', 'type'));
    }

    public function create()
    {
        try {
            return $this->showAddItemForm('lost');
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors du chargement du formulaire.");
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'item_name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'lost_date' => 'required|date',
                'images' => 'array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                'description' => 'required|string',
                'status' => 'required|in:lost,found',
            ], [
                'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
                'images.*.image' => 'Chaque fichier doit être une image (JPG, PNG, GIF).',
                'images.max' => '5 images maximum.',
            ]);

            // Initialisation du tableau des chemins d'images
            $imagePaths = [];

            // Traitement des images si elles existent
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = BlobStorage::store($image, self::ITEM_IMAGES_FOLDER);
                }
            }

            // Création de l'item avec les chemins d'images séparés par des virgules
            Item::create([
                'user_id' => Auth::id(),
                'item_name' => $request->item_name,
                'category_name' => $request->category,
                'date' => $request->lost_date,
                'images' => !empty($imagePaths) ? implode(',', $imagePaths) : null,
                'description' => $request->description,
                'status' => $request->status,
                'lost_found_status' => 'pending',
            ]);

            Session::flash("message", $request->status == 'lost'
                ? "Objet perdu ajouté, nous recherchons..."
                : "Objet trouvé ajouté, nous cherchons le propriétaire...");

            return redirect("my-items");
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // En cas d'erreur, supprimer les images déjà uploadées
            if (!empty($imagePaths)) {
                foreach ($imagePaths as $image) {
                    BlobStorage::delete($image);
                }
            }

            Session::flash("error", "Une erreur est survenue lors de l'ajout de l'objet: " . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function itemDetail($id)
    {
        try {
            $item = Item::with('user', 'foundUser', 'policeDeclaration.commissariat')->findOrFail($id);
            $commissariats = Commissariat::where('is_active', true)->orderBy('commune')->get();

            return view('item_detail', compact('item', 'commissariats'));
        } catch (\Exception $e) {
            Session::flash("error", "Objet introuvable.");
            return redirect()->back();
        }
    }

    public function foundItem()
    {
        try {
            return $this->showAddItemForm('found');
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors du chargement du formulaire.");
            return redirect()->back();
        }
    }

    public function itemEdit($id)
    {
        try {
            $item = Item::findOrFail($id);
            
            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à modifier cet objet.");
                return redirect()->back();
            }

            $categories = Category::all();

            return view('item_edit', compact('item', 'categories'));
        } catch (\Exception $e) {
            Session::flash("error", "Objet introuvable.");
            return redirect()->back();
        }
    }

    public function itemDelete($id)
    {
        try {
            $item = Item::findOrFail($id);

            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à supprimer cet objet.");
                return redirect()->back();
            }

            // Suppression des images associées
            if ($item->images) {
                foreach (explode(',', $item->images) as $image) {
                    BlobStorage::delete($image);
                }
            }

            $item->delete();

            Session::flash("message", "Objet supprimé avec succès !");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la suppression de l'objet.");
            return redirect()->back();
        }
    }

    public function updateItem(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:items,id',
                'item_name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'lost_date' => 'required|date',
                'description' => 'required|string',
                'images' => 'array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            ], [
                'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
                'images.*.image' => 'Chaque fichier doit être une image (JPG, PNG, GIF).',
                'images.max' => '5 images maximum.',
            ]);

            $item = Item::findOrFail($request->id);

            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à modifier cet objet.");
                return redirect()->back();
            }

            // Gestion des images
            $imagePaths = [];
            if ($item->images) {
                $imagePaths = explode(',', $item->images);
            }

            if ($request->hasFile('images')) {
                // Supprimer les anciennes images si nécessaire
                foreach ($imagePaths as $image) {
                    BlobStorage::delete($image);
                }

                // Ajouter les nouvelles images
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = BlobStorage::store($image, self::ITEM_IMAGES_FOLDER);
                }
            }

            $item->update([
                'item_name' => $request->item_name,
                'category_name' => $request->category,
                'date' => $request->lost_date,
                'description' => $request->description,
                'images' => implode(',', $imagePaths),
            ]);

            if ($item->policeDeclaration && $request->filled('commissariat_id')) {
                $request->validate([
                    'commissariat_id' => 'required|exists:commissariats,id',
                    'declaration_number' => 'required|string|max:100',
                    'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                ], [
                    'receipt_photo.max' => 'Le récépissé ne doit pas dépasser 5 Mo.',
                    'receipt_photo.image' => 'Le récépissé doit être une image (JPG, PNG).',
                ]);

                $this->upsertPoliceDeclaration($item, $request);
            }

            Session::flash("message", "Objet mis à jour avec succès !");
            return redirect("my-items");
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la mise à jour de l'objet.");
            return redirect()->back()->withInput();
        }
    }

    public function itemFound(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à modifier cet objet.");
                return redirect()->back();
            }

            if ($item->status === 'found') {
                $request->validate([
                    'commissariat_id' => 'required|exists:commissariats,id',
                    'declaration_number' => 'required|string|max:100',
                    'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                ], [
                    'receipt_photo.max' => 'Le récépissé ne doit pas dépasser 5 Mo.',
                    'receipt_photo.image' => 'Le récépissé doit être une image (JPG, PNG).',
                ]);

                $this->upsertPoliceDeclaration($item, $request);
            }

            $item->update(['lost_found_status' => 'found']);

            Session::flash("message", "Objet marqué comme trouvé !");
            return redirect('my-items');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la mise à jour du statut.");
            return redirect()->back();
        }
    }

    public function itemDeliver($id)
    {
        try {
            $item = Item::findOrFail($id);
            
            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à modifier cet objet.");
                return redirect()->back();
            }

            $item->update(['lost_found_status' => 'delivered']);

            Session::flash("message", "Objet marqué comme remis à son propriétaire !");
            return redirect('my-items');
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la mise à jour du statut.");
            return redirect()->back();
        }
    }

    public function allItems(Request $request)
    {
        try {
            $query = Item::with('user', 'foundUser');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('category')) {
                $query->where('category_name', $request->input('category'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            $items = $query->paginate(10);
            $categories = Category::all();

            return view('all_items', compact('items', 'categories'));
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la recherche d'objets.");
            return redirect()->back();
        }
    }

    // Dans ItemController.php

    public function claimItem(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            // Vérifier que l'objet n'a pas déjà été trouvé
            if ($item->lost_found_status != 'pending') {
                Session::flash("error", "Cet objet a déjà été marqué comme trouvé.");
                return redirect()->back();
            }

            // Mettre à jour l'objet
            $item->update([
                'found_user_id' => Auth::id(),
                'lost_found_status' => 'claimed',
            ]);

            // Envoyer une notification au propriétaire
            if ($item->user) {
                $item->user->notify(new ItemClaimedNotification($item, Auth::user()));
            }

            Session::flash("message", "Vous avez signalé avoir trouvé cet objet. Le propriétaire sera notifié.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la déclaration.");
            return redirect()->back();
        }
    }

    public function validateClaim(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            // Vérifier que l'utilisateur est bien le propriétaire
            if ($item->user_id != Auth::id()) {
                Session::flash("error", "Vous n'êtes pas autorisé à valider cette réclamation.");
                return redirect()->back();
            }

            // Vérifier que l'objet a bien été réclamé
            if ($item->lost_found_status != 'claimed') {
                Session::flash("error", "Cet objet n'a pas été réclamé.");
                return redirect()->back();
            }

            // Mettre à jour l'objet
            $item->update([
                'lost_found_status' => 'delivered',
            ]);

            // Envoyer une notification au trouveur
            if ($item->foundUser) {
                $item->foundUser->notify(new ClaimValidatedNotification($item, Auth::user()));
            }

            Session::flash("message", "Vous avez confirmé la récupération de votre objet. Merci !");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la validation.");
            return redirect()->back();
        }
    }

    // Ajoutez ces méthodes dans ItemController.php

    public function claimOwnership(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            // Vérifier que l'objet est bien marqué comme trouvé (found)
            if ($item->status != 'found') {
                Session::flash("error", "Cette action n'est possible que pour les objets trouvés.");
                return redirect()->back();
            }

            // Vérifier que l'objet n'a pas déjà été réclamé
            if ($item->lost_found_status != 'pending') {
                Session::flash("error", "Cet objet a déjà été réclamé.");
                return redirect()->back();
            }

            // Mettre à jour l'objet
            $item->update([
                'found_user_id' => Auth::id(),
                'lost_found_status' => 'ownership_claimed',
            ]);

            // Envoyer une notification au posteur original
            if ($item->user) {
                $item->user->notify(new OwnershipClaimedNotification($item, Auth::user()));
            }

            $message = $item->category_name == 'Personnes'
                ? "Vous avez signalé qu'il s'agit de votre proche. Le posteur sera notifié."
                : "Vous avez signalé que cet objet vous appartient. Le posteur sera notifié.";

            Session::flash("message", $message);
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la déclaration.");
            return redirect()->back();
        }
    }

    public function validateOwnership(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            // Vérifier que l'utilisateur est bien celui qui a posté l'annonce
            if ($item->user_id != Auth::id()) {
                Session::flash("error", "Vous n'êtes pas autorisé à valider cette réclamation.");
                return redirect()->back();
            }

            // Vérifier que l'objet a bien été réclamé
            if ($item->lost_found_status != 'ownership_claimed') {
                Session::flash("error", "Cet objet n'a pas été réclamé.");
                return redirect()->back();
            }

            // Mettre à jour l'objet
            $item->update([
                'lost_found_status' => 'returned',
            ]);

            // Envoyer une notification au réclamant
            if ($item->foundUser) {
                $item->foundUser->notify(new OwnershipValidatedNotification($item, Auth::user()));
            }

            $message = $item->category_name == 'Personnes'
                ? "Vous avez confirmé avoir retrouvé la personne avec son proche. Merci !"
                : "Vous avez confirmé avoir rendu l'objet à son propriétaire. Merci !";

            Session::flash("message", $message);
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la validation.");
            return redirect()->back();
        }
    }
}
