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
        <p class="site-header__logo">
            <a href="<?= $this->escape($this->url('/')) ?>">Tom Troc</a>
        </p>
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
