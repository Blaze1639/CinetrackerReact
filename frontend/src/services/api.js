// Service API centralisé — backend Symfony JWT
const BASE = import.meta.env.VITE_API_URL || 'https://cinetrack-backend-di8b.onrender.com'

export function createApi(apiFetch) {
  const post = (url, body) =>
    apiFetch(BASE + url, { method: 'POST', body: JSON.stringify(body) }).then(r => r.json())

  const put = (url, body) =>
    apiFetch(BASE + url, { method: 'PUT', body: JSON.stringify(body) }).then(r => r.json())

  const del = (url, body) =>
    apiFetch(BASE + url, { method: 'DELETE', body: body ? JSON.stringify(body) : undefined }).then(r => r.json())

  const get = (url) => apiFetch(BASE + url).then(r => r.json())

  return {
    auth: {
      session:  ()     => get('/api/auth/me'),
      login:    (body) => apiFetch(BASE + '/api/auth/login', { method: 'POST', body: JSON.stringify(body) }).then(r => r.json()),
      register: (body) => apiFetch(BASE + '/api/auth/register', { method: 'POST', body: JSON.stringify(body) }).then(r => r.json()),
      logout:   ()     => get('/api/auth/logout'),
      profile:  ()     => get('/api/profile'),
      delete:   ()     => post('/api/delete', {}),
    },
    media: {
      getAll:           (params = {}) => get('/api/media?' + new URLSearchParams(params)),
      add:              (body)        => post('/api/media', body),
      update:           (body)        => put('/api/media/' + body.media_id, body),
      remove:           (id)          => del('/api/media/' + id),
      toggleFavorite:   (id)          => post('/api/media/' + id + '/favorite', {}),
      incrementCounter: (id)          => post('/api/media/' + id + '/increment', {}),
    },
    watchlist: {
      getAll:  (params = {}) => get('/api/watchlist?' + new URLSearchParams(params)),
      add:     (body)        => post('/api/watchlist', body),
      update:  (body)        => put('/api/watchlist/' + body.media_id, body),
      remove:  (id)          => del('/api/watchlist/' + id),
      move:    (body)        => post('/api/watchlist/' + body.media_id + '/move', body),
    },
    accueil: {
      get: (year) => get(`/api/accueil?year=${year}`),
    },
    notifications: {
      getAll:   ()     => get('/api/notifications'),
      markRead: (id)   => get(`/api/notifications/${id}/read`),
      remove:   (id)   => del('/api/notifications/' + id),
      send:     (body) => post('/api/notifications', body),
    },
    actualites: {
      add:    (body) => post('/api/actualites', body),
      remove: (id)   => del('/api/actualites/' + id),
    },
  }
}

import { useAuth } from '../context/AuthContext'
export function useApi() {
  const { apiFetch } = useAuth()
  return createApi(apiFetch)
}
