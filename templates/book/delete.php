<?php
/**
 * Page de confirmation avant la suppression d'un livre.
 *
 * @var App\Core\View $this
 * @var App\Entity\Book $book Le livre à supprimer
 */
?>
<div class="book-delete container">
    <h1 class="page-title">Supprimer ce livre</h1>
    <p class="book-delete__text">
        Voulez-vous vraiment supprimer « <?= $this->escape($book->getTitle()) ?> » de votre bibliothèque ?
        Cette action est définitive.
    </p>
    <form class="book-delete__form" action="<?= $this->escape($this->url('/books/' . $book->getId() . '/delete')) ?>"
          method="post">
        <button class="btn btn--primary" type="submit">Oui, supprimer</button>
        <a class="btn btn--outline" href="<?= $this->escape($this->url('/account')) ?>">Annuler</a>
    </form>
</div>
