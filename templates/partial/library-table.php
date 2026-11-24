<?php
/**
 * Tableau des livres d'un membre. Pour le membre lui-même ("manage"), il affiche aussi
 * la disponibilité et les actions "Éditer" et "Supprimer".
 *
 * @var App\Core\View $this
 * @var App\Entity\Book[] $books
 * @var bool $manage Vrai pour la page "Mon compte" du membre
 */
?>
<?php if ($books === []) : ?>
<p class="library__empty">Vous n'avez pas encore de livre dans votre bibliothèque.</p>
<?php else : ?>
<div class="table-card">
    <table class="table table--library">
        <caption class="sr-only">Vos livres</caption>
        <thead>
            <tr>
                <th scope="col">Photo</th>
                <th scope="col">Titre</th>
                <th scope="col">Auteur</th>
                <th scope="col">Description</th>
<?php if ($manage) : ?>
                <th scope="col">Disponibilité</th>
                <th scope="col">Action</th>
<?php endif; ?>
            </tr>
        </thead>
        <tbody>
<?php foreach ($books as $book) : ?>
            <tr>
                <td>
                    <img class="table__cover" src="<?= $this->escape($this->bookImageUrl($book->getImage())) ?>"
                         width="78" height="78" alt="">
                </td>
                <td>
                    <a class="table__link" href="<?= $this->escape($this->url('/books/' . $book->getId())) ?>"><?= $this->escape($book->getTitle()) ?></a>
                </td>
                <td><?= $this->escape($book->getAuthor()) ?></td>
                <td><p class="table__description"><?= $this->escape($book->getDescription()) ?></p></td>
<?php if ($manage) : ?>
                <td>
<?php if ($book->isUnavailable()) : ?>
                    <span class="tag tag--unavailable">non dispo.</span>
<?php else : ?>
                    <span class="tag tag--available">disponible</span>
<?php endif; ?>
                </td>
                <td>
                    <div class="table__actions">
                        <a class="link" href="<?= $this->escape($this->url('/books/' . $book->getId() . '/edit')) ?>">Éditer<span class="sr-only"> <?= $this->escape($book->getTitle()) ?></span></a>
                        <a class="link link--danger" href="<?= $this->escape($this->url('/books/' . $book->getId() . '/delete')) ?>">Supprimer<span class="sr-only"> <?= $this->escape($book->getTitle()) ?></span></a>
                    </div>
                </td>
<?php endif; ?>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
