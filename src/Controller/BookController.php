<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Session;
use App\Entity\Book;
use App\Entity\User;
use App\Manager\BookManager;
use InvalidArgumentException;

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
     * Affiche la page d'un livre : image, titre, auteur et description.
     * Si aucun livre n'a cet identifiant, la page 404 est affichée.
     *
     * @param int $id Identifiant du livre, lu dans l'adresse ("/books/12")
     */
    public function show(int $id): void
    {
        $book = (new BookManager())->findById($id);

        if ($book === null) {
            (new ErrorController())->notFound();

            return;
        }

        $this->render('book/show', [
            'title' => $book->getTitle(),
            'activePage' => 'books',
            'book' => $book,
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
/**
     * Affiche le formulaire d'ajout d'un livre (réservé aux membres connectés).
     */
    public function create(): void
    {
        $this->requireLogin();

        $values = ['title' => '', 'author' => '', 'description' => '', 'status' => Book::STATUS_AVAILABLE];

        $this->renderBookForm(null, $values, []);
    }

    /**
     * Traite le formulaire d'ajout : vérifie les champs, enregistre la photo puis le livre.
     */
    public function store(): void
    {
        $user = $this->requireLogin();

        $values = $this->readBookValues();
        $errors = $this->validateBookValues($values);
        $imageName = null;

        if (count($errors) === 0) {
            try {
                $imageName = $this->createImageUploader()->upload($this->readUploadedFile('image'), 'books');
            } catch (InvalidArgumentException $exception) {
                $errors['image'] = $exception->getMessage();
            }
        }

        if (count($errors) > 0) {
            $this->renderBookForm(null, $values, $errors);

            return;
        }

        $book = new Book();
        $book->setUserId($user->getId());
        $this->fillBook($book, $values);
        $book->setImage($imageName);

        (new BookManager())->create($book);

        Session::addFlash('success', 'Le livre a bien été ajouté à votre bibliothèque.');
        $this->redirect('/account');
    }

    /**
     * Affiche le formulaire de modification d'un livre (réservé à son propriétaire).
     *
     * @param int $id Identifiant du livre, lu dans l'adresse ("/books/12/edit")
     */
    public function edit(int $id): void
    {
        $user = $this->requireLogin();
        $book = $this->findOwnedBook($id, $user);

        if ($book === null) {
            return;
        }

        $values = [
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'status' => $book->getStatus(),
        ];

        $this->renderBookForm($book, $values, []);
    }

    /**
     * Traite le formulaire de modification : vérifie les champs puis enregistre les changements.
     *
     * @param int $id Identifiant du livre, lu dans l'adresse
     */
    public function update(int $id): void
    {
        $user = $this->requireLogin();
        $book = $this->findOwnedBook($id, $user);

        if ($book === null) {
            return;
        }

        $values = $this->readBookValues();
        $errors = $this->validateBookValues($values);
        $newImageName = null;

        if (count($errors) === 0) {
            try {
                $newImageName = $this->createImageUploader()->upload($this->readUploadedFile('image'), 'books');
            } catch (InvalidArgumentException $exception) {
                $errors['image'] = $exception->getMessage();
            }
        }

        if (count($errors) > 0) {
            $this->renderBookForm($book, $values, $errors);

            return;
        }

        $previousImage = $book->getImage();
        $this->fillBook($book, $values);

        // Sans nouvelle photo, on garde l'ancienne
        if (is_string($newImageName)) {
            $book->setImage($newImageName);
        }

        (new BookManager())->update($book);

        if (is_string($newImageName)) {
            // L'ancienne photo n'est plus utilisée : on la supprime du serveur
            $this->createImageUploader()->delete('books', $previousImage);
        }

        Session::addFlash('success', 'Les informations du livre ont bien été enregistrées.');
        $this->redirect('/account');
    }

    /**
     * Affiche la page qui demande de confirmer la suppression d'un livre.
     *
     * @param int $id Identifiant du livre, lu dans l'adresse ("/books/12/delete")
     */
    public function confirmDelete(int $id): void
    {
        $user = $this->requireLogin();
        $book = $this->findOwnedBook($id, $user);

        if ($book === null) {
            return;
        }

        $this->render('book/delete', [
            'title' => 'Supprimer un livre',
            'activePage' => 'account',
            'book' => $book,
        ]);
    }

    /**
     * Supprime un livre après confirmation (réservé à son propriétaire).
     *
     * @param int $id Identifiant du livre, lu dans l'adresse
     */
    public function delete(int $id): void
    {
        $user = $this->requireLogin();
        $book = $this->findOwnedBook($id, $user);

        if ($book === null) {
            return;
        }

        (new BookManager())->delete($book->getId(), $user->getId());

        // La photo n'est plus utilisée : on la supprime du serveur
        $this->createImageUploader()->delete('books', $book->getImage());

        Session::addFlash('success', 'Le livre a bien été supprimé de votre bibliothèque.');
        $this->redirect('/account');
    }

    /**
     * Retourne le livre demandé s'il appartient au membre connecté.
     * Sinon, affiche la page d'erreur (404 si le livre n'existe pas, 403 s'il appartient à un autre membre)
     * et retourne null.
     */
    private function findOwnedBook(int $id, User $user): ?Book
    {
        $book = (new BookManager())->findById($id);

        if ($book === null) {
            (new ErrorController())->notFound();

            return null;
        }

        // Un membre ne peut modifier ou supprimer que ses propres livres
        if ($book->getUserId() === $user->getId()) {
            return $book;
        }

        (new ErrorController())->forbidden();

        return null;
    }

    /**
     * Lit les champs du formulaire d'un livre.
     *
     * @return array<string, string>
     */
    private function readBookValues(): array
    {
        return [
            'title' => $this->readPostText('title'),
            'author' => $this->readPostText('author'),
            'description' => $this->readPostText('description'),
            'status' => $this->readPostText('status'),
        ];
    }

    /**
     * Vérifie les champs du formulaire d'un livre.
     *
     * @param array<string, string> $values
     *
     * @return array<string, string> Les messages d'erreur, indexés par nom de champ (vide si tout est valide)
     */
    private function validateBookValues(array $values): array
    {
        $errors = [];

        if ($values['title'] === '' || mb_strlen($values['title']) > 255) {
            $errors['title'] = 'Le titre est obligatoire (255 caractères au maximum).';
        }

        if ($values['author'] === '' || mb_strlen($values['author']) > 255) {
            $errors['author'] = 'L\'auteur est obligatoire (255 caractères au maximum).';
        }

        if ($values['description'] === '' || mb_strlen($values['description']) > 10000) {
            $errors['description'] = 'Le commentaire est obligatoire (10 000 caractères au maximum).';
        }

        $isValidStatus = $values['status'] === Book::STATUS_AVAILABLE || $values['status'] === Book::STATUS_UNAVAILABLE;

        if ($isValidStatus === false) {
            $errors['status'] = 'Veuillez choisir une disponibilité.';
        }

        return $errors;
    }

    /**
     * Recopie les champs du formulaire dans le livre.
     *
     * @param array<string, string> $values
     */
    private function fillBook(Book $book, array $values): void
    {
        $book->setTitle($values['title']);
        $book->setAuthor($values['author']);
        $book->setDescription($values['description']);
        $book->setStatus($values['status']);
    }

    /**
     * Affiche le formulaire d'un livre, pour l'ajout ($book vaut null) ou la modification.
     *
     * @param array<string, string> $values Valeurs à mettre dans les champs
     * @param array<string, string> $errors Messages d'erreur, indexés par nom de champ
     */
    private function renderBookForm(?Book $book, array $values, array $errors): void
    {
        $statusCode = count($errors) > 0 ? 422 : 200;

        $this->render('book/form', [
            'title' => $book === null ? 'Ajouter un livre' : 'Modifier les informations',
            'activePage' => 'account',
            'book' => $book,
            'values' => $values,
            'errors' => $errors,
        ], $statusCode);
    }
}

