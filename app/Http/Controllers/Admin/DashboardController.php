<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $lostItems = Item::where("status", "lost")->count();
        $foundItems = Item::where("status", "found")->count();
        $deliverItems = Item::where("lost_found_status", "deliver")->count();
        $pendingItems = Item::where("lost_found_status", "pending")->count();
        $users = User::where("role", "user")->count();

        return view('Admin.Dashboard.dashboard', [
            "lostItems" => $lostItems,
            "foundItems" => $foundItems,
            "deliverItems" => $deliverItems,
            "pendingItems" => $pendingItems,
            "users" => $users,
        ]);
    }
}
