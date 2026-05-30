<?php

namespace App\Tests\Entity;

use App\Entity\Media;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Media::class)]
class MediaTest extends TestCase
{
    private Media $media;

    protected function setUp(): void
    {
        $this->media = new Media();
    }

    #[TestDox('Valeurs par défaut après instanciation')]
    public function testDefaultValues(): void
    {
        $this->assertSame('film', $this->media->getTypeMedia());
        $this->assertFalse($this->media->isFavorite());
        $this->assertSame(0, $this->media->getViewCount());
        $this->assertNull($this->media->getId());
        $this->assertNull($this->media->getRating());
        $this->assertNull($this->media->getImageUrl());
        $this->assertNull($this->media->getCommentaire());
        $this->assertNull($this->media->getFavoriteMoment());
    }

    #[TestDox('createdAt est initialisé dans le constructeur')]
    public function testCreatedAtIsSetOnConstruct(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->media->getCreatedAt());
    }

    #[TestDox('setTitle / getTitle')]
    public function testSetAndGetTitle(): void
    {
        $this->media->setTitle('Inception');
        $this->assertSame('Inception', $this->media->getTitle());
    }

    #[TestDox('setRating / getRating avec une valeur décimale')]
    public function testSetAndGetRating(): void
    {
        $this->media->setRating('4.5');
        $this->assertSame('4.5', $this->media->getRating());
    }

    #[TestDox('setRating accepte null')]
    public function testSetRatingToNull(): void
    {
        $this->media->setRating(null);
        $this->assertNull($this->media->getRating());
    }

    #[TestDox('setTypeMedia / getTypeMedia')]
    public function testSetAndGetTypeMedia(): void
    {
        $this->media->setTypeMedia('série');
        $this->assertSame('série', $this->media->getTypeMedia());
    }

    #[TestDox('setImageUrl / getImageUrl')]
    public function testSetAndGetImageUrl(): void
    {
        $this->media->setImageUrl('https://example.com/poster.jpg');
        $this->assertSame('https://example.com/poster.jpg', $this->media->getImageUrl());
    }

    #[TestDox('setFavorite(true) → isFavorite() retourne true')]
    public function testSetFavoriteTrue(): void
    {
        $this->media->setFavorite(true);
        $this->assertTrue($this->media->isFavorite());
    }

    #[TestDox('setFavorite(false) → isFavorite() retourne false')]
    public function testSetFavoriteFalse(): void
    {
        $this->media->setFavorite(true);
        $this->media->setFavorite(false);
        $this->assertFalse($this->media->isFavorite());
    }

    #[TestDox('Toggle favori via setFavorite(!isFavorite())')]
    public function testToggleFavorite(): void
    {
        $this->assertFalse($this->media->isFavorite());
        $this->media->setFavorite(!$this->media->isFavorite());
        $this->assertTrue($this->media->isFavorite());
        $this->media->setFavorite(!$this->media->isFavorite());
        $this->assertFalse($this->media->isFavorite());
    }

    #[TestDox('setUserId / getUserId')]
    public function testSetAndGetUserId(): void
    {
        $this->media->setUserId(42);
        $this->assertSame(42, $this->media->getUserId());
    }

    #[TestDox('setViewCount / getViewCount')]
    public function testSetAndGetViewCount(): void
    {
        $this->media->setViewCount(7);
        $this->assertSame(7, $this->media->getViewCount());
    }

    #[TestDox('Incrémenter le compteur de vues')]
    public function testIncrementViewCount(): void
    {
        $this->media->setViewCount(3);
        $this->media->setViewCount($this->media->getViewCount() + 1);
        $this->assertSame(4, $this->media->getViewCount());
    }

    #[TestDox('setCommentaire / getCommentaire')]
    public function testSetAndGetCommentaire(): void
    {
        $this->media->setCommentaire('Excellent film.');
        $this->assertSame('Excellent film.', $this->media->getCommentaire());
    }

    #[TestDox('setCommentaire accepte null')]
    public function testSetCommentaireToNull(): void
    {
        $this->media->setCommentaire(null);
        $this->assertNull($this->media->getCommentaire());
    }

    #[TestDox('setFavoriteMoment / getFavoriteMoment')]
    public function testSetAndGetFavoriteMoment(): void
    {
        $this->media->setFavoriteMoment('La scène du rêve dans le rêve');
        $this->assertSame('La scène du rêve dans le rêve', $this->media->getFavoriteMoment());
    }

    #[TestDox('toArray() contient toutes les clés attendues avec les bonnes valeurs')]
    public function testToArrayStructure(): void
    {
        $this->media->setTitle('Interstellar');
        $this->media->setRating('5.0');
        $this->media->setTypeMedia('film');
        $this->media->setImageUrl('https://img.example.com/inter.jpg');
        $this->media->setUserId(1);
        $this->media->setFavorite(true);
        $this->media->setViewCount(3);
        $this->media->setCommentaire('Magnifique');

        $array = $this->media->toArray();

        foreach (['id', 'title', 'rating', 'image_url', 'type_media', 'created_at', 'favorite', 'user_id', 'view_count', 'commentaire', 'favorite_moment'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }

        $this->assertSame('Interstellar', $array['title']);
        $this->assertSame('5.0', $array['rating']);
        $this->assertSame('film', $array['type_media']);
        $this->assertSame(1, $array['favorite']);
        $this->assertSame(3, $array['view_count']);
        $this->assertSame('Magnifique', $array['commentaire']);
    }

    #[TestDox('toArray() cast favorite en int (0 ou 1)')]
    public function testToArrayFavoriteIsCastToInt(): void
    {
        $this->media->setTitle('Test');
        $this->media->setUserId(1);

        $this->media->setFavorite(false);
        $this->assertSame(0, $this->media->toArray()['favorite']);

        $this->media->setFavorite(true);
        $this->assertSame(1, $this->media->toArray()['favorite']);
    }

    #[TestDox('created_at dans toArray() est formaté Y-m-d H:i:s')]
    public function testToArrayCreatedAtFormat(): void
    {
        $this->media->setTitle('Test');
        $this->media->setUserId(1);
        $date = new \DateTimeImmutable('2025-03-15 08:30:00');
        $this->media->setCreatedAt($date);

        $this->assertSame('2025-03-15 08:30:00', $this->media->toArray()['created_at']);
    }

    #[TestDox('Les setters sont fluents (retournent $this)')]
    public function testFluentSetters(): void
    {
        $result = $this->media
            ->setTitle('Dune')
            ->setTypeMedia('film')
            ->setRating('4.0')
            ->setUserId(10);

        $this->assertSame($this->media, $result);
    }

    /** @return array<string, array{string}> */
    public static function typeMediaProvider(): array
    {
        return [
            'film'  => ['film'],
            'série' => ['série'],
        ];
    }

    #[DataProvider('typeMediaProvider')]
    #[TestDox('setTypeMedia accepte film et série')]
    public function testValidTypeMedia(string $type): void
    {
        $this->media->setTypeMedia($type);
        $this->assertSame($type, $this->media->getTypeMedia());
    }
}
