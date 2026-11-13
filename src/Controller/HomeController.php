<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\BookManager;

/**
 * Contrôleur de la page d'accueil.
 */
final class HomeController extends AbstractController
{
    /** Nombre de livres affichés dans "Les derniers livres ajoutés" */
    private const LATEST_BOOKS_COUNT = 4;

    /**
     * Affiche la page d'accueil : présentation du site et derniers livres ajoutés.
     */
    public function index(): void
    {
        $latestBooks = (new BookManager())->findLatest(self::LATEST_BOOKS_COUNT);

        $this->render('home/index', [
            'title' => 'Accueil',
            'activePage' => 'home',
            'latestBooks' => $latestBooks,
        ]);
    }
}