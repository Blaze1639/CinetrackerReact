import { useState } from 'react'

const API_KEY = import.meta.env.VITE_TMDB_KEY || '22eef7e96585baa751a8384b942e4470'
const IMAGE_BASE = 'https://image.tmdb.org/t/p/w500'

export default function TmdbSearch({ onSelect }) {
  const [query, setQuery] = useState('')
  const [results, setResults] = useState([])
  const [loading, setLoading] = useState(false)
  const [selected, setSelected] = useState(null)

  const search = async () => {
    if (!query.trim()) return
    setLoading(true)
    setResults([])
    try {
      const res = await fetch(`https://api.themoviedb.org/3/search/multi?api_key=${API_KEY}&language=fr-FR&query=${encodeURIComponent(query)}`)
      const data = await res.json()
      setResults((data.results || []).filter(i => (i.media_type === 'movie' || i.media_type === 'tv') && i.poster_path))
    } catch (e) { console.error(e) }
    setLoading(false)
  }

  const selectItem = (item) => {
    setSelected(item)
    onSelect({
      title:    item.title || item.name,
      imageUrl: IMAGE_BASE + item.poster_path,
      type:     item.media_type === 'movie' ? 'film' : 'série'
    })
  }

  return (
    <div className="search-container">
      <label>Rechercher un film ou une série :</label>
      <div className="search-row">
        <input type="text" value={query}
          onChange={e => setQuery(e.target.value)}
          onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), search())}
          placeholder="Entrez le titre à rechercher..." />
        <button type="button" id="search-btn" onClick={search}>Rechercher</button>
      </div>

      {loading && <div className="loading">🔍 Recherche en cours...</div>}

      {results.length > 0 && (
        <div className="search-results">
          {results.map(item => {
            const title = item.title || item.name
            const year = item.release_date || item.first_air_date
            return (
              <div key={item.id}
                className={`result-item${selected?.id === item.id ? ' selected' : ''}`}
                onClick={() => selectItem(item)}>
                <img src={IMAGE_BASE + item.poster_path} alt={title} />
                <div className="title">{title}{year ? ` (${year.split('-')[0]})` : ''}</div>
              </div>
            )
          })}
        </div>
      )}

      {selected && (
        <div className="selected-preview">
          <p>✓ Image sélectionnée :</p>
          <img src={IMAGE_BASE + selected.poster_path} alt="" />
        </div>
      )}
    </div>
  )
}
