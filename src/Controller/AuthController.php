<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Session;
use App\Entity\User;
use App\Manager\UserManager;
use PDOException;

/**
 * Contrôleur de l'authentification : inscription, connexion et déconnexion.
 */
final class AuthController extends AbstractController
{
    private const USERNAME_MIN_LENGTH = 3;
    private const USERNAME_MAX_LENGTH = 30;

    /** Un pseudo contient des lettres, des chiffres et les signes _ . - & */
    private const USERNAME_PATTERN = '/^[\p{L}\p{N}_.&-]+$/u';

    private const PASSWORD_MIN_LENGTH = 8;

    /** bcrypt ignore tout ce qui dépasse 72 octets : on refuse les mots de passe plus longs */
    private const PASSWORD_MAX_LENGTH = 72;

    /**
     * Affiche le formulaire d'inscription.
     */
    public function showRegister(): void
    {
        $this->redirectIfLoggedIn();
        $this->renderRegisterPage([], '', '');
    }

    /**
     * Traite le formulaire d'inscription : vérifie les champs, crée le membre puis le connecte.
     */
    public function register(): void
    {
        $this->redirectIfLoggedIn();

        $username = $this->readText('username');
        $email = strtolower($this->readText('email'));
        $password = $this->readPassword();
        $userManager = new UserManager();

        $errors = $this->validateRegistration($userManager, $username, $email, $password);

        if (count($errors) > 0) {
            // On réaffiche le formulaire avec les erreurs, sans effacer le pseudo ni l'email saisis
            $this->renderRegisterPage($errors, $username, $email);

            return;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        // Le mot de passe n'est jamais enregistré en clair : seul son hash est stocké
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        try {
            $userId = $userManager->create($user);
        } catch (PDOException $exception) {
            // Code 23000 : le pseudo ou l'email vient d'être pris par une autre inscription
            if ((string) $exception->getCode() === '23000') {
                $errors = ['email' => 'Ce pseudo ou cette adresse email est déjà utilisé.'];
                $this->renderRegisterPage($errors, $username, $email);

                return;
            }

            throw $exception;
        }

        Session::login($userId);
        Session::addFlash('success', 'Bienvenue sur Tom Troc, votre compte a bien été créé.');
        $this->redirect('/');
    }

    /**
     * Affiche le formulaire de connexion.
     */
    public function showLogin(): void
    {
        $this->redirectIfLoggedIn();
        $this->renderLoginPage('', '');
    }

    /**
     * Traite le formulaire de connexion : vérifie l'email et le mot de passe.
     */
    public function login(): void
    {
        $this->redirectIfLoggedIn();

        $email = strtolower($this->readText('email'));
        $password = $this->readPassword();
        $user = (new UserManager())->findByEmail($email);

        $isPasswordCorrect = false;

        if ($user instanceof User) {
            // Compare le mot de passe saisi avec le hash enregistré
            $isPasswordCorrect = password_verify($password, $user->getPassword());
        }

        if ($isPasswordCorrect === false) {
            // Même message que l'email soit inconnu ou le mot de passe faux : on ne révèle pas quel compte existe
            $this->renderLoginPage('Adresse email ou mot de passe incorrect.', $email);

            return;
        }

        Session::login($user->getId());
        Session::addFlash('success', 'Vous êtes bien connecté.');
        $this->redirect('/');
    }

    /**
     * Déconnecte le membre puis retourne à l'accueil.
     */
    public function logout(): void
    {
        Session::logout();
        Session::addFlash('success', 'Vous avez bien été déconnecté.');
        $this->redirect('/');
    }

    /**
     * Vérifie les champs du formulaire d'inscription.
     *
     * @return array<string, string> Les messages d'erreur, indexés par nom de champ (vide si tout est valide)
     */
    private function validateRegistration(
        UserManager $userManager,
        string $username,
        string $email,
        string $password
    ): array {
        $errors = [];

        if ($this->isValidUsername($username) === false) {
            $errors['username'] = 'Le pseudo doit contenir de 3 à 30 caractères : lettres, chiffres, _ . - ou &.';
        } elseif ($userManager->isUsernameTaken($username)) {
            $errors['username'] = 'Ce pseudo est déjà utilisé.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Veuillez saisir une adresse email valide.';
        } elseif ($userManager->isEmailTaken($email)) {
            $errors['email'] = 'Cette adresse email est déjà utilisée.';
        }

        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Le mot de passe doit contenir au moins '
                . self::PASSWORD_MIN_LENGTH . ' caractères.';
        } elseif (strlen($password) > self::PASSWORD_MAX_LENGTH) {
            $errors['password'] = 'Le mot de passe ne doit pas dépasser '
                . self::PASSWORD_MAX_LENGTH . ' caractères.';
        }

        return $errors;
    }

    /**
     * Indique si le pseudo a la bonne longueur et ne contient que des caractères autorisés.
     */
    private function isValidUsername(string $username): bool
    {
        $length = mb_strlen($username);

        return $length >= self::USERNAME_MIN_LENGTH
            && $length <= self::USERNAME_MAX_LENGTH
            && preg_match(self::USERNAME_PATTERN, $username) === 1;
    }

    /**
     * Affiche la page d'inscription (code 422 quand le formulaire contient des erreurs).
     *
     * @param array<string, string> $errors
     */
    private function renderRegisterPage(array $errors, string $username, string $email): void
    {
        $statusCode = count($errors) > 0 ? 422 : 200;

        $this->render('auth/register', [
            'title' => 'Inscription',
            'activePage' => 'login',
            'errors' => $errors,
            'username' => $username,
            'email' => $email,
        ], $statusCode);
    }

    /**
     * Affiche la page de connexion (code 422 quand l'email ou le mot de passe est faux).
     */
    private function renderLoginPage(string $error, string $email): void
    {
        $statusCode = strlen($error) > 0 ? 422 : 200;

        $this->render('auth/login', [
            'title' => 'Connexion',
            'activePage' => 'login',
            'error' => $error,
            'email' => $email,
        ], $statusCode);
    }

    /**
     * Un membre déjà connecté n'a rien à faire sur ces pages : retour à l'accueil.
     */
    private function redirectIfLoggedIn(): void
    {
        if ($this->currentUser() instanceof User) {
            $this->redirect('/');
        }
    }

    /**
     * Lit un champ texte du formulaire, sans les espaces au début et à la fin.
     */
    private function readText(string $name): string
    {
        if (isset($_POST[$name]) && is_string($_POST[$name])) {
            return trim($_POST[$name]);
        }

        return '';
    }

    /**
     * Lit le mot de passe tel qu'il a été saisi : les espaces peuvent faire partie du mot de passe.
     */
    private function readPassword(): string
    {
        if (isset($_POST['password']) && is_string($_POST['password'])) {
            return $_POST['password'];
        }

        return '';
    }
}