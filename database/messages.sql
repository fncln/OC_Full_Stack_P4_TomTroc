-- Messagerie : table des messages (MySQL / MariaDB).
-- À importer UNE SEULE FOIS, après users.sql et account.sql.

USE `tomtroc`;

CREATE TABLE `messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sender_id` INT UNSIGNED NOT NULL,
    `recipient_id` INT UNSIGNED NOT NULL,
    `content` TEXT NOT NULL,
    `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- Vide tant que le destinataire n'a pas lu le message
    `read_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_messages_sender` (`sender_id`),
    KEY `idx_messages_recipient` (`recipient_id`, `read_at`),
    -- ON DELETE CASCADE : si un membre est supprimé, ses messages le sont aussi
    CONSTRAINT `fk_messages_sender`
        FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_messages_recipient`
        FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Conversations de démonstration (comme sur la maquette, vue depuis le compte Nathalire)
INSERT INTO `messages` (`sender_id`, `recipient_id`, `content`, `sent_at`, `read_at`)
SELECT s.`id`, r.`id`, 'Bonjour ! Votre exemplaire de The Kinfolk Table m''intéresse, est-il toujours disponible ?', NOW() - INTERVAL 3 DAY, NOW() - INTERVAL 3 DAY
FROM `users` s INNER JOIN `users` r ON r.`username` = 'Nathalire' WHERE s.`username` = 'Sas634';

INSERT INTO `messages` (`sender_id`, `recipient_id`, `content`, `sent_at`, `read_at`)
SELECT s.`id`, r.`id`, 'Bonjour Sas634, oui il est disponible. Que proposez-vous en échange ?', NOW() - INTERVAL 3 DAY + INTERVAL 5 MINUTE, NOW() - INTERVAL 2 DAY
FROM `users` s INNER JOIN `users` r ON r.`username` = 'Sas634' WHERE s.`username` = 'Nathalire';

INSERT INTO `messages` (`sender_id`, `recipient_id`, `content`, `sent_at`, `read_at`)
SELECT s.`id`, r.`id`, 'Salut Alexlecture, je viens de découvrir ta bibliothèque. Ton Wabi Sabi est-il encore à échanger ?', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY
FROM `users` s INNER JOIN `users` r ON r.`username` = 'Alexlecture' WHERE s.`username` = 'Nathalire';

-- Ce message n'est pas lu : il apparaît dans le compteur de la Messagerie
INSERT INTO `messages` (`sender_id`, `recipient_id`, `content`, `sent_at`, `read_at`)
SELECT s.`id`, r.`id`, 'Oui, avec plaisir ! Je cherche The Kinfolk Table, ça te dit ?', NOW() - INTERVAL 2 HOUR, NULL
FROM `users` s INNER JOIN `users` r ON r.`username` = 'Nathalire' WHERE s.`username` = 'Alexlecture';
