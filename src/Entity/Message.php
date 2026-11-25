<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

/**
 * Entité Message : un message envoyé d'un membre à un autre (une ligne de la table "messages").
 * Elle ne contient que des données : les requêtes SQL sont écrites dans MessageManager.
 */
final class Message
{
    private int $id = 0;
    private int $senderId = 0;
    private int $recipientId = 0;
    private string $content = '';
    private ?DateTimeImmutable $sentAt = null;
    private ?DateTimeImmutable $readAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Identifiant du membre qui a écrit le message.
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function setSenderId(int $senderId): void
    {
        $this->senderId = $senderId;
    }

    /**
     * Identifiant du membre qui reçoit le message.
     */
    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    public function setRecipientId(int $recipientId): void
    {
        $this->recipientId = $recipientId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTimeImmutable $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    /**
     * Date de lecture par le destinataire, ou null si le message n'a pas encore été lu.
     */
    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?DateTimeImmutable $readAt): void
    {
        $this->readAt = $readAt;
    }

    /**
     * Indique si le destinataire a lu le message.
     */
    public function isRead(): bool
    {
        return $this->readAt !== null;
    }
}
