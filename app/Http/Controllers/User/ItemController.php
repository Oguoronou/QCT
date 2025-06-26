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
        $user_id = Auth::user()->id ?? 1;

        $items = Item::where("user_id", $user_id)->get();
        if(isset($_GET["lost_found_status"])){
            $lost_found_status = $_GET["lost_found_status"];

            $items = Item::where("user_id", $user_id)->where("lost_found_status", $lost_found_status)->get();
        }

        return view('my_items', ["items" => $items]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('add_item', compact('categories'));
    }

    public function store(Request $request)
    {
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
    }

    public function itemDetail($id)
    {
        $item = Item::with('users')->findOrFail($id);
        return view('item_detail', compact('item'));
    }

    public function foundItem()
    {
        $categories = Category::all();
        return view('add_found_item', compact('categories'));
    }

    public function itemEdit($id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all();
        
        return view('item_edit', compact('item', 'categories'));
    }

    public function itemDelete($id)
    {
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
    }
      
    public function updateItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:items,id',
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'lost_date' => 'required|date',
            'description' => 'required|string',
        ]);

        $item = Item::findOrFail($request->id);
        
        $item->update([
            'item_name' => $request->item_name,
            'category_name' => $request->category,
            'date' => $request->lost_date,
            'description' => $request->description,
        ]);

        Session::flash("message", "Objet mis à jour avec succès !");
        return redirect("my-items");
    }

    public function itemFound($id)
    {
        $item = Item::findOrFail($id);
        $item->update(['lost_found_status' => 'found']);

        Session::flash("message", "Objet marqué comme trouvé !");
        return redirect('my-items');
    }

    public function itemDeliver($id)
    {
        $item = Item::findOrFail($id);
        $item->update(['lost_found_status' => 'delivered']);

        Session::flash("message", "Objet marqué comme remis à son propriétaire !");
        return redirect('my-items');
    }

    public function allItems(Request $request)
    {
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
    }
}