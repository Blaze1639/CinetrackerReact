import '../styles/modifier_a_voir.css'
import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import { useApi } from '../services/api'

export default function EditWatchlist() {
  const { id } = useParams()
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({title:'',type_media:'film',image_url:''})

  useEffect(() => {
    api.watchlist.getAll().then(d => {
      if (d.success) { const m=d.items.find(i=>i.id==id); if(m) setForm({title:m.title,type_media:m.type_media,image_url:m.image_url||''}) }
    })
  }, [id])

  const handle = async (e) => {
    e.preventDefault()
    await api.watchlist.update({media_id:id,...form})
    navigate('/liste-a-voir')
  }

  return (
    <>
      <Navbar />
      <h1>Modifier un film ou une série à voir</h1>
      <form onSubmit={handle}>
        <label>Titre :</label><input type="text" value={form.title} onChange={e=>setForm(p=>({...p,title:e.target.value}))} required />
        <label>Type :</label>
        <select value={form.type_media} onChange={e=>setForm(p=>({...p,type_media:e.target.value}))}>
          <option value="film">Film</option><option value="série">Série</option>
        </select>
        <label>Image URL :</label><input type="text" value={form.image_url} onChange={e=>setForm(p=>({...p,image_url:e.target.value}))} />
        <button type="submit">💾 Enregistrer</button>
        <a href="#" onClick={()=>navigate('/liste-a-voir')}>⟵ Retour</a>
      </form>
    </>
  )
}
