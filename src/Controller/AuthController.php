<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Session;
use App\Entity\User;
use App\Manager\UserManager;
use App\Validator\UserValidator;
use PDOException;

/**
 * Contrôleur de l'authentification : inscription, connexion et déconnexion.
 */
final class AuthController extends AbstractController
{
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

        $errors = $this->validateRegistration(new UserValidator($userManager), $username, $email, $password);

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
        UserValidator $validator,
        string $username,
        string $email,
        string $password
    ): array {
        $errors = [];

        $fieldErrors = [
            'username' => $validator->validateUsername($username),
            'email' => $validator->validateEmail($email),
            'password' => $validator->validatePassword($password),
        ];

        foreach ($fieldErrors as $field => $message) {
            if (strlen($message) > 0) {
                $errors[$field] = $message;
            }
        }

        return $errors;
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
}