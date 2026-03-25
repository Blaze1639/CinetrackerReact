import '../styles/aleatoire.css'
import { useState, useEffect } from 'react'
import Navbar from '../components/Navbar'

const API_KEY = import.meta.env.VITE_TMDB_KEY || '22eef7e96585baa751a8384b942e4470'
const GENRES = [
  {id:'',label:'Tous les genres'},{id:'28',label:'Action'},{id:'12',label:'Aventure'},
  {id:'16',label:'Animation'},{id:'35',label:'Comédie'},{id:'80',label:'Crime'},
  {id:'99',label:'Documentaire'},{id:'18',label:'Drame'},{id:'10751',label:'Famille'},
  {id:'14',label:'Fantastique'},{id:'36',label:'Histoire'},{id:'27',label:'Horreur'},
  {id:'10402',label:'Musique'},{id:'9648',label:'Mystère'},{id:'10749',label:'Romance'},
  {id:'878',label:'Science-Fiction'},{id:'53',label:'Thriller'},{id:'10752',label:'Guerre'},{id:'37',label:'Western'},
]
const YEARS = [{value:'',label:'Toutes les années'},...['2024','2023','2022','2021','2020','2019','2018','2017','2016','2015'].map(y=>({value:y,label:y})),{value:'2010',label:'2010 et avant'}]

export default function Aleatoire() {
  const [filters, setFilters] = useState({type:'tous',genre:'',year:'',duration:''})
  const [media, setMedia] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const fetchRandom = async (f = filters) => {
    setLoading(true); setError(null); setMedia(null)
    try {
      let type = f.type
      if (f.duration && type === 'tous') type = 'film'
      const page = Math.floor(Math.random() * 50) + 1
      let isMovie = type === 'série' ? false : type === 'film' ? true : Math.random() > 0.5
      let ep = isMovie ? `https://api.themoviedb.org/3/discover/movie?sort_by=popularity.desc&page=${page}` : `https://api.themoviedb.org/3/discover/tv?sort_by=popularity.desc&page=${page}`
      if (f.genre) ep += `&with_genres=${f.genre}`
      if (f.year) ep += f.year === '2010' ? '&primary_release_date.lte=2010-12-31' : `&primary_release_year=${f.year}`
      if (f.duration && isMovie) {
        if (f.duration === 'lt90') ep += '&with_runtime.lte=89'
        else if (f.duration === '90to120') ep += '&with_runtime.gte=90&with_runtime.lte=120'
        else if (f.duration === 'gt120') ep += '&with_runtime.gte=121'
      }
      ep += `&api_key=${API_KEY}&language=fr-FR`
      const res = await fetch(ep)
      const data = await res.json()
      if (!data.results?.length) { setError('Aucun résultat trouvé avec ces filtres.'); setLoading(false); return }
      const pick = data.results[Math.floor(Math.random() * data.results.length)]
      isMovie = !!pick.title
      const detail = await fetch(`https://api.themoviedb.org/3/${isMovie?'movie':'tv'}/${pick.id}?api_key=${API_KEY}&language=fr-FR`).then(r=>r.json())
      const runtime = isMovie ? detail.runtime || null : detail.episode_run_time?.[0] || null
      setMedia({ title: detail.title || detail.name || 'Titre inconnu', overview: detail.overview || '', poster_path: detail.poster_path || null, is_movie: isMovie, release_date: detail.release_date || detail.first_air_date || null, genres: detail.genres || [], runtime, number_of_seasons: detail.number_of_seasons || null, number_of_episodes: detail.number_of_episodes || null })
    } catch (e) { setError('Erreur : ' + e.message) }
    setLoading(false)
  }

  useEffect(() => { fetchRandom() }, [])

  const hf = (k, v) => { const nf = {...filters,[k]:v}; setFilters(nf); fetchRandom(nf) }

  return (
    <>
      <Navbar />
      <div className="aleatoire-wrapper">
        <h1>🎲 Découvrez un film/série aléatoire</h1>
        <div className="controls">
          <div className="control-row">
            <label>Type :</label>
            <select value={filters.type} onChange={e => hf('type', e.target.value)}>
              <option value="tous">Tous (Films & Séries)</option>
              <option value="film">Films uniquement</option>
              <option value="série">Séries uniquement</option>
            </select>
          </div>
          <div className="control-row">
            <label>Genre :</label>
            <select value={filters.genre} onChange={e => hf('genre', e.target.value)}>
              {GENRES.map(g => <option key={g.id} value={g.id}>{g.label}</option>)}
            </select>
          </div>
          <div className="control-row">
            <label>Année :</label>
            <select value={filters.year} onChange={e => hf('year', e.target.value)}>
              {YEARS.map(y => <option key={y.value} value={y.value}>{y.label}</option>)}
            </select>
          </div>
          <div className="control-row">
            <label>Durée :</label>
            <select value={filters.duration} onChange={e => hf('duration', e.target.value)}>
              <option value="">Toutes les durées</option>
              <option value="lt90">Moins de 90 min</option>
              <option value="90to120">90 - 120 min</option>
              <option value="gt120">Plus de 120 min</option>
            </select>
          </div>
        </div>
        <button className={`random-btn${loading?' loading':''}`} onClick={() => fetchRandom()} disabled={loading}>
          {loading ? '⏳ Chargement...' : 'Choisir un aléatoire'}
        </button>
        {error && <div className="error"><p>⚠️ {error}</p></div>}
        {media && !loading && (
          <div className="result">
            <div className="card">
              <div className="card-header"><h2 id="media-title">{media.title}</h2></div>
              {media.poster_path && <img src={`https://image.tmdb.org/t/p/w500${media.poster_path}`} alt={media.title} onError={e => e.target.src='https://via.placeholder.com/500x750?text=Image+non+disponible'} />}
              <div className="info-grid">
                <div className="media-type">{media.is_movie ? '🎬 Film' : '📺 Série'}</div>
                {media.genres.length > 0 && <div className="media-genres"><strong>Genres :</strong> {media.genres.map(g=>g.name).join(', ')}</div>}
                {media.release_date && <div className="media-date"><strong>Sortie :</strong> {new Date(media.release_date).toLocaleDateString('fr-FR')} ({new Date(media.release_date).getFullYear()})</div>}
                {media.runtime && <div className="media-duration"><strong>{media.is_movie?'Durée':'Durée épisode'} :</strong> {media.runtime} min</div>}
                {!media.is_movie && media.number_of_seasons && <div className="media-seasons"><strong>Saisons/Épisodes :</strong> {media.number_of_seasons} saison(s) — {media.number_of_episodes} épisode(s)</div>}
              </div>
              {media.overview && <div className="comment-section"><strong>Synopsis</strong><p className="comment-text">{media.overview}</p></div>}
            </div>
          </div>
        )}
      </div>
    </>
  )
}
