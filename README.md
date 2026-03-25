# 🎬 CineTrack — React + PHP + MySQL + Docker

## 🚀 Démarrage local (Docker)

```bash
docker-compose up --build
```
→ http://localhost:5173

---

## ☁️ Déploiement sur Railway

### Étape 1 — Créer un compte
https://railway.app → Se connecter avec GitHub

### Étape 2 — Nouveau projet
**New Project → Deploy from GitHub repo** → sélectionne ton repo

Railway va détecter le `docker-compose.yml` automatiquement.

### Étape 3 — Ajouter MySQL
Dans ton projet Railway :
**+ New Service → Database → MySQL**

Railway génère automatiquement les variables `MYSQL_URL`, `MYSQLHOST`, `MYSQLPASSWORD`, etc.

### Étape 4 — Configurer le service Backend
Dans le service **backend**, onglet **Variables**, ajouter :

| Variable | Valeur |
|---|---|
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_NAME` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USER` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASS` | `${{MySQL.MYSQLPASSWORD}}` |
| `FRONTEND_URL` | URL de ton service frontend (après déploiement) |

Dans **Settings → Build**, changer le Dockerfile en :
`backend/Dockerfile.prod`

### Étape 5 — Configurer le service Frontend
Dans le service **frontend**, onglet **Variables**, ajouter :

| Variable | Valeur |
|---|---|
| `VITE_TMDB_KEY` | `22eef7e96585baa751a8384b942e4470` |
| `VITE_API_URL` | URL de ton service backend (ex: `https://backend-xxx.railway.app`) |

Dans **Settings → Build**, changer le Dockerfile en :
`frontend/Dockerfile.prod`

### Étape 6 — Importer la base de données
Dans Railway, ouvre le service MySQL → **Query** → colle le contenu de `docker/mysql/init.sql`

### Étape 7 — Déployer
Railway déploie automatiquement à chaque push sur GitHub. 🎉

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
