# Concepts clés à connaître en React

## Props
Les props sont des données envoyées d’un composant parent à un composant enfant. Elles permettent de personnaliser le comportement ou l’affichage d’un composant.

**Exemple :**
```js
<MyComponent titre="Bonjour" />
```

## Context
Le Context permet de partager des données globales (comme un thème ou un utilisateur) à travers toute l’application, sans avoir à passer les props à chaque niveau.

**Exemple :**
```js
const ThemeContext = React.createContext();
```

## Router
Le Router (ex : react-router) permet de gérer la navigation entre différentes pages ou vues dans une application React.

**Exemple :**
```js
<Route path="/profile" element={<Profile />} />
```

## Lifting State Up
Remonter l’état dans un composant parent pour le partager entre plusieurs enfants. Cela permet de synchroniser des données entre plusieurs composants.

## Controlled/Uncontrolled Components
- Contrôlé : Le formulaire est géré par l’état React.
- Non contrôlé : Le formulaire est géré directement par le DOM.

## Memoization (React.memo, useMemo, useCallback)
Optimisation pour éviter des calculs ou rendus inutiles.
- `React.memo` : Empêche le re-rendu d’un composant si ses props n’ont pas changé.
- `useMemo` : Mémorise une valeur calculée.
- `useCallback` : Mémorise une fonction.

## Custom Hooks
Fonctions personnalisées qui utilisent les hooks React pour réutiliser de la logique dans plusieurs composants.

---

Ces notions sont essentielles pour structurer, optimiser et faire évoluer une application React moderne.