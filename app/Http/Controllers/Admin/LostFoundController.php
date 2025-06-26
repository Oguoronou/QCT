<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class LostFoundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $items = Item::all();

        return view('Admin.LostFound.view_lost_found', [
            "items" => $items,
        ]);
    }

    public function itemDetail($id)
    {
        $item = Item::where("id", $id)->with("users")->first();

        return view('Admin.LostFound.detail', ["item" => $item]);
    }
}
