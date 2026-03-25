import '../styles/formulaire_a_voir.css'
import '../styles/ajouter_a_voir.css'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import TmdbSearch from '../components/TmdbSearch'
import { useApi } from '../services/api'

export default function AddWatchlist() {
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({ title:'', image_url:'', type_media:'film' })
  const [msg, setMsg] = useState(null)

  const handleSelect = ({ title, imageUrl, type }) =>
    setForm(p => ({ ...p, title, image_url: imageUrl, type_media: type }))

  const handle = async (e) => {
    e.preventDefault()
    const data = await api.watchlist.add(form)
    if (data.success) { setMsg(data.message); setTimeout(() => navigate('/liste-a-voir'), 2500) }
  }

  if (msg) return (
    <>
      <Navbar />
      <div className="message-box">
        <h1>✓ {msg}</h1>
        <p>Redirection vers votre liste...</p>
        <div className="progress-bar-bg"><div className="progress-bar-fill" /></div>
      </div>
    </>
  )

  return (
    <>
      <Navbar />
      <div className="form-container" style={{ maxWidth: 860 }}>
        <h2>Ajouter un film ou une série à voir</h2>
        <TmdbSearch onSelect={handleSelect} />
        <form onSubmit={handle}>
          <label>Titre du film ou de la série :</label>
          <input type="text" value={form.title} onChange={e => setForm(p => ({...p, title:e.target.value}))} placeholder="Entrez le titre du film" required />
          <label>Lien de l'image (optionnel) :</label>
          <input type="url" value={form.image_url} placeholder="URL de l'image (sélectionnée automatiquement)" readOnly />
          <label>Type :</label>
          <select value={form.type_media} onChange={e => setForm(p => ({...p, type_media:e.target.value}))}>
            <option value="film">Film</option>
            <option value="série">Série</option>
          </select>
          <button type="submit">Ajouter à ma liste</button>
        </form>
      </div>
    </>
  )
}
