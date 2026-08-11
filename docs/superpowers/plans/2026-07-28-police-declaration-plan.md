# Déclaration au commissariat — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permettre à un trouveur de déclarer dans quel commissariat il a déposé un objet trouvé, avec un annuaire de commissariats géré en Admin, avant que l'objet ne puisse passer au statut `found` — en finissant au passage le câblage de l'action `itemFound`, qui existe déjà côté route/contrôleur mais n'a jamais été reliée à une vue.

**Architecture:** Laravel 10 / MySQL. Deux nouvelles tables (`commissariats`, `item_police_declarations`) et leurs modèles Eloquent. Un CRUD Admin classique (calqué sur `Admin\CategoryController`) pour gérer l'annuaire. `ItemController::itemFound()` et `updateItem()` gagnent une branche de validation/upsert conditionnelle. Les vues `item_detail.blade.php`, `item_edit.blade.php` et `Admin/LostFound/detail.blade.php` affichent/éditent la déclaration selon des règles de visibilité (commissariat public, référence privée).

**Tech Stack:** PHP 8.1+, Laravel 10.48, MySQL 8, PHPUnit 10 (`php artisan test`), Tailwind (vues user), NiceAdmin/Tailwind CDN (vues admin).

## Global Constraints

- PHP ^8.1, Laravel ^10.8 — aucune nouvelle dépendance Composer.
- Tous les textes visibles (labels, messages flash, placeholders) restent en français.
- Les uploads suivent le pattern existant : `$file->move(public_path($folder), $filename)`, jamais la façade `Storage` (voir `ItemController::ITEM_IMAGES_FOLDER`).
- Nouvelles tables uniquement : ne jamais modifier les migrations historiques (`2023_05_01_*`, `2023_05_02_*`, `2024_06_18_*`, `2026_07_26_*`).
- La déclaration au commissariat n'est obligatoire que pour `items.status === 'found'` ; pour `status === 'lost'`, `itemFound()` garde son comportement actuel (aucune déclaration exigée).
- Le déclarant d'une `item_police_declarations` est toujours l'auteur de l'item (`item->user_id`) — c'est lui qui appelle `itemFound()`/`updateItem()` sous couvert de `authorizeItem()`.
- Visibilité : nom + commune du commissariat publics dès qu'une déclaration existe ; `declaration_number`/`receipt_photo` visibles seulement par le déclarant, par l'admin, ou par le réclamant (`found_user_id`) une fois `lost_found_status === 'returned'` (pas dès `ownership_claimed`).
- Les tests s'exécutent contre la base MySQL dédiée `findme_test` (déjà configurée dans `.env.testing`), jamais contre `findme`.
- `git commit` à la fin de chaque tâche, avec uniquement les fichiers de cette tâche (pas de `git add -A`).

---

## File Structure

**Nouveaux fichiers :**
- `database/migrations/2026_07_28_000000_create_commissariats_table.php`
- `database/migrations/2026_07_28_000001_create_item_police_declarations_table.php`
- `app/Models/Commissariat.php`
- `app/Models/ItemPoliceDeclaration.php`
- `database/factories/CommissariatFactory.php`
- `database/factories/ItemPoliceDeclarationFactory.php`
- `database/seeders/CommissariatSeeder.php`
- `app/Http/Controllers/Admin/CommissariatController.php`
- `resources/views/Admin/Commissariats/view_commissariats.blade.php`
- `resources/views/Admin/Commissariats/add_commissariat.blade.php`
- `resources/views/Admin/Commissariats/edit_commissariat.blade.php`
- `tests/Feature/ItemPoliceDeclarationRelationTest.php`
- `tests/Feature/CommissariatSeederTest.php`
- `tests/Feature/AdminCommissariatCrudTest.php`
- `tests/Feature/ItemFoundDeclarationTest.php`
- `tests/Feature/ItemDetailPoliceDeclarationVisibilityTest.php`
- `tests/Feature/ItemPoliceDeclarationEditTest.php`
- `tests/Feature/AdminPoliceDeclarationAuditTest.php`

**Fichiers modifiés :**
- `app/Models/Item.php` — ajout de `policeDeclaration()`.
- `database/seeders/DatabaseSeeder.php` — appel de `CommissariatSeeder`.
- `routes/web.php` — nom de route `item-found`, routes `admin/*-commissariat*`.
- `app/Http/Controllers/User/ItemController.php` — `itemFound()`, `updateItem()`, `itemDetail()`, méthode privée `upsertPoliceDeclaration()`.
- `app/Http/Controllers/Admin/LostFoundController.php` — eager-load de `policeDeclaration.commissariat`.
- `resources/views/item_detail.blade.php` — section déclaration (formulaire + affichage).
- `resources/views/item_edit.blade.php` — section d'édition de la déclaration existante.
- `resources/views/Admin/LostFound/detail.blade.php` — carte d'audit de la déclaration.
- `resources/views/Admin/layout.blade.php` — lien de navigation "Commissariats".

---

### Task 1: Schéma BDD, modèles et factories

**Files:**
- Create: `database/migrations/2026_07_28_000000_create_commissariats_table.php`
- Create: `database/migrations/2026_07_28_000001_create_item_police_declarations_table.php`
- Create: `app/Models/Commissariat.php`
- Create: `app/Models/ItemPoliceDeclaration.php`
- Create: `database/factories/CommissariatFactory.php`
- Create: `database/factories/ItemPoliceDeclarationFactory.php`
- Modify: `app/Models/Item.php`
- Test: `tests/Feature/ItemPoliceDeclarationRelationTest.php`

**Interfaces:**
- Produces: `Commissariat` (fillable `name, commune, city, phone, address, is_active`; cast `is_active` → bool), `ItemPoliceDeclaration` (fillable `item_id, commissariat_id, declared_by_user_id, declaration_number, receipt_photo, declared_at`; cast `declared_at` → datetime; relations `item()`, `commissariat()`, `declaredBy()`), `Item::policeDeclaration()` (hasOne).
- Consumes: rien (fondation pour toutes les tâches suivantes).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPoliceDeclarationRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_has_one_police_declaration_with_commissariat(): void
    {
        $item = Item::factory()->create(['status' => 'found']);
        $commissariat = Commissariat::factory()->create(['name' => 'Commissariat de Cocody']);
        $declarant = User::factory()->create();

        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $declarant->id,
            'declaration_number' => 'DEC-2026-001',
        ]);

        $item->refresh();

        $this->assertNotNull($item->policeDeclaration);
        $this->assertEquals('Commissariat de Cocody', $item->policeDeclaration->commissariat->name);
        $this->assertEquals('DEC-2026-001', $item->policeDeclaration->declaration_number);
        $this->assertEquals($declarant->id, $item->policeDeclaration->declaredBy->id);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ItemPoliceDeclarationRelationTest`
Expected: FAIL — `Class "App\Models\Commissariat" not found` (ou table `commissariats` inexistante).

- [ ] **Step 3: Create the migrations**

`database/migrations/2026_07_28_000000_create_commissariats_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissariats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('commune');
            $table->string('city')->default('Abidjan');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissariats');
    }
};
```

`database/migrations/2026_07_28_000001_create_item_police_declarations_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_police_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->unique()->constrained('items')->cascadeOnDelete();
            $table->foreignId('commissariat_id')->constrained('commissariats');
            $table->foreignId('declared_by_user_id')->constrained('users');
            $table->string('declaration_number');
            $table->string('receipt_photo')->nullable();
            $table->timestamp('declared_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_police_declarations');
    }
};
```

- [ ] **Step 4: Create the models**

`app/Models/Commissariat.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commissariat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'commune',
        'city',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function declarations()
    {
        return $this->hasMany(ItemPoliceDeclaration::class);
    }
}
```

`app/Models/ItemPoliceDeclaration.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPoliceDeclaration extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'commissariat_id',
        'declared_by_user_id',
        'declaration_number',
        'receipt_photo',
        'declared_at',
    ];

    protected $casts = [
        'declared_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function commissariat()
    {
        return $this->belongsTo(Commissariat::class);
    }

    public function declaredBy()
    {
        return $this->belongsTo(User::class, 'declared_by_user_id');
    }
}
```

Add to `app/Models/Item.php` (inside the class, alongside `foundUser()`):

```php
    /**
     * Déclaration de dépôt au commissariat, le cas échéant
     */
    public function policeDeclaration()
    {
        return $this->hasOne(ItemPoliceDeclaration::class);
    }
```

- [ ] **Step 5: Create the factories**

`database/factories/CommissariatFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commissariat>
 */
class CommissariatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Commissariat de ' . fake()->city(),
            'commune' => fake()->city(),
            'city' => 'Abidjan',
            'phone' => null,
            'address' => null,
            'is_active' => true,
        ];
    }
}
```

`database/factories/ItemPoliceDeclarationFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemPoliceDeclaration>
 */
class ItemPoliceDeclarationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'commissariat_id' => Commissariat::factory(),
            'declared_by_user_id' => User::factory(),
            'declaration_number' => strtoupper(fake()->bothify('DEC-####-????')),
            'receipt_photo' => null,
            'declared_at' => now(),
        ];
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=ItemPoliceDeclarationRelationTest`
Expected: PASS

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_07_28_000000_create_commissariats_table.php database/migrations/2026_07_28_000001_create_item_police_declarations_table.php app/Models/Commissariat.php app/Models/ItemPoliceDeclaration.php app/Models/Item.php database/factories/CommissariatFactory.php database/factories/ItemPoliceDeclarationFactory.php tests/Feature/ItemPoliceDeclarationRelationTest.php
git commit -m "feat: add commissariats and item_police_declarations schema"
```

---

### Task 2: Annuaire seedé

**Files:**
- Create: `database/seeders/CommissariatSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/CommissariatSeederTest.php`

**Interfaces:**
- Consumes: `Commissariat` (Task 1).
- Produces: `CommissariatSeeder::run()` — appelable indépendamment via `$this->seed(CommissariatSeeder::class)`.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use Database\Seeders\CommissariatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissariatSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_active_commissariats_for_abidjan_communes(): void
    {
        $this->seed(CommissariatSeeder::class);

        $this->assertGreaterThanOrEqual(8, Commissariat::where('is_active', true)->count());
        $this->assertDatabaseHas('commissariats', ['commune' => 'Cocody', 'city' => 'Abidjan']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CommissariatSeederTest`
Expected: FAIL — `Class "Database\Seeders\CommissariatSeeder" not found`

- [ ] **Step 3: Write the seeder**

`database/seeders/CommissariatSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Commissariat;
use Illuminate\Database\Seeder;

class CommissariatSeeder extends Seeder
{
    /**
     * Noms et communes uniquement : téléphone et adresse doivent être
     * vérifiés et complétés par un administrateur avant d'être
     * communiqués aux utilisateurs — on n'invente pas de coordonnées
     * institutionnelles non vérifiées.
     */
    public function run(): void
    {
        $commissariats = [
            ['name' => 'Commissariat du Plateau', 'commune' => 'Le Plateau'],
            ['name' => 'Commissariat de Cocody', 'commune' => 'Cocody'],
            ['name' => 'Commissariat de Yopougon', 'commune' => 'Yopougon'],
            ['name' => "Commissariat d'Adjamé", 'commune' => 'Adjamé'],
            ['name' => 'Commissariat de Treichville', 'commune' => 'Treichville'],
            ['name' => 'Commissariat de Marcory', 'commune' => 'Marcory'],
            ['name' => 'Commissariat de Koumassi', 'commune' => 'Koumassi'],
            ['name' => "Commissariat d'Abobo", 'commune' => 'Abobo'],
        ];

        foreach ($commissariats as $commissariat) {
            Commissariat::firstOrCreate(
                ['name' => $commissariat['name'], 'commune' => $commissariat['commune']],
                ['city' => 'Abidjan', 'is_active' => true]
            );
        }
    }
}
```

Update `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CommissariatSeeder::class);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=CommissariatSeederTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add database/seeders/CommissariatSeeder.php database/seeders/DatabaseSeeder.php tests/Feature/CommissariatSeederTest.php
git commit -m "feat: seed Abidjan commissariat directory"
```

---

### Task 3: CRUD Admin de l'annuaire

**Files:**
- Create: `app/Http/Controllers/Admin/CommissariatController.php`
- Create: `resources/views/Admin/Commissariats/view_commissariats.blade.php`
- Create: `resources/views/Admin/Commissariats/add_commissariat.blade.php`
- Create: `resources/views/Admin/Commissariats/edit_commissariat.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/Admin/layout.blade.php`
- Test: `tests/Feature/AdminCommissariatCrudTest.php`

**Interfaces:**
- Consumes: `Commissariat` (Task 1).
- Produces: routes `GET admin/commissariats`, `GET admin/add-commissariat`, `POST admin/save-commissariat`, `GET admin/edit-commissariat/{id}`, `POST admin/update-commissariat/{id}`, `POST admin/toggle-commissariat/{id}`.

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommissariatCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_commissariat(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/save-commissariat', [
            'name' => 'Commissariat de Yopougon',
            'commune' => 'Yopougon',
            'city' => 'Abidjan',
        ]);

        $response->assertRedirect('admin/commissariats');
        $this->assertDatabaseHas('commissariats', ['name' => 'Commissariat de Yopougon', 'is_active' => true]);
    }

    public function test_admin_can_toggle_a_commissariat_active_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $commissariat = Commissariat::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->post('/admin/toggle-commissariat/' . $commissariat->id);

        $response->assertRedirect('admin/commissariats');
        $this->assertFalse($commissariat->fresh()->is_active);
    }

    public function test_non_admin_cannot_access_commissariat_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/commissariats');

        $response->assertRedirect('/my-account');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=AdminCommissariatCrudTest`
Expected: FAIL — routes `admin/save-commissariat` / `admin/toggle-commissariat` do not exist (404).

- [ ] **Step 3: Write the controller**

`app/Http/Controllers/Admin/CommissariatController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commissariat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CommissariatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $commissariats = Commissariat::orderBy('commune')->get();

        return view('Admin.Commissariats.view_commissariats', compact('commissariats'));
    }

    public function create()
    {
        return view('Admin.Commissariats.add_commissariat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);

        Commissariat::create([
            'name' => $request->name,
            'commune' => $request->commune,
            'city' => $request->city,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => true,
        ]);

        Session::flash('message', 'Commissariat ajouté avec succès !');
        return redirect('admin/commissariats');
    }

    public function edit($id)
    {
        $commissariat = Commissariat::findOrFail($id);

        return view('Admin.Commissariats.edit_commissariat', compact('commissariat'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);

        $commissariat = Commissariat::findOrFail($id);
        $commissariat->update($request->only(['name', 'commune', 'city', 'phone', 'address']));

        Session::flash('message', 'Commissariat mis à jour avec succès !');
        return redirect('admin/commissariats');
    }

    public function toggleActive($id)
    {
        $commissariat = Commissariat::findOrFail($id);
        $commissariat->update(['is_active' => !$commissariat->is_active]);

        Session::flash('message', $commissariat->is_active
            ? 'Commissariat réactivé.'
            : 'Commissariat désactivé.');
        return redirect('admin/commissariats');
    }
}
```

- [ ] **Step 4: Add the routes**

In `routes/web.php`, inside the existing `Route::middleware(['AdminLogin'])->group(function () { ... })` block, right after the `admin/lost-and-found` routes:

```php
    Route::get("admin/commissariats", [App\Http\Controllers\Admin\CommissariatController::class, "index"]);
    Route::get("admin/add-commissariat", [App\Http\Controllers\Admin\CommissariatController::class, "create"]);
    Route::post("admin/save-commissariat", [App\Http\Controllers\Admin\CommissariatController::class, "store"]);
    Route::get("admin/edit-commissariat/{id}", [App\Http\Controllers\Admin\CommissariatController::class, "edit"]);
    Route::post("admin/update-commissariat/{id}", [App\Http\Controllers\Admin\CommissariatController::class, "update"]);
    Route::post("admin/toggle-commissariat/{id}", [App\Http\Controllers\Admin\CommissariatController::class, "toggleActive"]);
```

- [ ] **Step 5: Write the views**

`resources/views/Admin/Commissariats/view_commissariats.blade.php`:

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Commissariats</h1>
            <p class="text-gray-600 mt-1">Annuaire des commissariats pour les déclarations de dépôt</p>
        </div>
        <a href="{{ url('admin/add-commissariat') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center justify-center transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Commissariat
        </a>
    </div>

    @if(session('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <div>
            <p class="font-medium">Succès !</p>
            <p>{{ session('message') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-alt mr-2 text-blue-600"></i>
                Liste des Commissariats
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commune</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($commissariats as $commissariat)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $commissariat->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $commissariat->commune }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $commissariat->city }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs {{ $commissariat->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                                {{ $commissariat->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ url('admin/edit-commissariat/'.$commissariat->id) }}"
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ url('admin/toggle-commissariat/'.$commissariat->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 p-2 rounded-full hover:bg-gray-100"
                                            title="{{ $commissariat->is_active ? 'Désactiver' : 'Réactiver' }}">
                                        <i class="fas fa-{{ $commissariat->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Aucun commissariat enregistré</p>
                                <a href="{{ url('admin/add-commissariat') }}" class="mt-4 text-blue-600 hover:text-blue-800 flex items-center">
                                    <i class="fas fa-plus mr-1"></i> Ajouter un commissariat
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
```

`resources/views/Admin/Commissariats/add_commissariat.blade.php`:

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ajouter un commissariat</h1>
        <nav class="flex mt-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li class="inline-flex items-center">
                    <a href="{{ url('admin/dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <a href="{{ url('admin/commissariats') }}" class="text-sm text-gray-600 hover:text-blue-600">Commissariats</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-sm font-medium text-blue-600 ml-1">Nouveau commissariat</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                Formulaire de création
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ URL::to('admin/save-commissariat') }}" method="post">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom du commissariat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Commissariat du Plateau" required>
                    </div>

                    <div>
                        <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">
                            Commune <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="commune" name="commune" value="{{ old('commune') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Le Plateau" required>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city', 'Abidjan') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="À vérifier avant de renseigner">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse
                        </label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="À vérifier avant de renseigner">
                    </div>
                </div>

                <div class="flex justify-end mt-8 space-x-3">
                    <button type="reset" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
                        <i class="fas fa-undo mr-2"></i> Réinitialiser
                    </button>
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

`resources/views/Admin/Commissariats/edit_commissariat.blade.php`:

```blade
@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier le commissariat</h1>
        <nav class="flex mt-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li class="inline-flex items-center">
                    <a href="{{ url('admin/dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <a href="{{ url('admin/commissariats') }}" class="text-sm text-gray-600 hover:text-blue-600">Commissariats</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-sm font-medium text-blue-600 ml-1">Édition</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-edit mr-2 text-blue-600"></i>
                Formulaire d'édition
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ URL::to('admin/update-commissariat/'.$commissariat->id) }}" method="post">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom du commissariat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $commissariat->name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">
                            Commune <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="commune" name="commune" value="{{ old('commune', $commissariat->commune) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city', $commissariat->city) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $commissariat->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse
                        </label>
                        <input type="text" id="address" name="address" value="{{ old('address', $commissariat->address) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end mt-8 space-x-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>
@endsection
```

- [ ] **Step 6: Add the nav link**

In `resources/views/Admin/layout.blade.php`, right after the `admin/lost-and-found` `<li>` block (before the `@if(!empty(Auth::user()))` logout block):

```blade
        <li>
          <a href="{{ url('admin/commissariats') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-shield-alt w-6 text-center"></i>
            <span class="ml-3">Commissariats</span>
          </a>
        </li>
```

- [ ] **Step 7: Run tests to verify they pass**

Run: `php artisan test --filter=AdminCommissariatCrudTest`
Expected: PASS

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Admin/CommissariatController.php resources/views/Admin/Commissariats routes/web.php resources/views/Admin/layout.blade.php tests/Feature/AdminCommissariatCrudTest.php
git commit -m "feat: add admin CRUD for the commissariat directory"
```

---

### Task 4: Déclaration obligatoire lors du passage à `found`

**Files:**
- Modify: `app/Http/Controllers/User/ItemController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/ItemFoundDeclarationTest.php`

**Interfaces:**
- Consumes: `Commissariat`, `ItemPoliceDeclaration` (Task 1).
- Produces: `ItemController::upsertPoliceDeclaration(Item $item, Request $request): void` (private helper reused by Task 6).

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ItemFoundDeclarationTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_a_found_item_as_found_requires_a_police_declaration(): void
    {
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($finder)->post('/item-found/' . $item->id, []);

        $response->assertRedirect();
        $this->assertEquals('pending', $item->fresh()->lost_found_status);
        $this->assertDatabaseMissing('item_police_declarations', ['item_id' => $item->id]);
    }

    public function test_marking_a_found_item_as_found_creates_the_police_declaration(): void
    {
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($finder)->post('/item-found/' . $item->id, [
            'commissariat_id' => $commissariat->id,
            'declaration_number' => 'DEC-2026-042',
        ]);

        $response->assertRedirect('my-items');
        $this->assertEquals('found', $item->fresh()->lost_found_status);
        $this->assertDatabaseHas('item_police_declarations', [
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-2026-042',
        ]);
    }

    public function test_marking_a_found_item_stores_the_optional_receipt_photo(): void
    {
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);
        $photo = UploadedFile::fake()->image('recepisse.jpg');

        $response = $this->actingAs($finder)->post('/item-found/' . $item->id, [
            'commissariat_id' => $commissariat->id,
            'declaration_number' => 'DEC-2026-099',
            'receipt_photo' => $photo,
        ]);

        $response->assertRedirect('my-items');
        $declaration = $item->fresh()->policeDeclaration;
        $this->assertNotNull($declaration->receipt_photo);
        $this->assertFileExists(public_path($declaration->receipt_photo));

        unlink(public_path($declaration->receipt_photo));
    }

    public function test_marking_a_lost_item_as_found_does_not_require_a_declaration(): void
    {
        $owner = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'status' => 'lost',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($owner)->post('/item-found/' . $item->id, []);

        $response->assertRedirect('my-items');
        $this->assertEquals('found', $item->fresh()->lost_found_status);
        $this->assertDatabaseMissing('item_police_declarations', ['item_id' => $item->id]);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=ItemFoundDeclarationTest`
Expected: FAIL — `itemFound()` currently has no `Request` parameter and never validates or writes a declaration.

- [ ] **Step 3: Update the route to accept a name**

In `routes/web.php`, change:

```php
Route::post("item-found/{id}", [App\Http\Controllers\User\ItemController::class, "itemFound"]);
```

to:

```php
Route::post("item-found/{id}", [App\Http\Controllers\User\ItemController::class, "itemFound"])->name('item-found');
```

- [ ] **Step 4: Update `ItemController`**

In `app/Http/Controllers/User/ItemController.php`, add the constant next to `ITEM_IMAGES_FOLDER`:

```php
    const DECLARATION_PHOTOS_FOLDER = 'uploads/declarations';
```

Add `use App\Models\ItemPoliceDeclaration;` to the imports.

Replace the existing `itemFound()` method with:

```php
    public function itemFound(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            if (!$this->authorizeItem($item)) {
                Session::flash("error", "Vous n'êtes pas autorisé à modifier cet objet.");
                return redirect()->back();
            }

            if ($item->status === 'found') {
                $request->validate([
                    'commissariat_id' => 'required|exists:commissariats,id',
                    'declaration_number' => 'required|string|max:100',
                    'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                ]);

                $this->upsertPoliceDeclaration($item, $request);
            }

            $item->update(['lost_found_status' => 'found']);

            Session::flash("message", "Objet marqué comme trouvé !");
            return redirect('my-items');
        } catch (\Exception $e) {
            Session::flash("error", "Une erreur est survenue lors de la mise à jour du statut.");
            return redirect()->back();
        }
    }
```

Add this private helper right after `authorizeItem()`:

```php
    /**
     * Crée ou met à jour la déclaration de dépôt au commissariat pour un item.
     * Ne touche pas declared_at lors d'une correction ultérieure.
     */
    private function upsertPoliceDeclaration(Item $item, Request $request): void
    {
        $declaration = ItemPoliceDeclaration::firstOrNew(['item_id' => $item->id]);
        $declaration->commissariat_id = $request->commissariat_id;
        $declaration->declared_by_user_id = Auth::id();
        $declaration->declaration_number = $request->declaration_number;

        if ($request->hasFile('receipt_photo')) {
            if ($declaration->receipt_photo && file_exists(public_path($declaration->receipt_photo))) {
                unlink(public_path($declaration->receipt_photo));
            }

            $photo = $request->file('receipt_photo');
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path(self::DECLARATION_PHOTOS_FOLDER), $filename);
            $declaration->receipt_photo = self::DECLARATION_PHOTOS_FOLDER . '/' . $filename;
        }

        if (!$declaration->exists) {
            $declaration->declared_at = now();
        }

        $declaration->save();
    }
```

Note importante (vérifié empiriquement sur ce projet) : `$request->validate(...)` lève une `ValidationException`, qui **est** attrapée par le `catch (\Exception $e)` générique de la méthode (`ValidationException extends Exception`) avant d'atteindre le handler global de Laravel. Le comportement obtenu n'est donc pas la redirection standard avec un `$errors` bag rempli, mais le flash générique existant ("Une erreur est survenue...") suivi d'un `redirect()->back()` — exactement le même comportement (déjà présent, non corrigé ici) que `store()` et `updateItem()` dans ce même fichier. Le test de l'étape 1 vérifie donc l'absence d'effet de bord (statut inchangé, aucune déclaration créée) plutôt qu'un `$errors` bag.

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=ItemFoundDeclarationTest`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/User/ItemController.php routes/web.php tests/Feature/ItemFoundDeclarationTest.php
git commit -m "feat: require a police declaration when marking a found item as deposited"
```

---

### Task 5: Formulaire et affichage sur la fiche item

**Files:**
- Modify: `app/Http/Controllers/User/ItemController.php` (`itemDetail()`)
- Modify: `resources/views/item_detail.blade.php`
- Test: `tests/Feature/ItemDetailPoliceDeclarationVisibilityTest.php`

**Interfaces:**
- Consumes: `Item::policeDeclaration` (Task 1), route `item-found` (Task 4), `Commissariat` (Task 1).
- Produces: rien de réutilisé par une tâche suivante — dernière brique visible côté finder/tiers.

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailPoliceDeclarationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_finder_sees_the_declaration_form_when_nothing_declared_yet(): void
    {
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($finder)->get('/item-detail/' . $item->id);

        $response->assertSee('Marquer comme déposé');
    }

    public function test_third_party_sees_commissariat_name_but_not_declaration_number(): void
    {
        $finder = User::factory()->create();
        $stranger = User::factory()->create();
        $commissariat = Commissariat::factory()->create(['name' => 'Commissariat de Marcory', 'commune' => 'Marcory']);
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'found',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-SECRET-1',
        ]);

        $response = $this->actingAs($stranger)->get('/item-detail/' . $item->id);

        $response->assertSee('Commissariat de Marcory');
        $response->assertDontSee('DEC-SECRET-1');
    }

    public function test_finder_sees_the_declaration_number(): void
    {
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'found',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-VISIBLE-1',
        ]);

        $response = $this->actingAs($finder)->get('/item-detail/' . $item->id);

        $response->assertSee('DEC-VISIBLE-1');
    }

    public function test_claimant_sees_declaration_number_only_after_ownership_is_validated(): void
    {
        $finder = User::factory()->create();
        $claimant = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'found_user_id' => $claimant->id,
            'status' => 'found',
            'lost_found_status' => 'ownership_claimed',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-PENDING-1',
        ]);

        $beforeValidation = $this->actingAs($claimant)->get('/item-detail/' . $item->id);
        $beforeValidation->assertDontSee('DEC-PENDING-1');

        $item->update(['lost_found_status' => 'returned']);

        $afterValidation = $this->actingAs($claimant)->get('/item-detail/' . $item->id);
        $afterValidation->assertSee('DEC-PENDING-1');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=ItemDetailPoliceDeclarationVisibilityTest`
Expected: FAIL — no declaration section exists yet in `item_detail.blade.php`.

- [ ] **Step 3: Update `ItemController::itemDetail()`**

Replace:

```php
    public function itemDetail($id)
    {
        try {
            $item = Item::with('user', 'foundUser')->findOrFail($id);
            return view('item_detail', compact('item'));
        } catch (\Exception $e) {
            Session::flash("error", "Objet introuvable.");
            return redirect()->back();
        }
    }
```

with:

```php
    public function itemDetail($id)
    {
        try {
            $item = Item::with('user', 'foundUser', 'policeDeclaration.commissariat')->findOrFail($id);
            $commissariats = Commissariat::where('is_active', true)->orderBy('commune')->get();

            return view('item_detail', compact('item', 'commissariats'));
        } catch (\Exception $e) {
            Session::flash("error", "Objet introuvable.");
            return redirect()->back();
        }
    }
```

Add `use App\Models\Commissariat;` to the imports.

- [ ] **Step 4: Add the declaration section to `item_detail.blade.php`**

Insert this new block right after the closing `</div>` of the "Images" section (before `<!-- Actions -->`):

```blade
                <!-- Déclaration au commissariat -->
                @if($item->status == 'found')
                @php
                    $isFinder = Auth::id() == $item->user_id;
                    $isValidatedClaimant = Auth::id() == $item->found_user_id && $item->lost_found_status == 'returned';
                    $canSeePrivateDeclaration = $isFinder || $isValidatedClaimant;
                @endphp
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-slate-50 mb-4 pb-3 border-b border-slate-700 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-500"></i>
                        Déclaration au commissariat
                    </h3>

                    @if($item->policeDeclaration)
                        <div class="bg-slate-900 border border-slate-700 p-5 rounded-xl space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Commissariat</span>
                                <span class="text-sm text-slate-50 font-semibold">{{ $item->policeDeclaration->commissariat->name }} ({{ $item->policeDeclaration->commissariat->commune }})</span>
                            </div>
                            @if($canSeePrivateDeclaration)
                                <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                    <span class="text-sm text-slate-400">N° de déclaration</span>
                                    <span class="text-sm text-slate-50 font-mono">{{ $item->policeDeclaration->declaration_number }}</span>
                                </div>
                                @if($item->policeDeclaration->receipt_photo)
                                    <div class="pt-2">
                                        <span class="text-sm text-slate-400 block mb-2">Récépissé</span>
                                        <img src="{{ asset($item->policeDeclaration->receipt_photo) }}"
                                             class="w-40 h-40 object-cover rounded-lg border border-slate-700 cursor-pointer"
                                             onclick="openImage('{{ asset($item->policeDeclaration->receipt_photo) }}')"
                                             alt="Récépissé de dépôt">
                                    </div>
                                @endif
                            @endif
                        </div>
                    @elseif($isFinder && $item->lost_found_status == 'pending')
                        <form action="{{ route('item-found', $item->id) }}" method="POST" enctype="multipart/form-data"
                              class="bg-slate-900 border border-slate-700 p-5 rounded-xl space-y-4">
                            @csrf
                            <p class="text-sm text-slate-400">Indiquez où vous avez déposé cet objet pour respecter l'obligation de signalement à la police.</p>
                            <div class="flex flex-col gap-1.5">
                                <label for="commissariat_id" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Commissariat *</label>
                                <select id="commissariat_id" name="commissariat_id" required
                                        class="w-full bg-slate-800 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none focus:border-blue-500">
                                    <option value="" disabled selected>Sélectionnez un commissariat</option>
                                    @foreach($commissariats as $commissariat)
                                        <option value="{{ $commissariat->id }}">{{ $commissariat->name }} ({{ $commissariat->commune }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="declaration_number" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">N° de déclaration *</label>
                                <input type="text" id="declaration_number" name="declaration_number" required
                                       class="w-full bg-slate-800 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none focus:border-blue-500"
                                       placeholder="Ex: DEC-2026-00123">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="receipt_photo" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Photo du récépissé (optionnel)</label>
                                <input type="file" id="receipt_photo" name="receipt_photo" accept="image/*"
                                       class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600">
                            </div>
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                                <i class="fas fa-shield-alt"></i>
                                Marquer comme déposé
                            </button>
                        </form>
                    @endif
                </div>
                @endif

```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=ItemDetailPoliceDeclarationVisibilityTest`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/User/ItemController.php resources/views/item_detail.blade.php tests/Feature/ItemDetailPoliceDeclarationVisibilityTest.php
git commit -m "feat: show and collect the police declaration on the item detail page"
```

---

### Task 6: Correction de la déclaration depuis l'édition

**Files:**
- Modify: `app/Http/Controllers/User/ItemController.php` (`updateItem()`)
- Modify: `resources/views/item_edit.blade.php`
- Test: `tests/Feature/ItemPoliceDeclarationEditTest.php`

**Interfaces:**
- Consumes: `upsertPoliceDeclaration()` (Task 4), `Item::policeDeclaration` (Task 1).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPoliceDeclarationEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_finder_can_correct_an_existing_declaration(): void
    {
        $finder = User::factory()->create();
        $originalCommissariat = Commissariat::factory()->create();
        $correctedCommissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'found',
            'item_name' => 'Clés de voiture',
            'category_name' => 'objets',
            'date' => '2026-07-01',
            'description' => 'Trouvées au marché',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $originalCommissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-OLD-1',
        ]);

        $response = $this->actingAs($finder)->post('/update-item', [
            'id' => $item->id,
            'item_name' => $item->item_name,
            'category' => $item->category_name,
            'lost_date' => $item->date,
            'description' => $item->description,
            'commissariat_id' => $correctedCommissariat->id,
            'declaration_number' => 'DEC-NEW-1',
        ]);

        $response->assertRedirect('my-items');
        $this->assertDatabaseHas('item_police_declarations', [
            'item_id' => $item->id,
            'commissariat_id' => $correctedCommissariat->id,
            'declaration_number' => 'DEC-NEW-1',
        ]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ItemPoliceDeclarationEditTest`
Expected: FAIL — `updateItem()` ignores `commissariat_id`/`declaration_number`, declaration stays unchanged.

- [ ] **Step 3: Update `ItemController::updateItem()`**

In `app/Http/Controllers/User/ItemController.php`, right after the existing `$item->update([...])` call inside `updateItem()` (before the success flash message), add:

```php
            if ($item->policeDeclaration && $request->filled('commissariat_id')) {
                $request->validate([
                    'commissariat_id' => 'required|exists:commissariats,id',
                    'declaration_number' => 'required|string|max:100',
                    'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                ]);

                $this->upsertPoliceDeclaration($item, $request);
            }
```

- [ ] **Step 4: Add the edit section to `item_edit.blade.php`**

Insert this block right after the closing `</div>` of the "Aperçu des images actuelles" `@if(count($images) > 0) ... @endif` block (before the "Description" field):

```blade
                    @if($item->policeDeclaration)
                    @php
                        $editCommissariats = \App\Models\Commissariat::where('is_active', true)->orWhere('id', $item->policeDeclaration->commissariat_id)->orderBy('commune')->get();
                    @endphp
                    <div>
                        <h3 class="text-lg font-semibold text-slate-50 mb-6 pb-3 border-b border-slate-700 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-500"></i>
                            Déclaration au commissariat
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-1.5">
                                <label for="commissariat_id" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Commissariat *</label>
                                <select id="commissariat_id" name="commissariat_id" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none transition-all focus:border-blue-500">
                                    @foreach($editCommissariats as $commissariat)
                                        <option value="{{ $commissariat->id }}" {{ old('commissariat_id', $item->policeDeclaration->commissariat_id) == $commissariat->id ? 'selected' : '' }}>
                                            {{ $commissariat->name }} ({{ $commissariat->commune }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="declaration_number" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">N° de déclaration *</label>
                                <input type="text" id="declaration_number" name="declaration_number" required
                                       value="{{ old('declaration_number', $item->policeDeclaration->declaration_number) }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none transition-all focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 mt-6">
                            <label for="receipt_photo" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Photo du récépissé (optionnel)</label>
                            <input type="file" id="receipt_photo" name="receipt_photo" accept="image/*"
                                   class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600">
                            @if($item->policeDeclaration->receipt_photo)
                                <p class="text-xs text-slate-500 mt-1">Laissez vide pour conserver le récépissé actuel</p>
                            @endif
                        </div>
                    </div>
                    @endif

```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=ItemPoliceDeclarationEditTest`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/User/ItemController.php resources/views/item_edit.blade.php tests/Feature/ItemPoliceDeclarationEditTest.php
git commit -m "feat: allow the finder to correct their police declaration on edit"
```

---

### Task 7: Audit de la déclaration côté Admin

**Files:**
- Modify: `app/Http/Controllers/Admin/LostFoundController.php`
- Modify: `resources/views/Admin/LostFound/detail.blade.php`
- Test: `tests/Feature/AdminPoliceDeclarationAuditTest.php`

**Interfaces:**
- Consumes: `Item::policeDeclaration.commissariat` (Task 1).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPoliceDeclarationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_the_full_declaration_on_item_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create(['name' => "Commissariat d'Abobo"]);
        $item = Item::factory()->create(['user_id' => $finder->id, 'status' => 'found']);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-AUDIT-1',
        ]);

        $response = $this->actingAs($admin)->get('/admin/item-detail/' . $item->id);

        $response->assertSee("Commissariat d'Abobo");
        $response->assertSee('DEC-AUDIT-1');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AdminPoliceDeclarationAuditTest`
Expected: FAIL — `LostFoundController::itemDetail()` doesn't eager-load the declaration, and the view doesn't render it.

- [ ] **Step 3: Update `LostFoundController::itemDetail()`**

In `app/Http/Controllers/Admin/LostFoundController.php`, replace:

```php
    public function itemDetail($id)
    {
        $item = Item::where("id", $id)->with("user")->first();

        return view('Admin.LostFound.detail', ["item" => $item]);
    }
```

with:

```php
    public function itemDetail($id)
    {
        $item = Item::where("id", $id)->with(["user", "policeDeclaration.commissariat"])->first();

        return view('Admin.LostFound.detail', ["item" => $item]);
    }
```

- [ ] **Step 4: Add the audit card to `Admin/LostFound/detail.blade.php`**

Insert this block right after the closing `</div>` of the "Image" section (before the final closing `</div>` of the card body):

```blade
                <!-- Déclaration au commissariat -->
                @if($item->policeDeclaration)
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-600"></i>
                        Déclaration au commissariat
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Commissariat:</div>
                            <div class="col-span-2 text-sm text-gray-800">{{ $item->policeDeclaration->commissariat->name }} ({{ $item->policeDeclaration->commissariat->commune }})</div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">N° de déclaration:</div>
                            <div class="col-span-2 text-sm text-gray-800">{{ $item->policeDeclaration->declaration_number }}</div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Déclaré le:</div>
                            <div class="col-span-2 text-sm text-gray-800">{{ $item->policeDeclaration->declared_at->format('d/m/Y à H:i') }}</div>
                        </div>
                        @if($item->policeDeclaration->receipt_photo)
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm font-medium text-gray-500">Récépissé:</div>
                            <div class="col-span-2 text-sm">
                                <img src="{{ asset($item->policeDeclaration->receipt_photo) }}" class="w-32 h-32 object-cover rounded-lg border" alt="Récépissé">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=AdminPoliceDeclarationAuditTest`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/LostFoundController.php resources/views/Admin/LostFound/detail.blade.php tests/Feature/AdminPoliceDeclarationAuditTest.php
git commit -m "feat: show police declaration details in the admin item audit view"
```

---

## Final check

- [ ] Run the full suite: `php artisan test` — all tests pass, including the pre-existing ones.
- [ ] `php artisan route:list --path=commissariat` and `--path=item-found` show the expected routes.
- [ ] Manually seed and click through once: `php artisan migrate --seed`, log in as a non-admin user, post a found item, open its detail page, submit the declaration form, confirm the commissariat name appears and the declaration number is hidden from another account.
