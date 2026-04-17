import {
  formatNote,
  filtrerParType,
  tronquerTitre,
  validerEmail,
  filtrerFavoris,
  calculerMoyenne,
} from '../utils/helpers';

// ── formatNote ────────────────────────────────────
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

// ── filtrerParType ────────────────────────────────
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



// ── filtrerFavoris ────────────────────────────────
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
    expect(filtrerFavoris([
      { titre: 'Test', favori: 0 }
    ])).toHaveLength(0);
  });

  it('should return all if all are favorites', function () {
    expect(filtrerFavoris([
      { titre: 'A', favori: 1 },
      { titre: 'B', favori: 1 },
    ])).toHaveLength(2);
  });

});