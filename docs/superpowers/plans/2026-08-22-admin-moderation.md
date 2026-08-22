# Modération du site (Paramètres, témoignages, pages de contenu) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give the Admin space the ability to moderate site-wide content: editable branding/contact/social settings, marking a contact message as a public testimonial, and a mini CMS for FAQ + 4 static pages (Comment ça marche, Politique de confidentialité, CGU, CGV).

**Architecture:** Three independent Laravel subsystems sharing the existing `Admin\*` controller/route/view conventions. Settings use a cached key-value store (`Setting::get`/`Setting::set`). Testimonials add a boolean flag to the existing `messages` table. Content pages use two new Eloquent models (`Page` for the 4 static pages, keyed by slug; `FaqItem` for the Q&A list), each with full admin CRUD (pages: edit-only, slug-addressed; FAQ: full CRUD) and public read-only routes.

**Tech Stack:** Laravel 10 / PHP 8.1, MySQL (`findme` DB), Blade views on two layouts (`layout.blade.php` public Tailwind, `Admin/layout.blade.php` admin Tailwind), TinyMCE (self-hosted, already bundled in `public/customerdesign/assets/vendor/tinymce`) for rich-text page content, PHPUnit 10 with `RefreshDatabase`.

## Global Constraints

- All user-facing copy (admin and public) is in **French**.
- Image/file uploads use `$file->move(public_path(...), $filename)` — never the `Storage` facade (no `storage:link` in this project).
- Every new admin route must sit inside the existing `Route::middleware(['AdminLogin'])->group(...)` block in `routes/web.php`; every new admin controller must call `$this->middleware('auth')` in its constructor (matches every existing `Admin\*` controller).
- Flash messages use `Session::flash('message', ...)` and are read via `Session::get('message')` — follow this exact key, matching `CommissariatController`/`CategoryController`.
- New Blade views for the admin area follow the Tailwind card/table style of `resources/views/Admin/Category/*.blade.php` (not the older Bootstrap style of `Admin/Message/messages.blade.php`).
- Feature tests use `Illuminate\Foundation\Testing\RefreshDatabase` and hit real routes with `$this->actingAs($admin)`, matching every existing test in `tests/Feature/Admin*Test.php`. Run tests with `php artisan test --filter=<TestClass>`.
- Seeders for reference/content data must be idempotent (`firstOrCreate`, not `create`), matching `CommissariatSeeder`, so re-running `db:seed` never clobbers content an admin has already edited.

---

## Task 1: Settings foundation (migration + model)

**Files:**
- Create: `database/migrations/2026_08_22_000000_create_settings_table.php`
- Create: `app/Models/Setting.php`
- Test: `tests/Feature/SettingModelTest.php`

**Interfaces:**
- Produces: `App\Models\Setting::get(string $key, $default = null): mixed` and `App\Models\Setting::set(string $key, $value): void` — used by every later task that reads or writes a site setting.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
```

Save as `database/migrations/2026_08_22_000000_create_settings_table.php`.

- [ ] **Step 2: Write the `Setting` model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $value = Cache::rememberForever("setting.$key", function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        return $value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.$key");
    }
}
```

- [ ] **Step 3: Run the migration**

Run: `php artisan migrate`
Expected: `2026_08_22_000000_create_settings_table` listed as `Migrated`.

- [ ] **Step 4: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_key_is_missing(): void
    {
        $this->assertSame('QCT', Setting::get('site_name', 'QCT'));
    }

    public function test_set_then_get_returns_the_stored_value(): void
    {
        Setting::set('site_name', 'Mon Site');

        $this->assertSame('Mon Site', Setting::get('site_name', 'QCT'));
    }

    public function test_get_reflects_updates_after_a_second_set(): void
    {
        Setting::set('contact_email', 'first@example.com');
        $this->assertSame('first@example.com', Setting::get('contact_email'));

        Setting::set('contact_email', 'second@example.com');
        $this->assertSame('second@example.com', Setting::get('contact_email'));
    }
}
```

Save as `tests/Feature/SettingModelTest.php`.

- [ ] **Step 5: Run test to verify it fails**

Run: `php artisan test --filter=SettingModelTest`
Expected: FAIL — `App\Models\Setting` not found (class doesn't exist as a test target yet, or table missing if Step 3 wasn't run — re-run Step 3 first if so).

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=SettingModelTest`
Expected: `OK (3 tests, ...)`

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_08_22_000000_create_settings_table.php app/Models/Setting.php tests/Feature/SettingModelTest.php
git commit -m "feat: add Setting key-value model for site configuration"
```

---

## Task 2: Admin settings management

**Files:**
- Create: `app/Http/Controllers/Admin/SettingController.php`
- Create: `resources/views/Admin/Settings/settings.blade.php`
- Modify: `routes/web.php` (add 2 routes inside the `AdminLogin` group)
- Modify: `resources/views/Admin/layout.blade.php` (add sidebar link)
- Test: `tests/Feature/AdminSettingsTest.php`

**Interfaces:**
- Consumes: `Setting::get(string $key, $default=null)`, `Setting::set(string $key, $value)` from Task 1.
- Produces: routes `GET admin/settings` and `POST admin/settings`, used by Task 3's sidebar and by nothing else downstream.

- [ ] **Step 1: Write the controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                unlink(public_path($oldLogo));
            }

            $logo = $request->file('site_logo');
            $filename = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path(self::LOGO_FOLDER), $filename);
            Setting::set('site_logo', self::LOGO_FOLDER . '/' . $filename);
        }

        Session::flash('message', 'Paramètres mis à jour avec succès !');
        return redirect('admin/settings');
    }
}
```

Save as `app/Http/Controllers/Admin/SettingController.php`.

- [ ] **Step 2: Write the admin view**

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Paramètres du site</h1>
        <p class="text-gray-600 mt-1">Logo, coordonnées et réseaux sociaux affichés sur le site public</p>
    </div>

    @if(Session::has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <p>{{ Session::get('message') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/settings') }}" method="post" enctype="multipart/form-data">
                @csrf

                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-image mr-2 text-blue-600"></i>Logo &amp; identité</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du site <span class="text-red-500">*</span></label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        @if(!empty($settings['site_logo']))
                            <img src="{{ asset($settings['site_logo']) }}" alt="Logo actuel" class="h-12 mb-2 rounded">
                        @endif
                        <input type="file" name="site_logo" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / slogan</label>
                        <textarea name="site_description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-address-book mr-2 text-blue-600"></i>Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-share-alt mr-2 text-blue-600"></i>Réseaux sociaux</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-facebook-f mr-1"></i> Facebook</label>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                               placeholder="https://facebook.com/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-twitter mr-1"></i> Twitter / X</label>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                               placeholder="https://x.com/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-instagram mr-1"></i> Instagram</label>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                               placeholder="https://instagram.com/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-whatsapp mr-1"></i> WhatsApp</label>
                        <input type="url" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}"
                               placeholder="https://wa.me/2250700000000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection
```

Save as `resources/views/Admin/Settings/settings.blade.php`.

- [ ] **Step 3: Register the routes**

In `routes/web.php`, inside the existing `Route::middleware(['AdminLogin'])->group(function () { ... });` block, add (right after the `admin/commissariats` block, before `admin/users`):

```php
    Route::get("admin/settings", [App\Http\Controllers\Admin\SettingController::class, "edit"]);
    Route::post("admin/settings", [App\Http\Controllers\Admin\SettingController::class, "update"]);
```

- [ ] **Step 4: Add the sidebar link**

In `resources/views/Admin/layout.blade.php`, immediately after the "Commissariats" `<li>` block (the one linking to `admin/commissariats`, right before the `@if(!empty(Auth::user()))` logout block), add:

```blade
        <li>
          <a href="{{ url('admin/settings') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-cog w-6 text-center"></i>
            <span class="ml-3">Paramètres du site</span>
          </a>
        </li>
```

- [ ] **Step 5: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/settings');

        $response->assertRedirect('/my-account');
    }

    public function test_admin_can_update_text_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Nouveau Nom',
            'contact_email' => 'contact@nouveau.ci',
        ]);

        $response->assertRedirect('admin/settings');
        $this->assertSame('Nouveau Nom', Setting::get('site_name'));
        $this->assertSame('contact@nouveau.ci', Setting::get('contact_email'));
    }

    public function test_admin_can_upload_a_new_logo_and_the_old_one_is_removed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::set('site_logo', 'uploads/settings/does-not-exist.png');

        $logo = UploadedFile::fake()->image('logo.png');
        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'QCT',
            'site_logo' => $logo,
        ]);

        $response->assertRedirect('admin/settings');
        $path = Setting::get('site_logo');
        $this->assertNotSame('uploads/settings/does-not-exist.png', $path);
        $this->assertFileExists(public_path($path));

        unlink(public_path($path));
    }
}
```

Save as `tests/Feature/AdminSettingsTest.php`.

- [ ] **Step 6: Run test to verify it fails**

Run: `php artisan test --filter=AdminSettingsTest`
Expected: FAIL — route `admin/settings` not defined (if Step 3 skipped) or view not found (if Step 2 skipped).

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=AdminSettingsTest`
Expected: `OK (3 tests, ...)`

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Admin/SettingController.php resources/views/Admin/Settings/settings.blade.php routes/web.php resources/views/Admin/layout.blade.php tests/Feature/AdminSettingsTest.php
git commit -m "feat: add admin settings management (logo, contact, social links)"
```

---

## Task 3: Public settings integration

**Files:**
- Modify: `resources/views/layout.blade.php` (header logo/name; footer logo, description, contact block, social icons)
- Test: `tests/Feature/PublicSettingsRenderTest.php`

**Interfaces:**
- Consumes: `Setting::get(string $key, $default=null)` from Task 1.

- [ ] **Step 1: Replace the header logo/name block**

In `resources/views/layout.blade.php`, replace:

```blade
            <!-- Logo -->
            <a class="flex items-center gap-2.5 no-underline shrink-0" href="{{ url('/') }}">
                <div class="w-9 h-9 bg-blue-500 rounded-[10px] flex items-center justify-center text-base text-white">
                    <i class="fas fa-search-location"></i>
                </div>
                <span class="text-xl font-extrabold text-white tracking-[-0.5px]">Q<span class="text-blue-500">CT</span></span>
            </a>
```

with:

```blade
            <!-- Logo -->
            <a class="flex items-center gap-2.5 no-underline shrink-0" href="{{ url('/') }}">
                @if (\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'QCT') }}" class="w-9 h-9 rounded-[10px] object-cover">
                @else
                    <div class="w-9 h-9 bg-blue-500 rounded-[10px] flex items-center justify-center text-base text-white">
                        <i class="fas fa-search-location"></i>
                    </div>
                @endif
                <span class="text-xl font-extrabold text-white tracking-[-0.5px]">{{ \App\Models\Setting::get('site_name', 'QCT') }}</span>
            </a>
```

- [ ] **Step 2: Replace the footer logo/description block**

Replace:

```blade
                <div>
                    <a class="flex items-center gap-2.5 no-underline shrink-0 mb-0" href="{{ url('/') }}">
                        <div class="w-9 h-9 bg-blue-500 rounded-[10px] flex items-center justify-center text-base text-white">
                            <i class="fas fa-search-location"></i>
                        </div>
                        <span class="text-xl font-extrabold text-white tracking-[-0.5px]">Q<span class="text-blue-500">CT</span></span>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed mt-3 max-w-[280px]">
                        La plateforme communautaire qui connecte objets perdus et propriétaires, et aide à retrouver les personnes disparues en Côte d'Ivoire.
                    </p>
                </div>
```

with:

```blade
                <div>
                    <a class="flex items-center gap-2.5 no-underline shrink-0 mb-0" href="{{ url('/') }}">
                        @if (\App\Models\Setting::get('site_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'QCT') }}" class="w-9 h-9 rounded-[10px] object-cover">
                        @else
                            <div class="w-9 h-9 bg-blue-500 rounded-[10px] flex items-center justify-center text-base text-white">
                                <i class="fas fa-search-location"></i>
                            </div>
                        @endif
                        <span class="text-xl font-extrabold text-white tracking-[-0.5px]">{{ \App\Models\Setting::get('site_name', 'QCT') }}</span>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed mt-3 max-w-[280px]">
                        {{ \App\Models\Setting::get('site_description', "La plateforme communautaire qui connecte objets perdus et propriétaires, et aide à retrouver les personnes disparues en Côte d'Ivoire.") }}
                    </p>
                </div>
```

- [ ] **Step 3: Replace the footer contact block**

Replace:

```blade
                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Contact</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        <li><a href="mailto:contact@qct.ci" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-envelope mr-1"></i> contact@qct.ci</a></li>
                        <li><a href="tel:+2250700000000" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-phone mr-1"></i> +225 07 00 00 00 00</a></li>
                        <li><a href="#" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-map-marker-alt mr-1"></i> Plateau, Abidjan</a></li>
                    </ul>
                </div>
```

with:

```blade
                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Contact</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        @php $contactEmail = \App\Models\Setting::get('contact_email', 'contact@qct.ci'); @endphp
                        @if($contactEmail)
                        <li><a href="mailto:{{ $contactEmail }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-envelope mr-1"></i> {{ $contactEmail }}</a></li>
                        @endif
                        @php $contactPhone = \App\Models\Setting::get('contact_phone', '+225 07 00 00 00 00'); @endphp
                        @if($contactPhone)
                        <li><a href="tel:{{ $contactPhone }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-phone mr-1"></i> {{ $contactPhone }}</a></li>
                        @endif
                        @php $contactAddress = \App\Models\Setting::get('contact_address', 'Plateau, Abidjan'); @endphp
                        @if($contactAddress)
                        <li><span class="text-slate-400 text-sm"><i class="fas fa-map-marker-alt mr-1"></i> {{ $contactAddress }}</span></li>
                        @endif
                    </ul>
                </div>
```

- [ ] **Step 4: Replace the social icons block**

Replace:

```blade
                <div class="flex gap-2">
                    <a href="#" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-whatsapp"></i></a>
                </div>
```

with:

```blade
                <div class="flex gap-2">
                    @if (\App\Models\Setting::get('social_facebook'))
                    <a href="{{ \App\Models\Setting::get('social_facebook') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('social_twitter'))
                    <a href="{{ \App\Models\Setting::get('social_twitter') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('social_instagram'))
                    <a href="{{ \App\Models\Setting::get('social_instagram') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('social_whatsapp'))
                    <a href="{{ \App\Models\Setting::get('social_whatsapp') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-whatsapp"></i></a>
                    @endif
                </div>
```

- [ ] **Step 5: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSettingsRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_falls_back_to_default_branding_when_no_settings_are_set(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('QCT');
        $response->assertDontSee('fab fa-facebook-f"></i></a>', false);
    }

    public function test_homepage_reflects_a_custom_site_name_and_social_link(): void
    {
        Setting::set('site_name', 'MonSite');
        Setting::set('social_facebook', 'https://facebook.com/monsite');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MonSite');
        $response->assertSee('https://facebook.com/monsite');
    }
}
```

Save as `tests/Feature/PublicSettingsRenderTest.php`.

Note on Step 5's first test: `assertDontSee('fab fa-facebook-f"></i></a>', false)` checks that the Facebook icon markup is not rendered as a dead link when no social setting is configured — the `false` argument disables HTML-escaping of the needle so the raw markup match works.

- [ ] **Step 6: Run test to verify it fails**

Run: `php artisan test --filter=PublicSettingsRenderTest`
Expected: FAIL — homepage still shows the hardcoded `Q<span class="text-blue-500">CT</span>` markup and always renders the 4 social icons, so `assertDontSee` fails until Steps 1–4 are applied.

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=PublicSettingsRenderTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 8: Commit**

```bash
git add resources/views/layout.blade.php tests/Feature/PublicSettingsRenderTest.php
git commit -m "feat: render site settings (logo, contact, social) on the public layout"
```

---

## Task 4: Testimonial flag on messages

**Files:**
- Create: `database/migrations/2026_08_22_000001_add_is_testimonial_to_messages_table.php`
- Modify: `app/Models/Message.php` (fillable)
- Modify: `app/Http/Controllers/MessageController.php` (add `toggleTestimonial`)
- Modify: `routes/web.php` (add 1 route inside the `AdminLogin` group)
- Modify: `resources/views/Admin/Message/messages.blade.php` (add toggle button/column)
- Test: `tests/Feature/AdminTestimonialToggleTest.php`

**Interfaces:**
- Produces: `messages.is_testimonial` boolean column, route `POST admin/toggle-testimonial/{id}` — consumed by Task 5's public query (`Message::where('is_testimonial', true)`).

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_testimonial')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('is_testimonial');
        });
    }
};
```

Save as `database/migrations/2026_08_22_000001_add_is_testimonial_to_messages_table.php`.

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_08_22_000001_add_is_testimonial_to_messages_table` listed as `Migrated`.

- [ ] **Step 3: Add `is_testimonial` to the `Message` model's fillable**

In `app/Models/Message.php`, change:

```php
    protected $fillable = [
      
          'name',
          'email',
          'message',
          'status',

    ];
```

to:

```php
    protected $fillable = [

          'name',
          'email',
          'message',
          'status',
          'is_testimonial',

    ];
```

- [ ] **Step 4: Add `toggleTestimonial` to `MessageController`**

In `app/Http/Controllers/MessageController.php`, add this method right after `pendingMessage`:

```php
        public function toggleTestimonial($id)
        {
            $message = Message::findOrFail($id);
            $message->update(['is_testimonial' => !$message->is_testimonial]);

            Session::flash("message", $message->is_testimonial
                ? "Message marqué comme témoignage"
                : "Témoignage retiré");

            return redirect()->back();
        }
```

(so the file ends with `}` closing `pendingMessage`, then this new method, then the final `}` closing the class.)

- [ ] **Step 5: Register the route**

In `routes/web.php`, inside the `AdminLogin` group, right after the `admin/mark-as-pending/{id}` line, add:

```php
    Route::post("admin/toggle-testimonial/{id}", [App\Http\Controllers\MessageController::class, "toggleTestimonial"]);
```

- [ ] **Step 6: Add the toggle button to the admin messages view**

In `resources/views/Admin/Message/messages.blade.php`, replace the `<thead>` row:

```blade
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Message</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                      </tr>
```

with:

```blade
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Message</th>
                        <th scope="col">Status</th>
                        <th scope="col">Témoignage</th>
                        <th scope="col">Action</th>
                      </tr>
```

and replace the `<td>` block containing the actions:

```blade
                            <td>
                                <a href="{{ url('admin/delete-message/'.$message->id) }}" class="btn btn-danger">Delete</a>
                                @if($message->status == "pending")
                                    <a href="{{ url('admin/mark-as-reply/'.$message->id) }}" class="btn btn-success">Mark as Replied</a>
                                @else
                                    <a href="{{ url('admin/mark-as-pending/'.$message->id) }}" class="btn btn-success">Mark as Pending</a>
                                @endif
                            </td>
```

with:

```blade
                            <td>
                                @if($message->is_testimonial)
                                    <span class="badge bg-primary text-white p-2">Témoignage</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('admin/delete-message/'.$message->id) }}" class="btn btn-danger">Delete</a>
                                @if($message->status == "pending")
                                    <a href="{{ url('admin/mark-as-reply/'.$message->id) }}" class="btn btn-success">Mark as Replied</a>
                                @else
                                    <a href="{{ url('admin/mark-as-pending/'.$message->id) }}" class="btn btn-success">Mark as Pending</a>
                                @endif
                                <form action="{{ url('admin/toggle-testimonial/'.$message->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        {{ $message->is_testimonial ? 'Retirer le témoignage' : 'Marquer comme témoignage' }}
                                    </button>
                                </form>
                            </td>
```

- [ ] **Step 7: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTestimonialToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_a_message_as_a_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = Message::create([
            'name' => 'Jean Koffi',
            'email' => 'jean@example.com',
            'message' => 'Merci QCT !',
        ]);

        $response = $this->actingAs($admin)->post('/admin/toggle-testimonial/' . $message->id);

        $response->assertRedirect();
        $this->assertTrue($message->fresh()->is_testimonial);
    }

    public function test_admin_can_unmark_a_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = Message::create([
            'name' => 'Jean Koffi',
            'email' => 'jean@example.com',
            'message' => 'Merci QCT !',
            'is_testimonial' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/toggle-testimonial/' . $message->id);

        $response->assertRedirect();
        $this->assertFalse($message->fresh()->is_testimonial);
    }

    public function test_non_admin_cannot_toggle_testimonial(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $message = Message::create([
            'name' => 'Jean Koffi',
            'email' => 'jean@example.com',
            'message' => 'Merci QCT !',
        ]);

        $response = $this->actingAs($user)->post('/admin/toggle-testimonial/' . $message->id);

        $response->assertRedirect('/my-account');
        $this->assertFalse($message->fresh()->is_testimonial);
    }
}
```

Save as `tests/Feature/AdminTestimonialToggleTest.php`.

- [ ] **Step 8: Run test to verify it fails**

Run: `php artisan test --filter=AdminTestimonialToggleTest`
Expected: FAIL — route not defined / `is_testimonial` not mass-assignable, until Steps 1–5 are applied.

- [ ] **Step 9: Run test to verify it passes**

Run: `php artisan test --filter=AdminTestimonialToggleTest`
Expected: `OK (3 tests, ...)`

- [ ] **Step 10: Commit**

```bash
git add database/migrations/2026_08_22_000001_add_is_testimonial_to_messages_table.php app/Models/Message.php app/Http/Controllers/MessageController.php routes/web.php resources/views/Admin/Message/messages.blade.php tests/Feature/AdminTestimonialToggleTest.php
git commit -m "feat: let admins mark a contact message as a testimonial"
```

---

## Task 5: Public testimonials display

**Files:**
- Modify: `routes/web.php` (`/` route: pass `$testimonials`)
- Modify: `resources/views/welcome.blade.php` (replace the hardcoded "TÉMOIGNAGES" section)
- Test: `tests/Feature/PublicTestimonialsDisplayTest.php`

**Interfaces:**
- Consumes: `messages.is_testimonial` from Task 4.

**Note:** `welcome.blade.php` already has a "TÉMOIGNAGES" section (a `@php $testis = [...]` array of 3 hand-written fake reviews) — this task replaces that hardcoded array with a real query, it does not add a new section.

- [ ] **Step 1: Pass `$testimonials` from the `/` route**

In `routes/web.php`, add the import at the top (alongside the existing `use App\Models\Item;` / `use App\Models\User;`):

```php
use App\Models\Message;
```

Then, inside the `Route::get('/', function () { ... });` closure, add (right after the `$resolvedItems` query, before the `return view(...)`):

```php
    $testimonials = Message::where('is_testimonial', true)
        ->latest()
        ->take(6)
        ->get();
```

and change the `return view(...)` call from:

```php
    return view('welcome', ["items" => $items, "persons" => $persons, "resolvedItems" => $resolvedItems]);
```

to:

```php
    return view('welcome', ["items" => $items, "persons" => $persons, "resolvedItems" => $resolvedItems, "testimonials" => $testimonials]);
```

- [ ] **Step 2: Replace the hardcoded testimonials section**

In `resources/views/welcome.blade.php`, replace the entire block from the `{{-- TÉMOIGNAGES --}}` comment through its closing `</section>`:

```blade
{{-- ════════════════════════════════════════
    TÉMOIGNAGES
════════════════════════════════════════ --}}
<section class="py-20 bg-[#0D1525] border-t border-slate-700">
    <div class="container mx-auto px-6">
        <div class="flex flex-col items-center text-center gap-3 mb-12">
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Témoignages</p>
            <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Ils nous font confiance</h2>
            <div class="w-10 h-[3px] bg-blue-500 rounded-sm mx-auto"></div>
        </div>

        <div class="grid grid-cols-[repeat(auto-fill,minmax(280px,1fr))] gap-5">
            @php
            $testis = [
                ['name'=>'Jean Koffi','city'=>'Abidjan','text'=>'Grâce à QCT, j\'ai retrouvé mon portefeuille avec tous mes documents en moins de 48h. Une plateforme incroyable !','stars'=>5],
                ['name'=>'Amina Traoré','city'=>'Bouaké','text'=>'Mon téléphone perdu dans un taxi a été retrouvé grâce à la communauté QCT. Merci infiniment !','stars'=>5],
                ['name'=>'Marc Kouadio','city'=>'Yamoussoukro','text'=>'J\'ai pu retrouver mon chien perdu grâce aux alertes QCT. La rapidité de la communauté est impressionnante.','stars'=>5],
            ];
            @endphp
            @foreach($testis as $t)
            <div class="bg-slate-800 border border-slate-700 rounded-[20px] p-7 flex flex-col gap-4 transition-colors hover:border-blue-500">
                <div class="text-amber-500 text-sm flex gap-[3px]">
                    @for($i=0;$i<$t['stars'];$i++)<i class="fas fa-star"></i>@endfor
                </div>
                <p class="text-sm text-slate-400 leading-relaxed flex-1 italic">"{{ $t['text'] }}"</p>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-700">
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
                        {{ strtoupper(substr($t['name'],0,1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm">{{ $t['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $t['city'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
```

with:

```blade
{{-- ════════════════════════════════════════
    TÉMOIGNAGES
════════════════════════════════════════ --}}
@if ($testimonials->isNotEmpty())
<section class="py-20 bg-[#0D1525] border-t border-slate-700">
    <div class="container mx-auto px-6">
        <div class="flex flex-col items-center text-center gap-3 mb-12">
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Témoignages</p>
            <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Ils nous font confiance</h2>
            <div class="w-10 h-[3px] bg-blue-500 rounded-sm mx-auto"></div>
        </div>

        <div class="grid grid-cols-[repeat(auto-fill,minmax(280px,1fr))] gap-5">
            @foreach($testimonials as $testimonial)
            <div class="bg-slate-800 border border-slate-700 rounded-[20px] p-7 flex flex-col gap-4 transition-colors hover:border-blue-500">
                <p class="text-sm text-slate-400 leading-relaxed flex-1 italic">"{{ Str::limit($testimonial->message, 200) }}"</p>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-700">
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm">{{ $testimonial->name }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
```

- [ ] **Step 3: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTestimonialsDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_does_not_show_testimonials_section_when_none_are_marked(): void
    {
        Message::create(['name' => 'Jean', 'email' => 'jean@example.com', 'message' => 'Non marqué']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Ils nous font confiance');
    }

    public function test_homepage_shows_testimonials_marked_by_an_admin(): void
    {
        Message::create([
            'name' => 'Amina Traoré',
            'email' => 'amina@example.com',
            'message' => 'Mon téléphone perdu a été retrouvé grâce à QCT !',
            'is_testimonial' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ils nous font confiance');
        $response->assertSee('Amina Traoré');
        $response->assertSee('Mon téléphone perdu a été retrouvé grâce à QCT !');
    }
}
```

Save as `tests/Feature/PublicTestimonialsDisplayTest.php`.

- [ ] **Step 4: Run test to verify it fails**

Run: `php artisan test --filter=PublicTestimonialsDisplayTest`
Expected: FAIL — `$testimonials` undefined in `welcome.blade.php` until Step 1 is applied; section always renders with fake data until Step 2 is applied.

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=PublicTestimonialsDisplayTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 6: Commit**

```bash
git add routes/web.php resources/views/welcome.blade.php tests/Feature/PublicTestimonialsDisplayTest.php
git commit -m "feat: show real testimonials from marked contact messages on the homepage"
```

---

## Task 6: Content pages foundation (migration + model + seeder)

**Files:**
- Create: `database/migrations/2026_08_22_000002_create_pages_table.php`
- Create: `app/Models/Page.php`
- Create: `database/seeders/PageSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php` (call `PageSeeder`)
- Test: `tests/Feature/PageSeederTest.php`

**Interfaces:**
- Produces: `App\Models\Page` (fillable `slug`, `title`, `content`), 4 seeded rows with slugs `comment-ca-marche`, `politique-confidentialite`, `cgu`, `cgv` — consumed by Task 7 (admin edit) and Task 8 (public show).

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
```

Save as `database/migrations/2026_08_22_000002_create_pages_table.php`.

- [ ] **Step 2: Write the `Page` model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content'];
}
```

Save as `app/Models/Page.php`.

- [ ] **Step 3: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Contenu de départ générique, à adapter par un administrateur
     * depuis l'espace Admin > Contenu.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'comment-ca-marche',
                'title' => 'Comment ça marche',
                'content' => <<<'HTML'
<h2>Comment fonctionne QCT ?</h2>
<p>QCT (&laquo; Qui Cherche, Trouve &raquo;) est une plateforme communautaire qui connecte les personnes ayant perdu un objet avec celles qui l'ont retrouv&eacute;, partout en C&ocirc;te d'Ivoire.</p>
<h3>1. Signalez</h3>
<p>Cr&eacute;ez une annonce en quelques minutes&nbsp;: d&eacute;crivez l'objet perdu ou trouv&eacute;, ajoutez des photos et pr&eacute;cisez le lieu et la date.</p>
<h3>2. La communaut&eacute; agit</h3>
<p>Votre annonce est visible par l'ensemble des membres de la plateforme, qui peuvent vous transmettre des informations utiles.</p>
<h3>3. Mise en relation s&eacute;curis&eacute;e</h3>
<p>Lorsqu'une correspondance est trouv&eacute;e, les deux parties sont mises en relation pour organiser la restitution de l'objet en toute confiance.</p>
<h3>4. Retrouvailles</h3>
<p>L'objet retrouve son propri&eacute;taire, ou la personne disparue est signal&eacute;e aux autorit&eacute;s comp&eacute;tentes. Pour les objets trouv&eacute;s, une d&eacute;claration au commissariat le plus proche peut &ecirc;tre demand&eacute;e avant la remise, conform&eacute;ment &agrave; la r&eacute;glementation en vigueur en C&ocirc;te d'Ivoire.</p>
HTML,
            ],
            [
                'slug' => 'politique-confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => <<<'HTML'
<h2>Politique de confidentialit&eacute;</h2>
<p>QCT accorde une grande importance &agrave; la protection de vos donn&eacute;es personnelles. Cette politique explique quelles informations nous collectons et comment elles sont utilis&eacute;es.</p>
<h3>Donn&eacute;es collect&eacute;es</h3>
<p>Nous collectons les informations que vous nous fournissez lors de votre inscription (nom, email) et lors de la publication d'une annonce (description, photos, localisation approximative).</p>
<h3>Utilisation des donn&eacute;es</h3>
<p>Vos donn&eacute;es sont utilis&eacute;es uniquement pour permettre le bon fonctionnement de la plateforme&nbsp;: mise en relation entre utilisateurs, notifications li&eacute;es &agrave; vos annonces, et am&eacute;lioration du service.</p>
<h3>Partage des donn&eacute;es</h3>
<p>Vos donn&eacute;es ne sont jamais vendues &agrave; des tiers. Elles peuvent &ecirc;tre partag&eacute;es avec les autorit&eacute;s comp&eacute;tentes (commissariats) dans le cadre l&eacute;gal de la restitution d'objets trouv&eacute;s.</p>
<h3>Vos droits</h3>
<p>Vous pouvez &agrave; tout moment demander l'acc&egrave;s, la rectification ou la suppression de vos donn&eacute;es personnelles en nous contactant via la page Contact.</p>
HTML,
            ],
            [
                'slug' => 'cgu',
                'title' => "Conditions Générales d'Utilisation",
                'content' => <<<'HTML'
<h2>Conditions G&eacute;n&eacute;rales d'Utilisation</h2>
<p>L'utilisation de la plateforme QCT implique l'acceptation pleine et enti&egrave;re des pr&eacute;sentes conditions g&eacute;n&eacute;rales d'utilisation.</p>
<h3>Objet</h3>
<p>QCT met &agrave; disposition une plateforme communautaire permettant de signaler et retrouver des objets perdus ou trouv&eacute;s, ainsi que des personnes disparues, en C&ocirc;te d'Ivoire.</p>
<h3>Inscription</h3>
<p>L'inscription est gratuite et requiert la fourniture d'informations exactes. Chaque utilisateur est responsable de la confidentialit&eacute; de ses identifiants de connexion.</p>
<h3>Obligations de l'utilisateur</h3>
<p>L'utilisateur s'engage &agrave; publier des annonces exactes et de bonne foi, &agrave; ne pas usurper l'identit&eacute; d'un tiers, et &agrave; respecter les lois en vigueur en C&ocirc;te d'Ivoire, notamment concernant la d&eacute;claration des objets trouv&eacute;s aupr&egrave;s des autorit&eacute;s.</p>
<h3>Responsabilit&eacute;</h3>
<p>QCT agit en tant qu'interm&eacute;diaire entre utilisateurs et ne garantit pas la restitution effective d'un objet ni l'exactitude des informations publi&eacute;es par les utilisateurs.</p>
HTML,
            ],
            [
                'slug' => 'cgv',
                'title' => 'Conditions Générales de Vente',
                'content' => <<<'HTML'
<h2>Conditions G&eacute;n&eacute;rales de Vente</h2>
<p>La plateforme QCT est gratuite pour la publication et la consultation d'annonces d'objets perdus ou trouv&eacute;s.</p>
<h3>Dons</h3>
<p>QCT propose une fonctionnalit&eacute; de don volontaire destin&eacute;e &agrave; soutenir le fonctionnement et le d&eacute;veloppement de la plateforme. Les dons sont trait&eacute;s par un prestataire de paiement tiers s&eacute;curis&eacute;.</p>
<h3>Aucune contrepartie commerciale</h3>
<p>Un don effectu&eacute; sur QCT ne constitue pas un paiement pour un service et n'ouvre droit &agrave; aucune contrepartie commerciale. Il s'agit d'un soutien volontaire &agrave; la mission de la plateforme.</p>
<h3>Remboursement</h3>
<p>Les dons effectu&eacute;s via QCT ne sont pas remboursables, sauf erreur manifeste de transaction signal&eacute;e dans les 48 heures suivant le paiement, &agrave; adresser via la page Contact.</p>
HTML,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], [
                'title' => $page['title'],
                'content' => $page['content'],
            ]);
        }
    }
}
```

Save as `database/seeders/PageSeeder.php`.

- [ ] **Step 4: Wire the seeder into `DatabaseSeeder`**

In `database/seeders/DatabaseSeeder.php`, change:

```php
        $this->call([
            CategorySeeder::class,
            CommissariatSeeder::class,
            UserSeeder::class,
            ItemSeeder::class,
        ]);
```

to:

```php
        $this->call([
            CategorySeeder::class,
            CommissariatSeeder::class,
            UserSeeder::class,
            ItemSeeder::class,
            PageSeeder::class,
        ]);
```

- [ ] **Step 5: Run the migration and seeder**

Run: `php artisan migrate --seed`
Expected: `2026_08_22_000002_create_pages_table` listed as `Migrated`, no seeder errors.

- [ ] **Step 6: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_the_four_expected_pages(): void
    {
        (new PageSeeder())->run();

        $slugs = Page::pluck('slug')->sort()->values()->all();

        $this->assertSame(
            ['cgu', 'cgv', 'comment-ca-marche', 'politique-confidentialite'],
            $slugs
        );
    }

    public function test_seeder_does_not_overwrite_an_already_edited_page(): void
    {
        (new PageSeeder())->run();
        $page = Page::where('slug', 'cgu')->firstOrFail();
        $page->update(['content' => 'Contenu personnalisé par un admin']);

        (new PageSeeder())->run();

        $this->assertSame('Contenu personnalisé par un admin', $page->fresh()->content);
    }
}
```

Save as `tests/Feature/PageSeederTest.php`.

- [ ] **Step 7: Run test to verify it fails**

Run: `php artisan test --filter=PageSeederTest`
Expected: FAIL — `App\Models\Page` / `Database\Seeders\PageSeeder` not found until Steps 1–3 are applied.

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=PageSeederTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 9: Commit**

```bash
git add database/migrations/2026_08_22_000002_create_pages_table.php app/Models/Page.php database/seeders/PageSeeder.php database/seeders/DatabaseSeeder.php tests/Feature/PageSeederTest.php
git commit -m "feat: add Page model with seeded content for comment-ca-marche/politique-confidentialite/cgu/cgv"
```

---

## Task 7: Admin content page management

**Files:**
- Create: `app/Http/Controllers/Admin/PageController.php`
- Create: `resources/views/Admin/Page/view_pages.blade.php`
- Create: `resources/views/Admin/Page/edit_page.blade.php`
- Modify: `routes/web.php` (add 3 routes inside the `AdminLogin` group)
- Modify: `resources/views/Admin/layout.blade.php` (add "Contenu" dropdown with direct page links)
- Test: `tests/Feature/AdminPageCrudTest.php`

**Interfaces:**
- Consumes: `App\Models\Page` from Task 6.
- Produces: routes `GET admin/pages`, `GET admin/pages/{slug}/edit`, `POST admin/pages/{slug}` (slug-addressed, not id-addressed, so the sidebar can link directly to each of the 4 fixed pages without an extra query).

- [ ] **Step 1: Write the controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pages = Page::orderBy('title')->get();

        return view('Admin.Page.view_pages', compact('pages'));
    }

    public function edit($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('Admin.Page.edit_page', compact('page'));
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $page = Page::where('slug', $slug)->firstOrFail();
        $page->update($request->only(['title', 'content']));

        Session::flash('message', 'Page mise à jour avec succès !');
        return redirect('admin/pages');
    }
}
```

Save as `app/Http/Controllers/Admin/PageController.php`.

- [ ] **Step 2: Write the list view**

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pages de contenu</h1>
        <p class="text-gray-600 mt-1">FAQ, Comment ça marche, Politique de confidentialité, CGU, CGV</p>
    </div>

    @if(Session::has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <p>{{ Session::get('message') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                Liste des pages
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière modification</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($pages as $page)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $page->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ url('admin/pages/'.$page->slug.'/edit') }}"
                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

@endsection
```

Save as `resources/views/Admin/Page/view_pages.blade.php`.

- [ ] **Step 3: Write the edit view with TinyMCE**

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier : {{ $page->title }}</h1>
        <p class="text-gray-600 mt-1">Slug : {{ $page->slug }}</p>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/pages/'.$page->slug) }}" method="post">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenu</label>
                    <textarea id="content" name="content" rows="15">{{ old('content', $page->content) }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ url('admin/pages') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<script src="{{ asset('customerdesign/assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 450,
        menubar: false,
        plugins: 'lists link table code',
        toolbar: 'undo redo | formatselect | bold italic | bullist numlist | link table | code',
    });
</script>

@endsection
```

Save as `resources/views/Admin/Page/edit_page.blade.php`.

- [ ] **Step 4: Register the routes**

In `routes/web.php`, inside the `AdminLogin` group, right after the `admin/settings` routes added in Task 2, add:

```php
    Route::get("admin/pages", [App\Http\Controllers\Admin\PageController::class, "index"]);
    Route::get("admin/pages/{slug}/edit", [App\Http\Controllers\Admin\PageController::class, "edit"]);
    Route::post("admin/pages/{slug}", [App\Http\Controllers\Admin\PageController::class, "update"]);
```

- [ ] **Step 5: Add the "Contenu" sidebar dropdown**

In `resources/views/Admin/layout.blade.php`, right after the "Paramètres du site" `<li>` added in Task 2 (and before the `@if(!empty(Auth::user()))` logout block), add:

```blade
        <!-- Content Dropdown -->
        <li>
          <button id="contentDropdown" class="flex items-center justify-between w-full p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <div class="flex items-center">
              <i class="fas fa-file-alt w-6 text-center"></i>
              <span class="ml-3">Contenu</span>
            </div>
            <i class="fas fa-chevron-down text-xs"></i>
          </button>
          <ul id="contentSubmenu" class="hidden py-2 space-y-1 pl-11">
            <li>
              <a href="{{ url('admin/pages/comment-ca-marche/edit') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                Comment ça marche
              </a>
            </li>
            <li>
              <a href="{{ url('admin/pages/politique-confidentialite/edit') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                Politique de confidentialité
              </a>
            </li>
            <li>
              <a href="{{ url('admin/pages/cgu/edit') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                CGU
              </a>
            </li>
            <li>
              <a href="{{ url('admin/pages/cgv/edit') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                CGV
              </a>
            </li>
            <li>
              <a href="{{ url('admin/faq') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                FAQ
              </a>
            </li>
          </ul>
        </li>
```

Then, in the same file's `<script>` block at the bottom, right after the existing `categoryDropdown` toggle handler, add:

```javascript
    // Toggle content dropdown
    document.getElementById('contentDropdown').addEventListener('click', function() {
      document.getElementById('contentSubmenu').classList.toggle('hidden');
      this.querySelector('i:last-child').classList.toggle('transform');
      this.querySelector('i:last-child').classList.toggle('rotate-180');
    });
```

- [ ] **Step 6: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPageCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new PageSeeder())->run();
    }

    public function test_non_admin_cannot_access_page_list(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/pages');

        $response->assertRedirect('/my-account');
    }

    public function test_admin_can_view_the_page_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/pages');

        $response->assertStatus(200);
        $response->assertSee('Comment ça marche');
    }

    public function test_admin_can_update_a_page_by_slug(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/pages/cgu', [
            'title' => "Conditions Générales d'Utilisation",
            'content' => '<p>Nouveau contenu CGU</p>',
        ]);

        $response->assertRedirect('admin/pages');
        $this->assertSame('<p>Nouveau contenu CGU</p>', Page::where('slug', 'cgu')->firstOrFail()->content);
    }
}
```

Save as `tests/Feature/AdminPageCrudTest.php`.

- [ ] **Step 7: Run test to verify it fails**

Run: `php artisan test --filter=AdminPageCrudTest`
Expected: FAIL — routes/controller/views not present until Steps 1–4 are applied.

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=AdminPageCrudTest`
Expected: `OK (3 tests, ...)`

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/Admin/PageController.php resources/views/Admin/Page/view_pages.blade.php resources/views/Admin/Page/edit_page.blade.php routes/web.php resources/views/Admin/layout.blade.php tests/Feature/AdminPageCrudTest.php
git commit -m "feat: add admin CRUD for static content pages (comment-ca-marche/politique-confidentialite/cgu/cgv)"
```

---

## Task 8: Public content page display

**Files:**
- Create: `app/Http/Controllers/PageController.php`
- Create: `resources/views/page_detail.blade.php`
- Modify: `routes/web.php` (add 1 public route)
- Test: `tests/Feature/PublicPageShowTest.php`

**Interfaces:**
- Consumes: `App\Models\Page` from Task 6.
- Produces: route `GET /page/{slug}` — consumed by Task 11's footer link rewrite.

- [ ] **Step 1: Write the public controller**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('page_detail', compact('page'));
    }
}
```

Save as `app/Http/Controllers/PageController.php`.

- [ ] **Step 2: Write the public view**

```blade
@extends('layout')

@section('content')
<section class="py-16 px-6">
    <div class="container mx-auto max-w-[820px]">
        <h1 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight mb-8">{{ $page->title }}</h1>
        <div class="prose prose-invert prose-slate max-w-none text-slate-300 leading-relaxed">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endsection
```

Save as `resources/views/page_detail.blade.php`.

- [ ] **Step 3: Register the route**

In `routes/web.php`, add this line right after the `contact-us` route (still outside the `AdminLogin` group — this is a public route):

```php
Route::get('/page/{slug}', [App\Http\Controllers\PageController::class, 'show']);
```

- [ ] **Step 4: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new PageSeeder())->run();
    }

    public function test_existing_page_slug_returns_200_with_its_content(): void
    {
        $response = $this->get('/page/cgu');

        $response->assertStatus(200);
        $response->assertSee("Conditions Générales d'Utilisation");
    }

    public function test_unknown_slug_returns_404(): void
    {
        $response = $this->get('/page/does-not-exist');

        $response->assertStatus(404);
    }
}
```

Save as `tests/Feature/PublicPageShowTest.php`.

- [ ] **Step 5: Run test to verify it fails**

Run: `php artisan test --filter=PublicPageShowTest`
Expected: FAIL — route `/page/{slug}` not defined until Steps 1–3 are applied.

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=PublicPageShowTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/PageController.php resources/views/page_detail.blade.php routes/web.php tests/Feature/PublicPageShowTest.php
git commit -m "feat: add public route to display a content page by slug"
```

---

## Task 9: FAQ foundation (migration + model + seeder)

**Files:**
- Create: `database/migrations/2026_08_22_000003_create_faq_items_table.php`
- Create: `app/Models/FaqItem.php`
- Create: `database/seeders/FaqItemSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php` (call `FaqItemSeeder`)
- Test: `tests/Feature/FaqItemSeederTest.php`

**Interfaces:**
- Produces: `App\Models\FaqItem` (fillable `question`, `answer`, `order`), 6 seeded rows — consumed by Task 10 (admin CRUD) and Task 11 (public display).

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_items');
    }
};
```

Save as `database/migrations/2026_08_22_000003_create_faq_items_table.php`.

- [ ] **Step 2: Write the `FaqItem` model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = ['question', 'answer', 'order'];
}
```

Save as `app/Models/FaqItem.php`.

- [ ] **Step 3: Write the seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'question' => 'Comment signaler un objet perdu ou trouvé ?',
                'answer' => "Connectez-vous à votre compte, cliquez sur « Signaler une perte » ou « Signaler une trouvaille », puis remplissez le formulaire avec une description précise et des photos si possible.",
            ],
            [
                'question' => "L'utilisation de QCT est-elle payante ?",
                'answer' => 'Non, QCT est entièrement gratuit pour publier et consulter des annonces. Un don volontaire est possible pour soutenir la plateforme.',
            ],
            [
                'question' => "Que faire si je retrouve un objet appartenant à quelqu'un d'autre ?",
                'answer' => "Publiez une annonce « objet trouvé » avec un maximum de détails. Selon la réglementation, une déclaration au commissariat le plus proche peut être nécessaire avant la remise de l'objet.",
            ],
            [
                'question' => "Comment récupérer un objet qui m'appartient ?",
                'answer' => "Recherchez votre objet dans la liste des annonces, puis utilisez le bouton « Cet objet m'appartient » sur l'annonce correspondante pour envoyer une demande au déclarant.",
            ],
            [
                'question' => 'Mes données personnelles sont-elles protégées ?',
                'answer' => 'Oui, consultez notre page Politique de confidentialité pour savoir comment vos données sont collectées et utilisées.',
            ],
            [
                'question' => "Comment signaler la disparition d'une personne ?",
                'answer' => "Utilisez la catégorie « Personnes » lors de la création d'une annonce. Ces signalements apparaissent en priorité dans la section « Personnes disparues » de la page d'accueil.",
            ],
        ];

        foreach ($items as $index => $item) {
            FaqItem::firstOrCreate(
                ['question' => $item['question']],
                ['answer' => $item['answer'], 'order' => $index]
            );
        }
    }
}
```

Save as `database/seeders/FaqItemSeeder.php`.

- [ ] **Step 4: Wire the seeder into `DatabaseSeeder`**

In `database/seeders/DatabaseSeeder.php`, change:

```php
        $this->call([
            CategorySeeder::class,
            CommissariatSeeder::class,
            UserSeeder::class,
            ItemSeeder::class,
            PageSeeder::class,
        ]);
```

to:

```php
        $this->call([
            CategorySeeder::class,
            CommissariatSeeder::class,
            UserSeeder::class,
            ItemSeeder::class,
            PageSeeder::class,
            FaqItemSeeder::class,
        ]);
```

- [ ] **Step 5: Run the migration and seeder**

Run: `php artisan migrate --seed`
Expected: `2026_08_22_000003_create_faq_items_table` listed as `Migrated`, no seeder errors.

- [ ] **Step 6: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use Database\Seeders\FaqItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqItemSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_six_faq_items(): void
    {
        (new FaqItemSeeder())->run();

        $this->assertSame(6, FaqItem::count());
    }

    public function test_seeder_does_not_duplicate_or_overwrite_edited_answers(): void
    {
        (new FaqItemSeeder())->run();
        $item = FaqItem::where('question', "L'utilisation de QCT est-elle payante ?")->firstOrFail();
        $item->update(['answer' => 'Réponse modifiée par un admin']);

        (new FaqItemSeeder())->run();

        $this->assertSame(6, FaqItem::count());
        $this->assertSame('Réponse modifiée par un admin', $item->fresh()->answer);
    }
}
```

Save as `tests/Feature/FaqItemSeederTest.php`.

- [ ] **Step 7: Run test to verify it fails**

Run: `php artisan test --filter=FaqItemSeederTest`
Expected: FAIL — `App\Models\FaqItem` / `Database\Seeders\FaqItemSeeder` not found until Steps 1–3 are applied.

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=FaqItemSeederTest`
Expected: `OK (2 tests, ...)`

- [ ] **Step 9: Commit**

```bash
git add database/migrations/2026_08_22_000003_create_faq_items_table.php app/Models/FaqItem.php database/seeders/FaqItemSeeder.php database/seeders/DatabaseSeeder.php tests/Feature/FaqItemSeederTest.php
git commit -m "feat: add FaqItem model with seeded starter questions"
```

---

## Task 10: Admin FAQ management (full CRUD)

**Files:**
- Create: `app/Http/Controllers/Admin/FaqController.php`
- Create: `resources/views/Admin/Faq/view_faq.blade.php`
- Create: `resources/views/Admin/Faq/add_faq.blade.php`
- Create: `resources/views/Admin/Faq/edit_faq.blade.php`
- Modify: `routes/web.php` (add 5 routes inside the `AdminLogin` group)
- Test: `tests/Feature/AdminFaqCrudTest.php`

**Interfaces:**
- Consumes: `App\Models\FaqItem` from Task 9.
- Produces: routes `GET admin/faq`, `GET admin/add-faq`, `POST admin/save-faq`, `GET admin/edit-faq/{id}`, `POST admin/update-faq/{id}`, `POST admin/delete-faq/{id}` — the sidebar "FAQ" link (added in Task 7 Step 5) targets `admin/faq`.

- [ ] **Step 1: Write the controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $faqItems = FaqItem::orderBy('order')->get();

        return view('Admin.Faq.view_faq', compact('faqItems'));
    }

    public function create()
    {
        return view('Admin.Faq.add_faq');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        FaqItem::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        Session::flash('message', 'Question ajoutée avec succès !');
        return redirect('admin/faq');
    }

    public function edit($id)
    {
        $faqItem = FaqItem::findOrFail($id);

        return view('Admin.Faq.edit_faq', compact('faqItem'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $faqItem = FaqItem::findOrFail($id);
        $faqItem->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        Session::flash('message', 'Question mise à jour avec succès !');
        return redirect('admin/faq');
    }

    public function delete($id)
    {
        FaqItem::findOrFail($id)->delete();

        Session::flash('message', 'Question supprimée avec succès !');
        return redirect('admin/faq');
    }
}
```

Save as `app/Http/Controllers/Admin/FaqController.php`.

- [ ] **Step 2: Write the list view**

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">FAQ</h1>
            <p class="text-gray-600 mt-1">Questions fréquentes affichées sur la page publique /faq</p>
        </div>
        <a href="{{ url('admin/add-faq') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center justify-center transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nouvelle question
        </a>
    </div>

    @if(Session::has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <p>{{ Session::get('message') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($faqItems as $faqItem)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $faqItem->order }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $faqItem->question }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ url('admin/edit-faq/'.$faqItem->id) }}"
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ url('admin/delete-faq/'.$faqItem->id) }}" method="POST"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                                    @csrf
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-full hover:bg-red-50"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Aucune question pour le moment</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

@endsection
```

Save as `resources/views/Admin/Faq/view_faq.blade.php`.

- [ ] **Step 3: Write the create view**

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ajouter une question</h1>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/save-faq') }}" method="post">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" value="{{ old('question') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Réponse <span class="text-red-500">*</span></label>
                    <textarea name="answer" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>{{ old('answer') }}</textarea>
                </div>

                <div class="mb-6 max-w-[160px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ url('admin/faq') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection
```

Save as `resources/views/Admin/Faq/add_faq.blade.php`.

- [ ] **Step 4: Write the edit view**

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier la question</h1>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/update-faq/'.$faqItem->id) }}" method="post">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" value="{{ old('question', $faqItem->question) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Réponse <span class="text-red-500">*</span></label>
                    <textarea name="answer" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>{{ old('answer', $faqItem->answer) }}</textarea>
                </div>

                <div class="mb-6 max-w-[160px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="order" value="{{ old('order', $faqItem->order) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ url('admin/faq') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection
```

Save as `resources/views/Admin/Faq/edit_faq.blade.php`.

- [ ] **Step 5: Register the routes**

In `routes/web.php`, inside the `AdminLogin` group, right after the `admin/pages/{slug}` routes added in Task 7, add:

```php
    Route::get("admin/faq", [App\Http\Controllers\Admin\FaqController::class, "index"]);
    Route::get("admin/add-faq", [App\Http\Controllers\Admin\FaqController::class, "create"]);
    Route::post("admin/save-faq", [App\Http\Controllers\Admin\FaqController::class, "store"]);
    Route::get("admin/edit-faq/{id}", [App\Http\Controllers\Admin\FaqController::class, "edit"]);
    Route::post("admin/update-faq/{id}", [App\Http\Controllers\Admin\FaqController::class, "update"]);
    Route::post("admin/delete-faq/{id}", [App\Http\Controllers\Admin\FaqController::class, "delete"]);
```

- [ ] **Step 6: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFaqCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_faq_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/faq');

        $response->assertRedirect('/my-account');
    }

    public function test_admin_can_create_a_faq_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/save-faq', [
            'question' => 'Ma question de test ?',
            'answer' => 'Ma réponse de test.',
            'order' => 3,
        ]);

        $response->assertRedirect('admin/faq');
        $this->assertDatabaseHas('faq_items', ['question' => 'Ma question de test ?', 'order' => 3]);
    }

    public function test_admin_can_update_a_faq_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faqItem = FaqItem::create(['question' => 'Q ?', 'answer' => 'A.', 'order' => 0]);

        $response = $this->actingAs($admin)->post('/admin/update-faq/' . $faqItem->id, [
            'question' => 'Q modifiée ?',
            'answer' => 'A modifiée.',
            'order' => 1,
        ]);

        $response->assertRedirect('admin/faq');
        $this->assertSame('Q modifiée ?', $faqItem->fresh()->question);
    }

    public function test_admin_can_delete_a_faq_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faqItem = FaqItem::create(['question' => 'Q ?', 'answer' => 'A.', 'order' => 0]);

        $response = $this->actingAs($admin)->post('/admin/delete-faq/' . $faqItem->id);

        $response->assertRedirect('admin/faq');
        $this->assertDatabaseMissing('faq_items', ['id' => $faqItem->id]);
    }
}
```

Save as `tests/Feature/AdminFaqCrudTest.php`.

- [ ] **Step 7: Run test to verify it fails**

Run: `php artisan test --filter=AdminFaqCrudTest`
Expected: FAIL — routes/controller/views not present until Steps 1–5 are applied.

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=AdminFaqCrudTest`
Expected: `OK (4 tests, ...)`

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/Admin/FaqController.php resources/views/Admin/Faq/view_faq.blade.php resources/views/Admin/Faq/add_faq.blade.php resources/views/Admin/Faq/edit_faq.blade.php routes/web.php tests/Feature/AdminFaqCrudTest.php
git commit -m "feat: add admin CRUD for FAQ items"
```

---

## Task 11: Public FAQ page + final footer link wiring

**Files:**
- Modify: `app/Http/Controllers/PageController.php` (add `faq` method)
- Create: `resources/views/faq.blade.php`
- Modify: `routes/web.php` (add 1 public route)
- Modify: `resources/views/layout.blade.php` (wire the footer "Aide" column to the 5 real URLs)
- Test: `tests/Feature/PublicFaqPageTest.php`

**Interfaces:**
- Consumes: `App\Models\FaqItem` from Task 9, route `GET /page/{slug}` from Task 8.

- [ ] **Step 1: Add the `faq` method to the public `PageController`**

In `app/Http/Controllers/PageController.php`, change:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('page_detail', compact('page'));
    }
}
```

to:

```php
<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('page_detail', compact('page'));
    }

    public function faq()
    {
        $faqItems = FaqItem::orderBy('order')->get();

        return view('faq', compact('faqItems'));
    }
}
```

- [ ] **Step 2: Write the public FAQ view**

```blade
@extends('layout')

@section('content')
<section class="py-16 px-6">
    <div class="container mx-auto max-w-[820px]">
        <h1 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight mb-10">Questions fréquentes</h1>

        <div class="flex flex-col gap-3" id="faqAccordion">
            @forelse ($faqItems as $faqItem)
            <div class="bg-slate-800 border border-slate-700 rounded-[14px] overflow-hidden">
                <button type="button"
                        class="faq-toggle w-full flex items-center justify-between text-left px-5 py-4 text-slate-50 font-semibold cursor-pointer bg-transparent border-none">
                    <span>{{ $faqItem->question }}</span>
                    <i class="fas fa-chevron-down text-slate-400 text-sm transition-transform"></i>
                </button>
                <div class="faq-answer hidden px-5 pb-4 text-sm text-slate-400 leading-relaxed">
                    {{ $faqItem->answer }}
                </div>
            </div>
            @empty
            <p class="text-slate-400">Aucune question pour le moment.</p>
            @endforelse
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.faq-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var answer = btn.nextElementSibling;
        var icon = btn.querySelector('i');
        answer.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });
});
</script>
@endsection
```

Save as `resources/views/faq.blade.php`.

- [ ] **Step 3: Register the route**

In `routes/web.php`, add this line right after the `/page/{slug}` route added in Task 8:

```php
Route::get('/faq', [App\Http\Controllers\PageController::class, 'faq']);
```

- [ ] **Step 4: Wire the footer "Aide" column**

In `resources/views/layout.blade.php`, replace:

```blade
                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Aide</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        <li><a href="#" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Comment ça marche</a></li>
                        <li><a href="#" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">FAQ</a></li>
                        <li><a href="#" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Politique de confidentialité</a></li>
                        <li><a href="#" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Conditions d'utilisation</a></li>
                    </ul>
                </div>
```

with:

```blade
                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Aide</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        <li><a href="{{ url('/page/comment-ca-marche') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Comment ça marche</a></li>
                        <li><a href="{{ url('/faq') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">FAQ</a></li>
                        <li><a href="{{ url('/page/politique-confidentialite') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Politique de confidentialité</a></li>
                        <li><a href="{{ url('/page/cgu') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Conditions d'utilisation</a></li>
                        <li><a href="{{ url('/page/cgv') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Conditions générales de vente</a></li>
                    </ul>
                </div>
```

- [ ] **Step 5: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Database\Seeders\FaqItemSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFaqPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_lists_seeded_questions_in_order(): void
    {
        (new FaqItemSeeder())->run();

        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Comment signaler un objet perdu ou trouvé ?');
    }

    public function test_faq_page_renders_without_error_when_empty(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Aucune question pour le moment.');
    }

    public function test_homepage_footer_links_to_faq_and_content_pages(): void
    {
        (new PageSeeder())->run();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(url('/faq'), false);
        $response->assertSee(url('/page/cgu'), false);
        $response->assertSee(url('/page/cgv'), false);
    }
}
```

Save as `tests/Feature/PublicFaqPageTest.php`.

- [ ] **Step 6: Run test to verify it fails**

Run: `php artisan test --filter=PublicFaqPageTest`
Expected: FAIL — route `/faq` not defined and footer still uses `#` links, until Steps 1–4 are applied.

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=PublicFaqPageTest`
Expected: `OK (3 tests, ...)`

- [ ] **Step 8: Run the full test suite to confirm no regressions**

Run: `php artisan test`
Expected: all tests pass (existing suite + all tests added across Tasks 1–11).

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/PageController.php resources/views/faq.blade.php routes/web.php resources/views/layout.blade.php tests/Feature/PublicFaqPageTest.php
git commit -m "feat: add public FAQ page and wire footer help links to real content pages"
```
