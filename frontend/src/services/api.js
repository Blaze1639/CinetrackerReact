// ─────────────────────────────────────────────────────────────
// Service API centralisé avec protection CSRF
// En dev  : les appels /api/... sont proxifiés vers le backend
// En prod : VITE_API_URL pointe vers l'URL Railway du backend
// ─────────────────────────────────────────────────────────────

const BASE = import.meta.env.VITE_API_URL || 'https://cinetrack-backend-di8b.onrender.com'; 
// BASE = '' en dev (proxy Vite)
// BASE = 'https://cinetrack-backend.railway.app' en prod

export function createApi(apiFetch) {
  const post = (url, body) => apiFetch(BASE + url, {
    method: 'POST',
  body: JSON.stringify(body)
  }).then(r => r.json())

  console.log("ENV:", import.meta.env);
console.log("API:", import.meta.env.VITE_API_URL);

  const get = (url) => apiFetch(BASE + url).then(r => r.json())

  return {
    auth: {
      session:   ()     => get('/api/session.php'),
      login:     (body) => apiFetch(BASE + '/api/connexion.php',   { method: 'POST', body: JSON.stringify(body) }).then(r => r.json()),
      register:  (body) => apiFetch(BASE + '/api/inscription.php', { method: 'POST', body: JSON.stringify(body) }).then(r => r.json()),
      logout:    ()     => get('/api/deconnexion.php'),
      profile:   ()     => get('/api/profile.php'),
      delete:    ()     => post('/api/delete.php', {}),
    },
    media: {
      getAll:           (params = {}) => get('/api/media.php?' + new URLSearchParams(params)),
      add:              (body)        => post('/api/ajouter_film.php', body),
      update:           (body)        => post('/api/modifier_film.php', body),
      remove:           (id)          => post('/api/supprimer_film.php', { media_id: id }),
      toggleFavorite:   (id)          => post('/api/basculer_favori.php', { media_id: id }),
      incrementCounter: (id)          => post('/api/incrementer_compteur.php', { media_id: id }),
    },
    watchlist: {
      getAll:  (params = {}) => get('/api/liste_a_voir.php?' + new URLSearchParams(params)),
      add:     (body)        => post('/api/ajouter_a_voir.php', body),
      update:  (body)        => post('/api/modifier_a_voir.php', body),
      remove:  (id)          => post('/api/supprimer_a_voir.php', { media_id: id }),
      move:    (body)        => post('/api/deplacement.php', body),
    },
    accueil: {
      get: (year) => get(`/api/accueil.php?year=${year}`),
    },
    notifications: {
      getAll:   ()     => get('/api/notifications.php'),
      markRead: (id)   => get(`/api/marquer_lu.php?id=${id}`),
      remove:   (id)   => post('/api/supprimer_notif.php', { id }),
      send:     (body) => post('/api/env_notif.php', body),
    },
    actualites: {
      add:    (body) => post('/api/ajouter_actu.php', body),
      remove: (id)   => post('/api/supprimer_actu.php', { id }),
    },
  }
}

import { useAuth } from '../context/AuthContext'
export function useApi() {
  const { apiFetch } = useAuth()
  return createApi(apiFetch)
}
