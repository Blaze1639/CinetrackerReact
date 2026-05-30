<?php

namespace App\Tests\Entity;

use App\Entity\MediaToWatch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(MediaToWatch::class)]
class MediaToWatchTest extends TestCase
{
    private MediaToWatch $item;

    protected function setUp(): void
    {
        $this->item = new MediaToWatch();
    }

    #[TestDox('Valeurs par défaut après instanciation')]
    public function testDefaultValues(): void
    {
        $this->assertSame('film', $this->item->getTypeMedia());
        $this->assertNull($this->item->getId());
        $this->assertNull($this->item->getImageUrl());
    }

    #[TestDox('addedDate est initialisé dans le constructeur')]
    public function testAddedDateIsSetOnConstruct(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->item->getAddedDate());
    }

    #[TestDox('setTitle / getTitle')]
    public function testSetAndGetTitle(): void
    {
        $this->item->setTitle('The Dark Knight');
        $this->assertSame('The Dark Knight', $this->item->getTitle());
    }

    #[TestDox('setTypeMedia / getTypeMedia avec "série"')]
    public function testSetTypeMediaSerie(): void
    {
        $this->item->setTypeMedia('série');
        $this->assertSame('série', $this->item->getTypeMedia());
    }

    #[TestDox('setTypeMedia / getTypeMedia avec "film"')]
    public function testSetTypeMediaFilm(): void
    {
        $this->item->setTypeMedia('film');
        $this->assertSame('film', $this->item->getTypeMedia());
    }

    #[TestDox('setImageUrl / getImageUrl')]
    public function testSetAndGetImageUrl(): void
    {
        $url = 'https://image.tmdb.org/t/p/w500/abc123.jpg';
        $this->item->setImageUrl($url);
        $this->assertSame($url, $this->item->getImageUrl());
    }

    #[TestDox('setImageUrl accepte null')]
    public function testSetImageUrlToNull(): void
    {
        $this->item->setImageUrl(null);
        $this->assertNull($this->item->getImageUrl());
    }

    #[TestDox('setUserId / getUserId')]
    public function testSetAndGetUserId(): void
    {
        $this->item->setUserId(15);
        $this->assertSame(15, $this->item->getUserId());
    }

    #[TestDox('setAddedDate accepte un DateTimeImmutable')]
    public function testSetAddedDate(): void
    {
        $date = new \DateTimeImmutable('2025-06-01 10:00:00');
        $this->item->setAddedDate($date);
        $this->assertSame($date, $this->item->getAddedDate());
    }

    #[TestDox('toArray() contient toutes les clés attendues avec les bonnes valeurs')]
    public function testToArrayStructure(): void
    {
        $this->item->setTitle('Breaking Bad');
        $this->item->setTypeMedia('série');
        $this->item->setImageUrl('https://img.example.com/bb.jpg');
        $this->item->setUserId(3);

        $array = $this->item->toArray();

        foreach (['id', 'title', 'type_media', 'image_url', 'user_id', 'added_date'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }

        $this->assertSame('Breaking Bad', $array['title']);
        $this->assertSame('série', $array['type_media']);
        $this->assertSame('https://img.example.com/bb.jpg', $array['image_url']);
        $this->assertSame(3, $array['user_id']);
    }

    #[TestDox('toArray() formate added_date en Y-m-d H:i:s')]
    public function testToArrayAddedDateFormat(): void
    {
        $this->item->setTitle('Test');
        $this->item->setUserId(1);
        $date = new \DateTimeImmutable('2025-01-15 12:30:00');
        $this->item->setAddedDate($date);

        $this->assertSame('2025-01-15 12:30:00', $this->item->toArray()['added_date']);
    }

    #[TestDox('Les setters sont fluents (retournent $this)')]
    public function testFluentSetters(): void
    {
        $result = $this->item
            ->setTitle('Oppenheimer')
            ->setTypeMedia('film')
            ->setImageUrl('https://img.example.com/oppenheimer.jpg')
            ->setUserId(7);

        $this->assertSame($this->item, $result);
        $this->assertSame('Oppenheimer', $this->item->getTitle());
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
        $this->item->setTypeMedia($type);
        $this->assertSame($type, $this->item->getTypeMedia());
    }
}
