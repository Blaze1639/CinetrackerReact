# CineTrack — Commandes du projet

Monorepo : `frontend` (React + Vite), `backend` (Symfony 7.3 + PHP 8.2+), `db` (MySQL 8.0), orchestrés via Docker Compose.

---

## Docker (tout le projet)

```bash
# Construire et démarrer les 3 services (db, backend, frontend)
docker-compose up --build

# Démarrer en arrière-plan
docker-compose up -d

# Arrêter les services
docker-compose down

# Arrêter et supprimer le volume de la base de données
docker-compose down -v

# Voir les logs d'un service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f db

# Reconstruire un seul service
docker-compose up --build backend
```

Accès une fois démarré :
- Frontend : http://localhost:5173
- Backend (API) : http://localhost:8080
- MySQL : localhost:3306

---

## Backend (Symfony, dans `backend/`)

```bash
# Installer les dépendances PHP
composer install

# Créer la base de données
php bin/console doctrine:database:create

# Jouer les migrations
php bin/console doctrine:migrations:migrate

# Générer une nouvelle migration depuis les entités
php bin/console make:migration

# Vider le cache Symfony
php bin/console cache:clear

# Lancer le serveur de dev (sans Docker)
symfony server:start   # http://localhost:8080

# Générer les clés JWT (RSA)
php bin/console lexik:jwt:generate-keypair

# Lancer les tests unitaires (PHPUnit)
php bin/phpunit
# ou
vendor/bin/phpunit

# Vérifier la syntaxe d'un fichier PHP
php -l src/Controller/AccueilController.php

# Vérifier la syntaxe de tout le projet
find src -name "*.php" -exec php -l {} \;
```

---

## Frontend (React + Vite, dans `frontend/`)

```bash
# Installer les dépendances
npm install
# (ou en CI, install reproductible)
npm ci

# Lancer le serveur de dev (http://localhost:5173)
npm run dev

# Build de production
npm run build

# Prévisualiser le build de production
npm run preview

# Lancer les tests (Jest)
npm test

# Lancer les tests avec couverture (mode CI)
npm test -- --watchAll=false --coverage
```

---

## CI/CD (`.github/workflows/ci-cd.yml`)

Déclenché sur push/PR vers `main` et `DEV`. Étapes principales (mêmes commandes que ci-dessus, exécutées automatiquement) :

```bash
# Job lint
npm ci
npm run build

# Job frontend-tests
npm ci
npm test -- --watchAll=false --coverage
npm run build

# Job backend-check
find backend/api -name "*.php" -exec php -l {} \;

# Job docker-build
docker build -f frontend/Dockerfile.prod ./frontend
docker build -f backend/Dockerfile.prod ./backend
```

---

## Git

```bash
git status
git pull
git checkout -b feature/ma-feature
git add <fichiers>
git commit -m "message"
git push -u origin feature/ma-feature
```
