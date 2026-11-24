<?php
/**
 * Formulaire d'un livre : ajout (page "Ajouter un livre") ou modification (page "Modifier les informations").
 *
 * @var App\Core\View $this
 * @var App\Entity\Book|null $book Le livre modifié, ou null pour un ajout
 * @var array<string, string> $values Valeurs des champs
 * @var array<string, string> $errors Messages d'erreur, indexés par nom de champ
 */

$isEdit = $book instanceof \App\Entity\Book;
$formAction = $isEdit ? '/books/' . $book->getId() . '/edit' : '/books/new';
$currentImage = $isEdit ? $book->getImage() : null;

// Attributs qui relient un champ en erreur à son message
$describe = static fn (string $field): string => isset($errors[$field])
    ? ' aria-invalid="true" aria-describedby="book-' . $field . '-error"'
    : '';
?>
<div class="book-form-page container">
    <a class="back-link" href="<?= $this->escape($this->url('/account')) ?>"><span aria-hidden="true">←</span> retour</a>
    <h1 class="page-title"><?= $isEdit ? 'Modifier les informations' : 'Ajouter un livre' ?></h1>

    <form class="book-form" action="<?= $this->escape($this->url($formAction)) ?>" method="post"
          enctype="multipart/form-data">
        <div class="book-form__photo">
            <p class="form__label">Photo</p>
            <img class="book-form__cover" src="<?= $this->escape($this->bookImageUrl($currentImage)) ?>"
                 width="488" height="488" alt="<?= $isEdit ? 'Photo actuelle du livre' : 'Aucune photo pour le moment' ?>">
            <label class="link" for="book-image"><?= $isEdit ? 'Modifier la photo' : 'Ajouter une photo' ?></label>
            <input id="book-image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   aria-describedby="book-image-hint<?= isset($errors['image']) ? ' book-image-error' : '' ?>">
            <p class="form__hint" id="book-image-hint">JPEG, PNG ou WebP, 2 Mo au maximum.</p>
<?php if (isset($errors['image'])) : ?>
            <p class="form__error" id="book-image-error"><?= $this->escape($errors['image']) ?></p>
<?php endif; ?>
        </div>

        <div class="book-form__fields">
            <?php $this->partial('form-field', [
                'id' => 'book-title',
                'name' => 'title',
                'label' => 'Titre',
                'value' => $values['title'],
                'error' => $errors['title'] ?? '',
            ]) ?>
            <?php $this->partial('form-field', [
                'id' => 'book-author',
                'name' => 'author',
                'label' => 'Auteur',
                'value' => $values['author'],
                'error' => $errors['author'] ?? '',
            ]) ?>

            <div class="form__group">
                <label class="form__label" for="book-description">Commentaire</label>
                <textarea class="form__textarea" id="book-description" name="description" rows="12" required<?= $describe('description') ?>><?= $this->escape($values['description']) ?></textarea>
<?php if (isset($errors['description'])) : ?>
                <p class="form__error" id="book-description-error"><?= $this->escape($errors['description']) ?></p>
<?php endif; ?>
            </div>

            <div class="form__group">
                <label class="form__label" for="book-status">Disponibilité</label>
                <select class="form__select" id="book-status" name="status"<?= $describe('status') ?>>
                    <option value="available"<?= $values['status'] === 'available' ? ' selected' : '' ?>>disponible</option>
                    <option value="unavailable"<?= $values['status'] === 'unavailable' ? ' selected' : '' ?>>non dispo.</option>
                </select>
<?php if (isset($errors['status'])) : ?>
                <p class="form__error" id="book-status-error"><?= $this->escape($errors['status']) ?></p>
<?php endif; ?>
            </div>

            <button class="btn btn--primary" type="submit">Valider</button>
        </div>
    </form>
</div>
