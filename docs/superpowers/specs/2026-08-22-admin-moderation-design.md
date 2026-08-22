# Modération du site — Paramètres, témoignages, pages de contenu

Date : 2026-08-22
Statut : Approuvé

## Contexte

L'espace Admin (`app/Http/Controllers/Admin/*`, `resources/views/Admin/**`, layout NiceAdmin/Tailwind) permet aujourd'hui de gérer catégories, commissariats, items, utilisateurs et messages de contact, mais rien du contenu public du site n'est administrable :

- Le logo, le nom « QCT », la description, l'email, le téléphone, l'adresse et les 4 icônes réseaux sociaux du footer (`resources/views/layout.blade.php`) sont **en dur**, et les réseaux sociaux pointent vers `#`.
- Les messages de contact (`messages` : `name`, `email`, `message`, `status`) n'ont aucune notion de « témoignage » à mettre en avant.
- Les liens « Comment ça marche », « FAQ », « Politique de confidentialité », « Conditions d'utilisation » du footer pointent vers `#` : ces pages n'existent pas.

Ce projet couvre ces trois besoins : ils sont indépendants techniquement mais relèvent tous de la même zone d'administration (« modération du site ») et sont conçus ensemble à la demande de l'utilisateur.

## 1. Paramètres du site (clé-valeur)

**Nouvelle table `settings`** : `id`, `key` (string, unique), `value` (text, nullable), timestamps.

**Modèle** `App\Models\Setting` avec deux méthodes statiques :
- `Setting::get(string $key, $default = null)` — lit via `Cache::rememberForever("setting.$key", ...)`.
- `Setting::set(string $key, $value)` — `updateOrCreate(['key' => $key], ['value' => $value])` puis `Cache::forget("setting.$key")`.

**Clés utilisées** : `site_name`, `site_logo` (chemin relatif type `uploads/settings/xxx.png`), `site_description`, `contact_email`, `contact_phone`, `contact_address`, `social_facebook`, `social_twitter`, `social_instagram`, `social_whatsapp`.

**Admin** — `App\Http\Controllers\Admin\SettingController` (`edit`/`update`, à l'image de `Admin\ProfileController` : pas de CRUD, un formulaire unique) :
- `edit()` : affiche le formulaire pré-rempli via `Setting::get()` pour chaque clé.
- `update(Request $request)` : valide (`site_name` requis, `contact_email` nullable email, `social_*` nullable url, `site_logo` nullable image max 2 Mo), boucle sur les champs texte pour `Setting::set`, et pour `site_logo` si un fichier est envoyé : supprime l'ancien (`unlink(public_path(...))` si existant) puis `move(public_path('uploads/settings'), $filename)` — même convention que `ITEM_IMAGES_FOLDER` dans `ItemController`.

Routes (dans le groupe `AdminLogin` existant) :
```
GET  admin/settings   -> SettingController@edit
POST admin/settings   -> SettingController@update
```
Vue `resources/views/Admin/Settings/settings.blade.php`, nouvelle entrée sidebar « Paramètres du site » (icône `fa-cog`) dans `Admin/layout.blade.php`.

**Affichage public** — `resources/views/layout.blade.php` :
- Header : logo remplacé par `<img src="{{ asset(Setting::get('site_logo')) }}">` si renseigné, sinon l'icône bleue actuelle en fallback ; nom du site depuis `Setting::get('site_name', 'QCT')`.
- Footer : description depuis `site_description` (fallback sur le texte actuel), bloc contact depuis `contact_email`/`contact_phone`/`contact_address` (chaque ligne masquée si vide), les 4 icônes réseaux sociaux masquées individuellement si leur URL est vide, sinon `href` vers la valeur stockée.
- `Setting::get()` étant static et caché, il est appelé directement dans le Blade sans passer par un contrôleur (cohérent avec l'absence de View Composer existant dans le projet).

## 2. Témoignages

**Migration** : ajoute `is_testimonial` (boolean, default `false`) à `messages`.

**Admin** — dans `resources/views/Admin/Message/messages.blade.php`, chaque ligne du tableau reçoit un bouton supplémentaire :
- `is_testimonial == false` → formulaire POST « Marquer comme témoignage »
- `is_testimonial == true` → formulaire POST « Retirer le témoignage » (+ badge visuel indiquant que le message est déjà un témoignage)

Contrairement aux actions existantes de cette page (`Delete`, `Mark as Replied`/`Mark as Pending`) qui sont des `<a href>` vers des routes `POST` — un bug préexistant hors-scope, laissé tel quel — la nouvelle action utilise un vrai `<form method="POST">` avec `@csrf`.

**Contrôleur** — nouvelle méthode `MessageController::toggleTestimonial($id)` :
```php
$message = Message::findOrFail($id);
$message->update(['is_testimonial' => !$message->is_testimonial]);
Session::flash('message', $message->is_testimonial ? 'Message marqué comme témoignage' : 'Témoignage retiré');
return redirect()->back();
```
Route : `POST admin/toggle-testimonial/{id}` dans le groupe `AdminLogin`.

**Affichage public** — `routes/web.php` (page `/`) ajoute `$testimonials = Message::where('is_testimonial', true)->latest()->take(6)->get()`, passé à `welcome.blade.php`. Nouvelle section « Ils en parlent » / « Témoignages » (grille de cartes : nom + extrait du message), insérée après la section des items résolus. **La section entière est omise si `$testimonials` est vide** (pas de placeholder vide).

## 3. Pages de contenu (Pages statiques + FAQ)

Deux modèles distincts car les besoins d'édition diffèrent (texte riche unique vs liste de paires question/réponse).

### 3.1 Pages statiques

**Nouvelle table `pages`** : `id`, `slug` (string, unique), `title` (string), `content` (longtext, nullable), timestamps.

**Modèle** `App\Models\Page` (fillable : `slug`, `title`, `content`).

**Seeder** `PageSeeder` crée 4 lignes avec un contenu générique de départ en français, adapté à QCT / Côte d'Ivoire, à affiner ensuite par l'admin :
- `comment-ca-marche` — « Comment ça marche »
- `politique-confidentialite` — « Politique de confidentialité »
- `cgu` — « Conditions Générales d'Utilisation »
- `cgv` — « Conditions Générales de Vente »

**Admin** — `App\Http\Controllers\Admin\PageController` :
- `index()` : liste les 4 pages (titre + lien éditer). Pas de create/delete — le jeu de pages est fixe.
- `edit($id)` : formulaire avec champ `title` et textarea `content` monté en éditeur **TinyMCE** (bundle déjà présent dans `public/customerdesign/assets/vendor/tinymce`, chargé en local, pas de CDN).
- `update(Request $request, $id)` : valide `title` requis, `content` nullable, `$page->update(...)`.

Routes (`AdminLogin`) :
```
GET  admin/pages              -> PageController@index
GET  admin/pages/{id}/edit    -> PageController@edit
POST admin/pages/{id}         -> PageController@update
```

**Public** — nouveau contrôleur `App\Http\Controllers\PageController` (namespace racine, comme `MessageController`) :
- `show($slug)` : `Page::where('slug', $slug)->firstOrFail()`, vue `resources/views/page_detail.blade.php` (étend `layout.blade.php`, affiche `title` + `{!! $page->content !!}`).

Routes publiques :
```
GET /page/comment-ca-marche         -> PageController@show (slug=comment-ca-marche)
GET /page/politique-confidentialite -> PageController@show (slug=politique-confidentialite)
GET /page/cgu                       -> PageController@show (slug=cgu)
GET /page/cgv                       -> PageController@show (slug=cgv)
```
(une seule route paramétrée `GET /page/{slug}` suffit puisque `show` prend le slug en paramètre).

### 3.2 FAQ

**Nouvelle table `faq_items`** : `id`, `question` (string), `answer` (text), `order` (integer, default 0), timestamps.

**Modèle** `App\Models\FaqItem` (fillable : `question`, `answer`, `order`).

**Seeder** `FaqItemSeeder` : ~6 questions/réponses génériques de départ (ex. « Comment signaler un objet trouvé ? », « Comment fonctionne la remise d'un objet ? », etc.), `order` séquentiel.

**Admin** — `App\Http\Controllers\Admin\FaqController`, CRUD complet à l'image de `CommissariatController` :
```
index()   : liste ordonnée par `order`
create()/store()   : formulaire question/réponse/ordre
edit($id)/update()
delete($id)         : suppression directe (pas de toggle — une FAQ obsolète n'a pas besoin d'historique)
```
Routes (`AdminLogin`) : `admin/faq`, `admin/add-faq`, `admin/save-faq`, `admin/edit-faq/{id}`, `admin/update-faq/{id}`, `admin/delete-faq/{id}`.

**Public** — `PageController::faq()` : `FaqItem::orderBy('order')->get()`, vue `resources/views/faq.blade.php` (accordéon simple, cohérent avec le thème Tailwind dark de `layout.blade.php`). Route `GET /faq`.

### 3.3 Navigation admin & liens footer

- `Admin/layout.blade.php` : nouvelle section sidebar « Contenu » (dropdown, même pattern que « Catégories ») listant les 4 pages statiques (lien direct vers leur `edit`) + « FAQ ».
- `resources/views/layout.blade.php` footer, colonne « Aide » : les 4 liens `#` existants sont remplacés par `url('/faq')`, `url('/page/comment-ca-marche')`, `url('/page/politique-confidentialite')`, `url('/page/cgu')`, et un 5ème `<li>` est ajouté pour `url('/page/cgv')` (« Conditions générales de vente »).

## Hors scope

- Fixer le bug préexistant des actions `Delete`/`Mark as Replied`/`Mark as Pending` en `<a href>` vers des routes POST (`Admin/Message/messages.blade.php`) — non lié à cette fonctionnalité.
- Historique de versions ou brouillons pour les pages de contenu (une seule version publiée, éditée en place).
- Modération/validation des témoignages par un tiers (l'admin qui les marque a déjà autorité — pas de workflow d'approbation supplémentaire).
- Multi-langue pour les pages de contenu (le site est entièrement en français, pas de i18n existant).

## Tests

- `Setting::get()`/`Setting::set()` : écrire une clé, la relire (avec et sans cache déjà chargé), vérifier le fallback `$default` si absente.
- `SettingController@update` : upload d'un nouveau logo supprime l'ancien fichier et persiste le nouveau chemin ; soumission sans fichier logo ne touche pas à `site_logo`.
- Footer/header : avec des `Setting` vides, le rendu retombe sur les valeurs par défaut (logo icône, nom « QCT ») sans erreur ; avec des réseaux sociaux vides, les icônes correspondantes n'apparaissent pas.
- `toggleTestimonial` : un message `is_testimonial=false` bascule à `true` puis à `false` sur deux appels successifs.
- Page d'accueil : la section témoignages n'apparaît pas si aucun message n'est marqué ; apparaît avec au plus 6 messages sinon.
- `PageController@show` : slug existant → 200 avec le bon contenu ; slug inconnu → 404 (`firstOrFail`).
- Admin `PageController@update` : contenu HTML (TinyMCE) est bien persisté et réaffiché dans le textarea à l'édition suivante.
- FAQ Admin CRUD : création, édition, suppression, et l'ordre d'affichage public suit bien la colonne `order`.
- Routes publiques `/faq`, `/page/{slug}` accessibles sans authentification (pas de middleware `auth`).
