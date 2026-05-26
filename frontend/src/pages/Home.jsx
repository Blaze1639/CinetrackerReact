import '../styles/home.css'
import { Link } from 'react-router-dom'
import Footer from '../components/Footer'

export default function Home() {
  return (
    <>
      <nav className="navbar" style={{position:'fixed',top:0,width:'100%',background:'rgba(10,10,10,0.95)',backdropFilter:'blur(10px)',padding:'1rem 3rem',display:'flex',justifyContent:'space-between',alignItems:'center',zIndex:1000,borderBottom:'1px solid rgba(229,9,20,0.3)'}}>
        <div className="logo">CINÉTRACKER</div>
        <div className="nav-links" style={{display:'flex',gap:'2rem',alignItems:'center'}}>
          <a href="#features" style={{color:'#f5f5f1',textDecoration:'none',fontSize:'1.1rem'}}>Fonctionnalités</a>
          <a href="#about" style={{color:'#f5f5f1',textDecoration:'none',fontSize:'1.1rem'}}>À propos</a>
          <Link to="/connexion" className="btn-login" style={{background:'linear-gradient(135deg,#e50914,#8b0000)',padding:'0.8rem 2rem',borderRadius:4,color:'white',textDecoration:'none'}}>Connexion</Link>
        </div>
      </nav>

      <section className="hero">
        <div className="hero-content">
          <h1>VOTRE CINÉMATHÈQUE PERSONNELLE</h1>
          <p className="subtitle">Cataloguez, notez et suivez tous vos films et séries en un seul endroit</p>
          <div className="cta-buttons">
            <Link to="/inscription" className="btn btn-primary">Créer un compte</Link>
            <Link to="/connexion" className="btn btn-secondary">Se connecter</Link>
          </div>
        </div>
      </section>

      <section className="features" id="features">
        <h2 className="section-title">FONCTIONNALITÉS</h2>
        <div className="feature-grid">
          {[
            ['CATALOGUE COMPLET','Gérez votre collection de films et séries avec un système de notation sur 5 étoiles et des commentaires personnalisés.'],
            ['RECHERCHE INTELLIGENTE','Intégration API TMDB pour rechercher et ajouter automatiquement les informations de vos films préférés avec leurs affiches.'],
            ['LISTE À VOIR','Créez votre watchlist personnalisée et ne perdez plus jamais de vue les films que vous voulez découvrir.'],
            ['FAVORIS','Marquez vos coups de cœur et retrouvez facilement vos films et séries préférés grâce au système de favoris.'],
            ['FILTRAGE AVANCÉ','Filtrez votre collection par type, favoris, ou utilisez la recherche dynamique pour trouver rapidement ce que vous cherchez.'],
            ['DOCKER','Application containerisée avec Docker — zéro configuration, tourne partout en une seule commande.'],
          ].map(([title, text]) => (
            <div key={title} className="feature-card">
              <h3>{title}</h3>
              <p>{text}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="stats">
        <div className="stats-grid">
          {[['2','Ans de réflexion'],['1.2','Version actuelle'],['100%','Personnel & Gratuit'],['∞','Films à cataloguer']].map(([n,l]) => (
            <div key={l} className="stat-item">
              <span className="stat-number">{n}</span>
              <span className="stat-label">{l}</span>
            </div>
          ))}
        </div>
      </section>

      <section className="journey" id="about">
        <h2 className="section-title">L'HISTOIRE DU PROJET</h2>
        <div className="timeline">
          {[
            ['IL Y A 2 ANS',"Identification du besoin : disposer d'un outil pour cataloguer mes films et séries visionnés ainsi que ma watchlist personnelle."],
            ['OCTOBRE 2024',"Opportunité de développement dans le cadre d'une présentation aux étudiants de 1ère année. Développement intensif de la version 1.0 en 2 semaines."],
            ['NOVEMBRE 2024',"Présentation réussie ! Application fonctionnelle avec les fonctionnalités essentielles : ajout, notation, affichage des listes."],
            ['DÉCEMBRE 2024',"Amélioration continue : intégration API films, système de favoris, filtrage avancé, réorganisation complète de l'architecture."],
            ["AUJOURD'HUI","Version 1.2 : Application React + Docker, optimisée, sécurisée et prête pour l'avenir !"],
          ].map(([date, content]) => (
            <div key={date} className="timeline-item">
              <div className="timeline-dot"></div>
              <div className="timeline-date">{date}</div>
              <div className="timeline-content">{content}</div>
            </div>
          ))}
        </div>
      </section>

      <Footer />
    </>
  )
}
