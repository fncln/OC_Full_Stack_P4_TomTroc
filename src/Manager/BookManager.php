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
    /**
     * Début de chaque requête sur les livres : les colonnes du livre et le pseudo de son propriétaire.
     * Un livre est relié à son propriétaire par la clé étrangère books.user_id (jointure avec la table "users").
     */
    private const SELECT_BOOKS = 'SELECT b.id, b.user_id, b.title, b.author, b.description, b.image, b.status,
            b.created_at, u.username AS owner_username, u.avatar AS owner_avatar
        FROM books b
        INNER JOIN users u ON u.id = b.user_id';

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
        $statement = $this->pdo->prepare(self::SELECT_BOOKS . ' ORDER BY b.id DESC');
        $statement->execute();

        return $this->createBooksFromRows($statement->fetchAll());
    }

    /**
     * Retourne le livre qui a cet identifiant, ou null s'il n'existe pas.
     */
    public function findById(int $id): ?Book
    {
        $statement = $this->pdo->prepare(self::SELECT_BOOKS . ' WHERE b.id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        // fetch() retourne false quand aucune ligne ne correspond
        if ($row === false) {
            return null;
        }

        return $this->createBookFromRow($row);
    }

    /**
     * Retourne les livres d'un membre, du plus récent au plus ancien.
     *
     * @return Book[]
     */
    public function findByUserId(int $userId): array
    {
        $statement = $this->pdo->prepare(self::SELECT_BOOKS . ' WHERE b.user_id = :user_id ORDER BY b.id DESC');
        $statement->execute(['user_id' => $userId]);

        return $this->createBooksFromRows($statement->fetchAll());
    }

    /**
     * Enregistre un nouveau livre et retourne son identifiant.
     */
    public function create(Book $book): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO books (user_id, title, author, description, image, status)
             VALUES (:user_id, :title, :author, :description, :image, :status)'
        );
        $statement->execute([
            'user_id' => $book->getUserId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'image' => $book->getImage(),
            'status' => $book->getStatus(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour un livre. Le "user_id" dans la condition garantit qu'on ne modifie que le livre de son propriétaire.
     */
    public function update(Book $book): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE books
             SET title = :title, author = :author, description = :description, image = :image, status = :status
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'image' => $book->getImage(),
            'status' => $book->getStatus(),
            'id' => $book->getId(),
            'user_id' => $book->getUserId(),
        ]);
    }

    /**
     * Supprime un livre. Le "user_id" dans la condition garantit qu'on ne supprime que le livre de son propriétaire.
     */
    public function delete(int $id, int $userId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM books WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Retourne les derniers livres ajoutés, du plus récent au plus ancien.
     *
     * @param int $limit Nombre de livres à retourner
     *
     * @return Book[]
     */
    public function findLatest(int $limit): array
    {
        $statement = $this->pdo->prepare(self::SELECT_BOOKS . ' ORDER BY b.id DESC LIMIT :limit');
        // LIMIT attend un nombre entier : on l'indique explicitement à PDO
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
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

        $statement = $this->pdo->prepare(self::SELECT_BOOKS . ' WHERE b.title LIKE :search ORDER BY b.id DESC');
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
        $book->setUserId((int) $row['user_id']);
        $book->setTitle((string) $row['title']);
        $book->setAuthor((string) $row['author']);
        $book->setDescription((string) $row['description']);
        $book->setImage($row['image']);
        $book->setStatus((string) $row['status']);
        $book->setCreatedAt(new DateTimeImmutable((string) $row['created_at']));
        $book->setOwnerUsername((string) $row['owner_username']);
        $book->setOwnerAvatar($row['owner_avatar']);

        return $book;
    }
}
