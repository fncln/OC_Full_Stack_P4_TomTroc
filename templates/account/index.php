<?php
/**
 * Page "Mon compte" : profil du membre, informations personnelles et bibliothèque.
 * Le bouton "Enregistrer" valide les informations et la nouvelle photo de profil en une seule fois.
 *
 * @var App\Core\View $this
 * @var App\Entity\User $user
 * @var App\Entity\Book[] $books Livres du membre
 * @var array<string, string> $errors Messages d'erreur du formulaire, indexés par nom de champ
 * @var string $username Pseudo à remettre dans le champ
 * @var string $email Email à remettre dans le champ
 */

$bookCount = count($books);
?>
<div class="account container">
    <h1 class="page-title">Mon compte</h1>

    <div class="account__top">
        <section class="card profile-card" aria-labelledby="profile-name">
            <div class="profile-card__avatar">
                <img class="avatar" src="<?= $this->escape($this->avatarUrl($user->getAvatar())) ?>"
                     width="135" height="135" alt="Votre photo de profil">
                <label class="profile-card__edit" for="avatar-file">modifier<span class="sr-only"> la photo de profil</span></label>
                <?php /* Le champ appartient au formulaire "Vos informations personnelles" (attribut form) */ ?>
                <input id="avatar-file" type="file" name="avatar" form="account-form"
                       accept="image/jpeg,image/png,image/webp"
                       aria-describedby="avatar-hint<?= isset($errors['avatar']) ? ' avatar-error' : '' ?>">
                <p class="sr-only" id="avatar-hint">JPEG, PNG ou WebP, 2 Mo au maximum. Validez avec le bouton Enregistrer.</p>
<?php if (isset($errors['avatar'])) : ?>
                <p class="form__error" id="avatar-error"><?= $this->escape($errors['avatar']) ?></p>
<?php endif; ?>
            </div>
            <hr class="profile-card__rule">
            <h2 class="profile-card__name" id="profile-name"><?= $this->escape($user->getUsername()) ?></h2>
            <p class="profile-card__since">
                Membre depuis <?= $this->escape($this->memberSince($user->getCreatedAt())) ?>
            </p>
            <h3 class="label-title">Bibliothèque</h3>
            <p class="profile-card__count"><?= $this->escape($bookCount) ?> livre<?= $bookCount > 1 ? 's' : '' ?></p>
        </section>

        <section class="card account-card" aria-labelledby="info-title">
            <form class="account-form" id="account-form" action="<?= $this->escape($this->url('/account/profile')) ?>"
                  method="post" enctype="multipart/form-data">
                <h2 class="account-form__title" id="info-title">Vos informations personnelles</h2>
                <?php $this->partial('form-field', [
                    'id' => 'account-email',
                    'name' => 'email',
                    'label' => 'Adresse email',
                    'type' => 'email',
                    'value' => $email,
                    'error' => $errors['email'] ?? '',
                    'autocomplete' => 'email',
                ]) ?>
                <?php $this->partial('form-field', [
                    'id' => 'account-password',
                    'name' => 'password',
                    'label' => 'Mot de passe',
                    'type' => 'password',
                    'required' => false,
                    'hint' => 'Laissez vide pour conserver votre mot de passe actuel.',
                    'error' => $errors['password'] ?? '',
                    'autocomplete' => 'new-password',
                ]) ?>
                <?php $this->partial('form-field', [
                    'id' => 'account-username',
                    'name' => 'username',
                    'label' => 'Pseudo',
                    'value' => $username,
                    'error' => $errors['username'] ?? '',
                    'autocomplete' => 'username',
                ]) ?>
                <button class="btn btn--outline" type="submit">Enregistrer</button>
            </form>
        </section>
    </div>

    <section class="library" aria-labelledby="library-title">
        <h2 class="sr-only" id="library-title">Ma bibliothèque</h2>
        <?php $this->partial('library-table', ['books' => $books, 'manage' => true]) ?>
        <a class="btn btn--primary library__add" href="<?= $this->escape($this->url('/books/new')) ?>">Ajouter un livre</a>
    </section>
</div>
