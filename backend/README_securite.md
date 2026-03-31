# Sécurité Web : Token, CSRF, Hashage, etc.

## Token
Un token est une clé unique générée lors de la connexion d’un utilisateur. Il permet d’identifier l’utilisateur lors de ses prochaines requêtes, sans avoir à se reconnecter. Il est souvent utilisé pour sécuriser les API.

## CSRF (Cross-Site Request Forgery)
Le CSRF est une attaque où un site malveillant fait faire des actions à un utilisateur sur un autre site où il est connecté. Pour se protéger, on utilise des jetons CSRF : un code unique envoyé avec chaque formulaire ou requête, que le serveur vérifie.

## Hashage
Le hashage transforme une donnée (comme un mot de passe) en une suite de caractères illisible. On ne peut pas retrouver la donnée d’origine à partir du hash. On utilise le hashage pour stocker les mots de passe de façon sécurisée.

---

Ces protections sont essentielles pour sécuriser les applications web et protéger les données des utilisateurs.