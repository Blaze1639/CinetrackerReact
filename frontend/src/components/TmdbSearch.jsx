import { useEffect, useState } from 'react'
import { useApi } from '../services/api'
import { isKnown } from '../utils/knownTitles'

const IMAGE_BASE = 'https://image.tmdb.org/t/p/w500'
const TYPE_FILTERS = [
  { value:'all',   label:'Tous' },
  { value:'movie', label:'Films' },
  { value:'tv',    label:'Séries' },
]
const YEAR_SUFFIX = /^(.{2,}?)\s+\(?((?:18|19|20)\d{2})\)?$/

// exclude : ensembles de titres déjà vus / à voir à ne pas proposer.
export default function TmdbSearch({ onSelect, exclude = [] }) {
  const api = useApi()
  const [query, setQuery] = useState('')
  const [type, setType] = useState('all')
  const [year, setYear] = useState('')
  const [results, setResults] = useState([])
  const [status, setStatus] = useState('idle')
  const [selected, setSelected] = useState(null)

  useEffect(() => {
    let normalizedQuery = query.trim()
    let searchYear = /^\d{4}$/.test(year) ? year : ''
    // « Dune 2021 » : l'année tapée dans le titre sert de filtre.
    const yearMatch = !searchYear && normalizedQuery.match(YEAR_SUFFIX)
    if (yearMatch) [, normalizedQuery, searchYear] = yearMatch
    if (normalizedQuery.length < 2) {
      setResults([])
      setStatus('idle')
      return undefined
    }

    const controller = new AbortController()
    setStatus('loading')
    const timer = setTimeout(async () => {
      try {
        const filters = { type }
        if (searchYear) filters.year = searchYear
        const data = await api.tmdb.search(normalizedQuery, filters, { signal:controller.signal })
        if (!data.success) throw new Error(data.error || 'La recherche est indisponible.')
        // Filtre aussi côté client : un backend pas encore à jour ignore type et année.
        const filtered = (data.results || []).filter(item =>
          (type === 'all' || item.media_type === type) && (!searchYear || item.year === searchYear))
        setResults(filtered)
        setStatus('done')
      } catch (error) {
        if (error.name !== 'AbortError') setStatus('error')
      }
    }, 350)

    return () => {
      clearTimeout(timer)
      controller.abort()
    }
  }, [api, query, type, year])

  const visibleResults = results.filter(item => !isKnown(exclude, {
    title: item.title,
    originalTitle: item.original_title,
    typeMedia: item.media_type === 'movie' ? 'film' : 'série',
  }))
  const hiddenCount = results.length - visibleResults.length

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
          placeholder="Ex. : Dune, Inception 2010..."
          aria-label="Rechercher un film ou une série" />
        <input type="text" inputMode="numeric" maxLength={4} className="search-year"
          value={year}
          onChange={e => setYear(e.target.value.replace(/\D/g, ''))}
          onKeyDown={e => e.key === 'Enter' && e.preventDefault()}
          placeholder="Année"
          aria-label="Filtrer par année de sortie" />
      </div>
      <div className="search-filters" role="group" aria-label="Type de média">
        {TYPE_FILTERS.map(filter => (
          <button key={filter.value} type="button"
            className={`search-filter${type === filter.value ? ' active' : ''}`}
            aria-pressed={type === filter.value}
            onClick={() => setType(filter.value)}>
            {filter.label}
          </button>
        ))}
      </div>

      {status === 'loading' && <div className="loading" role="status">Recherche en cours...</div>}
      {status === 'error' && <div className="search-feedback search-error" role="alert">La recherche a échoué. Vérifiez votre connexion et réessayez.</div>}
      {status === 'done' && visibleResults.length === 0 && <div className="search-feedback" role="status">
        {hiddenCount ? 'Tous les résultats sont déjà dans vos listes.' : 'Aucun film ou série trouvé.'} Vous pouvez saisir le titre manuellement.
      </div>}
      {status === 'done' && visibleResults.length > 0 && hiddenCount > 0 && <div className="search-hidden-count" role="status">
        {hiddenCount} titre{hiddenCount > 1 ? 's' : ''} déjà dans vos listes masqué{hiddenCount > 1 ? 's' : ''}
      </div>}

      {status === 'done' && visibleResults.length > 0 && (
        <div className="search-results">
          {visibleResults.map(item => {
            const title = item.title
            const year = item.year
            const imageUrl = item.poster_path ? IMAGE_BASE + item.poster_path : ''
            return (
              <button key={`${item.media_type}:${item.id}`} type="button"
                className={`result-item${selected?.id === item.id && selected?.media_type === item.media_type ? ' selected' : ''}`}
                onClick={() => selectItem(item)}
                title={item.overview || undefined}
                aria-pressed={selected?.id === item.id && selected?.media_type === item.media_type}>
                {imageUrl ? <img src={imageUrl} alt="" loading="lazy" /> : <span className="result-placeholder" aria-hidden="true">{item.media_type === 'movie' ? 'FILM' : 'SÉRIE'}</span>}
                <span className="result-details">
                  <span className="title">{title}</span>
                  {item.original_title && <span className="result-original">{item.original_title}</span>}
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
