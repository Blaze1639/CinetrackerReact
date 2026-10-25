import { useEffect, useState } from 'react'
import { useApi } from '../services/api'

const IMAGE_BASE = 'https://image.tmdb.org/t/p/w500'

export default function TmdbSearch({ onSelect }) {
  const api = useApi()
  const [query, setQuery] = useState('')
  const [results, setResults] = useState([])
  const [status, setStatus] = useState('idle')
  const [selected, setSelected] = useState(null)

  useEffect(() => {
    const normalizedQuery = query.trim()
    if (normalizedQuery.length < 2) {
      setResults([])
      setStatus('idle')
      return undefined
    }

    const controller = new AbortController()
    setStatus('loading')
    const timer = setTimeout(async () => {
      try {
        const data = await api.tmdb.search(normalizedQuery, { signal:controller.signal })
        if (!data.success) throw new Error(data.error || 'La recherche est indisponible.')
        setResults(data.results || [])
        setStatus(data.results?.length ? 'success' : 'empty')
      } catch (error) {
        if (error.name !== 'AbortError') setStatus('error')
      }
    }, 350)

    return () => {
      clearTimeout(timer)
      controller.abort()
    }
  }, [api, query])

  const selectItem = (item) => {
    setSelected(item)
    onSelect({
      title:    item.title || item.name,
      imageUrl: item.poster_path ? IMAGE_BASE + item.poster_path : '',
      type:     item.media_type === 'movie' ? 'film' : 'série'
    })
  }

  return (
    <div className="search-container">
      <label>Rechercher un film ou une série :</label>
      <div className="search-row">
        <input type="text" value={query}
          onChange={e => setQuery(e.target.value)}
          onKeyDown={e => e.key === 'Enter' && e.preventDefault()}
          placeholder="Entrez au moins 2 caractères..."
          aria-label="Rechercher un film ou une série" />
      </div>

      {status === 'loading' && <div className="loading" role="status">Recherche en cours...</div>}
      {status === 'error' && <div className="search-feedback search-error" role="alert">La recherche a échoué. Vérifiez votre connexion et réessayez.</div>}
      {status === 'empty' && <div className="search-feedback" role="status">Aucun film ou série trouvé. Vous pouvez saisir le titre manuellement.</div>}

      {status === 'success' && (
        <div className="search-results">
          {results.map(item => {
            const title = item.title
            const year = item.year
            const imageUrl = item.poster_path ? IMAGE_BASE + item.poster_path : ''
            return (
              <button key={`${item.media_type}:${item.id}`} type="button"
                className={`result-item${selected?.id === item.id && selected?.media_type === item.media_type ? ' selected' : ''}`}
                onClick={() => selectItem(item)}
                aria-pressed={selected?.id === item.id && selected?.media_type === item.media_type}>
                {imageUrl ? <img src={imageUrl} alt="" loading="lazy" /> : <span className="result-placeholder" aria-hidden="true">{item.media_type === 'movie' ? 'FILM' : 'SÉRIE'}</span>}
                <span className="result-details">
                  <span className="title">{title}</span>
                  <span className="result-meta">{item.media_type === 'movie' ? 'Film' : 'Série'}{year ? ` · ${year}` : ''}</span>
                </span>
              </button>
            )
          })}
        </div>
      )}

      {selected && (
        <div className="selected-preview">
          <p>✓ {selected.title || selected.name} sélectionné</p>
          {selected.poster_path && <img src={IMAGE_BASE + selected.poster_path} alt="" />}
        </div>
      )}
    </div>
  )
}
