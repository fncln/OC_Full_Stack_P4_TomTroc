<?php
/**
 * Gabarit commun à toutes les pages : le header et le footer sont écrits ici, une seule fois.
 *
 * @var App\Core\View $this
 * @var string $title      Titre de la page
 * @var string $activePage Page courante, pour repérer le lien actif du menu
 * @var string $content    HTML de la page, déjà généré par View::render()
 * @var App\Entity\User|null $currentUser Membre connecté (null pour un visiteur)
 * @var int $unreadCount Nombre de messages non lus du membre connecté
 * @var array<int, array{type: string, message: string}> $flashes Messages à afficher une fois
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->escape($title) ?> - Tom Troc</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&amp;family=Playfair+Display&amp;display=swap">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/base.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/layout.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/books.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/buttons.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/home.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/book-detail.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/forms.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/auth.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/library.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/account.css')) ?>">
    <link rel="stylesheet" href="<?= $this->escape($this->url('/assets/css/member.css')) ?>">

</head>
<body>
<a class="skip-link" href="#content">Aller au contenu</a>
<?php $this->partial('header', ['activePage' => $activePage, 'currentUser' => $currentUser, 'unreadCount' => $unreadCount]) ?>
<main class="main" id="content">
<?php $this->partial('flashes', ['flashes' => $flashes]) ?>
<?= $content ?>
</main>
<?php $this->partial('footer') ?>
</body>
</html>
