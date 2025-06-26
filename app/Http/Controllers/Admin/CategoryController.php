<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::all();

        return view('Admin.Category.view_categories', [
            "categories" => $categories,
        ]);
    }

    public function create()
    {
        return view('Admin.Category.add_category');
    }

    public function store(Request $request)
    {
        Category::create([
            "category_name" => $request->category_name,
            "icon" => $request->icon, 
        ]);

        Session::flash("message", "Category Created Successfully!");
        return redirect("admin/categories");
    }

    public function edit($id)
    {
        $editCategory = Category::find($id);

        return view("Admin.Category.edit_category",[
            "editCategory" => $editCategory,
        ]);
    }

    public function update(Request $request)
    {
        $updateCategory = Category::find($request->id);
        $updateCategory->category_name = $request->category_name;
        $updateCategory->icon = $request->icon;
        $updateCategory->update();

        Session::flash("message", "Category Updated Successfully!");
        return redirect("admin/categories");
    }

    public function delete($id)
    {
        Category::find($id)->delete();

        Session::flash("message", "Category Deleted Successfully!");
        return redirect("admin/categories");
    }
}

