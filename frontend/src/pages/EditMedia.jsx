import '../styles/modifier_film.css'
import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import StarRating from '../components/StarRating'
import { useApi } from '../services/api'

export default function EditMedia() {
  const { id } = useParams()
  const navigate = useNavigate()
  const api = useApi()
  const [form, setForm] = useState({title:'',type_media:'film',image_url:'',rating:'',commentaire:''})
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    api.media.getAll({id}).then(d => {
      if (d.success && d.media[0]) {
        const media = d.media[0]
        setForm({title:media.title,type_media:media.type_media,image_url:media.image_url||'',rating:Number(media.rating),commentaire:media.commentaire||''})
      } else {
        setError('Ce média est introuvable.')
      }
    }).catch(() => setError('Impossible de charger ce média.')).finally(() => setLoading(false))
  }, [id])

  const handle = async (e) => {
    e.preventDefault()
    setSaving(true)
    setError('')
    try {
      const result = await api.media.update({media_id:id,...form})
      if (!result.success) throw new Error(result.error || 'La modification a échoué.')
      navigate('/index')
    } catch (err) {
      setError(err.message || 'Impossible d’enregistrer les modifications.')
      setSaving(false)
    }
  }

  return (
    <>
      <Navbar />
      <main className="edit-media-page">
        <button type="button" className="edit-back-link" onClick={() => navigate('/index')}>← Ma collection</button>
        <header className="edit-page-heading">
          <span>COLLECTION · MODIFICATION</span>
          <h1>Modifier le média</h1>
          <p>Mettez à jour les informations et votre avis.</p>
        </header>

        {loading ? (
          <div className="edit-state">Chargement du média...</div>
        ) : form.title ? (
          <div className="edit-media-panel">
            <aside className="edit-media-preview">
              {form.image_url
                ? <img src={form.image_url} alt={`Affiche de ${form.title}`} />
                : <div className="edit-poster-placeholder">Affiche non disponible</div>}
              <span className={`edit-type-badge ${form.type_media === 'série' ? 'series' : ''}`}>
                {form.type_media === 'série' ? 'Série' : 'Film'}
              </span>
              <h2>{form.title}</h2>
            </aside>

            <form className="edit-media-form" onSubmit={handle}>
              {error && <div className="edit-error" role="alert">{error}</div>}
              <div className="edit-fields-row">
                <div className="edit-field edit-title-field">
                  <label htmlFor="edit-title">Titre</label>
                  <input id="edit-title" type="text" value={form.title} onChange={e=>setForm(p=>({...p,title:e.target.value}))} required />
                </div>
                <div className="edit-field edit-type-field">
                  <label htmlFor="edit-type">Type</label>
                  <select id="edit-type" value={form.type_media} onChange={e=>setForm(p=>({...p,type_media:e.target.value}))}>
                    <option value="film">Film</option>
                    <option value="série">Série</option>
                  </select>
                </div>
              </div>

              <div className="edit-field">
                <label htmlFor="edit-image">URL de l’affiche</label>
                <input id="edit-image" type="url" value={form.image_url} onChange={e=>setForm(p=>({...p,image_url:e.target.value}))} placeholder="https://..." />
              </div>

              <fieldset className="edit-rating-field">
                <legend>Votre note</legend>
                <StarRating value={form.rating} onChange={rating=>setForm(p=>({...p,rating}))} />
                <span>{form.rating ? `${form.rating} / 5` : 'Aucune note'}</span>
              </fieldset>

              <div className="edit-field edit-comment-field">
                <label htmlFor="edit-comment">Commentaire</label>
                <textarea id="edit-comment" value={form.commentaire} onChange={e=>setForm(p=>({...p,commentaire:e.target.value}))} rows="5" placeholder="Votre avis sur ce média..." />
              </div>

              <div className="edit-form-actions">
                <button type="button" className="edit-cancel-button" onClick={() => navigate('/index')}>Annuler</button>
                <button type="submit" className="edit-save-button" disabled={saving}>
                  {saving ? 'Enregistrement...' : 'Enregistrer les modifications'}
                </button>
              </div>
            </form>
          </div>
        ) : (
          <div className="edit-state edit-state-error" role="alert">{error || 'Ce média est introuvable.'}</div>
        )}
      </main>
      <Footer />
    </>
  )
}
