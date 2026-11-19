<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

/**
 * Entité User : représente un membre du site (une ligne de la table "users").
 * Elle ne contient que des données : les requêtes SQL sont écrites dans UserManager.
 */
final class User
{
    private int $id = 0;
    private string $username = '';
    private string $email = '';
    private string $password = '';
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retourne le hash du mot de passe (jamais le mot de passe en clair).
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Enregistre le hash du mot de passe, créé avec password_hash().
     */
    public function setPassword(string $passwordHash): void
    {
        $this->password = $passwordHash;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
