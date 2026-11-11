<?php
/**
 * Page d'erreur 404 : page introuvable.
 *
 * @var App\Core\View $this
 */
?>
<section class="error-page container">
    <h1>Page introuvable</h1>
    <p>La page que vous recherchez n'existe pas ou a été déplacée.</p>
    <p><a href="<?= $this->escape($this->url('/books')) ?>">Voir les livres à l'échange</a></p>
</section>
