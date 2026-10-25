import { createContext, useContext, useState, useEffect, useRef, useCallback } from 'react'

const BASE = import.meta.env.VITE_API_URL || 'https://cinetrack-backend-di8b.onrender.com'

const AuthContext = createContext(null)

function readCachedUser() {
  try {
    return JSON.parse(localStorage.getItem('user')) || null
  } catch {
    return null
  }
}

export function AuthProvider({ children }) {
  const tokenRef = useRef(localStorage.getItem('jwt_token') || '')
  // Utilisateur mémorisé : l'app s'affiche tout de suite, sans attendre la réponse de /me.
  const [user, setUser] = useState(() => (tokenRef.current ? readCachedUser() : null))
  const [loading, setLoading] = useState(() => Boolean(tokenRef.current) && !readCachedUser())

  const clearSession = () => {
    tokenRef.current = ''
    localStorage.removeItem('jwt_token')
    localStorage.removeItem('user')
    setUser(null)
  }

  useEffect(() => {
    const token = tokenRef.current
    if (!token) {
      // Réveille le serveur Render pendant que l'utilisateur saisit ses identifiants.
      fetch(BASE + '/api/health').catch(() => {})
      return
    }
    fetch(BASE + '/api/auth/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then(r => r.json())
      .then(data => {
        if (data.user_id) {
          setUser(data)
          localStorage.setItem('user', JSON.stringify(data))
        } else clearSession()
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const login = (userData) => {
    if (userData.token) {
      tokenRef.current = userData.token
      localStorage.setItem('jwt_token', userData.token)
    }
    const { token, ...cachedUser } = userData
    localStorage.setItem('user', JSON.stringify(cachedUser))
    setUser(userData)
  }

  const logout = () => {
    // Déconnexion immédiate côté client ; l'appel serveur se fait en arrière-plan.
    fetch(BASE + '/api/auth/logout', {
      headers: { Authorization: `Bearer ${tokenRef.current}` },
    }).catch(() => {})
    clearSession()
  }

  // Stable function: the token is read from tokenRef at call time.
  const apiFetch = useCallback((url, options = {}) => {
    return fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${tokenRef.current}`,
        ...(options.headers || {}),
      },
    })
  }, [])

  return (
    <AuthContext.Provider value={{ user, login, logout, loading, apiFetch, tokenRef }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)
