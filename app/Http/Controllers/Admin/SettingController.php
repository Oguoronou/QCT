<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\BlobStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    private const LOGO_FOLDER = 'uploads/settings';

    private const TEXT_KEYS = [
        'site_name',
        'site_description',
        'contact_email',
        'contact_phone',
        'contact_address',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_whatsapp',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $settings = [];
        foreach (self::TEXT_KEYS as $key) {
            $settings[$key] = Setting::get($key);
        }
        $settings['site_logo'] = Setting::get('site_logo');

        return view('Admin.Settings.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'contact_address' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_whatsapp' => 'nullable|url|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        foreach (self::TEXT_KEYS as $key) {
            Setting::set($key, $request->input($key));
        }

        if ($request->hasFile('site_logo')) {
            BlobStorage::delete(Setting::get('site_logo'));

            Setting::set('site_logo', BlobStorage::store($request->file('site_logo'), self::LOGO_FOLDER));
        }

        Session::flash('message', 'Paramètres mis à jour avec succès !');
        return redirect('admin/settings');
    }
}
