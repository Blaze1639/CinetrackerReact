import { lazy, Suspense } from 'react'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './context/AuthContext'

// Chaque page est téléchargée seulement quand on y va : premier chargement plus léger.
const Home = lazy(() => import('./pages/Home'))
const Auth = lazy(() => import('./pages/Auth'))
const Accueil = lazy(() => import('./pages/Accueil'))
const Aleatoire = lazy(() => import('./pages/Aleatoire'))
const MediaList = lazy(() => import('./pages/MediaList'))
const AddMedia = lazy(() => import('./pages/AddMedia'))
const EditMedia = lazy(() => import('./pages/EditMedia'))
const Watchlist = lazy(() => import('./pages/Watchlist'))
const AddWatchlist = lazy(() => import('./pages/AddWatchlist'))
const EditWatchlist = lazy(() => import('./pages/EditWatchlist'))
const Profile = lazy(() => import('./pages/Profile'))
const MentionsLegales = lazy(() => import('./pages/MentionsLegales'))

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
        <Suspense fallback={<div className="loading">Chargement...</div>}>
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
        </Suspense>
      </BrowserRouter>
    </AuthProvider>
  )
}
