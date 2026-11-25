<?php
/**
 * Profil public d'un membre : à gauche sa carte (photo, pseudo, ancienneté, nombre de livres,
 * bouton pour lui écrire), à droite sa bibliothèque.
 * L'adresse email du membre n'est jamais affichée.
 *
 * @var App\Core\View $this
 * @var App\Entity\User $member Membre dont on consulte le profil
 * @var App\Entity\Book[] $books Livres du membre
 */

$bookCount = count($books);
?>
<div class="member container">
    <section class="card profile-card" aria-labelledby="profile-name">
        <div class="profile-card__avatar">
            <img class="avatar" src="<?= $this->escape($this->avatarUrl($member->getAvatar())) ?>"
                 width="135" height="135" alt="Photo de profil de <?= $this->escape($member->getUsername()) ?>">
        </div>
        <hr class="profile-card__rule">
        <h1 class="profile-card__name" id="profile-name"><?= $this->escape($member->getUsername()) ?></h1>
        <p class="profile-card__since">
            Membre depuis <?= $this->escape($this->memberSince($member->getCreatedAt())) ?>
        </p>
        <h2 class="label-title">Bibliothèque</h2>
        <p class="profile-card__count"><?= $this->escape($bookCount) ?> livre<?= $bookCount > 1 ? 's' : '' ?></p>
        <a class="btn btn--outline" href="<?= $this->escape($this->url('/messages/' . $member->getId())) ?>">Écrire un message</a>
    </section>

    <section class="library" aria-labelledby="library-title">
        <h2 class="sr-only" id="library-title">Bibliothèque de <?= $this->escape($member->getUsername()) ?></h2>
        <?php $this->partial('library-table', [
            'books' => $books,
            'manage' => false,
            'caption' => 'Livres de ' . $member->getUsername(),
            'emptyMessage' => 'Ce membre n\'a pas encore de livre dans sa bibliothèque.',
        ]) ?>
    </section>
</div>
