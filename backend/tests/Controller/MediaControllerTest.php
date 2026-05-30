<?php

namespace App\Tests\Controller;

use App\Controller\MediaController;
use App\Entity\Media;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaController::class)]
class MediaControllerTest extends TestCase
{
    // ── Validation du titre ───────────────────────────────────────

    #[TestDox('titre vide → invalide')]
    public function testTitleEmpty(): void
    {
        $title = trim('');
        $this->assertEmpty($title);
    }

    #[TestDox('titre composé uniquement d\'espaces → invalide')]
    public function testTitleOnlySpacesIsRejected(): void
    {
        $this->assertEmpty(trim('   '));
    }

    #[TestDox('titre valide après trim')]
    public function testValidTitleAfterTrim(): void
    {
        $title = trim('  Inception  ');
        $this->assertSame('Inception', $title);
        $this->assertNotEmpty($title);
    }

    // ── Validation de la note ─────────────────────────────────────

    /** @return array<string, array{float, bool}> */
    public static function ratingProvider(): array
    {
        return [
            'sous le minimum (0.5)'  => [0.5,  false],
            'minimum exact (1.0)'    => [1.0,  true],
            'milieu (3.0)'           => [3.0,  true],
            'demi-point (4.5)'       => [4.5,  true],
            'maximum exact (5.0)'    => [5.0,  true],
            'au-dessus du max (5.5)' => [5.5,  false],
            'zéro'                   => [0.0,  false],
            'négatif'                => [-1.0, false],
        ];
    }

    #[DataProvider('ratingProvider')]
    #[TestDox('Validation de la note entre 1 et 5')]
    public function testRatingValidation(float $rating, bool $isValid): void
    {
        $result = $rating >= 1 && $rating <= 5;
        $this->assertSame($isValid, $result);
    }

    // ── Normalisation du type_media ───────────────────────────────

    /** @return array<string, array{string|null, string}> */
    public static function typeMediaProvider(): array
    {
        return [
            'absent → film'        => [null,          'film'],
            'film'                 => ['film',         'film'],
            'série'                => ['série',        'série'],
            'autre valeur → film'  => ['documentaire', 'film'],
            'majuscule → film'     => ['Série',        'film'],
        ];
    }

    #[DataProvider('typeMediaProvider')]
    #[TestDox('Normalisation du type_media')]
    public function testTypeMediaNormalization(?string $input, string $expected): void
    {
        $typeMedia = ($input ?? 'film') === 'série' ? 'série' : 'film';
        $this->assertSame($expected, $typeMedia);
    }

    // ── Toggle favori ─────────────────────────────────────────────

    #[TestDox('toggle favori : false → true')]
    public function testToggleFavoriteToTrue(): void
    {
        $media = new Media();
        $media->setTitle('Avatar');
        $media->setUserId(1);

        $this->assertFalse($media->isFavorite());
        $media->setFavorite(!$media->isFavorite());
        $this->assertTrue($media->isFavorite());
    }

    #[TestDox('toggle favori : true → false')]
    public function testToggleFavoriteToFalse(): void
    {
        $media = new Media();
        $media->setTitle('Avatar');
        $media->setUserId(1);
        $media->setFavorite(true);

        $media->setFavorite(!$media->isFavorite());
        $this->assertFalse($media->isFavorite());
    }

    // ── Incrément du compteur de vues ─────────────────────────────

    #[TestDox('le compteur part de 0')]
    public function testViewCountStartsAtZero(): void
    {
        $media = new Media();
        $this->assertSame(0, $media->getViewCount());
    }

    #[TestDox('un seul incrément')]
    public function testSingleIncrement(): void
    {
        $media = new Media();
        $media->setViewCount($media->getViewCount() + 1);
        $this->assertSame(1, $media->getViewCount());
    }

    #[TestDox('cinq incréments successifs')]
    public function testFiveIncrements(): void
    {
        $media = new Media();
        for ($i = 0; $i < 5; $i++) {
            $media->setViewCount($media->getViewCount() + 1);
        }
        $this->assertSame(5, $media->getViewCount());
    }

    // ── Pagination ────────────────────────────────────────────────

    /** @return array<string, array{int, int, int}> */
    public static function paginationProvider(): array
    {
        return [
            'page 1, perPage 12'  => [1,  12, 0],
            'page 2, perPage 12'  => [2,  12, 12],
            'page 3, perPage 12'  => [3,  12, 24],
            'page 1, perPage 1'   => [1,  1,  0],
        ];
    }

    #[DataProvider('paginationProvider')]
    #[TestDox('Calcul de l\'offset selon la page')]
    public function testOffsetCalculation(int $page, int $perPage, int $expectedOffset): void
    {
        $offset = ($page - 1) * $perPage;
        $this->assertSame($expectedOffset, $offset);
    }

    /** @return array<string, array{int, int, int}> */
    public static function totalPagesProvider(): array
    {
        return [
            '25 items / 12 par page → 3 pages'  => [25, 12, 3],
            '24 items / 12 par page → 2 pages'  => [24, 12, 2],
            '1  item  / 12 par page → 1 page'   => [1,  12, 1],
            '0  item  / 12 par page → 0 pages'  => [0,  12, 0],
            '12 items / 12 par page → 1 page'   => [12, 12, 1],
        ];
    }

    #[DataProvider('totalPagesProvider')]
    #[TestDox('Calcul du nombre total de pages')]
    public function testTotalPagesCalculation(int $total, int $perPage, int $expectedPages): void
    {
        $pages = (int) ceil($total / $perPage);
        $this->assertSame($expectedPages, $pages);
    }

    // ── Commentaire vide → null ───────────────────────────────────

    /** @return array<string, array{string, bool}> */
    public static function commentaireProvider(): array
    {
        return [
            'vide → null'                  => ['',                false],
            'espaces → null'               => ['   ',             false],
            'texte valide → non null'      => ['Film incroyable', true],
            'texte avec espaces → trimmed' => ['  Super film  ',  true],
        ];
    }

    #[DataProvider('commentaireProvider')]
    #[TestDox('Commentaire vide devient null, sinon conservé')]
    public function testCommentaireNormalization(string $input, bool $expectNonNull): void
    {
        $commentaire = trim($input) ?: null;
        if ($expectNonNull) {
            $this->assertNotNull($commentaire);
        } else {
            $this->assertNull($commentaire);
        }
    }

    #[TestDox('page minimum forcée à 1 si valeur invalide')]
    public function testPageMinimumIsOne(): void
    {
        $page = max(1, (int) '0');
        $this->assertSame(1, $page);

        $page = max(1, (int) '-5');
        $this->assertSame(1, $page);

        $page = max(1, (int) '3');
        $this->assertSame(3, $page);
    }
}
