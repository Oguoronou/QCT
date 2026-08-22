<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth callback failed: ' . $e->getMessage(), ['exception' => $e]);
            Session::flash("error", "La connexion avec Google a échoué. Veuillez réessayer.");
            return redirect("login");
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        }

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => null,
                'email_verified_at' => now(),
                'status' => 'active',
                'role' => 'user',
            ]);
        }

        Auth::login($user);

        if (!$user->mobile_no || !$user->country || !$user->city) {
            return redirect("complete-profile");
        }

        if ($user->role == "user") {
            return redirect("my-account");
        }
        return redirect("admin/dashboard");
    }

    public function showCompleteProfile()
    {
        return view('complete_profile');
    }

    public function completeProfile(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->mobile_no = $request->mobile_no;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->save();

        Session::flash("message", "Profil complété avec succès !");
        return redirect("my-account");
    }
}
