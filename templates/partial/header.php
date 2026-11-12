<?php
/**
 * En-tête du site : logo et menu de navigation.
 *
 * @var App\Core\View $this
 * @var string $activePage Page courante ("books" pour "Nos livres à l'échange")
 */
?>
<header class="site-header">
    <div class="site-header__inner container">
        <a class="site-header__logo" href="<?= $this->escape($this->url('/')) ?>">
            <img src="<?= $this->escape($this->asset('img/logo-header.svg')) ?>" width="155" height="51"
                 alt="Tom Troc, retour à l’accueil">
        </a>
        <nav class="site-nav" aria-label="Navigation principale">
            <ul class="site-nav__list">
                <li>
                    <a class="site-nav__link<?= $activePage === 'books' ? ' is-active' : '' ?>"
                       href="<?= $this->escape($this->url('/books')) ?>"
                       <?= $activePage === 'books' ? 'aria-current="page"' : '' ?>>Nos livres à l'échange</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
