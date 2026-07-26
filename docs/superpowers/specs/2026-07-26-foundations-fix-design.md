# Correction des fondations — QCT (Qui Cherche, Trouve)

Date : 2026-07-26
Statut : Approuvé

## Contexte

Une revue du projet (voir `CLAUDE.md`) a révélé plusieurs problèmes de fondation qui fragilisent le cœur du produit : schéma de base de données désynchronisé des migrations, notifications qui échouent silencieusement, routes admin exposées sans protection, et quelques bugs/nettoyages mineurs. L'objectif de ce projet est de corriger ces problèmes avant d'ajouter de nouvelles fonctionnalités (messagerie SMS/WhatsApp, géolocalisation, anti-fraude, réputation, recherche intelligente, PWA, outils admin — traités dans un projet séparé).

Ce projet ne touche pas à l'UI ni n'ajoute de fonctionnalité utilisateur visible, à l'exception d'un message de notification supplémentaire pour le flux "ownership".

## 1. Réconciliation du schéma BDD / migrations

**Problème** : `items.status` et `items.lost_found_status` sont déclarés en `enum` restreint dans `2023_05_01_133834_create_items_table.php` (`lost_found_status` n'accepte que `pending, found, draft, deliver`), alors que le code (`ItemController`) écrit aussi `claimed`, `ownership_claimed`, `delivered`, `returned`. En production, ces colonnes ont été converties en `varchar(50)` hors-migration, donc ça fonctionne aujourd'hui — mais un `migrate:fresh` sur un nouvel environnement recréerait l'enum d'origine et casserait le flux de réclamation. De plus, deux migrations correctives existent déjà dans le repo (`2024_06_18_000000_fix_items_image_column.php`, `2024_06_18_000001_add_production_indexes.php`) mais n'ont jamais été exécutées (la table `migrations` ne contient que le batch 1), et aucun index (hormis la clé primaire) n'existe sur `items` en production.

**Solution** : nouvelle migration `database/migrations/2026_07_26_000000_reconcile_items_schema.php`, sans modifier les migrations historiques :
- convertit `status` et `lost_found_status` en `VARCHAR(50)` via `DB::statement('ALTER TABLE items MODIFY status VARCHAR(50) NOT NULL')` (et équivalent pour `lost_found_status`) — le projet n'a pas `doctrine/dbal` installé, donc `$table->string(...)->change()` n'est pas utilisable sans ajouter cette dépendance ; le `MODIFY COLUMN` en SQL brut évite ça. La commande vérifie d'abord le type actuel de la colonne (`information_schema.COLUMNS`) pour rester un no-op si déjà en varchar ;
- s'assure que `images` (pas `image`) et `found_user_id` existent (gardes `hasColumn`) ;
- ajoute les index manquants avec vérification d'existence au préalable (requête brute `SHOW INDEX FROM items WHERE Key_name = '...'`, plus simple que la voie Doctrine puisque `doctrine/dbal` n'est pas installé) : `user_id`, `status`, `lost_found_status`, `created_at`, composite `[category_name, status]`, et full-text `[item_name, description]`.
- **Réconciliation de l'historique** : les deux migrations `2024_06_18_*` sont déjà satisfaites par l'état réel de la base (colonne `images` déjà renommée) ; on insère leurs entrées dans la table `migrations` (même batch que la nouvelle migration) plutôt que de les laisser se rejouer, pour éviter tout conflit avec la nouvelle migration corrective qui couvre le même terrain plus proprement. Cette insertion se fait via une méthode `up()` qui vérifie d'abord si l'entrée existe déjà avant de l'insérer.

**Critère de succès** : `php artisan migrate` s'exécute sans erreur sur la base de prod actuelle *et* sur une base fraîche (`migrate:fresh` sur une DB de test), et dans les deux cas `items.lost_found_status` accepte toutes les valeurs utilisées par `ItemController`.

## 2. Notifications

**Problème** : `via()` déclare le canal `database` sur `ItemClaimedNotification` et `OwnershipClaimedNotification`, mais la table `notifications` n'existe pas → échec à l'exécution. De plus, `ItemController::validateClaim()` appelle `$item->foundUser->notify(new \Illuminate\Notifications\Messages\MailMessage)`, ce qui n'est pas une classe `Notification` valide et provoque une erreur silencieuse (avalée par le `catch` générique du contrôleur).

**Solution** :
- Exécuter `php artisan notifications:table` pour générer la migration standard, puis l'inclure dans ce projet (fichier de migration ajouté au repo, à appliquer avec le reste).
- Créer `App\Notifications\ClaimValidatedNotification` (mail + database, en français, même style que `ItemClaimedNotification`) informant le trouveur que le propriétaire a confirmé la récupération de l'objet. Brancher cette notification dans `validateClaim()` à la place de l'appel invalide.
- Créer `App\Notifications\OwnershipValidatedNotification` (même structure) informant le réclamant que le posteur original a confirmé la remise. Brancher dans `validateOwnership()`, en remplaçant le commentaire `// $item->foundUser->notify(...)`.

**Critère de succès** : `validateClaim()` et `validateOwnership()` n'écrivent plus d'exception avalée ; une notification en base est bien créée pour l'utilisateur notifié après ces deux actions (vérifiable via `$user->notifications`).

## 3. Sécurité des routes admin

**Problème** : dans `routes/web.php`, les routes `admin/messages` (GET), `admin/delete-message/{id}`, `admin/mark-as-reply/{id}`, `admin/mark-as-pending/{id}` (POST) sont déclarées **avant** et **hors** du groupe `Route::middleware(['AdminLogin'])->group(...)`, donc accessibles à n'importe quel visiteur, authentifié ou non.

**Solution** : déplacer ces quatre routes à l'intérieur du groupe `AdminLogin` existant, aux côtés des autres routes `admin/*`. La route publique `contact-us` (POST, formulaire de contact visiteur) reste hors du groupe, c'est son comportement voulu.

**Critère de succès** : une requête non authentifiée vers `admin/messages` redirige vers `my-account` avec le message flash existant ("Vous n'êtes pas autorisé..."), au lieu de retourner la liste des messages.

## 4. Rate limiting

**Problème** : `App\Http\Middleware\RateLimitRequests` existe (limite configurable par requête/IP/minute, réponse JSON 429) mais n'est déclaré dans aucun alias de `app/Http/Kernel.php` ni appliqué à aucune route — code mort.

**Solution** :
- Ajouter l'alias `'throttle.custom' => \App\Http\Middleware\RateLimitRequests::class` dans `$middlewareAliases` de `app/Http/Kernel.php`.
- Appliquer `->middleware('throttle.custom:10,1')` (10 requêtes/minute) aux routes `POST login`, `POST register`, `POST contact-us` dans `routes/web.php`.

**Critère de succès** : après 11 tentatives de connexion en moins d'une minute depuis la même IP, la 11ᵉ retourne un 429 JSON au lieu de tenter l'authentification.

## 5. Petits bugs / nettoyage

- **Compteur dashboard admin** : dans `DashboardController::index()`, `Item::where("lost_found_status", "deliver")` → `"delivered"` (la valeur réellement écrite par `itemDeliver()`/`validateClaim()`/`validateOwnership()`).
- **Code mort `Item::users()`** : suppression de la méthode dans `app/Models/Item.php` ; mise à jour de son unique appelant, `Admin\LostFoundController::itemDetail()` (`->with("users")` → `->with("user")`).
- **`public/hot` obsolète** : suppression du fichier (il pointe vers `http://[::1]:4000`, un serveur Vite qui n'est plus lancé sur ce port). Il sera régénéré automatiquement au prochain `npm run dev` ; absent en production après `npm run build`. Déjà ignoré par Git (`/public/hot` dans `.gitignore`), donc suppression locale uniquement.
- **`.env`** : déjà absent du suivi Git (présent dans `.gitignore`, jamais committé) — aucune action Git nécessaire. Recommandation hors-scope de ce projet : remplacer le mot de passe de base de données `1234` par un mot de passe fort.

## Hors scope

- Toute nouvelle fonctionnalité utilisateur (messagerie interne, SMS/WhatsApp, carte, anti-fraude, réputation, recherche intelligente, PWA, outils admin avancés) — sera brainstormée séparément une fois les fondations posées.
- Rotation effective du mot de passe `.env` (action pour l'utilisateur, hors du code).
- Purge de l'historique Git (aucun secret n'y a été trouvé committé).

## Tests

Pas de suite de tests substantielle existante (seulement les deux tests d'exemple PHPUnit). Ce projet ajoute des tests Feature ciblés :
- Migration : test que `migrate:fresh` + insertion d'un item avec `lost_found_status = 'ownership_claimed'` réussit.
- Notifications : test que `validateClaim()` et `validateOwnership()` créent bien une notification en base pour le bon utilisateur.
- Sécurité routes : test qu'un visiteur non authentifié frappant `admin/messages` est redirigé.
- Rate limiting : test que la 11ᵉ requête POST `login` en une minute retourne 429.
