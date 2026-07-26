<?php

namespace App\Http\Controllers\User;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\ItemClaimedNotification;
use App\Notifications\OwnershipClaimedNotification;
use App\Notifications\ClaimValidatedNotification;

class ItemController extends Controller
{
    // Constantes pour les dossiers de stockage
    const ITEM_IMAGES_FOLDER = 'uploads/items';

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
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'status' => 'required|in:lost,found',
            ]);

            // Initialisation du tableau des chemins d'images
            $imagePaths = [];

            // Traitement des images si elles existent
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Génération d'un nom de fichier unique
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Déplacement du fichier vers le dossier de stockage
                    $image->move(public_path(self::ITEM_IMAGES_FOLDER), $filename);

                    // Stockage du chemin relatif
                    $imagePaths[] = self::ITEM_IMAGES_FOLDER . '/' . $filename;
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
        } catch (\Exception $e) {
            // En cas d'erreur, supprimer les images déjà uploadées
            if (!empty($imagePaths)) {
                foreach ($imagePaths as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
            }

            Session::flash("error", "Une erreur est survenue lors de l'ajout de l'objet: " . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function itemDetail($id)
    {
        try {
            $item = Item::with('user', 'foundUser')->findOrFail($id);
            return view('item_detail', compact('item'));
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
                $images = explode(',', $item->images);
                foreach ($images as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
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
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }

                // Ajouter les nouvelles images
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path(self::ITEM_IMAGES_FOLDER), $filename);
                    $imagePaths[] = self::ITEM_IMAGES_FOLDER . '/' . $filename;
                }
            }

            $item->update([
                'item_name' => $request->item_name,
                'category_name' => $request->category,
                'date' => $request->lost_date,
                'description' => $request->description,
                'images' => implode(',', $imagePaths),
            ]);

            Session::flash("message", "Objet mis à jour avec succès !");
            return redirect("my-items");
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la mise à jour de l'objet.");
            return redirect()->back()->withInput();
        }
    }

    public function itemFound($id)
    {
        try {
            $item = Item::findOrFail($id);
            
            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à modifier cet objet.");
                return redirect()->back();
            }

            $item->update(['lost_found_status' => 'found']);

            Session::flash("message", "Objet marqué comme trouvé !");
            return redirect('my-items');
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
            $item->user->notify(new ItemClaimedNotification($item, Auth::user()));

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
            $item->user->notify(new OwnershipClaimedNotification($item, Auth::user()));

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
            // $item->foundUser->notify(new OwnershipValidatedNotification($item));

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
