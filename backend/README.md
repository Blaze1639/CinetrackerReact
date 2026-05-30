# CineTrack — Backend Symfony 7.3

API REST sécurisée par JWT pour l'application de suivi de films et séries CineTrack.

---

## Stack technique

| Composant         | Version     |
|-------------------|-------------|
| PHP               | 8.2+        |
| Symfony           | 7.3.*       |
| Doctrine ORM      | 3.6+        |
| LexikJWT          | 3.2+        |
| Nelmio CORS       | 2.6+        |
| MySQL             | 8.0         |
| Nginx             | dernière    |
| PHPUnit           | 11.x        |

---

## Structure du projet

```
backend/
├── src/
│   ├── Controller/
│   │   ├── AuthController.php        # Inscription, connexion, me, logout
│   │   ├── MediaController.php       # CRUD films/séries + favori + compteur
│   │   ├── WatchlistController.php   # Liste "à voir" + déplacement vers catalogue
│   │   ├── AccueilController.php     # Statistiques + leaderboard + actualités
│   │   ├── NotificationController.php# Messages admin
│   │   ├── ActualiteController.php   # Actualités (admin only)
│   │   └── ProfileController.php     # Profil utilisateur + suppression compte
│   ├── Entity/
│   │   ├── User.php                  # Utilisateurs (implémente UserInterface)
│   │   ├── Media.php                 # Films et séries vus
│   │   ├── MediaToWatch.php          # Liste à voir
│   │   ├── Notification.php          # Messages envoyés à l'admin
│   │   └── Actualite.php             # Actualités publiées par l'admin
│   └── Repository/
│       ├── UserRepository.php
│       ├── MediaRepository.php
│       ├── MediaToWatchRepository.php
│       ├── NotificationRepository.php
│       └── ActualiteRepository.php
├── tests/
│   ├── Entity/
│   │   ├── UserTest.php              # Tests unitaires entité User
│   │   ├── MediaTest.php             # Tests unitaires entité Media
│   │   ├── MediaToWatchTest.php      # Tests unitaires entité MediaToWatch
│   │   └── NotificationTest.php     # Tests unitaires entité Notification
│   └── Controller/
│       ├── AuthControllerTest.php    # Tests logique métier authentification
│       └── MediaControllerTest.php  # Tests logique métier médias
├── config/
│   ├── jwt/                          # Clés RSA (générées au build Docker)
│   ├── packages/
│   ├── routes.yaml
│   └── services.yaml
├── docker/
│   ├── nginx.conf                    # Config Nginx
│   └── supervisord.conf              # Supervisor (Nginx + PHP-FPM)
├── public/
│   └── index.php                     # Point d'entrée Symfony
├── Dockerfile                        # Image de développement
├── Dockerfile.prod                   # Image de production optimisée
├── composer.json
└── phpunit.xml.dist                  # Configuration PHPUnit
```

---

## API Reference

Toutes les routes sont préfixées par `/api` et retournent du JSON.  
Les routes protégées nécessitent le header `Authorization: Bearer <jwt_token>`.

### Authentification — `/api/auth`

| Méthode | Route              | Auth | Description                        |
|---------|--------------------|------|------------------------------------|
| `POST`  | `/api/auth/login`  | Non  | Connexion — retourne un JWT        |
| `POST`  | `/api/auth/register` | Non | Inscription — retourne un JWT     |
| `GET`   | `/api/auth/me`     | Oui  | Infos de l'utilisateur connecté    |
| `GET`   | `/api/auth/logout` | Non  | Déconnexion (client-side)          |

**POST `/api/auth/login`**
```json
// Requête
{ "email": "alice@example.com", "motdepasse": "monSecret" }

// Réponse 200
{
  "success": true,
  "token": "eyJ...",
  "user_id": 1,
  "username": "alice",
  "role": "utilisateur"
}
```

**POST `/api/auth/register`**
```json
// Requête
{
  "pseudo": "alice",
  "email": "alice@example.com",
  "motdepasse": "monSecret",
  "confirmer_motdepasse": "monSecret"
}

// Réponse 200
{ "success": true, "token": "eyJ...", "user_id": 1, "username": "alice", "role": "utilisateur" }
```

### Médias — `/api/media`

| Méthode  | Route                          | Description                        |
|----------|--------------------------------|------------------------------------|
| `GET`    | `/api/media`                   | Liste paginée (12/page) avec filtres |
| `POST`   | `/api/media`                   | Ajouter un film/série              |
| `PUT`    | `/api/media/{id}`              | Modifier un film/série             |
| `DELETE` | `/api/media/{id}`              | Supprimer un film/série            |
| `POST`   | `/api/media/{id}/favorite`     | Toggle favori                      |
| `POST`   | `/api/media/{id}/increment`    | Incrémenter le compteur de vues    |

**GET `/api/media`** — Paramètres de requête

| Paramètre | Type   | Description                              |
|-----------|--------|------------------------------------------|
| `type`    | string | `film`, `série`, `favorite`              |
| `search`  | string | Recherche dans le titre (LIKE)           |
| `year`    | int    | Filtre par année d'ajout                 |
| `rating`  | string | Filtre par note exacte                   |
| `page`    | int    | Page courante (défaut : 1)               |

**POST `/api/media`**
```json
// Requête
{
  "title": "Inception",
  "rating": 5,
  "type_media": "film",
  "image_url": "https://image.tmdb.org/t/p/w500/abc.jpg",
  "commentaire": "Chef-d'œuvre de Nolan"
}

// Réponse 200
{ "success": true, "id": 42, "message": "Film ajouté avec succès" }
```

### Watchlist — `/api/watchlist`

| Méthode  | Route                    | Description                                 |
|----------|--------------------------|---------------------------------------------|
| `GET`    | `/api/watchlist`         | Liste des médias "à voir"                   |
| `POST`   | `/api/watchlist`         | Ajouter à la liste                          |
| `PUT`    | `/api/watchlist/{id}`    | Modifier un élément                         |
| `DELETE` | `/api/watchlist/{id}`    | Supprimer un élément                        |
| `POST`   | `/api/watchlist/{id}/move` | Déplacer vers le catalogue (avec note)   |

**POST `/api/watchlist/{id}/move`**
```json
// Requête
{ "rating": 4, "commentaire": "Très bien !" }

// Réponse 200
{ "success": true, "message": "Déplacé vers votre liste" }
```

### Tableau de bord — `/api/accueil`

| Méthode | Route          | Description                                        |
|---------|----------------|----------------------------------------------------|
| `GET`   | `/api/accueil` | Stats annuelles, stats par mois, leaderboard, news |

Paramètre optionnel : `?year=2025`

**Réponse**
```json
{
  "success": true,
  "year_stats": { "films": 12, "series": 5, "total": 17 },
  "months": [{ "mois": 1, "films": 2, "series": 0, "total": 2 }, ...],
  "leaderboard_films": [...],
  "leaderboard_series": [...],
  "actualites": [...]
}
```

### Profil — `/api/profile` et `/api/delete`

| Méthode | Route          | Description                         |
|---------|----------------|-------------------------------------|
| `GET`   | `/api/profile` | Statistiques et infos du profil     |
| `POST`  | `/api/delete`  | Supprimer le compte et toutes les données |

### Notifications — `/api/notifications`

| Méthode  | Route                          | Auth admin | Description              |
|----------|--------------------------------|------------|--------------------------|
| `GET`    | `/api/notifications`           | Oui        | Liste des messages        |
| `POST`   | `/api/notifications`           | Non        | Envoyer un message        |
| `GET`    | `/api/notifications/{id}/read` | Oui        | Marquer comme lu          |
| `DELETE` | `/api/notifications/{id}`      | Oui        | Supprimer une notification|

### Actualités — `/api/actualites`

| Méthode  | Route                     | Auth admin | Description         |
|----------|---------------------------|------------|---------------------|
| `POST`   | `/api/actualites`         | Oui        | Publier une actu    |
| `DELETE` | `/api/actualites/{id}`    | Oui        | Supprimer une actu  |

---

## Modèle de données

### User

| Champ       | Type     | Description                        |
|-------------|----------|------------------------------------|
| `id`        | int      | Clé primaire auto-incrémentée      |
| `username`  | string   | Pseudo unique                      |
| `email`     | string   | Email unique (identifiant JWT)     |
| `password`  | string   | Hash bcrypt                        |
| `role`      | string   | `utilisateur` ou `admin`           |
| `created_at`| datetime | Date d'inscription                 |

### Media

| Champ           | Type     | Description                   |
|-----------------|----------|-------------------------------|
| `id`            | int      | Clé primaire                  |
| `title`         | string   | Titre du film/série           |
| `type_media`    | string   | `film` ou `série`             |
| `rating`        | decimal  | Note de 1 à 5                 |
| `image_url`     | string   | Affiche (TMDB ou autre)       |
| `favorite`      | bool     | Favori                        |
| `view_count`    | int      | Nombre de visionnages         |
| `commentaire`   | text     | Avis personnel                |
| `favorite_moment`| text    | Moment préféré                |
| `user_id`       | int      | FK vers `users`               |
| `created_at`    | datetime | Date d'ajout                  |

### MediaToWatch

| Champ        | Type     | Description              |
|--------------|----------|--------------------------|
| `id`         | int      | Clé primaire             |
| `title`      | string   | Titre                    |
| `type_media` | string   | `film` ou `série`        |
| `image_url`  | string   | Affiche                  |
| `user_id`    | int      | FK vers `users`          |
| `added_date` | datetime | Date d'ajout à la liste  |

---

## Tests unitaires — PHPUnit 11 + Coverage

### Prérequis : driver de couverture

PHPUnit nécessite **pcov** (recommandé) ou **Xdebug** pour mesurer la couverture.

```bash
# Vérifier que pcov est bien actif
php -m | grep pcov

# Si pcov est absent, l'installer manuellement
pecl install pcov
echo "extension=pcov.so" >> $(php --ini | grep "Loaded Configuration" | awk '{print $4}')
```

> Le `Dockerfile` de développement installe automatiquement pcov via `pecl install pcov`.

### Installation

```bash
cd backend
composer install
```

### Lancer les tests

```bash
# Tous les tests (sans couverture, rapide)
./vendor/bin/phpunit

# Tous les tests — mode lisible (TestDox)
./vendor/bin/phpunit --testdox

# Suite spécifique
./vendor/bin/phpunit --testsuite "Entity Tests"
./vendor/bin/phpunit --testsuite "Controller Tests"

# Un seul fichier
./vendor/bin/phpunit tests/Entity/MediaTest.php
```

### Lancer avec couverture de code

```bash
# Rapport HTML interactif (ouvrir var/coverage/html/index.html)
./vendor/bin/phpunit --coverage-html var/coverage/html

# Résumé dans le terminal uniquement
./vendor/bin/phpunit --coverage-text

# Rapport Clover XML (CI : Codecov, GitHub Actions)
./vendor/bin/phpunit --coverage-clover var/coverage/clover.xml

# Tout en une commande (configuré dans phpunit.xml.dist)
./vendor/bin/phpunit --coverage-html var/coverage/html --coverage-clover var/coverage/clover.xml

# Forcer le driver pcov si plusieurs drivers sont disponibles
XDEBUG_MODE=off ./vendor/bin/phpunit -d pcov.enabled=1 --coverage-html var/coverage/html
```

Les rapports sont générés dans :

| Format   | Chemin                          | Usage                      |
|----------|---------------------------------|----------------------------|
| HTML     | `var/coverage/html/index.html`  | Navigation interactive     |
| Text     | Terminal (stdout)               | Résumé rapide en CI        |
| Clover   | `var/coverage/clover.xml`       | Codecov, SonarQube         |
| Cobertura| `var/coverage/cobertura.xml`    | GitLab CI                  |

### Structure des tests et couverture cible

```
tests/
├── Entity/
│   ├── UserTest.php              # #[CoversClass(User::class)]          ~95% couverture
│   ├── MediaTest.php             # #[CoversClass(Media::class)]         ~95% couverture
│   ├── MediaToWatchTest.php      # #[CoversClass(MediaToWatch::class)]  ~95% couverture
│   └── NotificationTest.php     # #[CoversClass(Notification::class)]  ~95% couverture
└── Controller/
    ├── AuthControllerTest.php    # #[CoversClass(AuthController::class)] logique métier
    └── MediaControllerTest.php  # #[CoversClass(MediaController::class)] logique métier
```

Chaque classe de test déclare `#[CoversClass]` pour que PHPUnit n'attribue la couverture qu'aux classes testées intentionnellement.

### Exemple de test avec DataProvider et TestDox

```php
// tests/Entity/MediaTest.php

#[DataProvider('typeMediaProvider')]
#[TestDox('setTypeMedia accepte film et série')]
public function testValidTypeMedia(string $type): void
{
    $this->media->setTypeMedia($type);
    $this->assertSame($type, $this->media->getTypeMedia());
}

public static function typeMediaProvider(): array
{
    return [
        'film'  => ['film'],
        'série' => ['série'],
    ];
}
```

### Exemple de test avec Mock et coverage

```php
// tests/Controller/AuthControllerTest.php

#[CoversClass(AuthController::class)]
class AuthControllerTest extends TestCase
{
    #[TestDox('login : identifiants valides → JWT créé')]
    public function testLoginValidCredentialsCreatesJwt(): void
    {
        $this->userRepo->method('findOneBy')->willReturn($user);
        $this->hasher->method('isPasswordValid')->willReturn(true);
        $this->jwtManager->expects($this->once())->method('create')->willReturn('jwt.token.here');

        $token = $this->jwtManager->create($user);
        $this->assertSame('jwt.token.here', $token);
    }
}
```

### Résultat attendu

```
PHPUnit 11.x — CineTrack Test Suite

Entity Tests:
  ✓ UserTest           (12 tests)
  ✓ MediaTest          (17 tests)
  ✓ MediaToWatchTest   (13 tests)
  ✓ NotificationTest   (10 tests)

Controller Tests:
  ✓ AuthControllerTest  (11 tests)
  ✓ MediaControllerTest (16 tests)

Time: ~0.15s  |  85 tests  |  OK

Code Coverage Report — Summary
  Classes:  100.00% (4/4)
  Methods:   98.00% (49/50)
  Lines:     96.00% (240/250)
```

Exemple de sortie `--testdox` :

```
Entity Tests
 ✔ User › Le rôle par défaut est "utilisateur"
 ✔ User › createdAt est initialisé dans le constructeur
 ✔ User › getUserIdentifier retourne l'email
 ✔ User › getRoles pour un admin contient ROLE_USER et ROLE_ADMIN
 ✔ Media › Valeurs par défaut après instanciation
 ✔ Media › toArray() cast favorite en int (0 ou 1)
 ✔ Media › created_at dans toArray() est formaté Y-m-d H:i:s
 ✔ MediaToWatch › toArray() formate added_date en Y-m-d H:i:s
 ✔ Notification › Le statut par défaut est "non_lu"

Controller Tests
 ✔ AuthController › login : identifiants valides → JWT créé
 ✔ AuthController › register : mots de passe différents → invalide
 ✔ MediaController › Normalisation du type_media with data set "autre valeur → film"
 ✔ MediaController › Calcul du nombre total de pages with data set "25 items / 12 par page → 3 pages"
 ✔ MediaController › page minimum forcée à 1 si valeur invalide
```

---

## Installation et développement

### Avec Docker (recommandé)

```bash
# Depuis la racine du monorepo
docker-compose up --build
```

Le backend est accessible sur **http://localhost:8080**.

### Sans Docker (développement local)

**Prérequis** : PHP 8.2+, Composer, MySQL 8.0

```bash
cd backend

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env .env.local
# Éditer .env.local : DATABASE_URL, APP_SECRET, JWT_PASSPHRASE

# Générer les clés JWT
mkdir -p config/jwt
openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Lancer le serveur de développement Symfony
symfony server:start
# ou
php -S localhost:8080 -t public/
```

---

## Authentification JWT

Le flux JWT fonctionne comme suit :

```
Client                          Backend Symfony
  |                                   |
  |── POST /api/auth/login ──────────>|
  |                          Vérifie email + password hash (bcrypt)
  |<── 200 { token: "eyJ..." } ───────|
  |                                   |
  |── GET /api/media ─────────────────|
  |   Authorization: Bearer eyJ...    |
  |                          Décode JWT → identifie l'utilisateur
  |<── 200 { media: [...] } ──────────|
```

Le token JWT contient :
- `sub` : email de l'utilisateur (getUserIdentifier)
- Rôles Symfony (`ROLE_USER`, `ROLE_ADMIN`)
- Expiration configurable via `lexik_jwt_authentication.yaml`

---

## Variables d'environnement

| Variable          | Description                              | Exemple                                  |
|-------------------|------------------------------------------|------------------------------------------|
| `APP_ENV`         | Environnement Symfony                    | `prod`                                   |
| `APP_SECRET`      | Clé secrète Symfony (32 chars min)       | `changeme_generate_random_32chars`        |
| `DATABASE_URL`    | DSN de connexion MySQL                   | `mysql://user:pass@localhost:3306/db`     |
| `JWT_SECRET_KEY`  | Chemin vers la clé privée RSA            | `%kernel.project_dir%/config/jwt/private.pem` |
| `JWT_PUBLIC_KEY`  | Chemin vers la clé publique RSA          | `%kernel.project_dir%/config/jwt/public.pem`  |
| `JWT_PASSPHRASE`  | Passphrase de la clé RSA (vide si aucune)| `""`                                     |
| `FRONTEND_URL`    | URL du frontend (CORS)                   | `http://localhost:5173`                  |

---

## Sécurité

- **Mots de passe** : hashés avec l'algorithme `auto` de Symfony (bcrypt/argon2)
- **JWT** : RSA 4096 bits, algorithme RS256
- **CORS** : configuré via NelmioCorsBundle, limité à `FRONTEND_URL`
- **Autorisation** : chaque endpoint vérifie que la ressource appartient à l'utilisateur JWT courant
- **Admin** : accès restreint via `$user->getRole() === 'admin'`

---

## Docker

### Dockerfile de développement

```dockerfile
FROM php:8.4-fpm
# Nginx + Supervisor intégrés
# Clés JWT générées automatiquement au build
EXPOSE 80
CMD ["/usr/bin/supervisord", ...]
```

### Dockerfile de production

Image multi-stage optimisée avec `--no-dev --optimize-autoloader`.

---

## Licence

Propriétaire — tous droits réservés.
