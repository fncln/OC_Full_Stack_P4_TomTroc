<?php

declare(strict_types=1);

namespace App\Manager;

use App\Core\Database;
use App\Entity\Conversation;
use App\Entity\Message;
use DateTimeImmutable;
use PDO;

/**
 * Manager des messages : c'est la seule classe qui écrit des requêtes SQL sur la table "messages".
 */
final class MessageManager
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne les conversations d'un membre (une par contact), de la plus récente à la plus ancienne.
     *
     * @return Conversation[]
     */
    public function findConversations(int $userId): array
    {
        // La sous-requête garde le dernier message de chaque paire de membres,
        // quel que soit celui qui l'a envoyé (LEAST/GREATEST donnent le même couple dans les deux sens)
        $statement = $this->pdo->prepare(
            'SELECT u.id AS contact_id, u.username AS contact_username, u.avatar AS contact_avatar,
                    m.content, m.sent_at,
                    (SELECT COUNT(*) FROM messages r
                     WHERE r.sender_id = u.id AND r.recipient_id = :unread_user_id AND r.read_at IS NULL) AS unread_count
             FROM messages m
             INNER JOIN users u ON u.id = IF(m.sender_id = :me, m.recipient_id, m.sender_id)
             WHERE m.id IN (
                 SELECT MAX(id) FROM messages
                 WHERE sender_id = :sent_user_id OR recipient_id = :received_user_id
                 GROUP BY LEAST(sender_id, recipient_id), GREATEST(sender_id, recipient_id)
             )
             ORDER BY m.id DESC'
        );
        $statement->execute([
            'unread_user_id' => $userId,
            'me' => $userId,
            'sent_user_id' => $userId,
            'received_user_id' => $userId,
        ]);

        $conversations = [];

        foreach ($statement->fetchAll() as $row) {
            $conversation = new Conversation();
            $conversation->setContactId((int) $row['contact_id']);
            $conversation->setContactUsername((string) $row['contact_username']);
            $conversation->setContactAvatar($row['contact_avatar']);
            $conversation->setLastContent((string) $row['content']);
            $conversation->setLastSentAt(new DateTimeImmutable((string) $row['sent_at']));
            $conversation->setUnreadCount((int) $row['unread_count']);
            $conversations[] = $conversation;
        }

        return $conversations;
    }

    /**
     * Retourne tous les messages échangés entre deux membres, du plus ancien au plus récent.
     *
     * @return Message[]
     */
    public function findThread(int $userId, int $contactId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, sender_id, recipient_id, content, sent_at, read_at
             FROM messages
             WHERE (sender_id = :me_sender AND recipient_id = :contact_recipient)
                OR (sender_id = :contact_sender AND recipient_id = :me_recipient)
             ORDER BY id ASC'
        );
        $statement->execute([
            'me_sender' => $userId,
            'contact_recipient' => $contactId,
            'contact_sender' => $contactId,
            'me_recipient' => $userId,
        ]);

        $messages = [];

        foreach ($statement->fetchAll() as $row) {
            $messages[] = $this->createMessageFromRow($row);
        }

        return $messages;
    }

    /**
     * Enregistre un nouveau message et retourne son identifiant.
     * La date d'envoi est ajoutée par la base de données.
     */
    public function create(Message $message): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO messages (sender_id, recipient_id, content) VALUES (:sender_id, :recipient_id, :content)'
        );
        $statement->execute([
            'sender_id' => $message->getSenderId(),
            'recipient_id' => $message->getRecipientId(),
            'content' => $message->getContent(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Marque comme lus les messages que ce contact a envoyés au membre.
     */
    public function markAsRead(int $userId, int $contactId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE messages SET read_at = NOW()
             WHERE recipient_id = :user_id AND sender_id = :contact_id AND read_at IS NULL'
        );
        $statement->execute(['user_id' => $userId, 'contact_id' => $contactId]);
    }

    /**
     * Retourne le nombre de messages reçus par le membre et pas encore lus.
     */
    public function countUnread(int $userId): int
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM messages WHERE recipient_id = :user_id AND read_at IS NULL'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Transforme une ligne de la base de données en objet Message.
     *
     * @param array<string, mixed> $row
     */
    private function createMessageFromRow(array $row): Message
    {
        $message = new Message();
        $message->setId((int) $row['id']);
        $message->setSenderId((int) $row['sender_id']);
        $message->setRecipientId((int) $row['recipient_id']);
        $message->setContent((string) $row['content']);
        $message->setSentAt(new DateTimeImmutable((string) $row['sent_at']));

        // read_at vaut null tant que le message n'est pas lu
        if ($row['read_at'] !== null) {
            $message->setReadAt(new DateTimeImmutable((string) $row['read_at']));
        }

        return $message;
    }
}
