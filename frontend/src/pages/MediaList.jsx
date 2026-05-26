import '../styles/index.css'
import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import { useApi } from '../services/api'

export default function MediaList() {
  const navigate = useNavigate()
  const api = useApi()
  const [media, setMedia] = useState([])
  const [pages, setPages] = useState(1)
  const [filters, setFilters] = useState({ type:'', year:'', rating:'', search:'', page:1 })
  const currentYear = new Date().getFullYear()

  const fetchMedia = (f = filters) => {
    const params = Object.fromEntries(Object.entries(f).filter(([,v]) => v !== ''))
    api.media.getAll(params).then(d => { if (d.success) { setMedia(d.media); setPages(d.pages) } })
  }

  useEffect(() => { fetchMedia() }, [])

  const setFilter = (k, v) => { const nf = {...filters, [k]:v, page:1}; setFilters(nf); fetchMedia(nf) }
  const setPage   = (p)    => { const nf = {...filters, page:p};       setFilters(nf); fetchMedia(nf) }

  const toggleFav     = (id) => api.media.toggleFavorite(id).then(() => fetchMedia())
  const incrementView = (id) => api.media.incrementCounter(id).then(d => {
    if (d.success) setMedia(prev => prev.map(m => m.id === id ? {...m, view_count: d.view_count} : m))
  })
  const deleteMedia = (id) => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) return
    api.media.remove(id).then(() => fetchMedia())
  }

  const pagesArray = () => {
    if (pages <= 7) return Array.from({length: pages}, (_, i) => i + 1)
    const p = filters.page
    const arr = new Set([1, 2, p - 1, p, p + 1, pages - 1, pages].filter(x => x >= 1 && x <= pages))
    return [...arr].sort((a, b) => a - b)
  }

  return (
    <>
      <Navbar />
      <h1>Liste des médias</h1>

      <div className="filter-container">
        <label>Filtrer par type :</label>
        <select value={filters.type} onChange={e => setFilter('type', e.target.value)}>
          <option value="">Tous</option>
          <option value="film">Films</option>
          <option value="série">Séries</option>
          <option value="favorite">Favoris</option>
        </select>

        <label>Année :</label>
        <select value={filters.year} onChange={e => setFilter('year', e.target.value)}>
          <option value="">Toutes</option>
          {Array.from({length: currentYear - 2019}, (_, i) => 2020 + i).map(y =>
            <option key={y} value={y}>{y}</option>
          )}
        </select>

        <label>Note :</label>
        <select value={filters.rating} onChange={e => setFilter('rating', e.target.value)}>
          <option value="">Toutes</option>
          {[1,2,3,4,5].map(r => <option key={r} value={r}>{r}/5</option>)}
        </select>

        <label>Rechercher :</label>
        <input type="text" placeholder="Entrez un titre..." value={filters.search}
          onChange={e => setFilter('search', e.target.value)}
          onKeyDown={e => e.key === 'Enter' && fetchMedia()} />
      </div>

      <div className="media-container">
        {media.length === 0
          ? <p className="empty-message">Aucun média trouvé.</p>
          : media.map(m => (
            <div key={m.id} className="card">
              <div className="card-header">
                <button className="favorite-icon" onClick={() => toggleFav(m.id)} title={m.favorite ? 'Retirer des favoris' : 'Ajouter aux favoris'}>
                  {m.favorite ? '★' : '☆'}
                </button>
                <span className={`type ${m.type_media}`}>{m.type_media}</span>
              </div>
              <h2>{m.title}</h2>
              {m.image_url && <img src={m.image_url} alt={m.title} />}
              <span className="rating">⭐ {m.rating}/5</span>
              <div className="view-counter">
                <button className="btn-view" onClick={() => incrementView(m.id)}>
                  Vu <span className="view-count">{m.view_count ?? 0}</span> fois
                </button>
              </div>
              {m.commentaire && (
                <div className="comment-section">
                  <strong>Commentaire :</strong>
                  <p className="comment-text">{String(m.commentaire).replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
                </div>
              )}
              <div className="card-actions">
                <button className="btn-edit" onClick={() => navigate(`/modifier/${m.id}`)}>Modifier</button>
                <button className="btn-delete" onClick={() => deleteMedia(m.id)}>Supprimer</button>
              </div>
            </div>
          ))
        }
      </div>

      {pages > 1 && (
        <div className="pagination">
          {filters.page > 1 && (
            <button className="page-link" onClick={() => setPage(filters.page - 1)}>« Précédent</button>
          )}
          {pagesArray().map((p, idx, arr) => (
            <>
              {idx > 0 && arr[idx] - arr[idx-1] > 1 && <span key={`dots-${p}`} className="page-dots">...</span>}
              <button key={p} className={`page-link${p === filters.page ? ' current' : ''}`} onClick={() => setPage(p)}>{p}</button>
            </>
          ))}
          {filters.page < pages && (
            <button className="page-link" onClick={() => setPage(filters.page + 1)}>Suivant »</button>
          )}
        </div>
      )}
      <Footer />
    </>
  )
}
