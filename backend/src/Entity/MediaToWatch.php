<?php

namespace App\Entity;

use App\Repository\MediaToWatchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaToWatchRepository::class)]
#[ORM\Table(name: 'media_to_watch')]
class MediaToWatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 10)]
    private string $typeMedia = 'film';

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column]
    private int $userId;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $addedDate;

    public function __construct()
    {
        $this->addedDate = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getTypeMedia(): string { return $this->typeMedia; }
    public function setTypeMedia(string $typeMedia): static { $this->typeMedia = $typeMedia; return $this; }

    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): static { $this->imageUrl = $imageUrl; return $this; }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): static { $this->userId = $userId; return $this; }

    public function getAddedDate(): \DateTimeImmutable { return $this->addedDate; }
    public function setAddedDate(\DateTimeImmutable $addedDate): static { $this->addedDate = $addedDate; return $this; }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'type_media' => $this->typeMedia,
            'image_url'  => $this->imageUrl,
            'user_id'    => $this->userId,
            'added_date' => $this->addedDate->format('Y-m-d H:i:s'),
        ];
    }
}
