import '../styles/formulaire.css'
import '../styles/ajouter_film.css'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import TmdbSearch from '../components/TmdbSearch'
import StarRating from '../components/StarRating'
import { useApi } from '../services/api'

export default function AddMedia() {
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({ title:'', rating:0, image_url:'', type_media:'film', commentaire:'' })
  const [msg, setMsg] = useState(null)

  const handleSelect = ({ title, imageUrl, type }) =>
    setForm(p => ({ ...p, title, image_url: imageUrl, type_media: type }))

  const handle = async (e) => {
    e.preventDefault()
    if (!form.rating) return setMsg({ type:'error', text:'Veuillez sélectionner une note (1-5)' })
    const data = await api.media.add(form)
    if (data.success) { setMsg({ type:'success', text: data.message }); setTimeout(() => navigate('/index'), 2500) }
    else setMsg({ type:'error', text: data.error })
  }

  if (msg?.type === 'success') return (
    <>
      <Navbar />
      <div className="message-box">
        <h1>✓ {msg.text}</h1>
        <p>Redirection vers la liste...</p>
        <div className="progress-bar-bg"><div className="progress-bar-fill" /></div>
      </div>
      <Footer />
    </>
  )

  return (
    <>
      <Navbar />
      <div className="form-container" style={{ maxWidth: 860 }}>
        <h2>Ajouter un Film ou une Série</h2>
        {msg?.type === 'error' && <div style={{ background:'rgba(229,9,20,0.1)', border:'1px solid #e50914', color:'#e50914', padding:'12px 16px', borderRadius:8, marginBottom:20, fontSize:14 }}>{msg.text}</div>}
        <TmdbSearch onSelect={handleSelect} />
        <form onSubmit={handle}>
          <label>Titre du Film ou de la Série :</label>
          <input type="text" value={form.title} onChange={e => setForm(p => ({...p, title:e.target.value}))} placeholder="Entrez le titre du film ou de la série" required />
          <label>Note (1-5) :</label>
          <StarRating value={form.rating} onChange={v => setForm(p => ({...p, rating:v}))} />
          <label>Lien de l'image :</label>
          <input type="url" value={form.image_url} placeholder="URL de l'image (sélectionnée automatiquement)" readOnly />
          <label>Commentaire (optionnel) :</label>
          <textarea value={form.commentaire} onChange={e => setForm(p => ({...p, commentaire:e.target.value}))} rows="4" placeholder="Ajoutez vos impressions" />
          <label>Type :</label>
          <select value={form.type_media} onChange={e => setForm(p => ({...p, type_media:e.target.value}))}>
            <option value="film">Film</option>
            <option value="série">Série</option>
          </select>
          <button type="submit">Ajouter</button>
        </form>
      </div>
      <Footer />
    </>
  )
}
