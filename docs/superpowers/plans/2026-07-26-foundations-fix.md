# Correction des fondations QCT — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Corriger les problèmes de fondation du projet QCT (schéma BDD désynchronisé, notifications cassées, routes admin exposées, rate limiting non branché, bugs mineurs) sans changer le comportement visible pour l'utilisateur, si ce n'est la réparation de flux déjà censés fonctionner.

**Architecture:** Laravel 10 / MySQL. Aucune nouvelle dépendance Composer. Les corrections passent par : une migration corrective supplémentaire (sans toucher aux fichiers historiques), deux nouvelles classes `Notification`, un déplacement de routes dans un groupe de middleware existant, un alias de middleware existant mais jamais branché, et quelques changements ponctuels dans des contrôleurs/vues/le modèle `Item`.

**Tech Stack:** PHP 8.1+, Laravel 10.48, MySQL 8, PHPUnit 10 (`php artisan test`).

## Global Constraints

- PHP ^8.1, Laravel ^10.8 (plancher du `composer.json` existant) — ne rien exiger de plus récent.
- Aucune nouvelle dépendance Composer : `doctrine/dbal` n'est pas installé, donc pas de `Schema::table(...)->change()` — utiliser du SQL brut (`DB::statement`) pour les modifications de colonnes.
- Tous les textes visibles par l'utilisateur, messages flash et commentaires de code restent en français, comme le reste du projet.
- Ne jamais modifier les fichiers de migration déjà livrés (`2023_05_01_*`, `2023_05_02_*`, `2024_06_18_*`) — uniquement en ajouter de nouveaux.
- Les tests s'exécutent contre une base MySQL dédiée `findme_test`, jamais contre `findme` (base de développement réelle avec de vraies données).
- `git commit` à la fin de chaque tâche, avec uniquement les fichiers concernés par cette tâche (pas de `git add -A`).

---

## File Structure

**Nouveaux fichiers :**
- `.env.testing` — config d'environnement de test, base `findme_test`
- `database/factories/ItemFactory.php` — factory manquante pour `Item`
- `database/migrations/2026_07_26_000000_reconcile_items_schema.php` — convertit `status`/`lost_found_status` en `VARCHAR`
- `database/migrations/2026_07_26_000001_create_notifications_table.php` — table `notifications` (stub standard Laravel)
- `app/Notifications/ClaimValidatedNotification.php`
- `app/Notifications/OwnershipValidatedNotification.php`
- `tests/Feature/ModelFactoriesTest.php`
- `tests/Feature/ItemsSchemaReconciliationTest.php`
- `tests/Feature/ClaimValidationNotificationTest.php`
- `tests/Feature/OwnershipValidationNotificationTest.php`
- `tests/Feature/AdminMessagesRouteSecurityTest.php`
- `tests/Feature/LoginRateLimitTest.php`
- `tests/Feature/AdminDashboardCounterTest.php`
- `tests/Feature/AdminItemDetailTest.php`
- `tests/Feature/AdminLostFoundListTest.php`

**Fichiers modifiés :**
- `.gitignore` — ajout de `.env.testing`
- `database/factories/UserFactory.php` — ajout des champs `mobile_no`, `country`, `city` requis
- `app/Http/Controllers/User/ItemController.php` — branchement des deux nouvelles notifications
- `routes/web.php` — déplacement des routes admin/messages, ajout du rate limiting
- `app/Http/Kernel.php` — alias `throttle.custom`
- `app/Http/Controllers/Admin/DashboardController.php` — `deliver` → `delivered`
- `app/Models/Item.php` — suppression de `users()`
- `app/Http/Controllers/Admin/LostFoundController.php` — `with("users")` → `with("user")`
- `resources/views/Admin/LostFound/detail.blade.php` — `$item->users` → `$item->user` (6 occurrences)
- `resources/views/Admin/LostFound/view_lost_found.blade.php` — `$item->users` → `$item->user` (1 occurrence)

**Fichier supprimé :**
- `public/hot`

---

### Task 1: Base de données de test isolée

**Files:**
- Create: `.env.testing`
- Modify: `.gitignore`

**Interfaces:**
- Produces: une base MySQL `findme_test` et un environnement `.env.testing` chargé automatiquement par `php artisan test` (via `phpunit.xml`, qui fixe déjà `APP_ENV=testing`). Toutes les tâches suivantes en dépendent.

- [ ] **Step 1: Créer la base de test**

Run:
```bash
php -r "(new PDO('mysql:host=127.0.0.1','oguoro','1234'))->exec('CREATE DATABASE IF NOT EXISTS findme_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');"
```
Expected: aucune sortie (succès silencieux).

- [ ] **Step 2: Créer `.env.testing`**

Contenu exact du fichier `.env.testing` (à la racine du projet, à côté de `.env`) :

```env
APP_NAME=QCT
APP_ENV=testing
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=findme_test
DB_USERNAME=oguoro
DB_PASSWORD=1234

BROADCAST_DRIVER=log
CACHE_DRIVER=array
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
SESSION_LIFETIME=120

MAIL_MAILER=array
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

- [ ] **Step 3: Générer une clé d'application pour l'environnement de test**

Run: `php artisan key:generate --env=testing`
Expected: `Application key set successfully.` — la ligne `APP_KEY=` dans `.env.testing` est maintenant remplie.

- [ ] **Step 4: Ignorer `.env.testing` dans Git**

Dans `.gitignore`, juste après la ligne `.env` (dernière ligne du fichier), ajouter :

```
.env.testing
```

- [ ] **Step 5: Vérifier que les migrations s'appliquent proprement sur la base de test**

Run: `php artisan migrate:fresh --env=testing --force`
Expected: la liste des migrations s'affiche avec `DONE` pour chacune, sans erreur.

- [ ] **Step 6: Commit**

```bash
git add .gitignore
git commit -m "chore: add isolated MySQL test database config"
```

Note : `.env.testing` n'est **pas** commité (il est maintenant dans `.gitignore`), seul le changement à `.gitignore` l'est.

---

### Task 2: Factories de test manquantes/incomplètes

**Files:**
- Modify: `database/factories/UserFactory.php`
- Create: `database/factories/ItemFactory.php`
- Test: `tests/Feature/ModelFactoriesTest.php`

**Interfaces:**
- Consumes: base de test créée en Task 1.
- Produces: `User::factory()` et `Item::factory()` utilisables et persistables par toutes les tâches suivantes.

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/ModelFactoriesTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelFactoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_creates_a_persistable_user(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_item_factory_creates_a_persistable_item(): void
    {
        $item = Item::factory()->create();

        $this->assertDatabaseHas('items', ['id' => $item->id]);
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=ModelFactoriesTest`
Expected: `test_user_factory_creates_a_persistable_user` échoue avec une erreur SQL du type `Column 'mobile_no' cannot be null` (le `UserFactory` actuel ne fournit pas `mobile_no`/`country`/`city`, pourtant requis par la table `users`). `test_item_factory_creates_a_persistable_item` échoue aussi : la classe `Database\Factories\ItemFactory` n'existe pas encore.

- [ ] **Step 3: Compléter `UserFactory`**

Dans `database/factories/UserFactory.php`, remplacer la méthode `definition()` :

```php
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'mobile_no' => fake()->numerify('07########'),
            'country' => "Côte d'Ivoire",
            'city' => fake()->city(),
            'remember_token' => Str::random(10),
        ];
    }
```

- [ ] **Step 4: Créer `ItemFactory`**

Créer `database/factories/ItemFactory.php` :

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'found_user_id' => null,
            'item_name' => fake()->words(2, true),
            'category_name' => 'objets',
            'date' => fake()->date(),
            'images' => null,
            'description' => fake()->sentence(),
            'status' => 'lost',
            'lost_found_status' => 'pending',
        ];
    }
}
```

`App\Models\Item` utilise déjà `use HasFactory;`, donc `Item::factory()` résout automatiquement vers cette classe (convention de nommage Laravel), aucune modification du modèle n'est nécessaire.

- [ ] **Step 5: Relancer le test et constater le succès**

Run: `php artisan test --filter=ModelFactoriesTest`
Expected: `OK (2 tests, 2 assertions)`

- [ ] **Step 6: Commit**

```bash
git add database/factories/UserFactory.php database/factories/ItemFactory.php tests/Feature/ModelFactoriesTest.php
git commit -m "test: fix UserFactory required fields and add ItemFactory"
```

---

### Task 3: Réconciliation du schéma `items` (status / lost_found_status)

**Files:**
- Create: `database/migrations/2026_07_26_000000_reconcile_items_schema.php`
- Test: `tests/Feature/ItemsSchemaReconciliationTest.php`

**Interfaces:**
- Consumes: `Item::factory()` (Task 2).
- Produces: colonnes `items.status` et `items.lost_found_status` en `VARCHAR(50)` acceptant toute valeur utilisée par `ItemController` (`pending`, `found`, `claimed`, `ownership_claimed`, `delivered`, `returned`).

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/ItemsSchemaReconciliationTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsSchemaReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_extended_lost_found_statuses_can_be_persisted(): void
    {
        $item = Item::factory()->create(['lost_found_status' => 'ownership_claimed']);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'lost_found_status' => 'ownership_claimed',
        ]);
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=ItemsSchemaReconciliationTest`
Expected: échec avec une erreur SQL du type `Data truncated for column 'lost_found_status'` ou `Incorrect enum value` — sur une base fraîche, `create_items_table` limite encore `lost_found_status` à `pending, found, draft, deliver`.

- [ ] **Step 3: Créer la migration corrective**

Créer `database/migrations/2026_07_26_000000_reconcile_items_schema.php` :

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Colonnes historiquement déclarées en enum restreint, mais dont le
     * code applicatif écrit des valeurs supplémentaires (claimed,
     * ownership_claimed, delivered, returned). On les convertit en texte
     * libre. Pas de dépendance à doctrine/dbal : SQL brut.
     */
    private array $columns = ['status', 'lost_found_status'];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->columns as $column) {
            if ($this->isEnum($column)) {
                DB::statement("ALTER TABLE items MODIFY {$column} VARCHAR(50) NOT NULL");
            }
        }
    }

    public function down(): void
    {
        // Volontairement irréversible : revenir à un enum restreint
        // casserait les lignes existantes portant claimed/ownership_claimed/
        // delivered/returned.
    }

    private function isEnum(string $column): bool
    {
        $row = DB::selectOne(
            'SELECT DATA_TYPE as type FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [DB::getDatabaseName(), 'items', $column]
        );

        return $row !== null && strtolower($row->type) === 'enum';
    }
};
```

- [ ] **Step 4: Relancer le test et constater le succès**

Run: `php artisan test --filter=ItemsSchemaReconciliationTest`
Expected: `OK (1 test, 1 assertion)`

- [ ] **Step 5: Vérifier que la migration s'applique aussi proprement sur la base de développement réelle**

Run: `php artisan migrate --force`
Expected: la migration `2026_07_26_000000_reconcile_items_schema` s'exécute (`DONE`) sans erreur. Comme `findme` a déjà `status`/`lost_found_status` en `VARCHAR(50)`, `isEnum()` renvoie `false` pour les deux colonnes et aucun `ALTER TABLE` n'est réellement émis — la migration est un no-op sûr sur cette base, mais elle est bien enregistrée dans la table `migrations` pour que `php artisan migrate` ne la propose plus comme "en attente".

Cette même commande applique aussi au passage les deux migrations `2024_06_18_*` déjà présentes dans le repo mais jamais exécutées sur `findme` : `fix_items_image_column` s'exécute en no-op (les colonnes sont déjà dans l'état cible), et `add_production_indexes` crée réellement les index manquants sur `items` (aucun index hormis `PRIMARY` n'existe aujourd'hui).

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_26_000000_reconcile_items_schema.php tests/Feature/ItemsSchemaReconciliationTest.php
git commit -m "fix: allow full lost_found_status range on items table"
```

---

### Task 4: Table `notifications` + `ClaimValidatedNotification`

**Files:**
- Create: `database/migrations/2026_07_26_000001_create_notifications_table.php`
- Create: `app/Notifications/ClaimValidatedNotification.php`
- Modify: `app/Http/Controllers/User/ItemController.php` (méthode `validateClaim`, imports)
- Test: `tests/Feature/ClaimValidationNotificationTest.php`

**Interfaces:**
- Consumes: `Item::factory()`, `User::factory()` (Task 2) ; schéma réconcilié (Task 3, pour pouvoir créer un item avec `lost_found_status = 'claimed'`).
- Produces: `App\Notifications\ClaimValidatedNotification` (constructeur `(Item $item, User $owner)`), utilisée par le trouveur (`$item->foundUser->notify(...)`).

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/ClaimValidationNotificationTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Notifications\ClaimValidatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimValidationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_validating_a_claim_notifies_the_finder_in_database(): void
    {
        $owner = User::factory()->create();
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'found_user_id' => $finder->id,
            'lost_found_status' => 'claimed',
        ]);

        $response = $this->actingAs($owner)->post('/validate-claim/' . $item->id);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $finder->id,
            'notifiable_type' => User::class,
            'type' => ClaimValidatedNotification::class,
        ]);
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=ClaimValidationNotificationTest`
Expected: échec — soit `Base table or view not found: notifications`, soit une erreur PHP car `ItemController::validateClaim()` instancie `new \Illuminate\Notifications\Messages\MailMessage` et l'envoie à `notify()`, ce qui n'est pas une classe `Notification` valide.

- [ ] **Step 3: Créer la migration de la table `notifications`**

Créer `database/migrations/2026_07_26_000001_create_notifications_table.php` (stub standard Laravel) :

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

- [ ] **Step 4: Créer `ClaimValidatedNotification`**

Créer `app/Notifications/ClaimValidatedNotification.php` :

```php
<?php

namespace App\Notifications;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimValidatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $item;
    public $owner;

    public function __construct(Item $item, User $owner)
    {
        $this->item = $item;
        $this->owner = $owner;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Remise confirmée pour ' . $this->item->item_name)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->owner->name . ' a confirmé avoir récupéré ' . $this->item->item_name . '.')
            ->line('Merci d\'avoir aidé à retrouver ce bien !')
            ->action('Voir l\'annonce', url('/item-detail/' . $this->item->id))
            ->line('Merci d\'avoir utilisé QCT!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->item_name,
            'owner_id' => $this->owner->id,
            'owner_name' => $this->owner->name,
            'message' => $this->owner->name . ' a confirmé la récupération de ' . $this->item->item_name,
        ];
    }
}
```

- [ ] **Step 5: Brancher la notification dans `validateClaim()`**

Dans `app/Http/Controllers/User/ItemController.php`, ajouter l'import en haut du fichier, juste après les imports de notifications existants (ligne 12) :

```php
use App\Notifications\ItemClaimedNotification;
use App\Notifications\OwnershipClaimedNotification;
use App\Notifications\ClaimValidatedNotification;
```

Puis, dans `validateClaim()`, remplacer :

```php
            // Envoyer une notification au trouveur
            if ($item->foundUser) {
                $item->foundUser->notify(new \Illuminate\Notifications\Messages\MailMessage);
            }
```

par :

```php
            // Envoyer une notification au trouveur
            if ($item->foundUser) {
                $item->foundUser->notify(new ClaimValidatedNotification($item, Auth::user()));
            }
```

- [ ] **Step 6: Relancer le test et constater le succès**

Run: `php artisan test --filter=ClaimValidationNotificationTest`
Expected: `OK (1 test, 2 assertions)`

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_07_26_000001_create_notifications_table.php app/Notifications/ClaimValidatedNotification.php app/Http/Controllers/User/ItemController.php tests/Feature/ClaimValidationNotificationTest.php
git commit -m "fix: create notifications table and send a real notification on validateClaim"
```

---

### Task 5: `OwnershipValidatedNotification`

**Files:**
- Create: `app/Notifications/OwnershipValidatedNotification.php`
- Modify: `app/Http/Controllers/User/ItemController.php` (méthode `validateOwnership`, imports)
- Test: `tests/Feature/OwnershipValidationNotificationTest.php`

**Interfaces:**
- Consumes: table `notifications` (Task 4), `Item::factory()`/`User::factory()` (Task 2), schéma réconcilié (Task 3, pour `lost_found_status = 'ownership_claimed'`).
- Produces: `App\Notifications\OwnershipValidatedNotification` (constructeur `(Item $item, User $poster)`), utilisée par le réclamant (`$item->foundUser->notify(...)`).

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/OwnershipValidationNotificationTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Notifications\OwnershipValidatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipValidationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_validating_ownership_notifies_the_claimant_in_database(): void
    {
        $poster = User::factory()->create();
        $claimant = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $poster->id,
            'found_user_id' => $claimant->id,
            'status' => 'found',
            'lost_found_status' => 'ownership_claimed',
        ]);

        $response = $this->actingAs($poster)->post('/validate-ownership/' . $item->id);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $claimant->id,
            'notifiable_type' => User::class,
            'type' => OwnershipValidatedNotification::class,
        ]);
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=OwnershipValidationNotificationTest`
Expected: échec — `validateOwnership()` ne notifie personne aujourd'hui (l'appel est commenté), donc aucune ligne n'apparaît dans `notifications`.

- [ ] **Step 3: Créer `OwnershipValidatedNotification`**

Créer `app/Notifications/OwnershipValidatedNotification.php` :

```php
<?php

namespace App\Notifications;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OwnershipValidatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $item;
    public $poster;

    public function __construct(Item $item, User $poster)
    {
        $this->item = $item;
        $this->poster = $poster;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isPerson = $this->item->category_name === 'personnes';

        $subject = $isPerson
            ? 'Retrouvailles confirmées'
            : 'Remise confirmée pour ' . $this->item->item_name;

        $line = $isPerson
            ? $this->poster->name . ' a confirmé que vous avez bien retrouvé la personne.'
            : $this->poster->name . ' a confirmé vous avoir remis ' . $this->item->item_name . '.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($line)
            ->action('Voir l\'annonce', url('/item-detail/' . $this->item->id))
            ->line('Merci d\'avoir utilisé QCT!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->item_name,
            'poster_id' => $this->poster->id,
            'poster_name' => $this->poster->name,
            'message' => $this->poster->name . ' a confirmé la remise de ' . $this->item->item_name,
        ];
    }
}
```

- [ ] **Step 4: Brancher la notification dans `validateOwnership()`**

Dans `app/Http/Controllers/User/ItemController.php`, l'import de `ClaimValidatedNotification` a déjà été ajouté à la Task 4. Ajouter, juste en dessous, une nouvelle ligne d'import :

```php
use App\Notifications\ClaimValidatedNotification;
use App\Notifications\OwnershipValidatedNotification;
```

(Si la Task 5 est exécutée seule, sans la Task 4, les deux lignes ci-dessus sont à ajouter ; sinon, seule la ligne `OwnershipValidatedNotification` est nouvelle.)

Puis, dans `validateOwnership()`, remplacer :

```php
            // Envoyer une notification au réclamant
            // $item->foundUser->notify(new OwnershipValidatedNotification($item));
```

par :

```php
            // Envoyer une notification au réclamant
            if ($item->foundUser) {
                $item->foundUser->notify(new OwnershipValidatedNotification($item, Auth::user()));
            }
```

- [ ] **Step 5: Relancer le test et constater le succès**

Run: `php artisan test --filter=OwnershipValidationNotificationTest`
Expected: `OK (1 test, 2 assertions)`

- [ ] **Step 6: Commit**

```bash
git add app/Notifications/OwnershipValidatedNotification.php app/Http/Controllers/User/ItemController.php tests/Feature/OwnershipValidationNotificationTest.php
git commit -m "fix: notify claimant when ownership is validated"
```

---

### Task 6: Sécuriser les routes admin/messages

**Files:**
- Modify: `routes/web.php`
- Test: `tests/Feature/AdminMessagesRouteSecurityTest.php`

**Interfaces:**
- Consumes: `User::factory()` (Task 2), middleware `AdminLogin` existant (`app/Http/Middleware/AdminLogin.php`, non modifié).
- Produces: routes `admin/messages`, `admin/delete-message/{id}`, `admin/mark-as-reply/{id}`, `admin/mark-as-pending/{id}` protégées par `AdminLogin`.

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/AdminMessagesRouteSecurityTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMessagesRouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_admin_messages(): void
    {
        $response = $this->get('/admin/messages');

        $response->assertRedirect('/my-account');
    }

    public function test_regular_user_cannot_view_admin_messages(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/messages');

        $response->assertRedirect('/my-account');
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=AdminMessagesRouteSecurityTest`
Expected: les deux tests échouent — la route répond `200` (liste des messages) au lieu de rediriger, car elle est déclarée hors du groupe `AdminLogin`.

- [ ] **Step 3: Déplacer les routes dans le groupe `AdminLogin`**

Dans `routes/web.php`, supprimer ces 4 lignes de leur emplacement actuel (juste après `contact-us`, avant le groupe `AdminLogin`) :

```php
Route::get("admin/messages", [App\Http\Controllers\MessageController::class, "adminMessages"]);
Route::post("admin/delete-message/{id}", [App\Http\Controllers\MessageController::class, "deleteMessage"]);
Route::post("admin/mark-as-reply/{id}", [App\Http\Controllers\MessageController::class, "replyMessage"]);
Route::post("admin/mark-as-pending/{id}", [App\Http\Controllers\MessageController::class, "pendingMessage"]);
```

La ligne `Route::post("contact-us", ...)` reste seule à cet endroit (route publique).

Ajouter ces mêmes 4 lignes à l'intérieur du groupe `Route::middleware(['AdminLogin'])->group(function () { ... })`, juste après `admin/users` :

```php
    Route::get("admin/users", [App\Http\Controllers\Admin\UserController::class, "index"]);

    Route::get("admin/messages", [App\Http\Controllers\MessageController::class, "adminMessages"]);
    Route::post("admin/delete-message/{id}", [App\Http\Controllers\MessageController::class, "deleteMessage"]);
    Route::post("admin/mark-as-reply/{id}", [App\Http\Controllers\MessageController::class, "replyMessage"]);
    Route::post("admin/mark-as-pending/{id}", [App\Http\Controllers\MessageController::class, "pendingMessage"]);
});
```

- [ ] **Step 4: Relancer le test et constater le succès**

Run: `php artisan test --filter=AdminMessagesRouteSecurityTest`
Expected: `OK (2 tests, 2 assertions)`

- [ ] **Step 5: Commit**

```bash
git add routes/web.php tests/Feature/AdminMessagesRouteSecurityTest.php
git commit -m "fix: require AdminLogin middleware on admin message routes"
```

---

### Task 7: Rate limiting sur login / register / contact-us

**Files:**
- Modify: `app/Http/Kernel.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/LoginRateLimitTest.php`

**Interfaces:**
- Consumes: `App\Http\Middleware\RateLimitRequests` (déjà existant, non modifié).
- Produces: alias de middleware `throttle.custom`, appliqué avec les paramètres `10,1` (10 requêtes/minute).

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/LoginRateLimitTest.php` :

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_excessive_login_attempts_are_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['email' => 'nobody@example.com', 'password' => 'wrong']);
        }

        $response = $this->post('/login', ['email' => 'nobody@example.com', 'password' => 'wrong']);

        $response->assertStatus(429);
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=LoginRateLimitTest`
Expected: échec — la 11ᵉ requête reçoit une redirection (302), pas un `429` : rien ne limite `login` aujourd'hui.

- [ ] **Step 3: Ajouter l'alias de middleware**

Dans `app/Http/Kernel.php`, dans `$middlewareAliases`, ajouter après la ligne `"AdminLogin" => AdminLogin::class,` :

```php
        "AdminLogin" => AdminLogin::class,
        'throttle.custom' => \App\Http\Middleware\RateLimitRequests::class,
    ];
```

- [ ] **Step 4: Appliquer le middleware aux routes sensibles**

Dans `routes/web.php`, remplacer :

```php
Route::post("register", [App\Http\Controllers\User\RegisterController::class, "register"]);
Route::post("login", [App\Http\Controllers\User\RegisterController::class, "login"]);
```

par :

```php
Route::post("register", [App\Http\Controllers\User\RegisterController::class, "register"])->middleware('throttle.custom:10,1');
Route::post("login", [App\Http\Controllers\User\RegisterController::class, "login"])->middleware('throttle.custom:10,1');
```

Et remplacer :

```php
Route::post("contact-us", [App\Http\Controllers\MessageController::class, "message"]);
```

par :

```php
Route::post("contact-us", [App\Http\Controllers\MessageController::class, "message"])->middleware('throttle.custom:10,1');
```

- [ ] **Step 5: Relancer le test et constater le succès**

Run: `php artisan test --filter=LoginRateLimitTest`
Expected: `OK (1 test, 1 assertion)`

- [ ] **Step 6: Commit**

```bash
git add app/Http/Kernel.php routes/web.php tests/Feature/LoginRateLimitTest.php
git commit -m "fix: rate limit login, register and contact-us"
```

---

### Task 8: Compteur "objets livrés" du dashboard admin

**Files:**
- Modify: `app/Http/Controllers/Admin/DashboardController.php`
- Test: `tests/Feature/AdminDashboardCounterTest.php`

**Interfaces:**
- Consumes: `Item::factory()`, `User::factory()` (Task 2).

- [ ] **Step 1: Écrire le test qui échoue**

Créer `tests/Feature/AdminDashboardCounterTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivered_items_counter_reflects_delivered_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Item::factory()->create(['lost_found_status' => 'delivered']);
        Item::factory()->create(['lost_found_status' => 'delivered']);
        Item::factory()->create(['lost_found_status' => 'pending']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertViewHas('deliverItems', 2);
    }
}
```

- [ ] **Step 2: Lancer le test et constater l'échec**

Run: `php artisan test --filter=AdminDashboardCounterTest`
Expected: échec — `deliverItems` vaut `0` car le contrôleur filtre sur `lost_found_status = 'deliver'`, une valeur que le code n'écrit plus jamais.

- [ ] **Step 3: Corriger le contrôleur**

Dans `app/Http/Controllers/Admin/DashboardController.php`, remplacer :

```php
        $deliverItems = Item::where("lost_found_status", "deliver")->count();
```

par :

```php
        $deliverItems = Item::where("lost_found_status", "delivered")->count();
```

- [ ] **Step 4: Relancer le test et constater le succès**

Run: `php artisan test --filter=AdminDashboardCounterTest`
Expected: `OK (1 test, 1 assertion)`

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/DashboardController.php tests/Feature/AdminDashboardCounterTest.php
git commit -m "fix: admin dashboard delivered-items counter"
```

---

### Task 9: Suppression de `Item::users()` (doublon legacy)

**Files:**
- Modify: `app/Models/Item.php`
- Modify: `app/Http/Controllers/Admin/LostFoundController.php`
- Modify: `resources/views/Admin/LostFound/detail.blade.php`
- Modify: `resources/views/Admin/LostFound/view_lost_found.blade.php`
- Test: `tests/Feature/AdminItemDetailTest.php`
- Test: `tests/Feature/AdminLostFoundListTest.php`

**Interfaces:**
- Consumes: `Item::factory()`, `User::factory()` (Task 2).

**Note d'investigation** : la spec supposait un seul appelant de `Item::users()` (`LostFoundController::itemDetail()`). En réalité, deux vues Blade y font référence directement en accès magique via `$item->users->...` : `Admin/LostFound/detail.blade.php` (6 occurrences) et `Admin/LostFound/view_lost_found.blade.php` (1 occurrence, sans eager loading — chargement paresseux via la relation). Les deux doivent être mises à jour pour utiliser `$item->user` avant de pouvoir supprimer la méthode sans casser ces pages.

- [ ] **Step 1: Écrire les tests qui échouent**

Créer `tests/Feature/AdminItemDetailTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_item_detail_with_owner_information(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['name' => 'Awa Koné']);
        $item = Item::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->get('/admin/item-detail/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('Awa Koné');
    }
}
```

Créer `tests/Feature/AdminLostFoundListTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLostFoundListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_lost_and_found_list_with_owner_names(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['name' => 'Kouassi Yao']);
        Item::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->get('/admin/lost-and-found');

        $response->assertStatus(200);
        $response->assertSee('Kouassi Yao');
    }
}
```

- [ ] **Step 2: Lancer les tests et constater qu'ils passent déjà (comportement actuel via `users()`)**

Run: `php artisan test --filter=AdminItemDetailTest --filter=AdminLostFoundListTest`
Expected: `OK (2 tests, 4 assertions)` — ces tests documentent le comportement actuel (encore basé sur `$item->users`). Ils servent de filet de sécurité pour l'étape suivante : ils doivent rester au vert après le renommage.

- [ ] **Step 3: Mettre à jour `detail.blade.php`**

Dans `resources/views/Admin/LostFound/detail.blade.php`, remplacer les 6 occurrences de `$item->users->` par `$item->user->` (lignes 131, 136, 141, 146, 151, 156) :

```php
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->name ?? "" }}</div>
```
```php
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->email ?? "" }}</div>
```
```php
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->mobile_no ?? "" }}</div>
```
```php
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->country ?? "" }}</div>
```
```php
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->city ?? "" }}</div>
```
```php
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->address ?? "" }}</div>
```

- [ ] **Step 4: Mettre à jour `view_lost_found.blade.php`**

Dans `resources/views/Admin/LostFound/view_lost_found.blade.php`, remplacer :

```php
                            <div class="text-sm font-medium text-gray-900">{{ $item->users->name ?? "N/A" }}</div>
```

par :

```php
                            <div class="text-sm font-medium text-gray-900">{{ $item->user->name ?? "N/A" }}</div>
```

- [ ] **Step 5: Corriger l'eager loading du contrôleur**

Dans `app/Http/Controllers/Admin/LostFoundController.php`, remplacer :

```php
        $item = Item::where("id", $id)->with("users")->first();
```

par :

```php
        $item = Item::where("id", $id)->with("user")->first();
```

- [ ] **Step 6: Supprimer la méthode legacy du modèle**

Dans `app/Models/Item.php`, supprimer entièrement ces lignes :

```php
    /**
     * Relation avec le propriétaire (legacy name)
     */
    public function users()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

```

- [ ] **Step 7: Relancer les tests et constater qu'ils passent toujours**

Run: `php artisan test --filter=AdminItemDetailTest --filter=AdminLostFoundListTest`
Expected: `OK (2 tests, 4 assertions)`

- [ ] **Step 8: Lancer la suite complète pour détecter toute régression**

Run: `php artisan test`
Expected: tous les tests passent (aucune autre référence à `Item::users()` dans le projet).

- [ ] **Step 9: Commit**

```bash
git add app/Models/Item.php app/Http/Controllers/Admin/LostFoundController.php resources/views/Admin/LostFound/detail.blade.php resources/views/Admin/LostFound/view_lost_found.blade.php tests/Feature/AdminItemDetailTest.php tests/Feature/AdminLostFoundListTest.php
git commit -m "refactor: remove legacy Item::users() alias in favor of user()"
```

---

### Task 10: Nettoyage de `public/hot` obsolète

**Files:**
- Delete: `public/hot`

**Interfaces:**
- Aucune (fichier d'état local généré par Vite, déjà listé dans `.gitignore` — aucun commit Git nécessaire pour ce fichier lui-même).

- [ ] **Step 1: Constater l'état actuel**

Run: `cat public/hot`
Expected : affiche `http://[::1]:4000` — un serveur Vite qui n'est plus lancé sur ce port.

- [ ] **Step 2: Supprimer le fichier**

Run: `rm public/hot`

- [ ] **Step 3: Vérifier**

Run: `test -f public/hot && echo "encore présent" || echo "supprimé"`
Expected: `supprimé`

Aucun `git add`/`commit` ici : `public/hot` est déjà dans `.gitignore` (`/public/hot`), donc jamais suivi par Git — sa suppression est purement locale. Il sera régénéré automatiquement au prochain `npm run dev`, et absent en production après `npm run build`.

---

## Vérification finale

Une fois les 10 tâches complétées :

- [ ] Run: `php artisan test` — tous les tests passent (suite complète, y compris les tests d'exemple préexistants).
- [ ] Run: `git log --oneline -10` — 9 commits distincts (Task 10 n'en produit pas), un par tâche.
- [ ] Run: `php artisan migrate --force` sur la base `findme` réelle — aucune migration en attente, aucune erreur.
