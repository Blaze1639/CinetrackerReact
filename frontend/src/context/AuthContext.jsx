import { createContext, useContext, useState, useEffect, useRef } from 'react'

const BASE = import.meta.env.VITE_API_URL || 'https://cinetrack-backend-di8b.onrender.com'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)
  const tokenRef = useRef(localStorage.getItem('jwt_token') || '')

  useEffect(() => {
    const token = tokenRef.current
    if (!token) {
      setLoading(false)
      return
    }
    fetch(BASE + '/api/auth/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then(r => r.json())
      .then(data => {
        if (data.user_id) setUser(data)
        else {
          tokenRef.current = ''
          localStorage.removeItem('jwt_token')
        }
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const login = (userData) => {
    if (userData.token) {
      tokenRef.current = userData.token
      localStorage.setItem('jwt_token', userData.token)
    }
    setUser(userData)
  }

  const logout = () => {
    fetch(BASE + '/api/auth/logout', {
      headers: { Authorization: `Bearer ${tokenRef.current}` },
    }).finally(() => {
      tokenRef.current = ''
      localStorage.removeItem('jwt_token')
      setUser(null)
    })
  }

  const apiFetch = (url, options = {}) => {
    return fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${tokenRef.current}`,
        ...(options.headers || {}),
      },
    })
  }

  return (
    <AuthContext.Provider value={{ user, login, logout, loading, apiFetch, tokenRef }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)
