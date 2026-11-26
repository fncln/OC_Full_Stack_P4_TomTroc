<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Session;
use App\Entity\User;
use App\Manager\BookManager;
use App\Manager\UserManager;
use App\Validator\UserValidator;
use InvalidArgumentException;
use PDOException;

/**
 * Contrôleur de la page "Mon compte" : informations du membre, photo de profil et bibliothèque.
 * Toutes les actions sont réservées aux membres connectés.
 */
final class AccountController extends AbstractController
{
    /**
     * Affiche la page "Mon compte".
     */
    public function index(): void
    {
        $user = $this->requireLogin();

        $this->renderAccountPage($user, [], $user->getUsername(), $user->getEmail());
    }

    /**
     * Traite le formulaire "Vos informations personnelles" : pseudo, email, mot de passe (facultatif)
     * et nouvelle photo de profil (facultative). Un seul bouton "Enregistrer" valide le tout.
     */
    public function updateProfile(): void
    {
        $user = $this->requireLogin();

        $username = $this->readPostText('username');
        $email = strtolower($this->readPostText('email'));
        $password = $this->readPostPassword();
        $userManager = new UserManager();
        $validator = new UserValidator($userManager);

        $fieldErrors = [
            'username' => $validator->validateUsername($username, $user->getId()),
            'email' => $validator->validateEmail($email, $user->getId()),
            'password' => '',
        ];

        // Un mot de passe vide veut dire "garder le mot de passe actuel"
        if (strlen($password) > 0) {
            $fieldErrors['password'] = $validator->validatePassword($password);
        }

        $errors = [];

        foreach ($fieldErrors as $field => $message) {
            if (strlen($message) > 0) {
                $errors[$field] = $message;
            }
        }

        if (count($errors) > 0) {
            $this->renderAccountPage($user, $errors, $username, $email);

            return;
        }

        // Photo de profil : null quand le membre n'a pas choisi de nouvelle image
        $uploader = $this->createImageUploader();

        try {
            $newAvatar = $uploader->upload($this->readUploadedFile('avatar'), 'avatars');
        } catch (InvalidArgumentException $exception) {
            $this->renderAccountPage($user, ['avatar' => $exception->getMessage()], $username, $email);

            return;
        }

        $hasNewAvatar = is_string($newAvatar);
        $previousAvatar = $user->getAvatar();

        $user->setUsername($username);
        $user->setEmail($email);

        if (strlen($password) > 0) {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }

        try {
            $userManager->update($user);

            if ($hasNewAvatar) {
                $userManager->updateAvatar($user->getId(), $newAvatar);
            }
        } catch (PDOException $exception) {
            // La nouvelle photo n'est pas gardée si rien n'a pu être enregistré
            if ($hasNewAvatar) {
                $uploader->delete('avatars', $newAvatar);
            }

            // Code 23000 : le pseudo ou l'email vient d'être pris par un autre membre
            if ((string) $exception->getCode() === '23000') {
                $errors = ['email' => 'Ce pseudo ou cette adresse email est déjà utilisé.'];
                $this->renderAccountPage($user, $errors, $username, $email);

                return;
            }

            throw $exception;
        }

        // L'ancienne photo n'est plus utilisée : on la supprime du serveur
        if ($hasNewAvatar) {
            $uploader->delete('avatars', $previousAvatar);
        }

        Session::addFlash('success', 'Vos informations ont bien été enregistrées.');
        $this->redirect('/account');
    }

    /**
     * Affiche la page "Mon compte" (code 422 quand le formulaire contient des erreurs).
     *
     * @param array<string, string> $errors   Messages d'erreur, indexés par nom de champ
     * @param string                $username Pseudo à remettre dans le champ
     * @param string                $email    Email à remettre dans le champ
     */
    private function renderAccountPage(User $user, array $errors, string $username, string $email): void
    {
        $statusCode = count($errors) > 0 ? 422 : 200;

        $this->render('account/index', [
            'title' => 'Mon compte',
            'activePage' => 'account',
            'user' => $user,
            'books' => (new BookManager())->findByUserId($user->getId()),
            'errors' => $errors,
            'username' => $username,
            'email' => $email,
        ], $statusCode);
    }
}
