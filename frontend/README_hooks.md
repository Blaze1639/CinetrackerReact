# Hooks React : useEffect, useState, map, etc.

## useState
Permet de créer et gérer une variable d’état dans un composant React. Quand la valeur change, le composant se réaffiche.

**Exemple :**
```js
const [compteur, setCompteur] = useState(0);
```

## useEffect
Permet d’exécuter du code après l’affichage du composant (par exemple, pour charger des données ou écouter un événement). Peut aussi s’exécuter à chaque changement d’une variable.

**Exemple :**
```js
useEffect(() => {
  // Code à exécuter
}, [compteur]); // S’exécute quand compteur change
```

## map
Permet de parcourir un tableau et de transformer chaque élément (souvent utilisé pour afficher une liste d’éléments en React).

**Exemple :**
```js
const liste = [1, 2, 3];
liste.map((item) => <div>{item}</div>);
```

---

Ces outils sont essentiels pour gérer l’état, les effets de bord et l’affichage dynamique dans une application React.