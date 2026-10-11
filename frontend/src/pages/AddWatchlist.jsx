import '../styles/formulaire_a_voir.css'
import '../styles/ajouter_a_voir.css'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import TmdbSearch from '../components/TmdbSearch'
import { useApi } from '../services/api'

export default function AddWatchlist() {
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({ title:'', image_url:'', type_media:'film' })
  const [items, setItems] = useState([])
  const [searchVersion, setSearchVersion] = useState(0)
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [msg, setMsg] = useState(null)

  const handleSelect = ({ title, imageUrl, type }) =>
    setForm(p => ({ ...p, title, image_url: imageUrl, type_media: type }))

  const addToQueue = () => {
    const title = form.title.trim()
    if (!title) return setMsg({ type:'error', text:'Veuillez renseigner un titre' })
    if (items.some(item => item.title.toLocaleLowerCase() === title.toLocaleLowerCase() && item.type_media === form.type_media)) {
      return setMsg({ type:'error', text:'Ce média est déjà dans la sélection' })
    }

    setItems(previous => [...previous, { ...form, title }])
    setForm({ title:'', image_url:'', type_media:'film' })
    setSearchVersion(version => version + 1)
    setMsg(null)
  }

  const handle = async (e) => {
    e.preventDefault()
    if (!items.length) return setMsg({ type:'error', text:'Ajoutez au moins un élément à la sélection' })

    setIsSubmitting(true)
    const failed = []
    for (const item of items) {
      try {
        const data = await api.watchlist.add(item)
        if (!data.success) failed.push({ ...item, error: data.error || 'Échec de l’ajout' })
      } catch {
        failed.push({ ...item, error:'Erreur réseau, veuillez réessayer' })
      }
    }

    const addedCount = items.length - failed.length
    setItems(failed)
    setIsSubmitting(false)
    if (failed.length) {
      setMsg({ type:'error', text:`${addedCount} ajouté(s), ${failed.length} en échec. Les éléments en échec restent dans la sélection.` })
    } else {
      setMsg({ type:'success', text:`${addedCount} élément(s) ajouté(s) à votre liste` })
      setTimeout(() => navigate('/liste-a-voir'), 2500)
    }
  }

  if (msg?.type === 'success') return (
    <>
      <Navbar />
      <div className="message-box">
        <h1>✓ {msg.text}</h1>
        <p>Redirection vers votre liste...</p>
        <div className="progress-bar-bg"><div className="progress-bar-fill" /></div>
      </div>
      <Footer />
    </>
  )

  return (
    <>
      <Navbar />
      <div className="form-container" style={{ maxWidth: 860 }}>
        <h2>Ajouter un film ou une série à voir</h2>
        {msg?.type === 'error' && <div role="alert" style={{ background:'rgba(229,9,20,0.1)', border:'1px solid #e50914', color:'#e50914', padding:'12px 16px', borderRadius:8, marginBottom:20, fontSize:14 }}>{msg.text}</div>}
        <TmdbSearch key={searchVersion} onSelect={handleSelect} />
        <form onSubmit={handle}>
          <label>Titre du film ou de la série :</label>
          <input type="text" value={form.title} onChange={e => setForm(p => ({...p, title:e.target.value}))} placeholder="Entrez le titre du film" />
          <label>Lien de l'image (optionnel) :</label>
          <input type="url" value={form.image_url} placeholder="URL de l'image (sélectionnée automatiquement)" readOnly />
          <label>Type :</label>
          <select value={form.type_media} onChange={e => setForm(p => ({...p, type_media:e.target.value}))}>
            <option value="film">Film</option>
            <option value="série">Série</option>
          </select>
          <button className="batch-add-button" type="button" onClick={addToQueue} disabled={isSubmitting}>Ajouter à la sélection</button>
          <div className="batch-queue" aria-live="polite">
            <h3>Sélection ({items.length})</h3>
            {items.length > 0 && <ul>
              {items.map((item, index) => (
                <li className="batch-item" key={`${item.type_media}:${item.title.toLocaleLowerCase()}`}>
                  <div><strong>{item.title}</strong><span>{item.type_media}{item.error ? ` · ${item.error}` : ''}</span></div>
                  <button type="button" aria-label={`Retirer ${item.title}`} onClick={() => setItems(previous => previous.filter((_, itemIndex) => itemIndex !== index))} disabled={isSubmitting}>Retirer</button>
                </li>
              ))}
            </ul>}
          </div>
          <button className="batch-submit-button" type="submit" disabled={isSubmitting || items.length === 0}>{isSubmitting ? 'Ajout en cours...' : `Ajouter ${items.length} élément(s)`}</button>
        </form>
      </div>
      <Footer />
    </>
  )
}
