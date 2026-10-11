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
  const [items, setItems] = useState([])
  const [searchVersion, setSearchVersion] = useState(0)
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [msg, setMsg] = useState(null)

  const handleSelect = ({ title, imageUrl, type }) =>
    setForm(p => ({ ...p, title, image_url: imageUrl, type_media: type }))

  const addToQueue = () => {
    const title = form.title.trim()
    if (!title) return setMsg({ type:'error', text:'Veuillez renseigner un titre' })
    if (!form.rating) return setMsg({ type:'error', text:'Veuillez sélectionner une note (1-5)' })
    if (items.some(item => item.title.toLocaleLowerCase() === title.toLocaleLowerCase() && item.type_media === form.type_media)) {
      return setMsg({ type:'error', text:'Ce média est déjà dans la sélection' })
    }

    setItems(previous => [...previous, { ...form, title }])
    setForm({ title:'', rating:0, image_url:'', type_media:'film', commentaire:'' })
    setSearchVersion(version => version + 1)
    setMsg(null)
  }

  const handle = async (e) => {
    e.preventDefault()
    if (!items.length) return setMsg({ type:'error', text:'Ajoutez au moins un média à la sélection' })

    setIsSubmitting(true)
    const failed = []
    let addedCount = 0
    let cancelledCount = 0
    for (const item of items) {
      try {
        const data = await api.media.add(item)
        if (data.duplicate) {
          const increment = window.confirm(`${item.title} existe déjà dans votre liste. Voulez-vous incrémenter le nombre de visionnages ?`)
          if (!increment) {
            cancelledCount += 1
            continue
          }

          const incrementResult = await api.media.add({ ...item, increment_if_exists:true })
          if (!incrementResult.success) failed.push({ ...item, error: incrementResult.error || 'Échec de l’incrémentation' })
          else addedCount += 1
        } else if (!data.success) {
          failed.push({ ...item, error: data.error || 'Échec de l’ajout' })
        } else {
          addedCount += 1
        }
      } catch {
        failed.push({ ...item, error:'Erreur réseau, veuillez réessayer' })
      }
    }

    setItems(failed)
    setIsSubmitting(false)
    if (failed.length) {
      setMsg({ type:'error', text:`${addedCount} ajouté(s), ${cancelledCount} annulé(s), ${failed.length} en échec. Les médias en échec restent dans la sélection.` })
    } else if (addedCount === 0) {
      setMsg({ type:'info', text:'Ajout annulé : le doublon n’a pas été ajouté ni incrémenté.' })
    } else {
      setMsg({ type:'success', text:`${addedCount} média(s) ajouté(s)${cancelledCount ? `, ${cancelledCount} doublon(s) annulé(s)` : ''}` })
      setTimeout(() => navigate('/index'), 2500)
    }
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
        {(msg?.type === 'error' || msg?.type === 'info') && <div role="alert" style={{ background:msg.type === 'error' ? 'rgba(229,9,20,0.1)' : 'rgba(255,255,255,0.08)', border:`1px solid ${msg.type === 'error' ? '#e50914' : '#777'}`, color:msg.type === 'error' ? '#e50914' : '#fff', padding:'12px 16px', borderRadius:8, marginBottom:20, fontSize:14 }}>{msg.text}</div>}
        <TmdbSearch key={searchVersion} onSelect={handleSelect} />
        <form onSubmit={handle}>
          <label>Titre du Film ou de la Série :</label>
          <input type="text" value={form.title} onChange={e => setForm(p => ({...p, title:e.target.value}))} placeholder="Entrez le titre du film ou de la série" />
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
          <button className="batch-add-button" type="button" onClick={addToQueue} disabled={isSubmitting}>Ajouter à la sélection</button>
          <div className="batch-queue" aria-live="polite">
            <h3>Sélection ({items.length})</h3>
            {items.length > 0 && <ul>
              {items.map((item, index) => (
                <li className="batch-item" key={`${item.type_media}:${item.title.toLocaleLowerCase()}`}>
                  <div><strong>{item.title}</strong><span>{item.type_media} · Note {item.rating}/5{item.error ? ` · ${item.error}` : ''}</span></div>
                  <button type="button" aria-label={`Retirer ${item.title}`} onClick={() => setItems(previous => previous.filter((_, itemIndex) => itemIndex !== index))} disabled={isSubmitting}>Retirer</button>
                </li>
              ))}
            </ul>}
          </div>
          <button className="batch-submit-button" type="submit" disabled={isSubmitting || items.length === 0}>{isSubmitting ? 'Ajout en cours...' : `Ajouter ${items.length} média(s)`}</button>
        </form>
      </div>
      <Footer />
    </>
  )
}
