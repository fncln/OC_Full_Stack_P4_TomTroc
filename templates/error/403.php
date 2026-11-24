<?php
/**
 * Page d'erreur 403 : accès refusé.
 *
 * @var App\Core\View $this
 */
?>
<section class="error-page container">
    <h1>Accès refusé</h1>
    <p>Vous n'avez pas le droit d'accéder à cette page.</p>
    <p><a href="<?= $this->escape($this->url('/account')) ?>">Retour à mon compte</a></p>
</section>
