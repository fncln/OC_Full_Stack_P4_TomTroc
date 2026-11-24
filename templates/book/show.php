<?php
/**
 * Page d'un livre : image, titre, auteur et description.
 *
 * @var App\Core\View $this
 * @var App\Entity\Book $book
 */
?>
<div class="breadcrumb-bar">
    <nav class="breadcrumb container" aria-label="Fil d'Ariane">
        <ol class="breadcrumb__list">
            <li><a class="breadcrumb__link" href="<?= $this->escape($this->url('/books')) ?>">Nos livres</a></li>
            <li aria-current="page"><?= $this->escape($book->getTitle()) ?></li>
        </ol>
    </nav>
</div>
<article class="book-detail">
<?php if ($book->hasImage()) : ?>
    <div class="book-detail__media">
        <img class="book-detail__image"
             src="<?= $this->escape($this->url('/uploads/books/' . $book->getImage())) ?>"
             width="720" height="720" alt="Couverture du livre <?= $this->escape($book->getTitle()) ?>">
    </div>
<?php endif; ?>
    <div class="book-detail__content">
        <h1 class="book-detail__title"><?= $this->escape($book->getTitle()) ?></h1>
        <p class="book-detail__author">par <?= $this->escape($book->getAuthor()) ?></p>
        <hr class="book-detail__rule">
        <h2 class="label-title">Description</h2>
        <div class="book-detail__description">
<?php foreach ($book->getDescriptionParagraphs() as $paragraph) : ?>
            <p><?= nl2br($this->escape($paragraph)) ?></p>
<?php endforeach; ?>
        </div>
        <h2 class="label-title book-detail__owner-label">Propriétaire</h2>
        <p class="owner-chip">
            <img class="owner-chip__avatar" src="<?= $this->escape($this->avatarurl($book->getOwnerAvatar())) ?>"
                 width="48" height="48" alt="">
            <span class="owner-chip__name"><?= $this->escape($book->getOwnerUsername()) ?></span>
        </p>
    </div>
</article>
