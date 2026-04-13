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
