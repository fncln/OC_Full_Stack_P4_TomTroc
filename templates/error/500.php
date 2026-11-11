<?php
/**
 * Page d'erreur 500 : erreur technique du serveur.
 *
 * @var App\Core\View $this
 * @var string $details Détail de l'erreur (vide hors mode debug)
 */
?>
<section class="error-page container">
    <h1>Une erreur est survenue</h1>
    <p>Le service est momentanément indisponible. Merci de réessayer dans quelques instants.</p>
<?php if (strlen($details) > 0) : ?>
    <pre><?= $this->escape($details) ?></pre>
<?php endif; ?>
    <p><a href="<?= $this->escape($this->url('/books')) ?>">Voir les livres à l'échange</a></p>
</section>
