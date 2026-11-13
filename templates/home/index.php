<?php
/**
 * Page d'accueil : présentation, derniers livres ajoutés, fonctionnement du site et valeurs.
 *
 * @var App\Core\View $this
 * @var App\Entity\Book[] $latestBooks Les derniers livres ajoutés
 */
?>
<section class="hero">
    <div class="hero__inner container">
        <div class="hero__text">
            <h1 class="hero__title">Rejoignez nos lecteurs passionnés</h1>
            <p class="hero__lead">
                Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture.
                Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres.
            </p>
            <a class="btn btn--primary" href="<?= $this->escape($this->url('/books')) ?>">Découvrir</a>
        </div>
        <figure class="hero__figure">
            <img class="hero__image" src="<?= $this->escape($this->url('/assets/img/hamza-nouasria-KXrvPthkmYQ-unsplash 1@2x.jpg')) ?>"
                 width="404" height="539"
                 alt="Un lecteur assis à l'entrée d'une librairie, entouré de piles de livres">
            <figcaption class="hero__credit">Hamza</figcaption>
        </figure>
    </div>
</section>

<section class="section section--light" aria-labelledby="latest-books-title">
    <div class="container">
        <h2 class="section__title" id="latest-books-title">Les derniers livres ajoutés</h2>
<?php if ($latestBooks === []) : ?>
        <p class="section__empty">Aucun livre n'est proposé pour le moment.</p>
<?php else : ?>
        <ul class="book-grid book-grid--home">
<?php foreach ($latestBooks as $book) : ?>
            <li class="book-grid__item">
                <?php $this->partial('book-card', ['book' => $book, 'headingLevel' => 3]) ?>
            </li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>
        <div class="section__action">
            <a class="btn btn--primary" href="<?= $this->escape($this->url('/books')) ?>">Voir tous les livres</a>
        </div>
    </div>
</section>

<section class="section section--alt" aria-labelledby="how-it-works-title">
    <div class="container">
        <h2 class="section__title" id="how-it-works-title">Comment ça marche ?</h2>
        <p class="section__lead">
            Échanger des livres avec TomTroc c’est simple et amusant ! Suivez ces étapes pour commencer :
        </p>
        <ol class="steps">
            <li class="steps__item">Inscrivez-vous gratuitement sur notre plateforme.</li>
            <li class="steps__item">Ajoutez les livres que vous souhaitez échanger à votre profil.</li>
            <li class="steps__item">Parcourez les livres disponibles chez d'autres membres.</li>
            <li class="steps__item">Proposez un échange et discutez avec d'autres passionnés de lecture.</li>
        </ol>
        <div class="section__action">
            <a class="btn btn--outline" href="<?= $this->escape($this->url('/books')) ?>">Voir tous les livres</a>
        </div>
    </div>
</section>

<div class="band">
    <img class="band__image" src="<?= $this->escape($this->url('/assets/img/clay-banks-4uH8rdyEbH4-unsplash 1.jpg')) ?>"
         width="1440" height="230" alt="">
</div>

<section class="section section--alt values" aria-labelledby="values-title">
    <div class="values__inner">
        <h2 class="section__title values__title" id="values-title">Nos valeurs</h2>
        <p>
            Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté. Nos valeurs sont
            ancrées dans notre passion pour les livres et notre désir de créer des liens entre les lecteurs. Nous
            croyons en la puissance des histoires pour rassembler les gens et inspirer des conversations
            enrichissantes.
        </p>
        <p>
            Notre association a été fondée avec une conviction profonde : chaque livre mérite d'être lu et partagé.
        </p>
        <p>
            Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux lecteurs de se
            connecter, de partager leurs découvertes littéraires et d'échanger des livres qui attendent patiemment
            sur les étagères.
        </p>
        <div class="values__footer">
            <p class="values__signature">L'équipe Tom Troc</p>
            <img class="values__heart" src="<?= $this->escape($this->url('/assets/img/Green_heart.svg')) ?>"
                 width="122" height="104" alt="">
        </div>
    </div>
</section>
