<?php

declare(strict_types=1);

namespace App\Manager;

use App\Core\Database;
use App\Entity\User;
use DateTimeImmutable;
use PDO;

/**
 * Manager des membres : c'est la seule classe qui écrit des requêtes SQL sur la table "users".
 */
final class UserManager
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne le membre qui a cet identifiant, ou null s'il n'existe pas.
     */
    public function findById(int $id): ?User
    {
        return $this->findOne('id = :id', ['id' => $id]);
    }

    /**
     * Retourne le membre qui a cette adresse email, ou null s'il n'existe pas.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findOne('email = :email', ['email' => $email]);
    }

    /**
     * Indique si ce pseudo est déjà utilisé par un membre.
     * 
     * @param int $exceptUserId Membre à ignorer (le membre connecté qui modifie son profil) ; 0 pour n'ignorer personne
     */
    public function isUsernameTaken(string $username, int $exceptUserId = 0): bool
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND id <> :except_id');
        $statement->execute(['username' => $username, 'except_id' => $exceptUserId]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Indique si cette adresse email est déjà utilisée par un membre.
     * 
     * @param int $exceptUserId Membre à ignorer (le membre connecté qui modifie son profil) ; 0 pour n'ignorer personne
     */
    public function isEmailTaken(string $email, int $exceptUserId = 0): bool
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email AND id <> :except_id');
        $statement->execute(['email' => $email, 'except_id' => $exceptUserId]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Enregistre un nouveau membre et retourne son identifiant.
     */
    public function create(User $user): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );
        $statement->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour le pseudo, l'email et le mot de passe (hash) d'un membre.
     */
    public function update(User $user): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id'
        );
        $statement->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'id' => $user->getId(),
        ]);
    }

    /**
     * Enregistre le nom du fichier de la photo de profil d'un membre.
     */
    public function updateAvatar(int $userId, string $avatar): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
        $statement->execute(['avatar' => $avatar, 'id' => $userId]);
    }

    /**
     * Cherche un seul membre avec une condition écrite dans le code (jamais saisie par le visiteur).
     *
     * @param array<string, mixed> $parameters Valeurs de la condition, envoyées séparément de la requête
     */
    private function findOne(string $condition, array $parameters): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, username, email, password, created_at FROM users WHERE ' . $condition
        );
        $statement->execute($parameters);
        $row = $statement->fetch();

        // fetch() retourne false quand aucune ligne ne correspond
        if ($row === false) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    /**
     * Transforme une ligne de la base de données en objet User.
     *
     * @param array<string, mixed> $row
     */
    private function createUserFromRow(array $row): User
    {
        $user = new User();
        $user->setId((int) $row['id']);
        $user->setUsername((string) $row['username']);
        $user->setEmail((string) $row['email']);
        $user->setPassword((string) $row['password']);
        $user->setAvatar($row['avatar']);
        $user->setCreatedAt(new DateTimeImmutable((string) $row['created_at']));

        return $user;
    }
}
