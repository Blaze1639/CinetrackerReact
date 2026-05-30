# CineTrack — Frontend React

Interface utilisateur de l'application CineTrack, construite avec React 18, Vite et Tailwind CSS.

---

## Stack technique

| Outil             | Version  | Rôle                               |
|-------------------|----------|------------------------------------|
| React             | 18.2     | Framework UI                       |
| React Router DOM  | 6.22     | Routage SPA                        |
| Vite              | 5.1      | Bundler & serveur de dev           |
| Tailwind CSS      | 4.3      | Styles utilitaires                 |
| Jest              | 29.7     | Runner de tests unitaires          |
| Testing Library   | 16.x     | Tests de composants React          |
| Babel             | 7.x      | Transpilation JSX pour Jest        |

---

## Structure du projet

```
frontend/
├── src/
│   ├── App.jsx                     # Routeur principal, guards PrivateRoute / PublicRoute
│   ├── main.jsx                    # Point d'entrée React DOM
│   ├── context/
│   │   └── AuthContext.jsx         # État global d'authentification JWT
│   ├── services/
│   │   └── api.js                  # Client API centralisé (createApi / useApi)
│   ├── pages/
│   │   ├── Home.jsx                # Landing page publique
│   │   ├── Auth.jsx                # Connexion & inscription (mode prop)
│   │   ├── Accueil.jsx             # Dashboard : stats, graphique, leaderboard, actus
│   │   ├── MediaList.jsx           # Catalogue films/séries avec filtres et pagination
│   │   ├── AddMedia.jsx            # Formulaire d'ajout d'un film/série
│   │   ├── EditMedia.jsx           # Formulaire de modification d'un film/série
│   │   ├── Watchlist.jsx           # Liste "à voir"
│   │   ├── AddWatchlist.jsx        # Ajout à la liste "à voir"
│   │   ├── EditWatchlist.jsx       # Modification d'un élément watchlist
│   │   ├── Aleatoire.jsx           # Suggestion aléatoire
│   │   ├── Profile.jsx             # Profil utilisateur et suppression de compte
│   │   └── MentionsLegales.jsx     # Mentions légales
│   ├── components/
│   │   ├── Navbar.jsx              # Navigation principale
│   │   ├── Footer.jsx              # Pied de page
│   │   ├── StarRating.jsx          # Composant de notation 1-5 étoiles
│   │   └── TmdbSearch.jsx          # Recherche TMDB (auto-complétion)
│   ├── utils/
│   │   └── helpers.js              # Fonctions utilitaires (formatage, etc.)
│   └── styles/                     # CSS par page/composant
│       ├── index.css
│       ├── common.css
│       ├── home.css
│       ├── dashboard.css
│       ├── formulaire.css
│       └── ...
├── __mocks__/                      # Mocks Jest (modules globaux)
├── coverage/                       # Rapport de couverture Jest
├── dist/                           # Build de production Vite
├── index.html                      # Template HTML principal
├── vite.config.js                  # Configuration Vite
├── jest.config.cjs                 # Configuration Jest
├── babel.config.cjs                # Configuration Babel (Jest)
├── Dockerfile                      # Image de développement
├── Dockerfile.prod                 # Image de production (Nginx)
├── nginx.conf                      # Config Nginx pour servir le build
└── package.json
```

---

## Pages et fonctionnalités

### Pages publiques

| Route             | Page            | Description                                  |
|-------------------|-----------------|----------------------------------------------|
| `/`               | Home            | Landing page avec présentation de l'app      |
| `/connexion`      | Auth (login)    | Formulaire de connexion                      |
| `/inscription`    | Auth (register) | Formulaire d'inscription                     |
| `/mentions-legales` | MentionsLegales | Mentions légales                           |

### Pages privées (JWT requis)

| Route                | Page          | Description                                   |
|----------------------|---------------|-----------------------------------------------|
| `/accueil`           | Accueil       | Dashboard annuel avec graphique et leaderboard |
| `/index`             | MediaList     | Catalogue personnel avec filtres              |
| `/ajouter`           | AddMedia      | Ajouter un film ou une série                  |
| `/modifier/:id`      | EditMedia     | Modifier un film/série existant               |
| `/liste-a-voir`      | Watchlist     | Liste des médias à regarder                   |
| `/ajouter-a-voir`    | AddWatchlist  | Ajouter à la liste à voir                     |
| `/modifier-a-voir/:id` | EditWatchlist | Modifier un élément watchlist              |
| `/aleatoire`         | Aleatoire     | Suggestion aléatoire depuis le catalogue      |
| `/profile`           | Profile       | Statistiques et gestion du compte             |

---

## Authentification

L'authentification est gérée par `AuthContext` qui expose :

```jsx
const { user, login, logout, loading, apiFetch } = useAuth()
```

| Valeur     | Type     | Description                                          |
|------------|----------|------------------------------------------------------|
| `user`     | object   | `{ user_id, username, role }` ou `null`              |
| `loading`  | boolean  | `true` pendant la vérification du token au démarrage |
| `login`    | function | Stocke le JWT dans localStorage et met à jour l'état |
| `logout`   | function | Supprime le JWT et réinitialise l'état               |
| `apiFetch` | function | `fetch` enrichi avec `Authorization: Bearer <token>` |

Le token JWT est persisté dans `localStorage` sous la clé `jwt_token`.

### Flux de navigation

```
URL publique         → PublicRoute → redirige vers /accueil si déjà connecté
URL privée           → PrivateRoute → redirige vers /connexion si non connecté
N'importe quelle URL → Navigate to="/" si route inconnue
```

---

## Service API

Le service `api.js` expose une interface structurée via le hook `useApi()` :

```js
const api = useApi()

// Authentification
api.auth.login({ email, motdepasse })
api.auth.register({ pseudo, email, motdepasse, confirmer_motdepasse })
api.auth.session()
api.auth.logout()
api.auth.profile()
api.auth.delete()

// Médias
api.media.getAll({ type, search, year, rating, page })
api.media.add({ title, rating, type_media, image_url, commentaire })
api.media.update({ media_id, title, rating, type_media, image_url, commentaire })
api.media.remove(id)
api.media.toggleFavorite(id)
api.media.incrementCounter(id)

// Watchlist
api.watchlist.getAll({ type, search })
api.watchlist.add({ title, type_media, image_url })
api.watchlist.update({ media_id, title, type_media, image_url })
api.watchlist.remove(id)
api.watchlist.move({ media_id, rating, commentaire })

// Tableau de bord
api.accueil.get(year)

// Notifications
api.notifications.getAll()
api.notifications.markRead(id)
api.notifications.remove(id)
api.notifications.send({ type_message, message })

// Actualités (admin)
api.actualites.add({ titre, contenu })
api.actualites.remove(id)
```

L'URL de base est lue depuis `VITE_API_URL` (variable d'environnement Vite).

---

## Composants réutilisables

### `StarRating`

Sélecteur de note de 1 à 5 étoiles interactif.

```jsx
<StarRating value={rating} onChange={setRating} />
```

### `TmdbSearch`

Recherche TMDB avec auto-complétion. Remplit automatiquement le titre et l'affiche.

```jsx
<TmdbSearch onSelect={({ title, image_url, type_media }) => { ... }} />
```

### `Navbar`

Navigation principale avec liens conditionnels selon le rôle (`admin`/`utilisateur`) et gestion de la déconnexion.

### `Footer`

Pied de page commun avec lien vers les mentions légales.

---

## Tests

### Lancer les tests

```bash
cd frontend
npm test
```

### Lancer avec couverture

```bash
npm test -- --watchAll=false --coverage
```

### Configuration Jest

```js
// jest.config.cjs
module.exports = {
  testEnvironment: 'jsdom',
  transform: { '^.+\\.[jt]sx?$': 'babel-jest' },
  setupFilesAfterFramework: ['@testing-library/jest-dom'],
}
```

### Tests existants

| Fichier                        | Description                              |
|--------------------------------|------------------------------------------|
| `src/App.test.js`              | Rendu de base du composant App           |
| `src/__tests__/helpers.test.js`| Tests des fonctions utilitaires          |

### Exemple de test

```jsx
// src/__tests__/helpers.test.js
import { formatDate } from '../utils/helpers'

test('formatDate retourne une chaîne lisible', () => {
  expect(formatDate('2025-01-15 10:00:00')).toBe('15 janv. 2025')
})
```

---

## Installation

### Avec Docker (recommandé)

```bash
# Depuis la racine du monorepo
docker-compose up --build
```

Le frontend est accessible sur **http://localhost:5173**.

### En développement local

**Prérequis** : Node.js 20+

```bash
cd frontend

# Installer les dépendances
npm install

# Configurer la variable d'environnement API
echo "VITE_API_URL=http://localhost:8080" > .env

# Lancer le serveur de développement
npm run dev
```

### Build de production

```bash
npm run build     # Génère dist/
npm run preview   # Prévisualise le build
```

---

## Variables d'environnement

| Variable        | Description                         | Valeur par défaut                              |
|-----------------|-------------------------------------|------------------------------------------------|
| `VITE_API_URL`  | URL du backend Symfony              | `https://cinetrack-backend-di8b.onrender.com`  |
| `VITE_TMDB_KEY` | Clé API TMDB (recherche de films)   | *(passée en arg Docker build)*                 |

---

## Docker

### Dockerfile de développement

Image Nginx légère qui sert le build Vite.

```dockerfile
FROM node:20-alpine AS builder
WORKDIR /app
COPY . .
ARG VITE_API_URL VITE_TMDB_KEY
RUN npm ci && npm run build

FROM nginx:alpine
COPY --from=builder /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
```

### Configuration Nginx

Toutes les routes sont renvoyées vers `index.html` pour que React Router gère le routage côté client.

```nginx
location / {
  try_files $uri $uri/ /index.html;
}
```

---

## CI/CD

Le pipeline GitHub Actions exécute automatiquement :

1. **Lint** — `npm run build` (vérifie la syntaxe JSX)
2. **Tests** — `npm test -- --watchAll=false --coverage`
3. **Build Docker** — build de l'image de production

---

## Licence

Propriétaire — tous droits réservés.
