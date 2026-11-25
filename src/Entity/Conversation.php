<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

/**
 * Entité Conversation : résumé des échanges avec un autre membre, pour la liste de la messagerie
 * (le contact, son dernier message et le nombre de messages non lus).
 * Ce n'est pas une table : elle est construite par MessageManager à partir des messages.
 */
final class Conversation
{
    private int $contactId = 0;
    private string $contactUsername = '';
    private ?string $contactAvatar = null;
    private string $lastContent = '';
    private ?DateTimeImmutable $lastSentAt = null;
    private int $unreadCount = 0;

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function setContactId(int $contactId): void
    {
        $this->contactId = $contactId;
    }

    public function getContactUsername(): string
    {
        return $this->contactUsername;
    }

    public function setContactUsername(string $contactUsername): void
    {
        $this->contactUsername = $contactUsername;
    }

    /**
     * Nom du fichier de la photo du contact, ou null s'il n'en a pas.
     */
    public function getContactAvatar(): ?string
    {
        return $this->contactAvatar;
    }

    public function setContactAvatar(?string $contactAvatar): void
    {
        $this->contactAvatar = $contactAvatar;
    }

    /**
     * Texte du dernier message échangé (envoyé ou reçu).
     */
    public function getLastContent(): string
    {
        return $this->lastContent;
    }

    public function setLastContent(string $lastContent): void
    {
        $this->lastContent = $lastContent;
    }

    public function getLastSentAt(): ?DateTimeImmutable
    {
        return $this->lastSentAt;
    }

    public function setLastSentAt(?DateTimeImmutable $lastSentAt): void
    {
        $this->lastSentAt = $lastSentAt;
    }

    /**
     * Nombre de messages reçus de ce contact et pas encore lus.
     */
    public function getUnreadCount(): int
    {
        return $this->unreadCount;
    }

    public function setUnreadCount(int $unreadCount): void
    {
        $this->unreadCount = $unreadCount;
    }
}
