# 📱 GUIDE COMPLET - QCT POUR LA POPULATION IVOIRIENNE

## 🎯 Vue d'Ensemble

**QCT (Qui Cherche, Trouve)** est une plateforme gratuite et accessible qui aide la population ivoirienne à:
- 🔍 Retrouver des **objets perdus**
- 🎁 Signaler des **objets trouvés**
- 👥 Retrouver des **personnes disparues**

## 📊 Problèmes Résolus dans Ce Déploiement

### ✅ Sécurité (CRITIQUE - RÉSOLU)
- **Avant:** Bug dans le middleware admin permettait aux utilisateurs normaux d'accéder au dashboard
- **Après:** Logique corrigée - seuls les admins y ont accès

- **Avant:** N'importe qui pouvait modifier/supprimer l'item d'un autre
- **Après:** Vérification d'autorisation ajoutée sur toutes les opérations

- **Avant:** Suppression via GET = vulnérable aux attaques CSRF
- **Après:** Utilisation de POST pour toutes les opérations destructives

### ✅ Performance (RÉSOLU)
- **Avant:** N+1 queries (requête supplémentaire par item)
- **Après:** Eager loading avec `with('user', 'foundUser')`

- **Avant:** Pas d'indexes sur les colonnes fréquemment recherchées
- **Après:** Indexes ajoutés sur status, category, dates

### ✅ Communications (RÉSOLU)
- **Avant:** Notifications en commentaire (non fonctionnelles)
- **Après:** Système de notifications par email implémenté

- **Avant:** Utilisateurs ne savaient jamais si quelqu'un trouvait leur objet
- **Après:** Emails automatiques quand quelqu'un signale avoir trouvé l'objet

### ✅ Base de Données (RÉSOLU)
- **Avant:** "image" vs "images" - colonne incohérente avec le code
- **Après:** Migration créée pour renommer vers "images"

- **Avant:** Statuts confus (deliver vs delivered)
- **Après:** Standardisation en cours

## 🚀 COMMENT DÉPLOYER SUR OVH (Recommandé pour Ivory Coast)

### Étape 1: SSH sur votre serveur
```bash
ssh user@your_server_ip
cd /var/www/qct
```

### Étape 2: Installer les dépendances
```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### Étape 3: Copier et configurer .env
```bash
cp .env.example .env
# Éditer .env avec vos paramètres
nano .env
```

### Étape 4: Générer clés et exécuter migrations
```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

### Étape 5: Configurer les permissions
```bash
chmod 775 storage bootstrap/cache
chown www-data:www-data -R storage bootstrap/cache
```

### Étape 6: Configurer le webserver (Nginx)
```nginx
server {
    listen 80;
    server_name qct.ci;
    root /var/www/qct/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Étape 7: Configurer HTTPS (Let's Encrypt)
```bash
sudo certbot certonly --nginx -d qct.ci
# Mise à jour nginx.conf pour HTTPS
```

### Étape 8: Configurer les emails (SendGrid recommandé pour Ivory Coast)
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=SG.xxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@qct.ci
MAIL_FROM_NAME=QCT
```

## 💰 COÛTS ESTIMÉS (Côte d'Ivoire)

| Service | Coût | Notes |
|---------|------|-------|
| Domaine (.ci) | ~5000 FCFA/an | Registrar: Afrinic |
| Hébergement OVH | ~30$ - 100$/mois | Dépend du plan |
| Email (SendGrid) | Gratuit (100/j) | Pour début; payant après |
| SSL Certificate | GRATUIT | Let's Encrypt |
| **TOTAL MENSUEL** | **~15,000-35,000 FCFA** | Plan minimal |

## 🎓 TUTORIELS POUR UTILISATEURS

### Signaler un objet perdu
1. Cliquez sur "J'ai perdu quelque chose"
2. Remplissez le formulaire avec:
   - **Nom de l'objet** (ex: "Téléphone Samsung")
   - **Catégorie** (ex: "Électronique")
   - **Date de perte**
   - **Description détaillée** (couleur, marques, etc)
   - **Photos** (maximum 5)
3. Cliquez "Publier"
4. Recevez une notification si quelqu'un le trouve!

### Signaler un objet trouvé
1. Cliquez sur "J'ai trouvé un objet"
2. Remplissez les détails de l'objet
3. Cliquez "Publier"
4. Le propriétaire recevra une notification par email!

### Chercher un objet
1. Allez sur "Voir tous les objets"
2. Utilisez les filtres:
   - 🔍 Recherche par nom
   - 📁 Catégorie
   - 🏷️ Perdu/Trouvé
3. Cliquez sur un résultat pour voir les détails

## 🌐 FEATURES CLÉS

### Pour les Utilisateurs Normaux
- ✅ Créer annonces d'objets perdus/trouvés
- ✅ Rechercher et filtrer objets
- ✅ Recevoir notifications par email
- ✅ Voir profil du trouveur
- ✅ Valider que c'est vraiment l'objet

### Pour les Administrateurs
- ✅ Dashboard avec statistiques
- ✅ Gestion des catégories
- ✅ Modération des annonces
- ✅ Voir tous les utilisateurs
- ✅ Gérer les messages

### À Ajouter (Phase 2)
- 📍 Intégration Google Maps
- 📱 Application mobile
- 💬 Chat en direct
- ⭐ Système d'avis/notation
- 🔔 Notifications push

## 📞 SUPPORT & CONTACT

- **Email Support:** support@qct.ci
- **WhatsApp:** +225 XXXXXXXXXX (optionnel)
- **Facebook:** facebook.com/QCT-CotedIvoire

## 📋 CHECKLIST AVANT LE LANCEMENT

- [ ] Domaine acheté et pointé
- [ ] Serveur configuré et sécurisé
- [ ] Base de données créée et migrée
- [ ] Email configuré et testé
- [ ] SSL/HTTPS activé
- [ ] Images compressées et optimisées
- [ ] Backups configurés
- [ ] Test de registration et création d'item
- [ ] Test des notifications par email
- [ ] Analytics (Google Analytics) configuré
- [ ] Cache configuré (Redis)
- [ ] Rate limiting activé

## 🔒 SÉCURITÉ - TOUJOURS VÉRIFIER

```bash
# Avant le lancement en production:
APP_DEBUG=false  # JAMAIS true en production!
php artisan config:cache
php artisan route:cache
```

## 📈 CROISSANCE & MARKETING

### Canaux de Promotion (Côte d'Ivoire)
1. **Facebook** - Créer page communautaire QCT
2. **WhatsApp** - Groupes d'utilisateurs par région
3. **Radio/Télé** - Publicités locales
4. **Écoles** - Partenariats pour sensibilisation
5. **Police/Gendarmerie** - Collaboration officielle

### Objectifs (Premier Trimestre)
- 1000 utilisateurs
- 500 annonces actives
- 50 objets retrouvés
- Couverture dans 3+ villes principales

