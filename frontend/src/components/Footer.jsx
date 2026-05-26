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
        textAlign: 'center',
        fontSize: '0.95rem',
        whiteSpace: 'nowrap',
        overflow: 'hidden',
        textOverflow: 'ellipsis',
      }}
    >
      <p style={{ margin: 0 }}>
        Mentions légales — Responsable de la publication : <strong>Axel Dion</strong> — Contact : <a href="mailto:axel77dion@gmail.com" style={{ color: '#f5f5f1' }}>axel77dion@gmail.com</a> — Téléphone : <span>à renseigner</span> — © {currentYear} CinéTracker — Application développée pour l’organisation de films et séries.
      </p>
    </footer>
  )
}
