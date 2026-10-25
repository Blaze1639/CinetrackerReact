import '../styles/liste_a_voir.css'
import '../styles/evaluer_film.css'
import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import StarRating from '../components/StarRating'
import { useApi } from '../services/api'

export default function Watchlist() {
  const navigate = useNavigate()
  const api = useApi()
  const [items, setItems] = useState([])
  const [filters, setFilters] = useState({ type:'', search:'' })
  const [modal, setModal] = useState(null)
  const [rating, setRating] = useState(0)
  const [commentaire, setCommentaire] = useState('')

  const fetchItems = (f = filters) => {
    const params = Object.fromEntries(Object.entries(f).filter(([,v]) => v))
    api.watchlist.getAll(params).then(d => { if (d.success) setItems(d.items) })
  }

  useEffect(() => { fetchItems() }, [])

  const setFilter = (k, v) => { const nf = {...filters, [k]:v}; setFilters(nf); fetchItems(nf) }
  // Retrait local immédiat : pas besoin de recharger toute la liste.
  const removeLocally = (id) => setItems(prev => prev.filter(i => i.id !== id))

  const deleteItem = (id) => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) return
    removeLocally(id)
    api.watchlist.remove(id).then(d => { if (!d.success) fetchItems() })
  }

  const incrementExisting = async (item) => {
    const data = await api.watchlist.move({ media_id:item.id, title:item.title, type_media:item.type_media, increment_if_exists:true })
    if (data.success) removeLocally(item.id)
    else alert(data.error || 'Impossible de mettre à jour ce média.')
  }

  const askIncrement = (item) => {
    if (confirm(`${item.title} est déjà dans votre liste. Voulez-vous incrémenter le nombre de visionnages et le retirer de la liste à voir ?`)) {
      return incrementExisting(item)
    }
  }

  const openModal = async (item) => {
    // Vérifie d'abord si le titre est déjà dans « mes films / séries » pour éviter un doublon.
    const found = await api.media.getAll({ search:item.title })
    const alreadySeen = found.success && found.media.some(m =>
      m.title.toLowerCase() === item.title.toLowerCase() && m.type_media === item.type_media)
    if (alreadySeen) return askIncrement(item)

    setModal(item); setRating(0); setCommentaire('')
  }

  const validateRating = async () => {
    if (!rating) return alert('Veuillez sélectionner une note')
    const item = modal
    const data = await api.watchlist.move({ media_id:item.id, title:item.title, type_media:item.type_media, image_url:item.image_url, rating, commentaire })
    setModal(null)
    if (data.duplicate) return askIncrement(item)
    if (data.success) removeLocally(item.id)
    else alert(data.error || 'Impossible de déplacer ce média.')
  }

  return (
    <>
      <Navbar />
      <h1>Liste des films ou séries à voir</h1>

      <div className="filter-container">
        <label>Filtrer par type :</label>
        <select value={filters.type} onChange={e => setFilter('type', e.target.value)}>
          <option value="">Tous</option>
          <option value="film">Films</option>
          <option value="série">Séries</option>
        </select>
        <label>Rechercher :</label>
        <input type="text" placeholder="Entrez un titre..." value={filters.search}
          onChange={e => setFilter('search', e.target.value)} />
      </div>

      <div className="media-container">
        {items.length === 0
          ? <p className="empty-message">Aucun média à voir pour le moment. Ajoutez-en un !</p>
          : items.map(m => (
            <div key={m.id} className="card">
              <span className={`type ${m.type_media}`}>{m.type_media}</span>
              <h2>{m.title}</h2>
              {m.image_url && <img src={m.image_url} alt={m.title} />}
              <span className="date-added">Ajouté le : {new Date(m.added_date).toLocaleDateString('fr-FR')}</span>
              <div className="button-group">
                <button className="btn-add" onClick={() => openModal(m)}>Vu</button>
                <button className="btn-edit" onClick={() => navigate(`/modifier-a-voir/${m.id}`)}>Modifier</button>
                <button className="btn-delete" onClick={() => deleteItem(m.id)}>Supprimer</button>
              </div>
            </div>
          ))
        }
      </div>

      {/* Modal notation */}
      {modal && (
        <div className="modal-overlay" onClick={e => e.target === e.currentTarget && setModal(null)}>
          <div className="rating-container">
            <h1>Notez ce film ou cette série</h1>
            <p className="media-title">{modal.title}</p>
            <StarRating value={rating} onChange={setRating} />
            {rating > 0 && <div className="selected-rating">Note sélectionnée : {rating}/5</div>}
            <div style={{ textAlign:'left', marginTop:16 }}>
              <label style={{ display:'block', marginBottom:8, color:'#ccc', fontWeight:600 }}>Commentaire (optionnel) :</label>
              <textarea value={commentaire} onChange={e => setCommentaire(e.target.value)} rows="4"
                placeholder="Ajoutez vos impressions, critiques ou notes personnelles..." />
            </div>
            <button onClick={validateRating} disabled={!rating}>Valider la note</button>
            <button className="cancel-btn" onClick={() => setModal(null)}>Annuler</button>
          </div>
        </div>
      )}
      <Footer />
    </>
  )
}
