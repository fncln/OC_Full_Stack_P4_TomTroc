<?php
/**
 * Champ de formulaire accessible : libellé visible et message d'erreur relié au champ.
 *
 * @var App\Core\View $this
 * @var string $id           Identifiant du champ (il relie le libellé au champ)
 * @var string $name         Nom du champ envoyé au serveur
 * @var string $label        Libellé affiché au-dessus du champ
 * @var string $type         "text" (par défaut), "email" ou "password"
 * @var string $value        Valeur remise dans le champ après une erreur (ignorée pour un mot de passe)
 * @var string $error        Message d'erreur (vide quand le champ est valide)
 * @var string $autocomplete Aide du navigateur pour remplir le champ (ex. "email", "new-password")
 */

$fieldType = $type ?? 'text';
$fieldValue = $value ?? '';
$errorMessage = $error ?? '';
$hasError = strlen($errorMessage) > 0;
?>
<div class="form__group">
    <label class="form__label" for="<?= $this->escape($id) ?>"><?= $this->escape($label) ?></label>
    <input class="form__input" id="<?= $this->escape($id) ?>" type="<?= $this->escape($fieldType) ?>"
           name="<?= $this->escape($name) ?>"
<?php if ($fieldType === 'password') : ?>
           value=""
<?php else : ?>
           value="<?= $this->escape($fieldValue) ?>"
<?php endif; ?>
<?php if (isset($autocomplete)) : ?>
           autocomplete="<?= $this->escape($autocomplete) ?>"
<?php endif; ?>
<?php if ($hasError) : ?>
           aria-invalid="true" aria-describedby="<?= $this->escape($id) ?>-error"
<?php endif; ?>
           required>
<?php if ($hasError) : ?>
    <p class="form__error" id="<?= $this->escape($id) ?>-error"><?= $this->escape($errorMessage) ?></p>
<?php endif; ?>
</div>
