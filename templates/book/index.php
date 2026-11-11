<?php
/**
 * Page "Nos livres à l'échange" : champ de recherche et liste des livres.
 *
 * @var App\Core\View $this
 * @var App\Entity\Book[] $books Livres à afficher
 * @var string $searchTerm       Texte recherché (vide si aucune recherche)
 */
?>
<div class="page-books container">
    <div class="page-books__header">
        <h1 class="page-title">Nos livres à l'échange</h1>
        <form class="search" action="<?= $this->escape($this->url('/books')) ?>" method="get" role="search">
            <label class="search__label" for="search-books">Rechercher un livre</label>
            <input class="search__input" id="search-books" type="search" name="q"
                   value="<?= $this->escape($searchTerm) ?>">
            <button class="search__button" type="submit">Rechercher</button>
        </form>
    </div>
<?php if ($books === []) : ?>
    <p class="page-books__empty">
<?php if ($searchTerm === '') : ?>
        Aucun livre n'est proposé pour le moment.
<?php else : ?>
        Aucun livre ne correspond à « <?= $this->escape($searchTerm) ?> ».
<?php endif; ?>
    </p>
<?php else : ?>
    <ul class="book-grid">
<?php foreach ($books as $book) : ?>
        <li class="book-grid__item">
            <article class="book-card">
<?php if ($book->hasImage()) : ?>
                <img class="book-card__image"
                     src="<?= $this->escape($this->url('/uploads/books/' . $book->getImage())) ?>"
                     width="400" height="400" alt="">
<?php endif; ?>
<?php if ($book->isUnavailable()) : ?>
                <span class="tag tag--unavailable book-card__tag">non dispo.</span>
<?php endif; ?>
                <h2 class="book-card__title"><?= $this->escape($book->getTitle()) ?></h2>
                <p class="book-card__author"><?= $this->escape($book->getAuthor()) ?></p>
            </article>
        </li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>
