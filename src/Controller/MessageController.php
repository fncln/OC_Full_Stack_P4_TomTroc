<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Manager\MessageManager;
use App\Manager\UserManager;

/**
 * Contrôleur de la messagerie : liste des conversations, fil de discussion avec un membre
 * et envoi d'un message. Toutes les actions sont réservées aux membres connectés.
 */
final class MessageController extends AbstractController
{
    /** Longueur maximale d'un message (en caractères) */
    private const CONTENT_MAX_LENGTH = 2000;

    /**
     * Affiche la messagerie : ouvre la conversation la plus récente.
     * Un membre qui n'a encore échangé avec personne voit la liste vide.
     */
    public function index(): void
    {
        $user = $this->requireLogin();
        $conversations = (new MessageManager())->findConversations($user->getId());

        if ($conversations !== []) {
            $this->redirect('/messages/' . $conversations[0]->getContactId());
        }

        $this->renderMessaging($user, null, '', '');
    }

    /**
     * Affiche le fil de discussion avec un membre, et le formulaire pour lui écrire.
     * Les messages reçus de ce membre sont alors marqués comme lus.
     *
     * @param int $id Identifiant du membre, lu dans l'adresse ("/messages/12")
     */
    public function show(int $id): void
    {
        $user = $this->requireLogin();
        $contact = $this->findContact($id, $user);

        if ($contact === null) {
            return;
        }

        $this->renderMessaging($user, $contact, '', '');
    }

    /**
     * Traite le formulaire d'envoi : vérifie le texte puis enregistre le message.
     *
     * @param int $id Identifiant du membre qui reçoit le message, lu dans l'adresse
     */
    public function send(int $id): void
    {
        $user = $this->requireLogin();
        $contact = $this->findContact($id, $user);

        if ($contact === null) {
            return;
        }

        $content = $this->readPostText('content');
        $error = $this->validateContent($content);

        if ($error !== '') {
            $this->renderMessaging($user, $contact, $content, $error);

            return;
        }

        $message = new Message();
        $message->setSenderId($user->getId());
        $message->setRecipientId($contact->getId());
        $message->setContent($content);

        (new MessageManager())->create($message);

        $this->redirect('/messages/' . $contact->getId());
    }

    /**
     * Retourne le membre avec qui discuter. Si aucun membre n'a cet identifiant, affiche la page 404
     * et retourne null. Un membre qui veut s'écrire à lui-même est renvoyé vers sa messagerie.
     */
    private function findContact(int $id, User $user): ?User
    {
        if ($id === $user->getId()) {
            $this->redirect('/messages');
        }

        $contact = (new UserManager())->findById($id);

        if ($contact === null) {
            (new ErrorController())->notFound();

            return null;
        }

        return $contact;
    }

    /**
     * Vérifie le texte d'un message.
     *
     * @return string Le message d'erreur, ou une chaîne vide quand le texte est valide
     */
    private function validateContent(string $content): string
    {
        if ($content === '') {
            return 'Le message ne peut pas être vide.';
        }

        if (mb_strlen($content) > self::CONTENT_MAX_LENGTH) {
            return 'Le message ne doit pas dépasser ' . self::CONTENT_MAX_LENGTH . ' caractères.';
        }

        return '';
    }

    /**
     * Affiche la messagerie (code 422 quand le formulaire contient une erreur).
     *
     * @param User|null $contact Membre dont on affiche le fil de discussion (null : aucune conversation ouverte)
     * @param string    $content Texte à remettre dans le champ après une erreur
     * @param string    $error   Message d'erreur du formulaire (vide si tout est valide)
     */
    private function renderMessaging(User $user, ?User $contact, string $content, string $error): void
    {
        $messageManager = new MessageManager();
        $messages = [];

        if ($contact !== null) {
            // Ouvrir le fil de discussion, c'est lire les messages reçus : on le fait avant de compter les non lus
            $messageManager->markAsRead($user->getId(), $contact->getId());
            $messages = $messageManager->findThread($user->getId(), $contact->getId());
        }

        $this->render('message/index', [
            'title' => 'Messagerie',
            'activePage' => 'messages',
            'conversations' => $messageManager->findConversations($user->getId()),
            'contact' => $contact,
            'messages' => $messages,
            'content' => $content,
            'error' => $error,
        ], $error === '' ? 200 : 422);
    }
}
