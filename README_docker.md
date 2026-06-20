# Docker — CineTrack

Ce document explique comment Docker est utilisé dans ce projet : les fichiers principaux, les concepts (image, conteneur, volume) et la différence entre virtualisation et conteneurisation.

---

## 1. Les fichiers principaux

### `docker-compose.yml` (racine du projet)

C'est le **chef d'orchestre**. Il décrit l'ensemble des services qui composent l'application et comment ils communiquent entre eux, sans avoir à taper une longue commande `docker run` pour chacun.

```yaml
services:
  db:        # MySQL 8.0 — base de données
  backend:   # Symfony (build depuis ./backend)
  frontend:  # React/Vite (build depuis ./frontend)
```

Pour chaque service, il définit :
- l'**image** à utiliser (`image: mysql:8.0`) ou le **Dockerfile** à builder (`build: ./backend`)
- les **ports** exposés sur la machine hôte (`"8080:80"`)
- les **variables d'environnement** (`DATABASE_URL`, `JWT_SECRET_KEY`...)
- les **volumes** à monter
- les **dépendances** entre services (`depends_on`)
- le **réseau** commun (`cinetrack_net`) qui permet aux conteneurs de se joindre par leur nom (ex : le backend contacte MySQL via `db:3306`, pas `localhost:3306`)

Une seule commande (`docker-compose up`) suffit alors à démarrer toute la stack.

### `backend/Dockerfile`

Recette de construction de l'**image de développement** du backend Symfony :
- part d'une image `php:8.4-fpm`
- installe les extensions PHP nécessaires (pdo_mysql, intl, zip...) et Composer
- copie le code source et installe les dépendances (`composer install`)
- génère les clés JWT RSA si elles n'existent pas encore
- installe Nginx + Supervisor pour servir l'API
- démarre via `supervisord` (qui lance PHP-FPM **et** Nginx dans le même conteneur)

### `backend/Dockerfile.prod`

Variante **multi-stage** optimisée pour la production :
- un stage `build` qui installe les dépendances et génère les clés JWT
- un stage final, plus léger, qui ne récupère que le code applicatif et le dossier `vendor/` du stage précédent (`COPY --from=build`)
- évite de garder dans l'image finale les outils de build inutiles à l'exécution → image plus petite et plus sûre

### `frontend/Dockerfile` / `frontend/Dockerfile.prod`

Recette **multi-stage** pour le frontend React :
1. **Stage `build`** : image `node:18`, installation des dépendances npm, puis `npm run build` → génère les fichiers statiques optimisés dans `dist/`
2. **Stage final** : image `nginx:alpine` très légère, qui ne contient que les fichiers statiques buildés (`COPY --from=build /app/dist`) servis par Nginx

Résultat : l'image finale ne contient ni Node.js, ni le code source, ni `node_modules` — uniquement du HTML/CSS/JS statique servi par Nginx.

### Fichiers de configuration associés

| Fichier | Rôle |
|---|---|
| `backend/docker/nginx.conf` | Configuration Nginx du backend : redirige toutes les requêtes vers `index.php` (front controller Symfony) et transmet le PHP à PHP-FPM via FastCGI |
| `backend/docker/supervisord.conf` | Permet de faire tourner **deux process** (PHP-FPM + Nginx) dans un seul conteneur backend |
| `frontend/nginx.conf` | Configuration Nginx du frontend : sert les fichiers statiques et redirige toute route inconnue vers `index.html` (nécessaire pour le routing côté client de React Router) |
| `docker/mysql/init.sql` | Script SQL exécuté automatiquement par l'image MySQL au premier démarrage (`docker-entrypoint-initdb.d`) : crée les tables et insère des données de départ |

---

## 2. Comprendre Docker Compose : image, conteneur, volume

### Une image

Une **image** est un modèle figé, en lecture seule, qui contient tout ce qu'il faut pour exécuter une application : le système de fichiers, les binaires, les dépendances, la configuration. C'est le résultat de la construction d'un `Dockerfile` (`docker build`), ou une image téléchargée toute prête (`mysql:8.0`, `nginx:alpine`).

Une image, c'est un peu comme **une recette de cuisine** : elle décrit précisément quoi assembler, mais ne fait rien tant qu'on ne la "cuisine" pas.

```
Dockerfile  ──(docker build)──►  Image
```

### Un conteneur

Un **conteneur** est une **instance en cours d'exécution** d'une image. C'est le plat préparé à partir de la recette : un processus isolé, avec son propre système de fichiers (une couche modifiable ajoutée par-dessus l'image en lecture seule), son réseau, mais qui partage le noyau du système d'exploitation hôte.

```
Image  ──(docker run / docker-compose up)──►  Conteneur (en cours d'exécution)
```

Dans ce projet, `docker-compose up` crée 3 conteneurs : `cinetrack_db`, `cinetrack_backend`, `cinetrack_frontend`, chacun lancé à partir de sa propre image. On peut démarrer plusieurs conteneurs à partir d'une même image (ex : pour scaler un service), chacun étant isolé des autres.

### Un volume

Par défaut, les données écrites dans un conteneur disparaissent quand celui-ci est supprimé (`docker-compose down`) puisque sa couche d'écriture est éphémère. Un **volume** est un espace de stockage géré par Docker, **externe au conteneur**, qui persiste indépendamment de son cycle de vie.

Dans `docker-compose.yml` :

```yaml
db:
  volumes:
    - db_data:/var/lib/mysql
```

`db_data` est un volume nommé : tout ce que MySQL écrit dans `/var/lib/mysql` à l'intérieur du conteneur est en réalité stocké dans ce volume, sur la machine hôte. On peut alors supprimer et recréer le conteneur `db` (mise à jour de l'image, redémarrage...) **sans perdre les données** — elles ne sont effacées que si on supprime explicitement le volume (`docker-compose down -v`).

Le projet utilise aussi un **bind mount** (montage d'un fichier de l'hôte, pas un volume géré par Docker) pour l'initialisation :

```yaml
volumes:
  - ./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
```

Ici, le fichier local `docker/mysql/init.sql` est directement monté dans le conteneur, ce qui permet à MySQL de l'exécuter automatiquement à la création de la base.

### Schéma récapitulatif

```
Dockerfile (recette)
      │ docker build
      ▼
   Image (modèle figé, en lecture seule)
      │ docker run / docker-compose up
      ▼
  Conteneur (instance qui s'exécute)
      │
      ├── couche d'écriture éphémère (perdue si le conteneur est supprimé)
      └── volume monté ───► données persistées sur l'hôte, indépendantes du conteneur
```

---

## 3. Virtualisation vs conteneurisation

### Virtualisation (machines virtuelles)

Une **machine virtuelle (VM)** simule un ordinateur complet, y compris son **propre système d'exploitation**, grâce à un hyperviseur (VMware, VirtualBox, Hyper-V) qui partage le matériel physique entre plusieurs OS invités.

```
┌─────────────────────────────────────────┐
│              Machine hôte                │
│  ┌───────────────────────────────────┐   │
│  │           Hyperviseur              │   │
│  │  ┌───────────┐   ┌───────────┐    │   │
│  │  │   VM 1    │   │   VM 2    │    │   │
│  │  │ OS invité │   │ OS invité │    │   │
│  │  │ + libs    │   │ + libs    │    │   │
│  │  │ + app     │   │ + app     │    │   │
│  │  └───────────┘   └───────────┘    │   │
│  └───────────────────────────────────┘   │
│              OS hôte + matériel           │
└─────────────────────────────────────────┘
```

- Chaque VM embarque un **OS complet** → lourd (plusieurs Go par VM), démarrage lent (minutes)
- Isolation très forte (niveau matériel virtualisé)
- Permet de faire tourner des OS différents (Linux + Windows sur la même machine)

### Conteneurisation (Docker)

Un **conteneur** ne contient pas d'OS : tous les conteneurs d'une machine **partagent le noyau du système d'exploitation hôte**. Docker isole uniquement les processus, le système de fichiers et le réseau de chaque conteneur via des mécanismes du noyau Linux (namespaces, cgroups).

```
┌─────────────────────────────────────────┐
│              Machine hôte                │
│         OS hôte (noyau partagé)           │
│  ┌──────────────┐   ┌──────────────┐     │
│  │  Docker Engine│  (gère les conteneurs) │
│  └──────────────┘                        │
│  ┌───────────┐ ┌───────────┐ ┌─────────┐ │
│  │Conteneur 1│ │Conteneur 2│ │Conteneur│ │
│  │  backend  │ │ frontend  │ │   db    │ │
│  │ + libs    │ │ + libs    │ │ + libs  │ │
│  │ + app     │ │ + app     │ │ + app   │ │
│  └───────────┘ └───────────┘ └─────────┘ │
└─────────────────────────────────────────┘
```

- Pas d'OS dupliqué → très léger (quelques dizaines de Mo à quelques centaines de Mo), démarrage en quelques secondes
- Isolation au niveau processus, plus légère que celle d'une VM
- Tous les conteneurs doivent utiliser le même noyau que l'hôte (sur macOS/Windows, Docker Desktop lance en coulisses une petite VM Linux pour fournir ce noyau)

### Tableau comparatif

| | Virtualisation (VM) | Conteneurisation (Docker) |
|---|---|---|
| Ce qui est isolé | Le matériel (via l'hyperviseur) | Les processus (via le noyau partagé) |
| Système d'exploitation | Un OS complet par VM | Aucun OS embarqué, noyau hôte partagé |
| Poids | Plusieurs Go | Quelques dizaines à centaines de Mo |
| Démarrage | Minutes | Secondes |
| Isolation | Très forte | Plus légère mais suffisante pour la plupart des usages |
| Cas d'usage typique | Faire cohabiter des OS différents, forte isolation sécuritaire | Empaqueter et déployer une application avec ses dépendances, de façon reproductible |

### Pourquoi Docker dans CineTrack ?

Le projet a 3 environnements d'exécution différents (MySQL, PHP/Symfony, Node/Nginx) qui doivent fonctionner ensemble de manière identique en développement et en production. Plutôt que d'installer MySQL, PHP 8.4, Nginx et Node sur chaque machine de développement (avec les risques de "ça marche chez moi mais pas ailleurs"), chaque service est empaqueté dans son image Docker : l'environnement d'exécution est ainsi **identique partout** (poste local, CI/CD, serveur de production), sans le poids ni la lenteur de plusieurs machines virtuelles.
