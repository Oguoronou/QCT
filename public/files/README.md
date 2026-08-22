# QCT — Qui cherche trouve · kit logo

Concept retenu : icône applicative + signature. Le Q est traité en loupe
(anneau + manche à 45°), le badge carré arrondi porte le symbole seul.

## Contenu

| Fichier | Usage |
|---|---|
| `qct-logo-horizontal.svg` | Verrouillage principal, fonds clairs |
| `qct-logo-horizontal-inverse.svg` | Fonds sombres (#1e293b, photos assombries) |
| `qct-logo-vertical.svg` | Supports carrés, réseaux sociaux, splash screen |
| `qct-logo-monochrome.svg` | Impression une couleur — hérite de `currentColor` en HTML |
| `qct-app-icon.svg` + PNG 1024/512/192/96 | Icône iOS / Android / PWA |
| `qct-app-icon-maskable.svg` + PNG 512 | Icône `maskable` Android et PWA (zone sûre 80 %) |
| `qct-favicon.svg` + `qct-favicon-64.png` | Favicon web |
| `preview.html` | Planche de marque à présenter au client |

## Intégration web

```html
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="64x64" href="/qct-favicon-64.png">
<link rel="apple-touch-icon" href="/qct-app-icon-192.png">
```

```json
{
  "icons": [
    { "src": "/qct-app-icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/qct-app-icon-512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/qct-app-icon-maskable-512.png", "sizes": "512x512", "type": "image/png", "purpose": "maskable" }
  ]
}
```

## Intégration Flutter

Placer `qct-app-icon-1024.png` dans `assets/icon/icon.png` puis :

```yaml
flutter_launcher_icons:
  android: true
  ios: true
  image_path: "assets/icon/icon.png"
  adaptive_icon_background: "#1e293b"
  adaptive_icon_foreground: "assets/icon/icon-foreground.png"
```

## Règles

- Zone de protection = rayon de la lentille (36 px pour un badge de 160 px).
- Taille minimale du verrouillage horizontal : 140 px de large ; en dessous, icône seule.
- Rayon du badge = 25 % du côté (40 sur 160). Ne pas l'aplatir.
- La signature « Qui cherche trouve » utilise une géométrique (Poppins / Montserrat /
  Outfit, Regular). Convertir le texte en courbes avant toute diffusion externe.
- Ne jamais recolorer le badge : seuls #1e293b et #ffffff sont autorisés.
