<?php
/**
 * Page de connexion : adresse email et mot de passe.
 *
 * @var App\Core\View $this
 * @var string $error Message d'erreur (vide si la connexion n'a pas encore été tentée)
 * @var string $email Email saisi (pour le remettre dans le champ)
 */
?>
<div class="auth">
    <div class="auth__column">
        <div class="auth__content">
            <h1 class="auth__title">Connexion</h1>
<?php if (strlen($error) > 0) : ?>
            <p class="alert alert--error" role="alert"><?= $this->escape($error) ?></p>
<?php endif; ?>
            <form action="<?= $this->escape($this->url('/login')) ?>" method="post">
                <?php $this->partial('form-field', [
                    'id' => 'login-email',
                    'name' => 'email',
                    'label' => 'Adresse email',
                    'type' => 'email',
                    'value' => $email,
                    'autocomplete' => 'email',
                ]) ?>
                <?php $this->partial('form-field', [
                    'id' => 'login-password',
                    'name' => 'password',
                    'label' => 'Mot de passe',
                    'type' => 'password',
                    'autocomplete' => 'current-password',
                ]) ?>
                <button class="btn btn--primary btn--block" type="submit">Se connecter</button>
            </form>
            <p class="auth__switch">
                Pas de compte ? <a class="link" href="<?= $this->escape($this->url('/register')) ?>">Inscrivez-vous</a>
            </p>
        </div>
    </div>
    <?php $this->partial('auth-image') ?>
</div>
