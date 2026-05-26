import '../styles/modifier_film.css'
import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import { useApi } from '../services/api'

export default function EditMedia() {
  const { id } = useParams()
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({title:'',type_media:'film',image_url:'',rating:'',commentaire:''})

  useEffect(() => {
    api.media.getAll({id}).then(d => { if(d.success && d.media[0]){ const m=d.media[0]; setForm({title:m.title,type_media:m.type_media,image_url:m.image_url||'',rating:m.rating,commentaire:m.commentaire||''}) } })
  }, [id])

  const handle = async (e) => {
    e.preventDefault()
    await api.media.update({media_id:id,...form})
    navigate('/index')
  }

  return (
    <>
      <Navbar />
      <h1>Modifier un film ou une série</h1>
      <form onSubmit={handle}>
        <label>Titre :</label><input type="text" value={form.title} onChange={e=>setForm(p=>({...p,title:e.target.value}))} required />
        <label>Type :</label>
        <select value={form.type_media} onChange={e=>setForm(p=>({...p,type_media:e.target.value}))}>
          <option value="film">Film</option><option value="série">Série</option>
        </select>
        <label>Image URL :</label><input type="text" value={form.image_url} onChange={e=>setForm(p=>({...p,image_url:e.target.value}))} />
        <label>Note (1-5) :</label><input type="number" min="1" max="5" value={form.rating} onChange={e=>setForm(p=>({...p,rating:e.target.value}))} required />
        <label>Commentaire :</label><textarea value={form.commentaire} onChange={e=>setForm(p=>({...p,commentaire:e.target.value}))} />
        <button type="submit">💾 Enregistrer</button>
        <a href="#" onClick={()=>navigate('/index')}>⟵ Retour</a>
      </form>
      <Footer />
    </>
  )
}
