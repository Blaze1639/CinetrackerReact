import { createContext, useContext, useState, useEffect, useRef } from 'react'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)
  const csrfToken = useRef('')

  useEffect(() => {
    fetch('/api/session.php', { credentials: 'include' })
      .then(r => r.json())
      .then(data => {
        // Stocker le token CSRF dès le démarrage
        if (data.csrf_token) csrfToken.current = data.csrf_token
        if (data.user_id) setUser(data)
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const login = (userData) => {
    // Mettre à jour le token CSRF après connexion
    if (userData.csrf_token) csrfToken.current = userData.csrf_token
    setUser(userData)
  }

  const logout = () => {
    fetch('/api/deconnexion.php', { credentials: 'include' })
      .finally(() => {
        csrfToken.current = ''
        setUser(null)
      })
  }

  // Helper pour faire des requêtes POST sécurisées avec le token CSRF
  const apiFetch = (url, options = {}) => {
    return fetch(url, {
      ...options,
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken.current,
        ...(options.headers || {}),
      },
    })
  }

  return (
    <AuthContext.Provider value={{ user, login, logout, loading, apiFetch, csrfToken }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)
