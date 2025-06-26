<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function create()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $profileImage = 'user.png';
        if ($imageUpload = $request->file('image')) {
            $destinationPath = 'images/';
            $profileImage = date('YmdHis') . "." . $imageUpload->getClientOriginalExtension();
            $imageUpload->move($destinationPath, $profileImage);
        }

        $userCheck = User::where("email", $request->email)->get();
        if(count($userCheck) > 0){
            Session::flash("message", "Email Already Registered!");
            return redirect("register");
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_no' => $request->mobile_no,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'image' => $profileImage,
        ]);

        Session::flash("message", "Your Account Registered Successfully!");
        return redirect("login");
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if(Auth::user()->role == "user"){
                return redirect("my-account");
            }
            return redirect("admin/dashboard");
        }

        Session::flash("message", "Record Not found!");
        return redirect()->back();
    }

    public function logout()
    {
        Auth::logout();

        return redirect("login");
    }
}
