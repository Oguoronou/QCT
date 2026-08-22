<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('page_detail', compact('page'));
    }

    public function faq()
    {
        $faqItems = FaqItem::orderBy('order')->get();

        return view('faq', compact('faqItems'));
    }
}
