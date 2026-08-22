<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $faqItems = FaqItem::orderBy('order')->get();

        return view('Admin.Faq.view_faq', compact('faqItems'));
    }

    public function create()
    {
        return view('Admin.Faq.add_faq');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        FaqItem::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        Session::flash('message', 'Question ajoutée avec succès !');
        return redirect('admin/faq');
    }

    public function edit($id)
    {
        $faqItem = FaqItem::findOrFail($id);

        return view('Admin.Faq.edit_faq', compact('faqItem'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $faqItem = FaqItem::findOrFail($id);
        $faqItem->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        Session::flash('message', 'Question mise à jour avec succès !');
        return redirect('admin/faq');
    }

    public function delete($id)
    {
        FaqItem::findOrFail($id)->delete();

        Session::flash('message', 'Question supprimée avec succès !');
        return redirect('admin/faq');
    }
}
