import '../styles/inscription_connexion.css'
import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { useApi } from '../services/api'
import Footer from '../components/Footer'

export default function Auth({ mode }) {
  const { login } = useAuth()
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({ pseudo:'', email:'', motdepasse:'', confirmer_motdepasse:'' })
  const [error, setError] = useState('')
  const isLogin = mode === 'login'

  const handle = async (e) => {
    e.preventDefault()
    setError('')
    const fn = isLogin ? api.auth.login : api.auth.register
    try {
      const data = await fn(form)
      if (data.success) { login(data); navigate('/accueil') }
      else setError(data.error || 'Erreur')
    } catch { setError('Erreur de connexion au serveur') }
  }

  const f = (k) => ({ value: form[k], onChange: e => setForm(p => ({...p, [k]: e.target.value})) })

  return (
    <div style={{ minHeight:'100vh', background:'#0a0a0a', display:'flex', alignItems:'center', justifyContent:'center', padding:'24px' }}>
      <div className={isLogin ? 'login-container' : 'inscription-container'}>
        <div className="logo">CINÉTRACKER</div>
        <h1>{isLogin ? 'Connexion' : 'Créer un compte'}</h1>

        {error && <div className="error">{error}</div>}

        <form onSubmit={handle}>
          {!isLogin && (
            <>
              <label>Pseudo</label>
              <input type="text" placeholder="Choisissez votre pseudo" required {...f('pseudo')} />
            </>
          )}
          <label>Email</label>
          <input type="email" placeholder="votre@email.com" required {...f('email')} />
          <label>Mot de passe</label>
          <input type="password" placeholder="Votre mot de passe" required {...f('motdepasse')} />
          {!isLogin && (
            <>
              <label>Confirmer le mot de passe</label>
              <input type="password" placeholder="Confirmez votre mot de passe" required {...f('confirmer_motdepasse')} />
            </>
          )}
          <button type="submit">{isLogin ? 'Se connecter' : "S'inscrire"}</button>
        </form>

        <div className="divider" />
        <div className="links">
          {isLogin
            ? <><Link to="/inscription">Créer un compte</Link><Link to="/">← Retour à l'accueil</Link></>
            : <><Link to="/connexion">J'ai déjà un compte</Link><Link to="/">← Retour à l'accueil</Link></>
          }
        </div>
      </div>
      <Footer />
    </div>
  )
}
