<?php

declare(strict_types=1);

namespace App\Manager;

use App\Core\Database;
use App\Entity\Book;
use DateTimeImmutable;
use PDO;

/**
 * Manager des livres : c'est la seule classe qui écrit des requêtes SQL sur la table "books".
 * Elle transforme les lignes de la base de données en objets Book.
 */
final class BookManager
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne tous les livres, du plus récent au plus ancien.
     *
     * @return Book[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, title, author, description, image, status, created_at
             FROM books
             ORDER BY id DESC'
        );
        $statement->execute();

        return $this->createBooksFromRows($statement->fetchAll());
    }

    /**
     * Retourne les livres dont le titre contient le texte cherché, du plus récent au plus ancien.
     *
     * @return Book[]
     */
    public function searchByTitle(string $searchTerm): array
    {
        // Dans un LIKE, les caractères % et _ ont un sens spécial : on les neutralise
        $escapedTerm = addcslashes($searchTerm, '%_\\');

        $statement = $this->pdo->prepare(
            'SELECT id, title, author, description, image, status, created_at
             FROM books
             WHERE title LIKE :search
             ORDER BY id DESC'
        );
        // La valeur est envoyée séparément de la requête : aucune injection SQL possible
        $statement->execute(['search' => '%' . $escapedTerm . '%']);

        return $this->createBooksFromRows($statement->fetchAll());
    }

    /**
     * Transforme plusieurs lignes de la base de données en objets Book.
     *
     * @param array<int, array<string, mixed>> $rows
     *
     * @return Book[]
     */
    private function createBooksFromRows(array $rows): array
    {
        $books = [];

        foreach ($rows as $row) {
            $books[] = $this->createBookFromRow($row);
        }

        return $books;
    }

    /**
     * Transforme une ligne de la base de données en objet Book.
     *
     * @param array<string, mixed> $row
     */
    private function createBookFromRow(array $row): Book
    {
        $book = new Book();
        $book->setId((int) $row['id']);
        $book->setTitle((string) $row['title']);
        $book->setAuthor((string) $row['author']);
        $book->setDescription((string) $row['description']);
        $book->setImage($row['image']);
        $book->setStatus((string) $row['status']);
        $book->setCreatedAt(new DateTimeImmutable((string) $row['created_at']));

        return $book;
    }
}
