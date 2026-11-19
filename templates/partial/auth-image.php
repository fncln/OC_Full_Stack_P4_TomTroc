<?php
/**
 * Photo d'étagères de livres, à droite des pages d'inscription et de connexion.
 *
 * @var App\Core\View $this
 */
?>
<div class="auth__media">
    <img class="auth__image" src="<?= $this->escape($this->url('/assets/img/auth-shelf.jpg')) ?>"
         width="720" height="886" alt="">
</div>
