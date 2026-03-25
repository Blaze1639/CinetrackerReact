import { useState } from 'react'

export default function StarRating({ value, onChange }) {
  const [hover, setHover] = useState(0)

  return (
    <div className="stars" style={{ display: 'flex', justifyContent: 'center', gap: 5, margin: '20px 0' }}>
      {[1, 2, 3, 4, 5].map(star => (
        <span
          key={star}
          className={`star${(hover || value) >= star ? ' active' : ''}`}
          style={{ fontSize: 48, cursor: 'pointer', color: (hover || value) >= star ? '#FFD700' : '#444', transition: 'all 0.2s ease', userSelect: 'none' }}
          onClick={() => onChange(star)}
          onMouseEnter={() => setHover(star)}
          onMouseLeave={() => setHover(0)}
        >★</span>
      ))}
    </div>
  )
}
