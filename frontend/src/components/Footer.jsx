export default function Footer() {
  const currentYear = new Date().getFullYear()

  return (
    <footer
      style={{
        marginTop: '3rem',
        padding: '2rem 1.5rem',
        background: '#0a0a0a',
        borderTop: '1px solid rgba(229, 9, 20, 0.3)',
        color: 'rgba(245, 245, 241, 0.8)',
        textAlign: 'center',
        fontSize: '0.95rem',
        lineHeight: 1.8,
      }}
    >
      <p style={{ margin: 0, fontWeight: 700, color: '#f5f5f1' }}>Mentions légales</p>
      <p style={{ margin: '0.75rem 0 0' }}>
        Responsable de la publication : <strong>Axel Dion</strong>
      </p>
      <p style={{ margin: 0 }}>
        Contact : <a href="mailto:axel77dion@gmail.com" style={{ color: '#f5f5f1' }}>axel77dion@gmail.com</a>
      </p>
      <p style={{ margin: 0 }}>
        Téléphone : <span>à renseigner</span>
      </p>
      <p style={{ margin: '1rem 0 0', opacity: 0.75 }}>
        © {currentYear} CinéTracker — Tous droits réservés.
      </p>
      <p style={{ margin: 0, opacity: 0.75 }}>
        Application développée pour l’organisation de films et séries.
      </p>
    </footer>
  )
}
