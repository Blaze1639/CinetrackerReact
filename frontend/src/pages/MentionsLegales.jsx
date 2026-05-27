import { Link } from 'react-router-dom'
import Footer from '../components/Footer'

export default function MentionsLegales() {
  return (
    <main style={{
      minHeight: 'calc(100vh - 120px)',
      padding: '4rem 1.5rem',
      background: '#050505',
      color: '#f5f5f1',
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
    }}>
      <div style={{ maxWidth: 900, width: '100%', marginBottom: '2rem' }}>
        <h1 style={{ fontSize: '2.5rem', marginBottom: '1rem', color: '#e50914' }}>Mentions légales</h1>
        <p style={{ fontSize: '1rem', lineHeight: 1.8, marginBottom: '1.5rem' }}>
          Ce site est une application personnelle de gestion de films et séries développée par Axel Dion.
          Elle permet de cataloguer, noter et organiser une collection de films et séries.
        </p>

        <section style={{ marginBottom: '1.5rem' }}>
          <h2 style={{ fontSize: '1.25rem', marginBottom: '0.75rem' }}>Éditeur</h2>
          <p style={{ lineHeight: 1.7, margin: 0 }}>
            Responsable de la publication : <strong>Axel Dion</strong>
          </p>
          <p style={{ lineHeight: 1.7, margin: '0.5rem 0 0' }}>
            Contact : <a href="mailto:axel77dion@gmail.com" style={{ color: '#f5f5f1', textDecoration: 'underline' }}>axel77dion@gmail.com</a>
          </p>
          <p style={{ lineHeight: 1.7, margin: '0.5rem 0 0' }}>
            Téléphone : <strong>à renseigner</strong>
          </p>
        </section>

        <section style={{ marginBottom: '1.5rem' }}>
          <h2 style={{ fontSize: '1.25rem', marginBottom: '0.75rem' }}>Hébergement</h2>
          <p style={{ lineHeight: 1.7, margin: 0 }}>
            Hébergement non précisé dans le projet. Le service est destiné à un usage personnel ou de démonstration.
          </p>
        </section>

        <section style={{ marginBottom: '1.5rem' }}>
          <h2 style={{ fontSize: '1.25rem', marginBottom: '0.75rem' }}>Propriété intellectuelle</h2>
          <p style={{ lineHeight: 1.7, margin: 0 }}>
            Tous les contenus, marques, logos et textes présents sur ce site sont la propriété de l'éditeur.
          </p>
        </section>

        <section style={{ marginBottom: '1.5rem' }}>
          <h2 style={{ fontSize: '1.25rem', marginBottom: '0.75rem' }}>Responsabilité</h2>
          <p style={{ lineHeight: 1.7, margin: 0 }}>
            L'application est fournie "en l'état" sans garantie particulière. L'éditeur ne peut être tenu responsable des éventuelles erreurs ou interruptions de service.
          </p>
        </section>

        <div style={{ marginTop: '2rem' }}>
          <Link to="/" style={{ color: '#e50914', textDecoration: 'none', fontWeight: 700 }}>
            Retour à l'accueil
          </Link>
        </div>
      </div>

      <Footer />
    </main>
  )
}
