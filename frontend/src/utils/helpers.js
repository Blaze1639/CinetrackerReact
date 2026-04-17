// Formate une note en étoiles
export function formatNote(note) {
  if (!note || note < 1 || note > 5) return 'Non noté';
  return '⭐'.repeat(note);
}

// Filtre les médias par type
export function filtrerParType(medias, type) {
  if (!type || type === 'tous') return medias;
  return medias.filter(m => m.type === type);
}

// Tronque un titre trop long
export function tronquerTitre(titre, max = 30) {
  if (!titre) return '';
  if (titre.length <= max) return titre;
  return titre.slice(0, max) + '...';
}

// Valide un email
export function validerEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Filtre les favoris
export function filtrerFavoris(medias) {
  return medias.filter(m => m.favori === 1);
}

// Calcule la moyenne des notes
export function calculerMoyenne(medias) {
  const notes = medias.filter(m => m.note).map(m => m.note);
  if (notes.length === 0) return 0;
  return notes.reduce((a, b) => a + b, 0) / notes.length;
}