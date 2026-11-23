-- Photo de profil des membres (MySQL / MariaDB).
-- À importer UNE SEULE FOIS, après users.sql.

USE `tomtroc`;

-- Nom du fichier de la photo (dans public/uploads/avatars/) : vide tant que le membre n'a pas de photo
ALTER TABLE `users`
    ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `password`;

-- Photos de deux membres de démonstration
UPDATE `users` SET `avatar` = 'avatar-alexlecture.png' WHERE `username` = 'Alexlecture';
UPDATE `users` SET `avatar` = 'avatar-nathalire.png' WHERE `username` = 'Nathalire';
UPDATE `users` SET `avatar` = 'avatar-Sas634.png' WHERE `username` = 'Sas634';

-- Les membres de démonstration sont inscrits depuis un an (comme sur la maquette)
UPDATE `users` SET `created_at` = DATE_SUB(`created_at`, INTERVAL 1 YEAR);
