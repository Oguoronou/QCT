# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**QCT ("Qui Cherche, Trouve")** — a Laravel 10 lost-and-found application for Côte d'Ivoire. Users post lost or found items (and missing *persons*, via the `personnes` category), other users claim them, and the original poster validates the claim. All user-facing copy, flash messages, and comments are in **French**.

`README.md` is the stock Laravel readme and carries no project information. The `*.md` audit documents at the root (`AUDIT_PRODUCTION.md`, `CORRECTIONS_TECHNIQUES.md`, `DIAGRAMMES_ARCHITECTURE.md`, `PRODUCTION_CHECKLIST.md`, `GUIDE_COMPLET_IVOIRE.md`, `RÉSUMÉ_FINAL.md`) are a French audit + remediation log describing what was fixed and what remains before production.

## Environment & commands

Laravel 10.48 / PHP 8.1+ / MySQL, developed under **Laragon on Windows**. Local DB is `findme` on `127.0.0.1:3306`.

```bash
php artisan serve              # app on http://localhost:8000 (or use the Laragon vhost)
npm run dev                    # Vite dev server (Tailwind); writes public/hot
npm run build                  # production assets into public/build
php artisan migrate            # see the migration warning below before running
php artisan tinker
php artisan route:list
./vendor/bin/pint              # Laravel Pint (code style) — no config file, uses defaults
```

Tests (PHPUnit 10, only the two stock example tests exist):

```bash
php artisan test                                   # everything
php artisan test --testsuite=Feature               # one suite
php artisan test --filter=ExampleTest              # one test class/method
./vendor/bin/phpunit tests/Feature/ExampleTest.php # one file
```

`phpunit.xml` has the sqlite in-memory `DB_CONNECTION`/`DB_DATABASE` lines **commented out**, so tests run against the real MySQL database in `.env`. Uncomment them (or set the env vars) before writing tests that touch the DB.

## Architecture

### Two parallel auth stacks — only one is live

- **Live**: `App\Http\Controllers\User\RegisterController` handles `register` / `login` / `logout` against the flat views `resources/views/login.blade.php` and `register.blade.php`. Login redirects on `role`: `user` → `/my-account`, anything else → `/admin/dashboard`.
- **Dead**: the `laravel/ui` scaffolding (`app/Http/Controllers/Auth/*`, `resources/views/auth/*`, `resources/views/layouts/app.blade.php`, `HomeController`). `Auth::routes()` is never called in `routes/web.php`, so those controllers and views are unreachable. Don't extend them; add to the `User\` namespace instead.

Authorization is role-based on `users.role` (`user` | `admin`), enforced by the `AdminLogin` middleware alias registered in `app/Http/Kernel.php`. Controllers call `$this->middleware('auth')` in their constructors rather than declaring middleware on routes.

### Item lifecycle (the core domain)

`items.status` is the *kind* of post (`lost` | `found`); `items.lost_found_status` is the *workflow state*. Two distinct claim flows exist in [ItemController.php](app/Http/Controllers/User/ItemController.php):

- **Someone found your lost item**: `claimItem` sets `found_user_id` + `lost_found_status = 'claimed'` and notifies the owner → owner calls `validateClaim`, which sets `'delivered'`.
- **A found item is mine**: `claimOwnership` (only when `status == 'found'`) sets `'ownership_claimed'` and notifies the poster → poster calls `validateOwnership`, which sets `'returned'`.

Plus the owner-driven shortcuts `itemFound` → `'found'` and `itemDeliver` → `'delivered'`. Ownership checks go through the private `authorizeItem()` helper (`$item->user_id === Auth::id()`); there are no Policies or Gates. Every action is wrapped in try/catch that flashes a French message and redirects back.

### Images

Uploads are moved with `$image->move(public_path('uploads/items'), ...)` — the `Storage` facade and `storage:link` are **not** used. Multiple paths are stored as a **comma-separated string** in `items.images`; read them via `Item::getImagesArray()` / `getFirstImage()`. Deleting or replacing an item unlinks the files directly. Profile images land in `images/` (relative to the working directory) with just the filename stored on `users.image`.

### Views: two unrelated design systems

- **Public/user side** — `resources/views/layout.blade.php` plus the flat `welcome`, `all_items`, `my_items`, `item_detail`, `item_edit`, `add_item`, `my_account` views: dark Tailwind theme compiled through Vite (`@vite(['resources/css/app.css'])`), with Inter + Font Awesome from CDN. Tailwind custom colors (`primary`, `primary-hover`, `dark`) live in `tailwind.config.js`.
- **Admin side** — `resources/views/Admin/**` extends `Admin/layout.blade.php`, which uses the static NiceAdmin-style bundle in `public/customerdesign/` and CDN links. It does **not** go through Vite.

`add-item` and `add-found-item` both render `add_item.blade.php` with a `$type` of `'lost'` or `'found'`.

## Gotchas

- **The DB schema has drifted from the migrations.** Only the batch-1 migrations are recorded as run. Live `items` has `images` (not `image`) and `status`/`lost_found_status` as `varchar(50)`, while `2023_05_01_133834_create_items_table.php` still declares `image` and narrow enums (`pending, found, draft, deliver`) that don't include `claimed`, `delivered`, `ownership_claimed`, or `returned`. A `migrate:fresh` will produce a schema the claim workflow cannot write to. The two untracked `2024_06_18_*` migrations were written to reconcile this but have not been applied.
- **`notifications` table does not exist.** `ItemClaimedNotification` and `OwnershipClaimedNotification` declare `via() = ['mail', 'database']`, so the database channel will fail until `php artisan notifications:table && php artisan migrate` is run. They are also `ShouldQueue` with `QUEUE_CONNECTION=sync`, so mail sends inline on the request.
- `validateClaim` notifies with a bare `MailMessage` instead of a Notification class — that call is broken.
- Admin dashboard counts `lost_found_status == "deliver"`, but the item flow writes `"delivered"`; the counter reads zero.
- The `admin/messages`, `admin/delete-message`, `admin/mark-as-*` and `contact-us` routes sit **outside** the `AdminLogin` middleware group in [web.php](routes/web.php).
- `RateLimitRequests` middleware exists but is not registered in `app/Http/Kernel.php` and is unused.
- `public/hot` may be stale from a previous `npm run dev` (it points at port 4000). If assets 404, delete it and run `npm run build`.
- `Item::users()` is a legacy duplicate of `Item::user()`, still referenced by `Admin\LostFoundController`.
- `.env` is committed to the repository with real local credentials.
