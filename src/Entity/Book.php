<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

/**
 * Entité Book : représente un livre proposé à l'échange (une ligne de la table "books").
 * Elle ne contient que des données : les requêtes SQL sont écrites dans BookManager.
 */
final class Book
{
    /** Valeur de la colonne "status" pour un livre disponible à l'échange */
    public const STATUS_AVAILABLE = 'available';

    /** Valeur de la colonne "status" pour un livre qui n'est plus disponible */
    public const STATUS_UNAVAILABLE = 'unavailable';

    private int $id = 0;
    private string $title = '';
    private string $author = '';
    private string $description = '';
    private ?string $image = null;
    private string $status = self::STATUS_AVAILABLE;
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Nom du fichier image du livre (dans public/uploads/books/), ou null s'il n'a pas de photo.
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Découpe la description en paragraphes : une ligne vide sépare deux paragraphes.
     *
     * @return string[]
     */
    public function getDescriptionParagraphs(): array
    {
        // \R = un retour à la ligne (Windows ou autre), "{2,}" = au moins deux de suite
        $paragraphs = preg_split('/\R{2,}/', trim($this->description));

        if ($paragraphs === false) {
            return [$this->description];
        }

        return $paragraphs;
    }

    /**
     * Indique si le livre a une photo.
     */
    public function hasImage(): bool
    {
        return strlen((string) $this->image) > 0;
    }

    /**
     * Indique si le livre est disponible à l'échange.
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Indique si le livre n'est plus disponible à l'échange.
     */
    public function isUnavailable(): bool
    {
        return $this->status === self::STATUS_UNAVAILABLE;
    }
}
