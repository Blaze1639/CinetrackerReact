import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

export default function Navbar() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [addOpen, setAddOpen] = useState(false)
  const [listOpen, setListOpen] = useState(false)

  const handleLogout = () => {
    logout()
    navigate('/')
  }

  return (
    <div className="navbar">
      <Link to="/">Home</Link>
      <Link to="/accueil">Accueil</Link>
      <Link to="/aleatoire">Film/Série Aléatoire</Link>

      <div className="dropdown" onMouseEnter={() => setAddOpen(true)} onMouseLeave={() => setAddOpen(false)}>
        <button className="dropbtn">Ajouter ▼</button>
        {addOpen && (
          <div className="dropdown-content">
            <Link to="/ajouter">Ajouter un film ou une série</Link>
            <Link to="/ajouter-a-voir">Film ou série à voir</Link>
          </div>
        )}
      </div>

      <div className="dropdown" onMouseEnter={() => setListOpen(true)} onMouseLeave={() => setListOpen(false)}>
        <button className="dropbtn">Listes ▼</button>
        {listOpen && (
          <div className="dropdown-content">
            <Link to="/index">Liste des films ou séries</Link>
            <Link to="/liste-a-voir">Liste à voir</Link>
          </div>
        )}
      </div>

      <Link to="/profile" className="profile-link" title="Mon profil">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </Link>

      <button onClick={handleLogout} style={{ background: 'none', border: 'none', color: 'white', cursor: 'pointer', padding: '12px 18px', fontSize: 16, fontWeight: 'bold' }}>
        Déconnexion
      </button>
    </div>
  )
}
