# Sécurité — Backend Symfony CineTrack

Ce document détaille chaque couche de sécurité mise en place dans le backend Symfony, et répond à la question : **Doctrine ORM gère-t-il la sécurité ?**

---

## Table des matières

1. [Vue d'ensemble des couches de sécurité](#1-vue-densemble)
2. [Authentification JWT (LexikJWT)](#2-authentification-jwt)
3. [Hashage des mots de passe (bcrypt)](#3-hashage-des-mots-de-passe)
4. [Firewall Symfony](#4-firewall-symfony)
5. [Contrôle d'accès aux routes](#5-contrôle-daccès-aux-routes)
6. [Autorisation par ressource (ownership)](#6-autorisation-par-ressource)
7. [Protection CORS (NelmioCorsBundle)](#7-protection-cors)
8. [Doctrine ORM et la sécurité SQL](#8-doctrine-orm-et-la-sécurité-sql)
9. [Ce que Doctrine NE gère PAS](#9-ce-que-doctrine-ne-gère-pas)
10. [Tableau récapitulatif](#10-tableau-récapitulatif)

---

## 1. Vue d'ensemble

```
Requête HTTP entrante
        │
        ▼
┌───────────────────────────────────────────────────────┐
│  NelmioCorsBundle — vérification de l'origine (CORS)  │  ← Couche 1
└───────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────┐
│  Firewall Symfony — route publique ou protégée ?      │  ← Couche 2
└───────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────┐
│  LexikJWT — décodage et vérification du token RS256   │  ← Couche 3
└───────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────┐
│  access_control — rôle requis (IS_AUTHENTICATED_FULLY)│  ← Couche 4
└───────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────┐
│  Controller — ownership check (user_id == JWT sub)    │  ← Couche 5
└───────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────┐
│  Doctrine ORM — requêtes préparées (anti-SQLi)        │  ← Couche 6 (passive)
└───────────────────────────────────────────────────────┘
```

---

## 2. Authentification JWT

### Fichier de configuration

```yaml
# config/packages/lexik_jwt_authentication.yaml
lexik_jwt_authentication:
    secret_key:  '%env(resolve:JWT_SECRET_KEY)%'   # Clé privée RSA
    public_key:  '%env(resolve:JWT_PUBLIC_KEY)%'   # Clé publique RSA
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl:   7200                              # Expiration : 2 heures
    user_id_claim: email                           # Identifiant dans le token
    token_extractors:
        authorization_header:
            enabled: true
            prefix:  Bearer
            name:    Authorization
```

### Algorithme : RS256 (RSA + SHA-256)

CineTrack utilise une **paire de clés RSA 4096 bits** asymétrique :

| Clé            | Fichier                      | Usage                              |
|----------------|------------------------------|------------------------------------|
| Clé privée     | `config/jwt/private.pem`     | Signe les tokens à la connexion    |
| Clé publique   | `config/jwt/public.pem`      | Vérifie les tokens à chaque requête|

**Avantage de RS256 vs HS256** : la clé privée ne quitte jamais le serveur. Un service tiers pourrait vérifier les tokens avec la clé publique sans pouvoir en émettre.

Les clés sont **générées automatiquement** au build Docker :

```dockerfile
# Dockerfile
RUN openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
RUN openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

### Contenu d'un token décodé

```json
{
  "iat": 1748600000,
  "exp": 1748607200,
  "roles": ["ROLE_USER"],
  "email": "alice@example.com"
}
```

| Champ   | Signification                        |
|---------|--------------------------------------|
| `iat`   | Timestamp d'émission (issued at)     |
| `exp`   | Timestamp d'expiration (2h après)    |
| `roles` | Rôles Symfony de l'utilisateur       |
| `email` | Identifiant unique (`user_id_claim`) |

### Flux de connexion

```
POST /api/auth/login  { email, motdepasse }
           │
           ▼
   UserRepository.findOneBy(['email' => $email])
           │
           ▼
   PasswordHasher.isPasswordValid($user, $motdepasse)
           │          (bcrypt verify)
           ▼
   JWTTokenManager.create($user)
           │          (signe avec private.pem RSA 4096)
           ▼
   { success: true, token: "eyJ..." }
```

---

## 3. Hashage des mots de passe

### Configuration

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: bcrypt
            cost: 12
```

### Algorithme : bcrypt (coût 12)

bcrypt est un algorithme de hashage **intentionnellement lent**, conçu pour résister aux attaques par force brute.

| Paramètre | Valeur | Signification                              |
|-----------|--------|--------------------------------------------|
| algorithme| bcrypt | Standard recommandé pour les mots de passe |
| cost      | 12     | 2¹² = 4096 itérations internes             |

**Ce que bcrypt garantit :**
- Irréversibilité : impossible de retrouver le mot de passe depuis le hash
- Unicité : deux hashages du même mot de passe donnent des résultats différents (salt aléatoire intégré)
- Résistance : un attaquant doit tester chaque mot de passe individuellement, ce qui est très lent

**En environnement de test**, le coût est réduit à 4 pour accélérer les tests :

```yaml
when@test:
    security:
        password_hashers:
            Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface:
                algorithm: auto
                cost: 4
```

### Dans le code

```php
// AuthController.php — à l'inscription
$user->setPassword($hasher->hashPassword($user, $motdepasse));

// AuthController.php — à la connexion
if (!$hasher->isPasswordValid($user, $motdepasse)) {
    return $this->json(['error' => 'Email ou mot de passe incorrect'], 401);
}
```

---

## 4. Firewall Symfony

```yaml
# config/packages/security.yaml
security:
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false       # Pas de sécurité sur les outils dev

        api:
            pattern:   ^/api
            stateless: true       # Pas de session serveur (SPA + JWT)
            jwt: ~                # Délègue l'auth à LexikJWT
```

### Pourquoi `stateless: true` ?

L'API ne stocke **aucune session** côté serveur. Chaque requête est authentifiée uniquement via le token JWT dans le header `Authorization`. Cela permet :
- Le déploiement sur plusieurs serveurs sans partage de session
- La compatibilité avec le frontend React qui gère l'état dans `localStorage`
- Une architecture sans état, cohérente avec les principes REST

---

## 5. Contrôle d'accès aux routes

```yaml
# config/packages/security.yaml
    access_control:
        - { path: ^/api/auth/login,    roles: PUBLIC_ACCESS }
        - { path: ^/api/auth/register, roles: PUBLIC_ACCESS }
        - { path: ^/api,               roles: IS_AUTHENTICATED_FULLY }
```

### Logique évaluée dans l'ordre

| Route                  | Rôle requis             | Résultat                               |
|------------------------|-------------------------|----------------------------------------|
| `/api/auth/login`      | `PUBLIC_ACCESS`         | Accessible sans token                  |
| `/api/auth/register`   | `PUBLIC_ACCESS`         | Accessible sans token                  |
| Tout `/api/*`          | `IS_AUTHENTICATED_FULLY`| JWT valide obligatoire, sinon → 401   |

Si un JWT est absent, expiré ou invalide, Symfony retourne automatiquement `401 Unauthorized` **avant même d'atteindre le controller**.

---

## 6. Autorisation par ressource

Le firewall garantit qu'un utilisateur est **authentifié**, mais pas qu'il est **autorisé** à accéder à une ressource spécifique. Cette vérification est faite dans chaque controller.

### Principe d'ownership (appartenance)

Chaque requête sur une ressource vérifie que `user_id` de la ressource correspond à l'utilisateur du JWT courant :

```php
// MediaController.php
$media = $em->getRepository(Media::class)->findOneBy([
    'id'     => $id,
    'userId' => $user->getId()   // ← userId du JWT, pas du corps de la requête
]);

if (!$media) {
    return $this->json(['error' => 'Média introuvable'], 404);  // 404 et non 403
}
```

> **Pourquoi 404 et non 403 ?** Retourner 403 (Forbidden) confirmerait l'existence de la ressource. Retourner 404 ne révèle rien à un attaquant.

### Vérification du rôle admin

Certains endpoints sont réservés aux administrateurs :

```php
// NotificationController.php
$user = $this->getUser();
if ($user->getRole() !== 'admin') {
    return $this->json(['error' => 'Non autorisé'], 403);
}
```

Routes admin uniquement :

| Route                          | Vérification              |
|--------------------------------|---------------------------|
| `GET /api/notifications`       | `role === 'admin'`        |
| `GET /api/notifications/{id}/read` | `role === 'admin'`    |
| `DELETE /api/notifications/{id}` | `role === 'admin'`      |
| `POST /api/actualites`         | `role === 'admin'`        |
| `DELETE /api/actualites/{id}`  | `role === 'admin'`        |

### Validation des données en entrée

Les controllers valident toutes les données avant de les utiliser :

```php
// AuthController.php — register
if (!$pseudo || !$email || !$motdepasse) {
    return $this->json(['error' => 'Tous les champs sont obligatoires'], 400);
}
if ($motdepasse !== $confirmer) {
    return $this->json(['error' => 'Les mots de passe ne correspondent pas'], 400);
}
if (strlen($motdepasse) < 6) {
    return $this->json(['error' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
}
```

```php
// MediaController.php — add
if ($rating < 1 || $rating > 5) {
    return $this->json(['error' => 'La note doit être entre 1 et 5'], 400);
}
```

---

## 7. Protection CORS

```yaml
# config/packages/nelmio_cors.yaml
nelmio_cors:
    defaults:
        allow_origin:
            - 'http://localhost:5173'
            - 'http://localhost:3000'
            - '%env(FRONTEND_URL)%'          # URL de prod configurée via .env
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        allow_credentials: false
        max_age: 3600
    paths:
        '^/api/': ~
```

### Ce que CORS protège

CORS (Cross-Origin Resource Sharing) empêche les sites tiers d'appeler l'API depuis un navigateur.

**Sans CORS**, un site malveillant `evil.com` pourrait exécuter depuis le navigateur de la victime :
```js
fetch('http://localhost:8080/api/media', {
    headers: { Authorization: `Bearer ${tokenVolé}` }
})
```

**Avec NelmioCorsBundle**, le navigateur refuse la réponse si l'origine (`evil.com`) n'est pas dans la liste blanche. Le serveur répond aux requêtes `OPTIONS` (preflight) avec les en-têtes CORS appropriés.

| Header de réponse              | Valeur                                    |
|-------------------------------|-------------------------------------------|
| `Access-Control-Allow-Origin` | `http://localhost:5173` (ou FRONTEND_URL) |
| `Access-Control-Allow-Methods`| GET, POST, PUT, DELETE, OPTIONS, PATCH    |
| `Access-Control-Allow-Headers`| Content-Type, Authorization               |
| `Access-Control-Max-Age`      | 3600 (preflight mis en cache 1h)          |

> `allow_credentials: false` est intentionnel : l'API JWT n'utilise pas de cookies, donc il n'y a pas de credentials à autoriser.

---

## 8. Doctrine ORM et la sécurité SQL

### Réponse directe

> **Doctrine ORM ne gère pas la sécurité applicative** (authentification, autorisation, rôles). Ces responsabilités appartiennent à Symfony Security.
>
> En revanche, Doctrine **protège passivement contre les injections SQL** grâce aux requêtes préparées PDO.

### Comment Doctrine protège contre les injections SQL

Toutes les requêtes DQL (Doctrine Query Language) utilisent des paramètres liés (`setParameter`) qui sont **automatiquement échappés par PDO** :

```php
// MediaController.php — SAFE : paramètre lié
$qb->andWhere('m.title LIKE :search')
   ->setParameter('search', '%' . $search . '%');

// AccueilController.php — SAFE : paramètre lié
$yearStats = $em->createQuery('SELECT ... FROM App\Entity\Media m WHERE m.userId = :uid')
    ->setParameter('uid', $uid)
    ->getSingleResult();
```

**Même les requêtes SQL natives** utilisent des paramètres liés :

```php
// AccueilController.php — SQL natif, toujours avec paramètres
$conn->executeQuery($sql, ['year' => $year, 'uid' => $uid]);
```

### Ce que PDO garantit techniquement

Quand on écrit :
```php
->setParameter('search', $valeurUtilisateur)
```

PDO envoie la requête et la valeur **séparément** au moteur MySQL. Le moteur traite la valeur comme une donnée pure, jamais comme du SQL. Même si `$valeurUtilisateur` contient `'; DROP TABLE media; --`, MySQL ne l'interprète pas comme une instruction.

### Exemple d'attaque bloquée

```
Utilisateur malveillant saisit dans le champ recherche :
  %'; DROP TABLE media; --

Requête construite par Doctrine (simplifiée) :
  SELECT m FROM Media m WHERE m.title LIKE ?

Valeur envoyée à MySQL séparément :
  %'; DROP TABLE media; --   ← traité comme une chaîne, pas comme du SQL ✓

Résultat : aucun résultat, pas d'injection.
```

---

## 9. Ce que Doctrine NE gère PAS

| Responsabilité de sécurité          | Géré par                       | Doctrine impliqué ? |
|-------------------------------------|--------------------------------|---------------------|
| Authentification (qui es-tu ?)      | Symfony Security + LexikJWT    | Non                 |
| Autorisation (as-tu le droit ?)     | Controllers (ownership check)  | Non                 |
| Hashage des mots de passe           | Symfony PasswordHasher (bcrypt)| Non                 |
| Vérification du token JWT           | LexikJWT + Symfony Firewall    | Non                 |
| Contrôle des rôles                  | Symfony access_control         | Non                 |
| Restriction CORS                    | NelmioCorsBundle               | Non                 |
| Protection injection SQL            | PDO (via Doctrine)             | **Oui (passif)**    |

**Doctrine est un ORM** (Object-Relational Mapper). Son rôle est de faire le lien entre les objets PHP et la base de données. La sécurité est une préoccupation transversale gérée par d'autres couches.

---

## 10. Tableau récapitulatif

| Menace                  | Protection mise en place              | Composant Symfony          |
|-------------------------|---------------------------------------|----------------------------|
| Accès sans authentification | Token JWT obligatoire sur `/api/*` | Symfony Security + LexikJWT |
| Vol de mot de passe (fuite DB) | bcrypt coût 12 (hash irréversible) | PasswordHasher              |
| Token forgé ou expiré   | Signature RSA 4096 bits vérifiée     | LexikJWT (RS256)           |
| Accès aux données d'autrui | `findOneBy(['userId' => JWT.sub])`  | Controllers (ownership)     |
| Action admin sans droits | `if role !== 'admin' → 403`          | Controllers                 |
| Injection SQL           | Requêtes préparées PDO               | Doctrine ORM (passif)       |
| Appel depuis un domaine tiers | Liste blanche d'origines CORS    | NelmioCorsBundle            |
| Session hijacking       | Pas de session (stateless JWT)       | Firewall `stateless: true`  |
| Durée de vie du token   | Expiration à 2 heures (`token_ttl`) | LexikJWT                   |

---

## Points d'amélioration possibles

| Amélioration                   | Impact                                         |
|-------------------------------|------------------------------------------------|
| Refresh token                 | Renouveler le JWT sans se reconnecter          |
| Rate limiting sur `/api/auth/login` | Bloquer les attaques par force brute    |
| Validation Symfony `@Assert`  | Centraliser la validation hors controllers     |
| Révocation de token (blacklist)| Invalider un JWT avant son expiration          |
| HTTPS uniquement              | Chiffrement du transport (TLS)                 |
| Audit log                     | Tracer les connexions et actions sensibles     |
