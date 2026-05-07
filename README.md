
# 🎬 CineTrack — React + PHP + MySQL + Docker

## 🚀 Démarrage local (Docker)

```bash
docker-compose up --build
```
→ http://localhost:5173

---

## 🏗️ Architecture

```
CinetrackerReact/
├── backend/
│   ├── api/
│   │   ├── _helpers.php
│   │   ├── accueil.php
│   │   ├── ajouter_a_voir.php
│   │   ├── ajouter_actu.php
│   │   ├── ajouter_film.php
│   │   ├── basculer_favori.php
│   │   ├── connexion.php
│   │   ├── deconnexion.php
│   │   ├── delete.php
│   │   ├── deplacement.php
│   │   ├── env_notif.php
│   │   ├── incrementer_compteur.php
│   │   ├── inscription.php
│   │   ├── liste_a_voir.php
│   │   ├── marquer_lu.php
│   │   ├── media.php
│   │   ├── modifier_a_voir.php
│   │   ├── modifier_film.php
│   │   ├── notifications.php
│   │   ├── profile.php
│   │   ├── session.php
│   │   ├── session_handler.php
│   │   ├── supprimer_a_voir.php
│   │   ├── supprimer_actu.php
│   │   ├── supprimer_film.php
│   │   ├── supprimer_notif.php
│   ├── db.php
│   ├── Dockerfile
│   ├── Dockerfile.prod
│   ├── README_securite.md
├── docker/
│   └── mysql/
│       └── init.sql
├── frontend/
│   ├── __mocks__/
│   │   └── fileMock.js
│   ├── src/
│   │   ├── __tests__/
│   │   │   └── helpers.test.js
│   │   ├── components/
│   │   │   ├── Navbar.jsx
│   │   │   ├── StarRating.jsx
│   │   │   └── TmdbSearch.jsx
│   │   ├── context/
│   │   │   └── AuthContext.jsx
│   │   ├── pages/
│   │   │   ├── Accueil.jsx
│   │   │   ├── AddMedia.jsx
│   │   │   ├── AddWatchlist.jsx
│   │   │   ├── Aleatoire.jsx
│   │   │   ├── Auth.jsx
│   │   │   ├── EditMedia.jsx
│   │   │   ├── EditWatchlist.jsx
│   │   │   ├── Home.jsx
│   │   │   ├── MediaList.jsx
│   │   │   ├── Profile.jsx
│   │   │   └── Watchlist.jsx
│   │   ├── services/
│   │   │   └── api.js
│   │   ├── styles/
│   │   │   ├── accueil.css
│   │   │   ├── ajouter_a_voir.css
│   │   │   ├── ajouter_film.css
│   │   │   ├── aleatoire.css
│   │   │   ├── common.css
│   │   │   ├── dashboard.css
│   │   │   ├── deplacement.css
│   │   │   ├── evaluer_film.css
│   │   │   ├── formulaire.css
│   │   │   ├── formulaire_a_voir.css
│   │   │   ├── home.css
│   │   │   ├── index.css
│   │   │   ├── inscription_connexion.css
│   │   │   ├── liste_a_voir.css
│   │   │   ├── modifier_a_voir.css
│   │   │   ├── modifier_film.css
│   │   │   ├── profile.css
│   │   │   └── style.css
│   │   ├── utils/
│   │   │   └── helpers.js
│   │   ├── App.jsx
│   │   └── main.jsx
│   ├── babel.config.cjs
│   ├── Dockerfile
│   ├── Dockerfile.prod
│   ├── index.html
│   ├── jest.config.cjs
│   ├── nginx.conf
│   ├── package.json
│   └── vite.config.js
├── docker-compose.yml
├── package.json
├── README.md
├── static.json

---

## 🔑 Compte admin par défaut
- Email : axel77dion@gmail.com
- Rôle : admin

---

## 🚀 Déploiement sur Render

### 1. Backend (PHP + MySQL)

#### a. Déployer la base de données
1. Créez un service **PostgreSQL** ou **MySQL** sur Render (ou utilisez un service externe).
2. Récupérez l’URL de connexion et configurez les variables d’environnement nécessaires dans Render pour le backend (voir `.env`).
3. Importez le schéma de la base depuis `docker/mysql/init.sql` si besoin.

#### b. Déployer l’API PHP
1. Créez un nouveau service **Web Service** sur Render.
2. Choisissez le repo GitHub ou connectez Render à votre dépôt.
3. Renseignez le chemin du dossier `backend/` comme racine du service.
4. Utilisez le Dockerfile de prod :
    - **Docker build command** : *(laissez vide ou personnalisé selon vos besoins)*
    - **Dockerfile path** : `backend/Dockerfile.prod`
5. Ajoutez les variables d’environnement nécessaires (connexion DB, etc).
6. Définissez le port d’écoute à `80` (ou celui exposé dans le Dockerfile).

### 2. Frontend (React)
1. Créez un autre service **Web Service** sur Render.
2. Renseignez le chemin du dossier `frontend/` comme racine du service.
3. Utilisez le Dockerfile de prod :
    - **Docker build command** : *(laissez vide ou personnalisé selon vos besoins)*
    - **Dockerfile path** : `frontend/Dockerfile.prod`
4. Définissez le port d’écoute à `80` (ou celui exposé dans le Dockerfile).
5. Ajoutez la variable d’environnement `VITE_API_URL` pour pointer vers l’URL du backend Render.

### 3. Configuration des variables d’environnement
Adaptez les variables d’environnement dans Render pour chaque service (backend et frontend) selon vos besoins (URL API, DB, etc).

### 4. Domaines personnalisés
Vous pouvez ajouter un domaine personnalisé dans Render pour chaque service (frontend et backend).

---

**Astuce** : Consultez la [documentation Render](https://render.com/docs) pour plus de détails sur le déploiement Docker multi-services.
