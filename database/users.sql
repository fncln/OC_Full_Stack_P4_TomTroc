-- Membres du site et propriétaire de chaque livre (MySQL / MariaDB).
-- À importer UNE SEULE FOIS, par-dessus la base "tomtroc" déjà créée avec les livres.
-- Le mot de passe de tous les comptes de démonstration est : Password123!

USE `tomtroc`;

-- 1. Table des membres
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(30) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    -- Hash du mot de passe créé par password_hash() : jamais le mot de passe en clair
    `password` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    -- Deux membres ne peuvent pas avoir le même pseudo, ni la même adresse email
    UNIQUE KEY `uq_users_username` (`username`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB;

-- 2. Membres de démonstration (les vendeurs de la maquette).
-- Le mot de passe est le même pour tous, mais chaque hash est différent : PHP ajoute un "sel" aléatoire.
INSERT INTO `users` (`username`, `email`, `password`) VALUES
    ('Alexlecture', 'alexlecture@mail.com', '$2y$12$4IiRLKQo8bwDdHjbfRrVE.HOvMZB6i29bPYUj0bzLJB42PJISY0Za'),
    ('Nathalire', 'nathalie@mail.com', '$2y$12$KhNlnXJlWWDtnqO2.O3peeN.j654WrZwI62ErBO1zYwsDvGaSLB7a'),
    ('Sas634', 'sas634@mail.com', '$2y$12$OHv4OC1W0Kxx50.qPM8I9uNowiI2dLCJBwsYPE737vjyUk4H0.W/m'),
    ('CamilleClubLit', 'camille@mail.com', '$2y$12$H8DOyEfGKOgxKjb02/4uFuSz7r8vURtl45NvuJNCsiHygMX9df74u'),
    ('Hugo1990_12', 'hugo@mail.com', '$2y$12$2AxbMH6yT.u3DNvxEK/WweDcjeTdjz.d27NO14KmjqN7S3HENRKoa'),
    ('Juju1432', 'juju@mail.com', '$2y$12$Nel.H6SI5zPTof7EEhHPDOUH7D/k.k04zEkWRJd.ojGY9/9HfJ2Ae'),
    ('Christiane75014', 'christiane@mail.com', '$2y$12$cF1t73PDJOiSyIYC6f57XOCy759mo28mcjwtDGx/kWe0zocTRHIx.'),
    ('Hamzalecture', 'hamza@mail.com', '$2y$12$oJOmHabVluncI4xF6l.Ow.U0jAsLxNzfji9OxT5gM/S916b2RXGF6'),
    ('Lou&Ben50', 'louben@mail.com', '$2y$12$JKxMCmm9fpzevjulJ.409ORCWWFQ9boatQ37mN2/jJ4GGErdvnXPy'),
    ('Lolobzh', 'lolo@mail.com', '$2y$12$okgwT2PSVd1HxIF/mTx4nu5r61CDhkREF1DGgaOWS2cbJxCh5n4fq'),
    ('ML95', 'ml95@mail.com', '$2y$12$P10q90CuVLYiPFz5dfNtjOO8WPgsWxHVPvijLztzQpKNreTQdHGgG'),
    ('Verogo33', 'vero@mail.com', '$2y$12$199HLW7OWd5cVJAWtnUiZu6m1tqp.v6/dBByAgeC44cm68YpdaYka'),
    ('AnnikaBrahms', 'annika@mail.com', '$2y$12$DNetANOe2rCz4GgPDpjq8eUvZlZqILE7R2St46xOULsLIZ/DUbK5C'),
    ('Victoirefabr912', 'victoire@mail.com', '$2y$12$6z2KxFlkIjqqDuh8RmqQjueek4URbkjCyBRPsntmPPHR7m4/zd1nK'),
    ('Lotrfanclub67', 'lotr@mail.com', '$2y$12$0mEcNhxKgRDFNM0Yc34xeebJfNix.pTs8T1N5lRkub9aQrZAumLHi');

-- 3. Chaque livre appartient à un membre : on ajoute d'abord la colonne (vide)...
ALTER TABLE `books`
    ADD COLUMN `user_id` INT UNSIGNED NULL AFTER `id`;

-- ...puis on la remplit avec le vendeur de chaque livre
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Lotrfanclub67' SET b.`user_id` = u.`id` WHERE b.`title` = 'The Two Towers';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Victoirefabr912' SET b.`user_id` = u.`id` WHERE b.`title` = 'Company of One';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'AnnikaBrahms' SET b.`user_id` = u.`id` WHERE b.`title` = 'Narnia';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Verogo33' SET b.`user_id` = u.`id` WHERE b.`title` = 'The Subtle Art of Not Giving a Fuck';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'ML95' SET b.`user_id` = u.`id` WHERE b.`title` = 'A Book Full of Hope';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Sas634' SET b.`user_id` = u.`id` WHERE b.`title` = 'Thinking, Fast and Slow';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Lolobzh' SET b.`user_id` = u.`id` WHERE b.`title` = 'Psalms';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Lou&Ben50' SET b.`user_id` = u.`id` WHERE b.`title` = 'Innovation';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Hugo1990_12' SET b.`user_id` = u.`id` WHERE b.`title` = 'Hygge';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Hamzalecture' SET b.`user_id` = u.`id` WHERE b.`title` = 'Minimalist Graphics';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Christiane75014' SET b.`user_id` = u.`id` WHERE b.`title` = 'Milwaukee Mission';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Juju1432' SET b.`user_id` = u.`id` WHERE b.`title` = 'Delight';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Hugo1990_12' SET b.`user_id` = u.`id` WHERE b.`title` = 'Milk & Honey';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Alexlecture' SET b.`user_id` = u.`id` WHERE b.`title` = 'Wabi Sabi';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'Nathalire' SET b.`user_id` = u.`id` WHERE b.`title` = 'The Kinfolk Table';
UPDATE `books` b INNER JOIN `users` u ON u.`username` = 'CamilleClubLit' SET b.`user_id` = u.`id` WHERE b.`title` = 'Esther';

-- Sécurité : un livre éventuellement ajouté à la main est confié au premier membre
UPDATE `books` SET `user_id` = (SELECT MIN(`id`) FROM `users`) WHERE `user_id` IS NULL;

-- 4. La colonne devient obligatoire, avec une clé étrangère : impossible d'avoir un livre sans membre valide.
-- ON DELETE CASCADE : si un membre est supprimé, ses livres le sont aussi.
ALTER TABLE `books`
    MODIFY `user_id` INT UNSIGNED NOT NULL,
    ADD KEY `idx_books_user` (`user_id`),
    ADD CONSTRAINT `fk_books_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;
