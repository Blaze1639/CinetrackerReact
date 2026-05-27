import { Link } from 'react-router-dom'

export default function Footer() {
  const currentYear = new Date().getFullYear()

  return (
    <footer
      style={{
        marginTop: '3rem',
        padding: '1.25rem 1.5rem',
        background: '#0a0a0a',
        borderTop: '1px solid rgba(229, 9, 20, 0.3)',
        color: 'rgba(245, 245, 241, 0.9)',
        fontSize: '0.95rem',
        lineHeight: 1.4,
        textAlign: 'center',
        whiteSpace: 'nowrap',
        overflowX: 'auto',
      }}
    >
      <p style={{ margin: 0, display: 'inline' }}>
        <Link to="/mentions-legales" style={{ color: '#f5f5f1', textDecoration: 'underline', fontWeight: 700 }}>Mentions légales</Link>
        <span style={{ margin: '0 0.75rem' }}>•</span>
        Responsable de la publication : <strong>Axel Dion</strong>
        <span style={{ margin: '0 0.75rem' }}>•</span>
        Contact : <a href="mailto:axel77dion@gmail.com" style={{ color: '#f5f5f1', textDecoration: 'underline' }}>axel77dion@gmail.com</a>
        <span style={{ margin: '0 0.75rem' }}>•</span>
        Téléphone : <span>à renseigner</span>
        <span style={{ margin: '0 0.75rem' }}>•</span>
        © {currentYear} Mention Légales — Tous droits réservés.
        <span style={{ margin: '0 0.75rem' }}>•</span>
        Application développée pour l’organisation de films et séries.
      </p>
    </footer>
  )
}
