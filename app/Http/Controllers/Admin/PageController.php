<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pages = Page::orderBy('title')->get();

        return view('Admin.Page.view_pages', compact('pages'));
    }

    public function edit($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('Admin.Page.edit_page', compact('page'));
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $page = Page::where('slug', $slug)->firstOrFail();
        $page->update($request->only(['title', 'content']));

        Session::flash('message', 'Page mise à jour avec succès !');
        return redirect('admin/pages');
    }
}
