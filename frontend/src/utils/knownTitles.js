import { useEffect, useState } from 'react'
import { useApi } from '../services/api'

// « Spider-Man : Far From Home » et « spider man far from home » doivent correspondre.
export const normalizeTitle = (title = '') => title
  .toLocaleLowerCase('fr-FR')
  .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  .replace(/[^a-z0-9]+/g, ' ')
  .trim()

export const titleKey = (title, typeMedia) => `${typeMedia}:${normalizeTitle(title)}`
const toKeys = (list = []) => new Set(list.map(m => titleKey(m.title, m.type_media)))

// Ancien backend sans /api/media/titles : on parcourt la liste paginée.
async function loadSeenFromPages(api) {
  const first = await api.media.getAll({ page:1 })
  if (!first.success) return []
  const others = await Promise.all(
    Array.from({ length: Math.max(0, first.pages - 1) }, (_, i) => api.media.getAll({ page:i + 2 }))
  )
  return [first, ...others].flatMap(d => d.success ? d.media : [])
}

export async function loadKnownTitles(api) {
  try {
    const data = await api.media.titles()
    if (data.success) return { seen: toKeys(data.media), toWatch: toKeys(data.watchlist) }
  } catch { /* endpoint absent : repli ci-dessous */ }

  const [media, watchlist] = await Promise.all([
    loadSeenFromPages(api).catch(() => []),
    api.watchlist.getAll().then(d => d.success ? d.items : []).catch(() => []),
  ])
  return { seen: toKeys(media), toWatch: toKeys(watchlist) }
}

// Vrai si ce titre (titre français ou original) est dans l'un des ensembles donnés.
export function isKnown(sets, { title, originalTitle, typeMedia }) {
  return sets.some(set => set?.has(titleKey(title, typeMedia)) || (originalTitle && set?.has(titleKey(originalTitle, typeMedia))))
}

export function useKnownTitles() {
  const api = useApi()
  const [known, setKnown] = useState({ seen: new Set(), toWatch: new Set() })
  useEffect(() => {
    let active = true
    loadKnownTitles(api).then(k => { if (active) setKnown(k) })
    return () => { active = false }
  }, [api])
  return known
}
