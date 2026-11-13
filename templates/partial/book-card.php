<?php
/**
 * Carte d'un livre : image, étiquette "non dispo.", titre et auteur.
 * Utilisée sur la page d'accueil et sur la page "Nos livres à l'échange".
 *
 * @var App\Core\View $this
 * @var App\Entity\Book $book
 * @var int $headingLevel Niveau du titre de la carte : 2 si elle suit un titre h1, 3 si elle suit un titre h2
 */
?>
<article class="book-card">
<?php if ($book->hasImage()) : ?>
    <img class="book-card__image"
         src="<?= $this->escape($this->url('/uploads/books/' . $book->getImage())) ?>"
         width="400" height="400" alt="">
<?php endif; ?>
<?php if ($book->isUnavailable()) : ?>
    <span class="tag tag--unavailable book-card__tag">non dispo.</span>
<?php endif; ?>
    <div class="book-card__body">
        <h<?= $headingLevel ?> class="book-card__title"><?= $this->escape($book->getTitle()) ?></h<?= $headingLevel ?>>
        <p class="book-card__author"><?= $this->escape($book->getAuthor()) ?></p>
    </div>
</article>
