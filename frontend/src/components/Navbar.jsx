import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Navbar() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [addOpen, setAddOpen] = useState(false)
  const [listOpen, setListOpen] = useState(false)
  const [menuOpen, setMenuOpen] = useState(false)

  const handleLogout = () => {
    logout()
    setMenuOpen(false)
    navigate('/')
  }

  const closeMenu = () => {
    setMenuOpen(false)
    setAddOpen(false)
    setListOpen(false)
  }

  return (
    <div className="navbar">
      <div className="navbar-brand">
        <Link to="/accueil" className="logo">CINÉTRACKER</Link>
        <button
          className={`menu-toggle ${menuOpen ? 'open' : ''}`}
          onClick={() => setMenuOpen(prev => !prev)}
          aria-label={menuOpen ? 'Fermer le menu' : 'Ouvrir le menu'}
          type="button"
        >
          <span />
          <span />
          <span />
        </button>
      </div>

      <div className={`nav-links ${menuOpen ? 'open' : ''}`}>
        <Link to="/accueil" onClick={closeMenu}>Accueil</Link>
        <Link to="/aleatoire" onClick={closeMenu}>Film/Série Aléatoire</Link>

        <div className="dropdown">
          <button className="dropbtn" type="button" aria-expanded={addOpen} aria-controls="add-dropdown" onClick={() => { setAddOpen(prev => !prev); setListOpen(false) }}>
            Ajouter ▼
          </button>
          <div id="add-dropdown" className={`dropdown-content${addOpen ? ' open' : ''}`}>
            <Link to="/ajouter" onClick={closeMenu}>Ajouter un film ou une série</Link>
            <Link to="/ajouter-a-voir" onClick={closeMenu}>Film ou série à voir</Link>
          </div>
        </div>

        <div className="dropdown">
          <button className="dropbtn" type="button" aria-expanded={listOpen} aria-controls="list-dropdown" onClick={() => { setListOpen(prev => !prev); setAddOpen(false) }}>
            Listes ▼
          </button>
          <div id="list-dropdown" className={`dropdown-content${listOpen ? ' open' : ''}`}>
            <Link to="/index" onClick={closeMenu}>Liste des films ou séries</Link>
            <Link to="/liste-a-voir" onClick={closeMenu}>Liste à voir</Link>
          </div>
        </div>

        <Link to="/profile" className="profile-link" title="Mon profil" onClick={closeMenu}>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </Link>

        <button
          onClick={handleLogout}
          className="logout-button"
          type="button"
        >
          Déconnexion
        </button>
      </div>
    </div>
  )
}
