<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function myAccount()
    {
        return view('my_account');
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id ?? 1;
        $user = User::find($id);

        $user->name = $request->name;
        $user->mobile_no = $request->mobile_no;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->address = $request->address;

        $user->update();

        Session::flash("message", "Profile Updated Successfully!");
        return redirect()->back();
    }
}
