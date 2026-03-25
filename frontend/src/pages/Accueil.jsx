import '../styles/accueil.css'
import '../styles/dashboard.css'
import { useState, useEffect, useRef } from 'react'
import Navbar from '../components/Navbar'
import { useAuth } from '../context/AuthContext'
import { useApi } from '../services/api'

const MOIS = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre']

export default function Accueil() {
  const { user } = useAuth()
  const api = useApi()
  const [year, setYear] = useState(new Date().getFullYear())
  const [data, setData] = useState({ year_stats:{}, months:[], leaderboard_films:[], leaderboard_series:[], actualites:[] })

  const deleteActu = async (id) => {
    if (!confirm('Supprimer cette actualité ?')) return
    const res = await api.actualites.remove(id)
    if (res.success) setData(prev => ({ ...prev, actualites: prev.actualites.filter(a => a.id !== id) }))
  }
  const chartRef = useRef(null)
  const chartInstance = useRef(null)
  const currentYear = new Date().getFullYear()
  const years = Array.from({ length: currentYear - 2019 }, (_, i) => 2020 + i)

  useEffect(() => {
    api.accueil.get(year).then(d => { if (d.success) setData(d) })
  }, [year])

  useEffect(() => {
    if (!data.months.length || !chartRef.current) return
    const draw = (Chart) => {
      if (chartInstance.current) chartInstance.current.destroy()
      chartInstance.current = new Chart(chartRef.current.getContext('2d'), {
        type: 'bar',
        data: {
          labels: MOIS,
          datasets: [
            { label: 'Films',  data: data.months.map(m => m.films),  backgroundColor: 'rgba(54,162,235,0.7)' },
            { label: 'Séries', data: data.months.map(m => m.series), backgroundColor: 'rgba(255,99,132,0.7)' }
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            title: { display: true, text: `Films et séries ajoutés par mois – ${year}`, color: '#fff', font: { size: 15 } },
            legend: { labels: { color: '#ccc' } }
          },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, color: '#999' }, grid: { color: '#2a2a2a' } },
            x: { ticks: { color: '#999' }, grid: { color: '#2a2a2a' } }
          }
        }
      })
    }
    if (window.Chart) { draw(window.Chart) }
    else {
      const s = document.createElement('script')
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js'
      s.onload = () => draw(window.Chart)
      document.head.appendChild(s)
    }
    return () => { if (chartInstance.current) chartInstance.current.destroy() }
  }, [data.months, year])

  const stars = r => '★'.repeat(Number(r)) + '☆'.repeat(5 - Number(r))

  const renderCards = (list) => list.length === 0
    ? <p style={{ textAlign:'center', color:'#777', padding:'20px', width:'100%' }}>Aucun résultat.</p>
    : list.map((item, i) => (
      <div key={i} className="card">
        <div className="card-header">
          <span className={`type ${item.type_media}`}>{item.type_media}</span>
        </div>
        <h2>{item.title}</h2>
        {item.image_url && <img src={item.image_url} alt={item.title} />}
        <span className="rating">{stars(item.rating)} ({item.rating}/5)</span>
        {item.commentaire && (
          <div className="comment-section">
            <strong>Commentaire :</strong>
            <p className="comment-text">{item.commentaire}</p>
          </div>
        )}
        <div className="card-info">
          <p className="username">Par : {item.username}</p>
          <p className="date">Ajouté le : {new Date(item.created_at).toLocaleDateString('fr-FR')}</p>
        </div>
      </div>
    ))

  return (
    <>
      <Navbar />
      <div className="accueil-main">
        <h1>Bienvenue {user?.username} !</h1>

        {/* Actualités */}
        {data.actualites.length > 0 && (
          <div className="actualites-banner">
            <h2>Actualités de la plateforme</h2>
            <div className="actualites-carousel">
              {data.actualites.map(a => (
                <div key={a.id} className="actu-card">
                  {user?.role === 'admin' && (
                    <button className="actu-delete-btn" onClick={() => deleteActu(a.id)} title="Supprimer cette actualité">🗑</button>
                  )}
                  <h3>{a.titre}</h3>
                  <p>{a.contenu}</p>
                  <div className="actu-meta">
                    <span className="actu-author">Par {a.admin_username}</span>
                    <span className="actu-date">{new Date(a.created_at).toLocaleDateString('fr-FR')}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Dashboard */}
        <section className="dashboard">
          <h2>Dashboard</h2>
          <div className="year-select-wrap">
            <label htmlFor="year-sel">Année :</label>
            <select id="year-sel" value={year} onChange={e => setYear(Number(e.target.value))}>
              {years.map(y => <option key={y} value={y}>{y}</option>)}
            </select>
          </div>
          <h3>Statistiques pour {year}</h3>
          <div className="chart-wrap">
            <canvas ref={chartRef} />
          </div>
          <div className="summary">
            <div className="card_media"><h3>Films</h3><strong>{data.year_stats.films || 0}</strong></div>
            <div className="card_media"><h3>Séries</h3><strong>{data.year_stats.series || 0}</strong></div>
            <div className="card_media"><h3>Total</h3><strong>{data.year_stats.total || 0}</strong></div>
          </div>
        </section>

        {/* Leaderboard */}
        <section className="leaderboard">
          <h2>Top Médias des Autres Utilisateurs</h2>
          <div className="leaderboard-section">
            <h3>Films les Mieux Notés</h3>
            <div className="media-container">{renderCards(data.leaderboard_films)}</div>
          </div>
          <div className="leaderboard-section">
            <h3>Séries les Mieux Notées</h3>
            <div className="media-container">{renderCards(data.leaderboard_series)}</div>
          </div>
        </section>
      </div>
    </>
  )
}
