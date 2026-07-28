# Déclaration au commissariat — QCT (Qui Cherche, Trouve)

Date : 2026-07-28
Statut : Approuvé

## Contexte

En Côte d'Ivoire, la remise d'un objet trouvé (ou le signalement d'une personne retrouvée, catégorie `personnes`) doit légalement transiter par un commissariat de police. QCT ne capture aujourd'hui aucune notion de localisation ni de commissariat : la table `items` n'a aucun champ de dépôt/localisation, et il n'existe aucun annuaire de postes de police dans le schéma.

Ce projet est le premier des trois sous-systèmes identifiés pour rendre la plateforme plus professionnelle et conforme (les deux autres — carte des objets perdus/retrouvés, canal de contact avec la police — seront brainstormés séparément). Il couvre : un annuaire de commissariats géré en Admin, et l'obligation pour un trouveur de déclarer où l'objet a été déposé avant que l'item ne passe au statut `found`.

**Découverte en cours de route** : l'action `ItemController::itemFound()` (route `POST item-found/{id}`) existe déjà dans le code mais n'est appelée par **aucune vue** — c'est un bouton jamais câblé, dans la lignée des autres dérives documentées dans `CLAUDE.md`. Ce projet termine ce câblage en même temps qu'il y ajoute l'obligation de déclaration.

## 1. Annuaire des commissariats

**Nouvelle table `commissariats`** :
- `id`, `name` (string), `commune` (string), `city` (string, default `'Abidjan'`), `phone` (string, nullable), `address` (string, nullable), `is_active` (boolean, default `true`), timestamps.

**Modèle** `App\Models\Commissariat` (fillable sur les champs ci-dessus, `hasMany(ItemPoliceDeclaration::class)`).

**Seed** (`CommissariatSeeder`) : une liste des principaux commissariats des communes d'Abidjan (Plateau, Cocody, Yopougon, Adjamé, Treichville, Marcory, Koumassi, Abobo) — **nom et commune uniquement**. `phone` et `address` restent `null` dans le seed : ce sont des informations institutionnelles qui doivent être vérifiées avant publication, pas inventées. Un commentaire dans le seeder l'indique explicitement pour que l'admin les complète avec des données vérifiées.

**Admin CRUD** : `App\Http\Controllers\Admin\CommissariatController` (`index`, `create`, `store`, `edit`, `update`). Pas de `destroy` — un commissariat déjà référencé par une déclaration ne doit pas pouvoir disparaître ; à la place, un bouton bascule `is_active` (désactivé = n'apparaît plus dans le `<select>` du formulaire de déclaration, mais reste visible sur les déclarations historiques). Routes sous `/admin/commissariats/*`, à l'intérieur du groupe `AdminLogin` existant dans `routes/web.php`. Vues `resources/views/Admin/Commissariats/{index,form}.blade.php` sur le layout `Admin/layout.blade.php` (NiceAdmin, cohérent avec le reste de l'espace admin).

## 2. Déclaration de dépôt

**Nouvelle table `item_police_declarations`** (plutôt que d'ajouter encore des colonnes sur `items`, déjà en dérive selon `CLAUDE.md`) :
- `id`, `item_id` (FK unique vers `items`), `commissariat_id` (FK vers `commissariats`), `declared_by_user_id` (FK vers `users`), `declaration_number` (string), `receipt_photo` (string, nullable — chemin relatif, même convention que `items.images`), `declared_at` (timestamp), timestamps.

**Modèle** `App\Models\ItemPoliceDeclaration` : `belongsTo(Item::class)`, `belongsTo(Commissariat::class)`, `belongsTo(User::class, 'declared_by_user_id')`. `Item::policeDeclaration()` en `hasOne`.

**Portée de l'obligation** : uniquement pour les items `status === 'found'` (quelqu'un poste un objet qu'il a trouvé). Pour `status === 'lost'`, il n'y a pas d'obligation légale de déclaration (l'original propriétaire qui retrouve lui-même son objet n'a rien à signaler à la police) — `itemFound()` garde son comportement actuel (simple passage de `lost_found_status` à `found`) dans ce cas.

## 3. Flux & intégration UI

**Déclencheur** : `resources/views/my_items.blade.php` affiche un bouton "Marquer comme déposé" sur les items où `status === 'found' && lost_found_status === 'pending'` (aujourd'hui aucun bouton n'existe pour cette transition). Le clic ouvre une petite section/modal avec :
- `<select>` des commissariats `is_active = true` (obligatoire),
- champ texte `declaration_number` (obligatoire),
- upload `receipt_photo` (optionnel, image, max 2 Mo — mêmes contraintes que les photos d'objet).

**Contrôleur** — `ItemController::itemFound(Request $request, $id)` :
```
si $item->status === 'found':
    valider ['commissariat_id' => 'required|exists:commissariats,id',
             'declaration_number' => 'required|string|max:100',
             'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048']
    upload receipt_photo si présent -> public_path('uploads/declarations') (même pattern que ITEM_IMAGES_FOLDER)
    créer/mettre à jour ItemPoliceDeclaration (upsert par item_id)
    $item->update(['lost_found_status' => 'found'])
sinon (status === 'lost'):
    comportement actuel inchangé
```

**Édition** : le trouveur (déclarant) peut corriger sa déclaration (n° erroné, ajout tardif de la photo) via une section ajoutée à `resources/views/item_edit.blade.php`, visible seulement si `Auth::id() === $item->policeDeclaration->declared_by_user_id`.

**Affichage** (`item_detail.blade.php`, et son équivalent `Admin/LostFound/detail.blade.php`) :
- Nom + commune du commissariat : **visible publiquement** dès qu'une déclaration existe (utile pour que le futur réclamant sache où aller).
- `declaration_number` + `receipt_photo` : **visibles uniquement** si `Auth::id()` est le déclarant, ou si `Auth::id() === $item->found_user_id` **et** `lost_found_status === 'returned'` (le réclamant ne voit ces détails privés qu'une fois sa réclamation validée par `validateOwnership`, pas dès `ownership_claimed`), ou côté Admin (accès systématique, cohérent avec le rôle d'audit décrit ci-dessous).

## 4. Audit Admin

Pas de modération/validation bloquante : la déclaration est effective dès sa saisie par le trouveur (rapport de confiance). L'admin peut uniquement consulter — `Admin/LostFound/detail.blade.php` est enrichi pour afficher la déclaration complète (commissariat, n°, photo, date, déclarant) quand elle existe, pour audit ou en cas de litige.

## Hors scope

- Carte des objets perdus/retrouvés (géolocalisation) — sous-projet suivant.
- Canal de contact/intégration avec la police (API, notification institutionnelle) — sous-projet suivant, et de toute façon hors de portée technique seule sans partenariat officiel.
- Modération/validation admin des déclarations avant effet.
- Champs `phone`/`address` réels des commissariats seedés (laissés vides, à compléter avec des données vérifiées par un admin).

## Tests

- `itemFound` sur un item `found` sans `commissariat_id` ni `declaration_number` échoue (validation), le statut ne change pas.
- `itemFound` sur un item `found` avec des données valides crée la `ItemPoliceDeclaration` et passe `lost_found_status` à `found`.
- `itemFound` sur un item `lost` fonctionne toujours sans déclaration exigée (non-régression).
- Upload optionnel de `receipt_photo` : présent → fichier stocké et chemin persisté ; absent → pas d'erreur.
- Visibilité : un visiteur tiers voit le nom du commissariat mais pas `declaration_number` ni la photo ; le déclarant voit tout ; le réclamant ne voit les détails privés qu'après validation de sa réclamation ; l'admin voit toujours tout.
- CRUD Admin `commissariats` : création, édition, toggle `is_active`, et un commissariat désactivé n'apparaît plus dans le `<select>` du formulaire de déclaration mais reste affiché sur les déclarations historiques déjà créées.
