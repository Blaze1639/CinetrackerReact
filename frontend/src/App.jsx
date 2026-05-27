import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './context/AuthContext'

import Home from './pages/Home'
import Auth from './pages/Auth'
import Accueil from './pages/Accueil'
import Aleatoire from './pages/Aleatoire'
import MediaList from './pages/MediaList'
import AddMedia from './pages/AddMedia'
import EditMedia from './pages/EditMedia'
import Watchlist from './pages/Watchlist'
import AddWatchlist from './pages/AddWatchlist'
import EditWatchlist from './pages/EditWatchlist'
import Profile from './pages/Profile'
import MentionsLegales from './pages/MentionsLegales'

function PrivateRoute({ children }) {
  const { user, loading } = useAuth()
  if (loading) return <div className="loading">Chargement...</div>
  return user ? children : <Navigate to="/connexion" />
}

function PublicRoute({ children }) {
  const { user, loading } = useAuth()
  if (loading) return <div className="loading">Chargement...</div>
  return user ? <Navigate to="/accueil" /> : children
}

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/connexion" element={<PublicRoute><Auth mode="login" /></PublicRoute>} />
          <Route path="/inscription" element={<PublicRoute><Auth mode="register" /></PublicRoute>} />
          <Route path="/accueil" element={<PrivateRoute><Accueil /></PrivateRoute>} />
          <Route path="/aleatoire" element={<PrivateRoute><Aleatoire /></PrivateRoute>} />
          <Route path="/index" element={<PrivateRoute><MediaList /></PrivateRoute>} />
          <Route path="/ajouter" element={<PrivateRoute><AddMedia /></PrivateRoute>} />
          <Route path="/modifier/:id" element={<PrivateRoute><EditMedia /></PrivateRoute>} />
          <Route path="/liste-a-voir" element={<PrivateRoute><Watchlist /></PrivateRoute>} />
          <Route path="/ajouter-a-voir" element={<PrivateRoute><AddWatchlist /></PrivateRoute>} />
          <Route path="/modifier-a-voir/:id" element={<PrivateRoute><EditWatchlist /></PrivateRoute>} />
          <Route path="/profile" element={<PrivateRoute><Profile /></PrivateRoute>} />
          <Route path="/mentions-legales" element={<MentionsLegales />} />
          <Route path="*" element={<Navigate to="/" />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  )
}
