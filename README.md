# CineTrack — React + Symfony + MySQL + Docker

Application de suivi de films et séries avec un frontend React/Vite et un backend Symfony 7 exposant une API REST sécurisée par JWT.

---

## Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et lancé

---

## Lancement en local (Docker)

```bash
# 1. Cloner le projet
git clone <url-du-repo>
cd CinetrackerReact

# 2. Lancer tous les services
docker-compose up --build
```

| Service  | URL locale              |
|----------|-------------------------|
| Frontend | http://localhost:5173   |
| Backend  | http://localhost:8080   |
| MySQL    | localhost:3306          |

La base de données est initialisée automatiquement depuis `docker/mysql/init.sql`.

---

## Architecture

```
CinetrackerReact/
├── backend/                    ← API Symfony 7 (PHP-FPM + Nginx)
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── AuthController.php          POST /api/auth/login|register, GET /api/auth/me|logout
│   │   │   ├── MediaController.php         CRUD /api/media, /api/media/{id}/favorite|increment
│   │   │   ├── WatchlistController.php     CRUD /api/watchlist, /api/watchlist/{id}/move
│   │   │   ├── AccueilController.php       GET /api/accueil
│   │   │   ├── NotificationController.php  /api/notifications
│   │   │   ├── ActualiteController.php     /api/actualites
│   │   │   └── ProfileController.php       GET /api/profile, POST /api/delete
│   │   └── Entity/
│   │       ├── User.php
│   │       ├── Media.php
│   │       ├── MediaToWatch.php
│   │       ├── Actualite.php
│   │       └── Notification.php
│   ├── config/
│   │   ├── jwt/                ← Clés RSA pour JWT (générées, ne pas committer private.pem)
│   │   └── packages/
│   │       ├── security.yaml   ← Auth JWT (routes publiques : login + register)
│   │       ├── nelmio_cors.yaml
│   │       └── doctrine.yaml
│   ├── docker/
│   │   ├── nginx.conf
│   │   └── supervisord.conf
│   ├── Dockerfile              ← Build local (PHP-FPM + Nginx + Supervisor)
│   └── Dockerfile.prod         ← Build prod (multi-stage)
├── frontend/                   ← React 18 + Vite
│   └── src/
│       ├── context/AuthContext.jsx   ← Gestion du JWT (localStorage)
│       └── services/api.js           ← Tous les appels API centralisés
├── docker/
│   └── mysql/init.sql          ← Schéma + données initiales
└── docker-compose.yml
```

---

## API REST — Routes principales

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| POST | `/api/auth/login` | Non | Connexion → retourne un JWT |
| POST | `/api/auth/register` | Non | Inscription → retourne un JWT |
| GET | `/api/auth/me` | JWT | Utilisateur courant |
| GET | `/api/auth/logout` | JWT | Déconnexion |
| GET | `/api/media` | JWT | Liste des médias (filtres : type, search, year, rating, page) |
| POST | `/api/media` | JWT | Ajouter un média |
| PUT | `/api/media/{id}` | JWT | Modifier un média |
| DELETE | `/api/media/{id}` | JWT | Supprimer un média |
| POST | `/api/media/{id}/favorite` | JWT | Basculer favori |
| POST | `/api/media/{id}/increment` | JWT | Incrémenter le compteur de vues |
| GET | `/api/watchlist` | JWT | Liste à voir |
| POST | `/api/watchlist` | JWT | Ajouter à la watchlist |
| PUT | `/api/watchlist/{id}` | JWT | Modifier un item watchlist |
| DELETE | `/api/watchlist/{id}` | JWT | Supprimer de la watchlist |
| POST | `/api/watchlist/{id}/move` | JWT | Déplacer vers la liste vue |
| GET | `/api/accueil` | JWT | Stats dashboard + leaderboard + actus |
| GET | `/api/profile` | JWT | Profil + statistiques utilisateur |
| POST | `/api/delete` | JWT | Supprimer le compte |
| GET | `/api/notifications` | JWT + admin | Liste des notifications |
| GET | `/api/notifications/{id}/read` | JWT + admin | Marquer comme lu |
| DELETE | `/api/notifications/{id}` | JWT + admin | Supprimer une notification |
| POST | `/api/notifications` | JWT | Envoyer un message à l'admin |
| POST | `/api/actualites` | JWT + admin | Publier une actualité |
| DELETE | `/api/actualites/{id}` | JWT + admin | Supprimer une actualité |

Toutes les requêtes protégées nécessitent le header : `Authorization: Bearer <token>`

---

## Variables d'environnement

### Backend (`backend/.env`)

| Variable | Description | Valeur par défaut |
|----------|-------------|-------------------|
| `APP_ENV` | Environnement Symfony | `prod` |
| `APP_SECRET` | Clé secrète Symfony | À changer en prod |
| `DATABASE_URL` | URL de connexion MySQL | `mysql://cinetrack:cinetrack@db:3306/media_tracker` |
| `JWT_SECRET_KEY` | Chemin clé privée RSA | `%kernel.project_dir%/config/jwt/private.pem` |
| `JWT_PUBLIC_KEY` | Chemin clé publique RSA | `%kernel.project_dir%/config/jwt/public.pem` |
| `JWT_PASSPHRASE` | Passphrase clé JWT | Vide par défaut |
| `FRONTEND_URL` | URL du frontend (CORS) | `https://cinetrack-frontend.onrender.com` |

### Frontend (`frontend/.env`)

| Variable | Description |
|----------|-------------|
| `VITE_API_URL` | URL de l'API backend |
| `VITE_TMDB_KEY` | Clé API TMDB (recherche de films) |

---

## Authentification

L'authentification utilise des **tokens JWT** (JSON Web Token) :

1. L'utilisateur se connecte via `POST /api/auth/login` avec `{email, motdepasse}`
2. Le backend retourne un token JWT valable **2 heures**
3. Le frontend stocke le token dans `localStorage` et l'envoie dans chaque requête : `Authorization: Bearer <token>`
4. À la déconnexion, le token est supprimé du `localStorage`

---

## Compte admin par défaut

Créé par `docker/mysql/init.sql` :

- **Email** : axel77dion@gmail.com
- **Rôle** : admin

---

## Déploiement sur Render

### Base de données
1. Créer un service **MySQL** (ou utiliser PlanetScale, Aiven, etc.)
2. Importer `docker/mysql/init.sql`

### Backend Symfony
1. Créer un **Web Service** depuis le dossier `backend/`
2. **Dockerfile path** : `backend/Dockerfile.prod`
3. Variables d'environnement à renseigner :
   - `APP_ENV=prod`
   - `APP_SECRET=<valeur aléatoire 32 chars>`
   - `DATABASE_URL=mysql://user:pass@host:3306/db?serverVersion=8.0&charset=utf8mb4`
   - `JWT_PASSPHRASE=<passphrase>`
   - `FRONTEND_URL=https://<votre-frontend>.onrender.com`
4. Générer de nouvelles clés JWT pour la prod :
   ```bash
   openssl genpkey -algorithm RSA -out private.pem -pkeyopt rsa_keygen_bits:4096
   openssl rsa -pubout -in private.pem -out public.pem
   ```
   Ajouter les clés via les variables d'environnement Render ou les inclure dans le build.

### Frontend React
1. Créer un **Web Service** depuis le dossier `frontend/`
2. **Dockerfile path** : `frontend/Dockerfile.prod`
3. Variables d'environnement :
   - `VITE_API_URL=https://<votre-backend>.onrender.com`
   - `VITE_TMDB_KEY=<votre clé TMDB>`

---

## Développement sans Docker

### Backend
```bash
cd backend

# Installer les dépendances
composer install

# Configurer la base de données dans .env.local
echo 'DATABASE_URL="mysql://root:root@127.0.0.1:3306/media_tracker?serverVersion=8.0&charset=utf8mb4"' > .env.local

# Lancer le serveur de développement
php -S localhost:8080 -t public/
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```
