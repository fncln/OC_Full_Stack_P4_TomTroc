<?php
/**
 * En-tête du site : logo et menu de navigation.
 *
 * @var App\Core\View $this
 * @var App\Entity\User|null $currentUser Membre connecté (null pour un visiteur)
 * @var string $activePage Page courante ("books" pour "Nos livres à l'échange")
 * @var int $unreadCount Nombre de messages non lus du membre connecté
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
                    <a class="site-nav__link<?= $activePage === 'home' ? ' is-active' : '' ?>"
                       href="<?= $this->escape($this->url('/')) ?>"
                       <?= $activePage === 'home' ? 'aria-current="page"' : '' ?>>Accueil</a>
                </li>
                <li>
                    <a class="site-nav__link<?= $activePage === 'books' ? ' is-active' : '' ?>"
                       href="<?= $this->escape($this->url('/books')) ?>"
                       <?= $activePage === 'books' ? 'aria-current="page"' : '' ?>>Nos livres à l'échange</a>
                </li>
            </ul>
            <ul class="site-nav__list site-nav__list--member">
<?php if ($currentUser === null) : ?>
                <li>
                    <a class="site-nav__link<?= $activePage === 'login' ? ' is-active' : '' ?>"
                       href="<?= $this->escape($this->url('/login')) ?>"
                       <?= $activePage === 'login' ? 'aria-current="page"' : '' ?>>Connexion</a>
                </li>
<?php else : ?>
                <li>
                    <a class="site-nav__link site-nav__link--messages<?= $activePage === 'messages' ? ' is-active' : '' ?>"
                       href="<?= $this->escape($this->url('/messages')) ?>"
                       <?= $activePage === 'messages' ? 'aria-current="page"' : '' ?>>Messagerie<?php if ($unreadCount > 0) : ?>
 <span class="badge"><?= $this->escape($unreadCount) ?><span class="sr-only"> message<?= $unreadCount > 1 ? 's' : '' ?> non lu<?= $unreadCount > 1 ? 's' : '' ?></span></span><?php endif; ?></a>
                </li>
                <li>
                    <a class="site-nav__link site-nav__link--account<?= $activePage === 'account' ? ' is-active' : '' ?>"
                       href="<?= $this->escape($this->url('/account')) ?>"
                       <?= $activePage === 'account' ? 'aria-current="page"' : '' ?>>Mon compte</a>
                </li>
                <li>
                    <form action="<?= $this->escape($this->url('/logout')) ?>" method="post">
                        <button class="site-nav__link site-nav__button" type="submit">Déconnexion</button>
                    </form>
                </li>
<?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
