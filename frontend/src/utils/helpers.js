import {
  formatNote,
  filtrerParType,
  tronquerTitre,
  validerEmail,
  filtrerFavoris,
  calculerMoyenne,
} from '../utils/helpers';

// ─────────────────────────────────────────────
// formatNote
// ─────────────────────────────────────────────
describe('Test formatNote', function () {
  it('should return stars for a valid note of 3', function () {
    expect(formatNote(3)).toBe('⭐⭐⭐');
  });
  it('should return all 5 stars for a note of 5', function () {
    expect(formatNote(5)).toBe('⭐⭐⭐⭐⭐');
  });
  it('should return Non noté if note is null', function () {
    expect(formatNote(null)).toBe('Non noté');
  });
  it('should return Non noté if note is 0', function () {
    expect(formatNote(0)).toBe('Non noté');
  });
  it('should return Non noté if note is above 5', function () {
    expect(formatNote(6)).toBe('Non noté');
  });
});

// ─────────────────────────────────────────────
// filtrerParType
// ─────────────────────────────────────────────
describe('Test filtrerParType', function () {
  let medias;
  beforeEach(function () {
    medias = [
      { titre: 'Batman', type: 'film' },
      { titre: 'Breaking Bad', type: 'serie' },
      { titre: 'Dune', type: 'film' },
      { titre: 'Narcos', type: 'serie' },
    ];
  });
  it('should return only films when type is film', function () {
    expect(filtrerParType(medias, 'film')).toHaveLength(2);
  });
  it('should return only series when type is serie', function () {
    expect(filtrerParType(medias, 'serie')).toHaveLength(2);
  });
  it('should return all medias when type is tous', function () {
    expect(filtrerParType(medias, 'tous')).toHaveLength(4);
  });
  it('should return all medias when type is empty', function () {
    expect(filtrerParType(medias, '')).toHaveLength(4);
  });
});

// ─────────────────────────────────────────────
// tronquerTitre
// ─────────────────────────────────────────────
describe('Test tronquerTitre', function () {
  it('should return empty string if titre is null', function () {
    expect(tronquerTitre(null)).toBe('');
  });
  it('should return empty string if titre is undefined', function () {
    expect(tronquerTitre(undefined)).toBe('');
  });
  it('should return the titre as-is if shorter than max', function () {
    expect(tronquerTitre('Dune', 30)).toBe('Dune');
  });
  it('should return the titre as-is if exactly equal to max', function () {
    expect(tronquerTitre('a'.repeat(30), 30)).toBe('a'.repeat(30));
  });
  it('should truncate and add ... if longer than max', function () {
    expect(tronquerTitre('Le Seigneur des Anneaux : La Communauté', 30)).toBe(
      'Le Seigneur des Anneaux : La C...'
    );
  });
  it('should use default max of 30 if not provided', function () {
    const long = 'a'.repeat(35);
    expect(tronquerTitre(long)).toBe('a'.repeat(30) + '...');
  });
});

// ─────────────────────────────────────────────
// validerEmail
// ─────────────────────────────────────────────
describe('Test validerEmail', function () {
  it('should return true for a valid email', function () {
    expect(validerEmail('axel@gmail.com')).toBe(true);
  });
  it('should return true for a valid email with subdomain', function () {
    expect(validerEmail('axel@mail.example.com')).toBe(true);
  });
  it('should return false if @ is missing', function () {
    expect(validerEmail('axelgmail.com')).toBe(false);
  });
  it('should return false if domain is missing', function () {
    expect(validerEmail('axel@')).toBe(false);
  });
  it('should return false for empty string', function () {
    expect(validerEmail('')).toBe(false);
  });
  it('should return false if there are spaces', function () {
    expect(validerEmail('axel @gmail.com')).toBe(false);
  });
});

// ─────────────────────────────────────────────
// filtrerFavoris
// ─────────────────────────────────────────────
describe('Test filtrerFavoris', function () {
  let medias;
  beforeEach(function () {
    medias = [
      { titre: 'Batman', favori: 1 },
      { titre: 'Dune', favori: 0 },
      { titre: 'Oppenheimer', favori: 1 },
      { titre: 'Narcos', favori: 0 },
    ];
  });
  it('should return only favorited medias', function () {
    expect(filtrerFavoris(medias)).toHaveLength(2);
  });
  it('should return empty array if no favorites', function () {
    expect(filtrerFavoris([{ titre: 'Test', favori: 0 }])).toHaveLength(0);
  });
  it('should return all if all are favorites', function () {
    expect(
      filtrerFavoris([
        { titre: 'A', favori: 1 },
        { titre: 'B', favori: 1 },
      ])
    ).toHaveLength(2);
  });
});

// ─────────────────────────────────────────────
// calculerMoyenne
// ─────────────────────────────────────────────
describe('Test calculerMoyenne', function () {
  it('should return 0 if no medias have a note', function () {
    expect(calculerMoyenne([{ titre: 'Test', note: null }])).toBe(0);
  });
  it('should return 0 for empty array', function () {
    expect(calculerMoyenne([])).toBe(0);
  });
  it('should return the correct average', function () {
    const medias = [
      { note: 4 },
      { note: 5 },
      { note: 3 },
    ];
    expect(calculerMoyenne(medias)).toBeCloseTo(4);
  });
  it('should ignore medias without note', function () {
    const medias = [
      { note: 4 },
      { note: null },
      { note: 2 },
    ];
    expect(calculerMoyenne(medias)).toBeCloseTo(3);
  });
  it('should return the note itself if only one media', function () {
    expect(calculerMoyenne([{ note: 5 }])).toBe(5);
  });
});