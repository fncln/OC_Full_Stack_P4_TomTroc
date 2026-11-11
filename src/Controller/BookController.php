<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Manager\BookManager;

/**
 * Contrôleur des livres.
 */
final class BookController extends AbstractController
{
    /**
     * Affiche la page "Nos livres à l'échange".
     * Si un texte est saisi dans le champ de recherche, seuls les livres dont le titre
     * contient ce texte sont affichés.
     */
    public function index(): void
    {
        $searchTerm = $this->readSearchTerm();
        $bookManager = new BookManager();

        if ($searchTerm === '') {
            $books = $bookManager->findAll();
        } else {
            $books = $bookManager->searchByTitle($searchTerm);
        }

        $this->render('book/index', [
            'title' => 'Nos livres à l\'échange',
            'activePage' => 'books',
            'books' => $books,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Lit le texte saisi dans le champ de recherche (adresse "/books?q=hygge").
     * Retourne une chaîne vide si aucune recherche n'a été faite.
     */
    private function readSearchTerm(): string
    {
        if (isset($_GET['q']) && is_string($_GET['q'])) {
            return trim($_GET['q']);
        }

        return '';
    }
}
