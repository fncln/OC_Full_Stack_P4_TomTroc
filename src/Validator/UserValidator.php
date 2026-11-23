<?php

declare(strict_types=1);

namespace App\Validator;

use App\Manager\UserManager;

/**
 * Vérifie les champs d'un membre (pseudo, email, mot de passe).
 * Cette classe est utilisée par l'inscription et par la page "Mon compte" : les règles sont écrites une seule fois.
 *
 * Chaque méthode retourne un message d'erreur, ou une chaîne vide quand la valeur est valide.
 */
final class UserValidator
{
    private const USERNAME_MIN_LENGTH = 3;
    private const USERNAME_MAX_LENGTH = 50;

    /** Un pseudo contient des lettres, des chiffres et les signes _ . - & */
    private const USERNAME_PATTERN = '/^[\p{L}\p{N}_.&-]+$/u';

    private const PASSWORD_MIN_LENGTH = 8;

    /** bcrypt ignore tout ce qui dépasse 72 octets : on refuse les mots de passe plus longs */
    private const PASSWORD_MAX_LENGTH = 72;

    public function __construct(private UserManager $userManager)
    {
    }

    /**
     * Vérifie le pseudo.
     *
     * @param int $currentUserId Membre qui modifie son profil (son pseudo n'est pas "déjà pris") ; 0 à l'inscription
     */
    public function validateUsername(string $username, int $currentUserId = 0): string
    {
        $length = mb_strlen($username);
        $hasValidLength = $length >= self::USERNAME_MIN_LENGTH && $length <= self::USERNAME_MAX_LENGTH;
        $hasValidCharacters = preg_match(self::USERNAME_PATTERN, $username) === 1;

        if ($hasValidLength === false || $hasValidCharacters === false) {
            return 'Le pseudo doit contenir de 3 à 50 caractères : lettres, chiffres, _ . - ou &.';
        }

        if ($this->userManager->isUsernameTaken($username, $currentUserId)) {
            return 'Ce pseudo est déjà utilisé.';
        }

        return '';
    }

    /**
     * Vérifie l'adresse email.
     *
     * @param int $currentUserId Membre qui modifie son profil (son email n'est pas "déjà utilisé") ; 0 à l'inscription
     */
    public function validateEmail(string $email, int $currentUserId = 0): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 'Veuillez saisir une adresse email valide.';
        }

        if ($this->userManager->isEmailTaken($email, $currentUserId)) {
            return 'Cette adresse email est déjà utilisée.';
        }

        return '';
    }

    /**
     * Vérifie le mot de passe saisi (avant de le hacher).
     */
    public function validatePassword(string $password): string
    {
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            return 'Le mot de passe doit contenir au moins ' . self::PASSWORD_MIN_LENGTH . ' caractères.';
        }

        if (strlen($password) > self::PASSWORD_MAX_LENGTH) {
            return 'Le mot de passe ne doit pas dépasser ' . self::PASSWORD_MAX_LENGTH . ' caractères.';
        }

        return '';
    }
}
