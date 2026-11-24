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
 * @var string $hint         Texte d'aide affiché sous le champ (facultatif)
 * @var bool   $required     Champ obligatoire (par défaut : oui)
 * @var string $autocomplete Aide du navigateur pour remplir le champ (ex. "email", "new-password")
 */

$fieldType = $type ?? 'text';
$fieldValue = $value ?? '';
$errorMessage = $error ?? '';
$hintMessage = $hint ?? '';
$isRequired = $required ?? true;
$hasError = strlen($errorMessage) > 0;
$hasHint = strlen($hintMessage) > 0;

// Les textes liés au champ sont lus par les lecteurs d'écran avec le champ
$describedBy = [];

if ($hasHint) {
    $describedBy[] = $id . '-hint';
}

if ($hasError) {
    $describedBy[] = $id . '-error';
}
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
           aria-invalid="true"
<?php endif; ?>
<?php if (count($describedBy) > 0) : ?>
           aria-describedby="<?= $this->escape(implode(' ', $describedBy)) ?>"
<?php endif; ?>
<?php if ($isRequired) : ?>
           required>
<?php endif; ?>>
<?php if ($hasHint) : ?>
    <p class="form__hint" id="<?= $this->escape($id) ?>-hint"><?= $this->escape($hintMessage) ?></p>
<?php endif; ?>
<?php if ($hasError) : ?>
    <p class="form__error" id="<?= $this->escape($id) ?>-error"><?= $this->escape($errorMessage) ?></p>
<?php endif; ?>
</div>
