<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\BookManager;
use App\Manager\UserManager;

/**
 * Contrôleur du profil public d'un membre : pseudo, photo, ancienneté et bibliothèque.
 * La page est visible par tous les visiteurs, mais elle n'est accessible que depuis un livre
 * (il n'existe pas de page qui liste tous les membres).
 */
final class MemberController extends AbstractController
{
    /**
     * Affiche le profil public d'un membre.
     * Si aucun membre n'a cet identifiant, la page 404 est affichée.
     * Un membre connecté qui ouvre son propre profil est renvoyé vers "Mon compte".
     *
     * @param int $id Identifiant du membre, lu dans l'adresse ("/members/12")
     */
    public function show(int $id): void
    {
        $currentUser = $this->currentUser();

        if ($currentUser !== null && $currentUser->getId() === $id) {
            $this->redirect('/account');
        }

        $member = (new UserManager())->findById($id);

        if ($member === null) {
            (new ErrorController())->notFound();

            return;
        }

        $this->render('member/show', [
            'title' => 'Profil de ' . $member->getUsername(),
            'activePage' => 'books',
            'member' => $member,
            'books' => (new BookManager())->findByUserId($member->getId()),
        ]);
    }
}
