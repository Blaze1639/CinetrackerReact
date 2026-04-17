
# 🎬 CineTrack — React + PHP + MySQL + Docker

## 🚀 Démarrage local (Docker)

```bash
docker-compose up --build
```
→ http://localhost:5173

---

## 🏗️ Architecture

```
cinetrack/
├── docker-compose.yml        ← Dev local
├── frontend/
│   ├── Dockerfile            ← Dev (Node + Vite)
│   ├── Dockerfile.prod       ← Prod (build + Nginx)
│   ├── nginx.conf
│   └── src/
└── backend/
    ├── Dockerfile            ← Dev
    ├── Dockerfile.prod       ← Prod
    └── api/
```

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
