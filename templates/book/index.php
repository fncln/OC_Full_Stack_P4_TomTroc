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
            <label class="sr-only" for="search-books">Rechercher un livre</label>
            <button class="search__button" type="submit">
                <img src="<?= $this->escape($this->url('/assets/img/icon-search.svg')) ?>" width="20" height="20" alt="">
                <span class="sr-only">Lancer la recherche</span>
            </button>
            <input class="search__input" id="search-books" type="search" name="q"
                   value="<?= $this->escape($searchTerm) ?>" placeholder="Rechercher un livre">
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
            <?php $this->partial('book-card', ['book' => $book, 'headingLevel' => 2]) ?>
        </li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>
