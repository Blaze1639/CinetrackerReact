<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: 'media')]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1, nullable: true)]
    private ?string $rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $favoriteMoment = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 10)]
    private string $typeMedia = 'film';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(options: ['default' => 0])]
    private bool $favorite = false;

    #[ORM\Column]
    private int $userId;

    #[ORM\Column(options: ['default' => 0])]
    private int $viewCount = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getRating(): ?string { return $this->rating; }
    public function setRating(?string $rating): static { $this->rating = $rating; return $this; }

    public function getFavoriteMoment(): ?string { return $this->favoriteMoment; }
    public function setFavoriteMoment(?string $v): static { $this->favoriteMoment = $v; return $this; }

    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): static { $this->imageUrl = $imageUrl; return $this; }

    public function getTypeMedia(): string { return $this->typeMedia; }
    public function setTypeMedia(string $typeMedia): static { $this->typeMedia = $typeMedia; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $v): static { $this->createdAt = $v; return $this; }

    public function isFavorite(): bool { return $this->favorite; }
    public function setFavorite(bool $favorite): static { $this->favorite = $favorite; return $this; }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): static { $this->userId = $userId; return $this; }

    public function getViewCount(): int { return $this->viewCount; }
    public function setViewCount(int $viewCount): static { $this->viewCount = $viewCount; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $commentaire): static { $this->commentaire = $commentaire; return $this; }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'rating'         => $this->rating,
            'image_url'      => $this->imageUrl,
            'type_media'     => $this->typeMedia,
            'created_at'     => $this->createdAt->format('Y-m-d H:i:s'),
            'favorite'       => (int) $this->favorite,
            'user_id'        => $this->userId,
            'view_count'     => $this->viewCount,
            'commentaire'    => $this->commentaire,
            'favorite_moment'=> $this->favoriteMoment,
        ];
    }
}
