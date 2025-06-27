<?php

namespace App\Http\Controllers\User;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ItemController extends Controller
{
    // Constantes pour les dossiers de stockage
    const ITEM_IMAGES_FOLDER = 'uploads/items';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $user_id = Auth::user()->id ?? 1;

            $items = Item::where("user_id", $user_id)->get();
            if(isset($_GET["lost_found_status"])){
                $lost_found_status = $_GET["lost_found_status"];
                $items = Item::where("user_id", $user_id)->where("lost_found_status", $lost_found_status)->get();
            }

            return view('my_items', ["items" => $items]);
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la récupération des objets.");
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            $categories = Category::all();
            return view('add_item', compact('categories'));
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
                'type' => 'required|in:lost,found',
            ]);

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path(self::ITEM_IMAGES_FOLDER), $filename);
                    $imagePaths[] = self::ITEM_IMAGES_FOLDER . '/' . $filename;
                }
            }

            Item::create([
                'user_id' => Auth::id(),
                'item_name' => $request->item_name,
                'category_name' => $request->category,
                'date' => $request->lost_date,
                'images' => implode(',', $imagePaths),
                'description' => $request->description,
                'status' => $request->type,
                'lost_found_status' => 'pending',
            ]);

            Session::flash("message", $request->type == 'lost' 
                ? "Objet perdu ajouté, nous recherchons..." 
                : "Objet trouvé ajouté, nous cherchons le propriétaire...");

            return redirect("my-items");
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de l'ajout de l'objet.");
            return redirect()->back()->withInput();
        }
    }

    public function itemDetail($id)
    {
        try {
            $item = Item::with('users')->findOrFail($id);
            return view('item_detail', compact('item'));
        } catch (\Exception $e) {
            Session::flash("error", "Objet introuvable.");
            return redirect()->back();
        }
    }

    public function foundItem()
    {
        try {
            $categories = Category::all();
            return view('add_found_item', compact('categories'));
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors du chargement du formulaire.");
            return redirect()->back();
        }
    }

    public function itemEdit($id)
    {
        try {
            $item = Item::findOrFail($id);
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
            $query = Item::query();

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
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

            $items = $query->simplePaginate(10);
            $categories = Category::all();

            return view('all_items', compact('items', 'categories'));
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la recherche d'objets.");
            return redirect()->back();
        }
    }
}