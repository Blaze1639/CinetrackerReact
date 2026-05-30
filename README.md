# CineTrack

Application web de suivi personnel de films et séries. Chaque utilisateur gère son catalogue, sa liste "à voir", ses favoris et consulte son tableau de bord annuel.

---

## Architecture logicielle

### Vue d'ensemble

CineTrack est une application **monorepo** organisée en deux services indépendants communiquant via une API REST JSON sécurisée par JWT.

```
┌─────────────────────────────────────────────────────────────────────┐
│                          Navigateur Web                             │
│                                                                     │
│   ┌─────────────────────────────────────────────────────────────┐   │
│   │               React 18 + Vite (SPA)                        │   │
│   │                                                             │   │
│   │   Routes         Context           Services                 │   │
│   │  /accueil  ───  AuthContext  ───  api.js (useApi)          │   │
│   │  /index          JWT Token         fetch + Bearer Token     │   │
│   │  /profile        localStorage                              │   │
│   └─────────────────────┬───────────────────────────────────────┘   │
└─────────────────────────┼───────────────────────────────────────────┘
                          │  HTTPS  /api/*
                          │  Authorization: Bearer <JWT RS256>
                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    Symfony 7.3 (PHP 8.2+)                           │
│                                                                     │
│   Nginx  ──►  PHP-FPM  ──►  Kernel  ──►  Router                   │
│                                              │                      │
│                              ┌───────────────┼──────────────────┐   │
│                              │               │                  │   │
│                         AuthController  MediaController  ...    │   │
│                              │               │                  │   │
│                         LexikJWT         Doctrine ORM           │   │
│                         (RS256/4096)          │                 │   │
│                                          Entities / Repos        │   │
└──────────────────────────────┬──────────────────────────────────────┘
                               │  PDO / MySQL 8.0
                               ▼
                    ┌──────────────────────┐
                    │      MySQL 8.0       │
                    │   media_tracker      │
                    │                      │
                    │  users               │
                    │  media               │
                    │  media_to_watch      │
                    │  notifications       │
                    │  actualite           │
                    └──────────────────────┘
```

### Séparation des responsabilités

| Couche          | Technologie          | Rôle                                               |
|-----------------|----------------------|----------------------------------------------------|
| Présentation    | React 18 + JSX       | Rendu, navigation, état UI                         |
| État global     | React Context (Auth) | Session JWT, apiFetch centralisé                   |
| Routage SPA     | React Router v6      | Guards `PrivateRoute` / `PublicRoute`              |
| Client HTTP     | Fetch API + useApi   | Appels REST avec Bearer token automatique          |
| API REST        | Symfony Controllers  | Validation, logique métier, sérialisation JSON     |
| Sécurité        | LexikJWT (RS256)     | Émission et vérification des tokens               |
| ORM             | Doctrine ORM 3       | Mapping objet-relationnel, requêtes DQL/SQL natif  |
| Base de données | MySQL 8.0            | Persistance de toutes les données                  |

---

## Modèle de domaine

```
┌──────────────────┐    1──*    ┌──────────────────────┐
│      users       │───────────►│        media         │
│──────────────────│           │──────────────────────│
│ id               │           │ id                   │
│ username         │           │ title                │
│ email (unique)   │           │ type_media           │
│ password (bcrypt)│           │ rating (1-5)         │
│ role             │           │ image_url            │
│ created_at       │           │ favorite             │
└──────────────────┘           │ view_count           │
        │                      │ commentaire          │
        │                      │ user_id (FK)         │
        │                      └──────────────────────┘
        │
        │  1──*   ┌──────────────────────┐
        ├────────►│    media_to_watch    │
        │         │──────────────────────│
        │         │ id                  │
        │         │ title               │
        │         │ type_media          │
        │         │ image_url           │
        │         │ user_id (FK)        │
        │         │ added_date          │
        │         └──────────────────────┘
        │
        │  1──*   ┌──────────────────────┐
        ├────────►│    notifications     │
        │         │──────────────────────│
        │         │ id                  │
        │         │ user_id (FK)        │
        │         │ type_message        │
        │         │ message             │
        │         │ status (non_lu|lu)  │
        │         │ created_at          │
        │         └──────────────────────┘
        │
        │  1──*   ┌──────────────────────┐
        └────────►│      actualite       │
                  │──────────────────────│
                  │ id                  │
                  │ titre               │
                  │ contenu             │
                  │ user_id (FK admin)  │
                  │ created_at          │
                  └──────────────────────┘
```

---

## Structure du monorepo

```
CinetrackerReact/
│
├── frontend/                        # Application React + Vite
│   ├── src/
│   │   ├── App.jsx                  # Routeur principal + guards auth
│   │   ├── context/AuthContext.jsx  # État global JWT (localStorage)
│   │   ├── services/api.js          # Client API centralisé (useApi)
│   │   ├── pages/                   # 13 pages (accueil, médias, watchlist...)
│   │   ├── components/              # Navbar, Footer, StarRating, TmdbSearch
│   │   └── utils/helpers.js         # Fonctions utilitaires
│   ├── Dockerfile                   # Image de développement
│   ├── Dockerfile.prod              # Image de production (Nginx multi-stage)
│   ├── package.json
│   └── README.md                    # ► Documentation frontend complète
│
├── backend/                         # API Symfony 7.3
│   ├── src/
│   │   ├── Controller/              # 7 controllers REST
│   │   │   ├── AuthController.php   # login, register, me, logout
│   │   │   ├── MediaController.php  # CRUD + favorite + increment
│   │   │   ├── WatchlistController.php
│   │   │   ├── AccueilController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── ActualiteController.php
│   │   │   └── ProfileController.php
│   │   ├── Entity/                  # 5 entités Doctrine
│   │   └── Repository/              # 5 repositories
│   ├── tests/
│   │   ├── Entity/                  # 4 fichiers — tests unitaires entités
│   │   └── Controller/              # 2 fichiers — tests logique métier
│   ├── config/jwt/                  # Clés RSA générées au build Docker
│   ├── Dockerfile
│   ├── Dockerfile.prod
│   ├── phpunit.xml.dist             # Configuration PHPUnit 11
│   ├── composer.json
│   └── README.md                    # ► Documentation backend complète
│
├── docker/
│   └── mysql/init.sql               # Schéma SQL initial + compte admin
│
├── .github/
│   └── workflows/ci-cd.yml          # Pipeline CI/CD GitHub Actions
│
└── docker-compose.yml               # Orchestration locale (3 services)
```

---

## Flux d'authentification JWT

```
  React                        Symfony                    MySQL
    │                             │                          │
    │  POST /api/auth/login       │                          │
    │  { email, motdepasse }      │                          │
    │────────────────────────────►│                          │
    │                             │  SELECT FROM users       │
    │                             │  WHERE email = ?         │
    │                             │─────────────────────────►│
    │                             │◄─── User entity ─────────│
    │                             │                          │
    │                             │  bcrypt.verify(plain, hash)
    │                             │  JWT.sign(user) RS256/4096
    │                             │                          │
    │◄── { token, user_id } ──────│                          │
    │                             │                          │
    │  localStorage.setItem(token)│                          │
    │                             │                          │
    │  GET /api/media             │                          │
    │  Authorization: Bearer ...  │                          │
    │────────────────────────────►│                          │
    │                             │  JWT.verify(token) ✓     │
    │                             │  SELECT FROM media       │
    │                             │  WHERE user_id = sub     │
    │                             │─────────────────────────►│
    │                             │◄─── rows ────────────────│
    │◄── { media: [...] } ────────│                          │
```

---

## Démarrage rapide

### Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et lancé

### Lancement complet

```bash
git clone <url-du-repo>
cd CinetrackerReact
docker-compose up --build
```

| Service  | URL                   | Description                 |
|----------|-----------------------|-----------------------------|
| Frontend | http://localhost:5173 | Application React (Nginx)   |
| Backend  | http://localhost:8080 | API Symfony (PHP-FPM+Nginx) |
| MySQL    | localhost:3306        | Base de données             |

La base de données est initialisée automatiquement depuis `docker/mysql/init.sql`.

### Développement local (sans Docker)

```bash
# Backend
cd backend
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start   # http://localhost:8080

# Frontend
cd frontend
npm install
echo "VITE_API_URL=http://localhost:8080" > .env
npm run dev            # http://localhost:5173
```

---

## Pipeline CI/CD

Déclenché sur chaque push/PR vers `main` ou `DEV` :

```
push → main/DEV
    │
    ├── lint ──────────────────────── npm run build (syntaxe JSX)
    │
    ├── frontend-tests (needs lint)
    │     ├── npm test --coverage     (Jest + Testing Library)
    │     └── npm run build
    │
    ├── backend-check (needs lint)
    │     └── php -l                  (syntaxe PHP)
    │
    └── docker-build (needs frontend + backend)
          ├── Build image frontend    (Nginx multi-stage)
          └── Build image backend     (PHP-FPM + Nginx + Supervisor)
```

---

## API REST — Toutes les routes

| Méthode  | Route                            | Auth        | Description                     |
|----------|----------------------------------|-------------|---------------------------------|
| `POST`   | `/api/auth/login`                | Non         | Connexion → retourne un JWT     |
| `POST`   | `/api/auth/register`             | Non         | Inscription → retourne un JWT   |
| `GET`    | `/api/auth/me`                   | JWT         | Utilisateur courant             |
| `GET`    | `/api/auth/logout`               | Non         | Déconnexion (client-side)       |
| `GET`    | `/api/media`                     | JWT         | Liste paginée + filtres         |
| `POST`   | `/api/media`                     | JWT         | Ajouter un film/série           |
| `PUT`    | `/api/media/{id}`                | JWT         | Modifier                        |
| `DELETE` | `/api/media/{id}`                | JWT         | Supprimer                       |
| `POST`   | `/api/media/{id}/favorite`       | JWT         | Toggle favori                   |
| `POST`   | `/api/media/{id}/increment`      | JWT         | Incrémenter le compteur de vues |
| `GET`    | `/api/watchlist`                 | JWT         | Liste "à voir"                  |
| `POST`   | `/api/watchlist`                 | JWT         | Ajouter à la watchlist          |
| `PUT`    | `/api/watchlist/{id}`            | JWT         | Modifier                        |
| `DELETE` | `/api/watchlist/{id}`            | JWT         | Supprimer                       |
| `POST`   | `/api/watchlist/{id}/move`       | JWT         | Déplacer vers le catalogue      |
| `GET`    | `/api/accueil`                   | JWT         | Stats + leaderboard + actualités|
| `GET`    | `/api/profile`                   | JWT         | Profil + statistiques           |
| `POST`   | `/api/delete`                    | JWT         | Supprimer le compte             |
| `GET`    | `/api/notifications`             | JWT + admin | Liste des notifications         |
| `POST`   | `/api/notifications`             | JWT         | Envoyer un message à l'admin    |
| `GET`    | `/api/notifications/{id}/read`   | JWT + admin | Marquer comme lu                |
| `DELETE` | `/api/notifications/{id}`        | JWT + admin | Supprimer                       |
| `POST`   | `/api/actualites`                | JWT + admin | Publier une actualité           |
| `DELETE` | `/api/actualites/{id}`           | JWT + admin | Supprimer une actualité         |

---

## Variables d'environnement

### Backend (`backend/.env`)

| Variable          | Description                        | Valeur par défaut (docker-compose)             |
|-------------------|------------------------------------|------------------------------------------------|
| `APP_ENV`         | Environnement Symfony              | `prod`                                         |
| `APP_SECRET`      | Clé secrète Symfony (32 chars+)    | À changer en production                        |
| `DATABASE_URL`    | DSN MySQL                          | `mysql://cinetrack:cinetrack@db:3306/media_tracker` |
| `JWT_SECRET_KEY`  | Chemin clé privée RSA              | `%kernel.project_dir%/config/jwt/private.pem`  |
| `JWT_PUBLIC_KEY`  | Chemin clé publique RSA            | `%kernel.project_dir%/config/jwt/public.pem`   |
| `JWT_PASSPHRASE`  | Passphrase RSA                     | `""` (vide)                                    |
| `FRONTEND_URL`    | URL du frontend (CORS)             | `http://localhost:5173`                        |

### Frontend (`frontend/.env`)

| Variable        | Description                    |
|-----------------|--------------------------------|
| `VITE_API_URL`  | URL du backend Symfony         |
| `VITE_TMDB_KEY` | Clé API TMDB (auto-complétion) |

---

## Compte admin par défaut

Créé par `docker/mysql/init.sql` :

- **Email** : axel77dion@gmail.com
- **Rôle** : `admin`

---

## Décisions d'architecture

**Pourquoi Symfony (migration depuis PHP natif) ?**
L'ancien backend était du PHP natif avec des scripts isolés dans `api/`. Symfony 7.3 apporte sécurité (UserInterface, PasswordHasher), maintenabilité (DI, ORM) et testabilité (PHPUnit intégré).

**Pourquoi JWT sans session serveur ?**
Le frontend et le backend peuvent être déployés sur des domaines différents (Render, Vercel). JWT permet une authentification stateless sans partage de session entre services.

**Pourquoi React Context plutôt que Redux ?**
L'état partagé se réduit à la session utilisateur (connecté/déconnecté + rôle). Un simple `Context + useState` est suffisant et évite la complexité Redux.

---

## Documentation détaillée

| Composant | Lien |
|-----------|------|
| Backend   | [backend/README.md](./backend/README.md) — API complète, tests PHPUnit, configuration Symfony |
| Frontend  | [frontend/README.md](./frontend/README.md) — Pages, composants, hooks, tests Jest |

---

## Déploiement sur Render

### Base de données
1. Créer un service **MySQL** (ou PlanetScale, Aiven...)
2. Importer `docker/mysql/init.sql`

### Backend Symfony
1. **Web Service** — contexte `backend/`, Dockerfile : `backend/Dockerfile.prod`
2. Variables d'environnement : `APP_ENV`, `APP_SECRET`, `DATABASE_URL`, `JWT_PASSPHRASE`, `FRONTEND_URL`

### Frontend React
1. **Web Service** — contexte `frontend/`, Dockerfile : `frontend/Dockerfile.prod`
2. **Build variables** (⚠️ nécessaires au moment du build Vite, pas seulement au runtime) :
   - `VITE_API_URL=https://<backend>.onrender.com`
   - `VITE_TMDB_KEY=<clé TMDB>`

---

## Licence

Propriétaire — tous droits réservés.
