<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\BlobStorage;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'mobile_no' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'image.image' => 'Le fichier doit être une image (JPG, PNG, GIF).',
            'image.max' => "L'image ne doit pas dépasser 5 Mo.",
        ]);

        $profileImage = null;
        if ($imageUpload = $request->file('image')) {
            $profileImage = BlobStorage::store($imageUpload, 'uploads/profiles');
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

        Session::flash("message", "Votre compte a été créé avec succès !");
        return redirect("login");
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if(Auth::user()->role == "user"){
                return redirect("my-account");
            }
            return redirect("admin/dashboard");
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect !'])->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::logout();

        return redirect("login");
    }
}
