CREATE DATABASE IF NOT EXISTS `tomtroc` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tomtroc`;

-- Attention : la table est supprimée puis recréée à chaque import (usage en développement).
DROP TABLE IF EXISTS `books`;

-- Création de la table books
CREATE TABLE IF NOT EXISTS `books` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `status` ENUM('available', 'unavailable') NOT NULL DEFAULT 'available',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertion de tous les livres
INSERT INTO `books` (`title`, `author`, `description`, `image`, `status`) VALUES
(
    'The Two Towers',
    'J.R.R. Tolkien',
    'Deuxième partie de la célèbre trilogie du Seigneur des Anneaux, suivant la séparation de la communauté et la quête vers le Mordor.',
    'livre-the-two-towers-j-r-r-tolkien.jpg',
    'available'
),
(
    'Company of One',
    'Paul Jarvis',
    'Un guide de stratégie d''entreprise expliquant pourquoi rester une petite structure dynamique peut être la meilleure décision pour réussir.',
    'livre-company-of-one-paul-jarvis.jpg',
    'available'
),
(
    'Narnia',
    'C.S. Lewis',
    'L''intégrale des récits fantastiques à travers le monde magique de Narnia, peuplé d''animaux doués de parole et de créatures mythologiques.',
    'livre-narnia-c-s-lewis.jpg',
    'unavailable'
),
(
    'The Subtle Art of Not Giving a Fuck',
    'Mark Manson',
    'Un livre de développement personnel à contre-courant qui propose une approche réaliste et directe pour se concentrer sur ce qui importe vraiment.',
    'livre-the-subtle-art-of-not-giving-a-fuck-mark-manson.jpg',
    'available'
),
(
    'A Book Full of Hope',
    'Rupi Kaur',
    'Un recueil poétique et inspirant explorant les thèmes de la guérison, de la résilience et de l''espoir au quotidien.',
    'livre-a-book-full-of-hope-rupi-kaur.jpg',
    'available'
),
(
    'Thinking, Fast and Slow',
    'Daniel Kahneman',
    'Une analyse approfondie des deux systèmes qui régissent notre façon de penser : l''intuitif et rapide, et le logique et lent.',
    'livre-thinking-fast-and-slow-daniel-kahneman.jpg',
    'unavailable'
),
(
    'Psalms',
    'Alabaster',
    'Une édition artistique et contemporaine du livre des Psaumes, mêlant texte sacré et photographies épurées.',
    'livre-psalms-alabaster.jpg',
    'available'
),
(
    'Innovation',
    'Matt Ridley',
    'Une étude fascinante sur la manière dont les innovations émergent, se diffusent et façonnent le progrès humain.',
    'livre-innovation-matt-ridley.jpg',
    'available'
),
(
    'Hygge',
    'Meik Wiking',
    'Le guide officiel du bonheur à la danoise, dévoilant les secrets du réconfort, du bien-être et du partage.',
    'livre-hygge-meik-wiking.jpg',
    'available'
),
(
    'Minimalist Graphics',
    'Julia Schonlau',
    'Un ouvrage inspirant compilant des créations graphiques modernes axées sur le minimalisme et la simplicité visuelle.',
    'livre-minimalist-graphics-julia-schonlau.jpg',
    'available'
),
(
    'Milwaukee Mission',
    'Elder Cooper Low',
    'Un livre souvenir institutionnel de mission retraçant l''expérience au sein de la mission de Milwaukee dans le Wisconsin.',
    'livre-milwaukee-mission-elder-cooper-low.jpg',
    'available'
),
(
    'Delight',
    'Justin Rossow',
    'Une réflexion théologique et pratique sur le discipletude perçu comme une aventure relationnelle d''amour partagé.',
    'livre-delight-justin-rossow.jpg',
    'unavailable'
),
(
    'Milk & Honey',
    'Rupi Kaur',
    'Un recueil de poésie et de prose sur la survie, la violence, l''abus, l''amour, la perte et la féminité.',
    'livre-milk-and-honey-rupi-kaur.jpg',
    'available'
),
(
    'Wabi Sabi',
    'Beth Kempton',
    'Un voyage littéraire inspirant sur l''art japonais de trouver la beauté dans l''imperfection et la simplicité.',
    'livre-wabi-sabi-beth-kempton.jpg',
    'available'
),
(
    'The Kinfolk Table',
    'Nathan Williams',
    'J\'ai récemment plongé dans les pages de \'The Kinfolk Table\' et j\'ai été enchanté par cette œuvre captivante. Ce livre va bien au-delà d\'une simple collection de recettes ; il célèbre l\'art de partager des moments authentiques autour de la table.\r\n\r\nLes photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des recettes et des histoires qui mettent en avant la beauté de la simplicité et de la convivialité.\r\n\r\nChaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers.\r\n\r\n\'The Kinfolk Table\' incarne parfaitement l\'esprit de la cuisine et de la camaraderie, et il est certain que ce livre trouvera une place spéciale dans le cœur de tout amoureux de la cuisine et des rencontres inspirantes.',
    'livre-the-kinfolk-table-nathan-williams.jpg',
    'available'
),
(
    'Esther',
    'Alabaster',
    'Un ouvrage captivant mis en avant dans la bibliothèque de la plateforme.',
    'livre-esther-alabaster.jpg',
    'available'
);