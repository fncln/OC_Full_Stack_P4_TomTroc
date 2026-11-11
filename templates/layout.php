<?php
/**
 * Gabarit commun à toutes les pages : le header et le footer sont écrits ici, une seule fois.
 *
 * @var App\Core\View $this
 * @var string $title      Titre de la page
 * @var string $activePage Page courante, pour repérer le lien actif du menu
 * @var string $content    HTML de la page, déjà généré par View::render()
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->escape($title) ?> - Tom Troc</title>
</head>
<body>
<?php $this->partial('header', ['activePage' => $activePage]) ?>
<main class="main">
<?= $content ?>
</main>
<?php $this->partial('footer') ?>
</body>
</html>
