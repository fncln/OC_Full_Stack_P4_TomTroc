<?php
/**
 * Page d'inscription : pseudo, adresse email et mot de passe.
 *
 * @var App\Core\View $this
 * @var array<string, string> $errors Messages d'erreur, indexés par nom de champ
 * @var string $username              Pseudo saisi (pour le remettre dans le champ)
 * @var string $email                 Email saisi (pour le remettre dans le champ)
 */
?>
<div class="auth">
    <div class="auth__column">
        <div class="auth__content">
            <h1 class="auth__title">Inscription</h1>
            <form action="<?= $this->escape($this->url('/register')) ?>" method="post">
                <?php $this->partial('form-field', [
                    'id' => 'register-username',
                    'name' => 'username',
                    'label' => 'Pseudo',
                    'value' => $username,
                    'error' => $errors['username'] ?? '',
                    'autocomplete' => 'username',
                ]) ?>
                <?php $this->partial('form-field', [
                    'id' => 'register-email',
                    'name' => 'email',
                    'label' => 'Adresse email',
                    'type' => 'email',
                    'value' => $email,
                    'error' => $errors['email'] ?? '',
                    'autocomplete' => 'email',
                ]) ?>
                <?php $this->partial('form-field', [
                    'id' => 'register-password',
                    'name' => 'password',
                    'label' => 'Mot de passe',
                    'type' => 'password',
                    'error' => $errors['password'] ?? '',
                    'autocomplete' => 'new-password',
                ]) ?>
                <button class="btn btn--primary btn--block" type="submit">S’inscrire</button>
            </form>
            <p class="auth__switch">
                Déjà inscrit ? <a class="link" href="<?= $this->escape($this->url('/login')) ?>">Connectez-vous</a>
            </p>
        </div>
    </div>
    <?php $this->partial('auth-image') ?>
</div>
