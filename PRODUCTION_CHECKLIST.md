# 📋 CHECKLIST PRODUCTION - QCT

## Configuration Environnement

```env
# .env.example - À mettre à jour en production

# Application
APP_NAME=QCT
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votredomaine.ci

# Database (Ivory Coast)
DB_CONNECTION=mysql
DB_HOST=your_host
DB_PORT=3306
DB_DATABASE=qct_db
DB_USERNAME=qct_user
DB_PASSWORD=strong_password_here

# Mail Configuration (SendGrid ou autre)
MAIL_MAILER=sendgrid
MAIL_FROM_ADDRESS=noreply@qct.ci
MAIL_FROM_NAME=QCT
SENDGRID_API_KEY=your_api_key

# SMS Configuration (Twilio pour notifications)
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_PHONE_NUMBER=

# File Storage
FILESYSTEM_DISK=public
FILESYSTEM_VISIBILITY=public

# Cache
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=warning

# Sanctum (API)
SANCTUM_STATEFUL_DOMAINS=localhost,yourdomain.ci
```

## À Faire Avant le Déploiement

### 🔒 Sécurité
- [ ] `APP_DEBUG=false` en production
- [ ] Générer une nouvelle `APP_KEY` pour production
- [ ] Configurer HTTPS et SSL
- [ ] Mettre à jour CORS config
- [ ] Activer le rate limiting
- [ ] Configurer les emails

### 📊 Performance
- [ ] Mettre en cache la configuration: `php artisan config:cache`
- [ ] Optimiser l'autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Compiler les routes: `php artisan route:cache`
- [ ] Compiler les views: `php artisan view:cache`
- [ ] Mettre en place Redis pour le cache

### 📦 Base de Données
- [ ] Exécuter les migrations: `php artisan migrate --force`
- [ ] Créer les indexes: `php artisan db:seed` (si nécessaire)
- [ ] Configurer les backups automatiques

### 📧 Communications
- [ ] Configurer SendGrid pour les emails
- [ ] Configurer Twilio pour les SMS (optionnel)
- [ ] Tester les notifications

### 🚀 Déploiement
- [ ] Fichiers `.env` sécurisés (pas de contrôle de version)
- [ ] Directives Apache/Nginx configurées
- [ ] Permissions des dossiers correctes
- [ ] CDN configuré pour les images
- [ ] Backups automatiques activés

## Commandes Pré-Production

```bash
# Compil et optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev

# Base de données
php artisan migrate --force

# Stocker les fichiers env de façon sécurisée
chmod 600 .env
```

## URLs Importantes
- Prod: https://qct.ci
- Admin: https://qct.ci/admin/dashboard
- Support Email: support@qct.ci

