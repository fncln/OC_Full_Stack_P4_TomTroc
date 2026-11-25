<?php
/**
 * Messagerie : à gauche la liste des conversations, à droite le fil de discussion
 * avec le membre choisi et le formulaire pour lui écrire.
 *
 * @var App\Core\View $this
 * @var App\Entity\User $currentUser Membre connecté
 * @var App\Entity\Conversation[] $conversations Conversations du membre, de la plus récente à la plus ancienne
 * @var App\Entity\User|null $contact Membre dont on affiche le fil de discussion (null : aucune conversation ouverte)
 * @var App\Entity\Message[] $messages Messages échangés avec ce membre, du plus ancien au plus récent
 * @var string $content Texte à remettre dans le champ après une erreur
 * @var string $error Message d'erreur du formulaire (vide si tout est valide)
 */
?>
<div class="messaging container">
    <aside class="messaging__list" aria-labelledby="messaging-title">
        <h1 class="messaging__title" id="messaging-title">Messagerie</h1>
<?php if ($conversations === []) : ?>
        <p class="messaging__empty">Vous n'avez pas encore de conversation.</p>
<?php else : ?>
        <ul class="conversation-list">
<?php foreach ($conversations as $conversation) : ?>
<?php $isActive = $contact !== null && $conversation->getContactId() === $contact->getId(); ?>
            <li class="conversation-list__item<?= $isActive ? ' is-active' : '' ?>">
                <a class="conversation"
                   href="<?= $this->escape($this->url('/messages/' . $conversation->getContactId())) ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <img class="conversation__avatar"
                         src="<?= $this->escape($this->avatarUrl($conversation->getContactAvatar())) ?>"
                         width="48" height="48" alt="">
                    <span class="conversation__body">
                        <span class="conversation__name"><?= $this->escape($conversation->getContactUsername()) ?></span>
                        <time class="conversation__date"
                              datetime="<?= $this->escape($conversation->getLastSentAt()?->format('c')) ?>"><?= $this->escape($this->conversationDate($conversation->getLastSentAt())) ?></time>
                        <span class="conversation__preview"><?= $this->escape($this->excerpt($conversation->getLastContent(), 30)) ?></span>
                    </span>
<?php if ($conversation->getUnreadCount() > 0) : ?>
                    <span class="badge"><?= $this->escape($conversation->getUnreadCount()) ?><span class="sr-only"> message<?= $conversation->getUnreadCount() > 1 ? 's' : '' ?> non lu<?= $conversation->getUnreadCount() > 1 ? 's' : '' ?></span></span>
<?php endif; ?>
                </a>
            </li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>
    </aside>

    <section class="thread" aria-label="Fil de discussion">
<?php if ($contact === null) : ?>
        <p class="thread__empty">Pour écrire à un membre, ouvrez son profil depuis la page d'un livre.</p>
<?php else : ?>
        <header class="thread__header">
            <a class="thread__contact" href="<?= $this->escape($this->url('/members/' . $contact->getId())) ?>">
                <img class="thread__avatar" src="<?= $this->escape($this->avatarUrl($contact->getAvatar())) ?>"
                     width="64" height="64" alt="">
                <span class="thread__name"><?= $this->escape($contact->getUsername()) ?></span>
            </a>
        </header>

<?php if ($messages === []) : ?>
        <p class="thread__empty">Vous n'avez pas encore échangé avec <?= $this->escape($contact->getUsername()) ?>. Écrivez-lui le premier message !</p>
<?php else : ?>
        <ol class="thread__messages">
<?php foreach ($messages as $message) : ?>
<?php $isSent = $message->getSenderId() === $currentUser->getId(); ?>
            <li class="message <?= $isSent ? 'message--sent' : 'message--received' ?>">
                <p class="message__meta">
<?php if ($isSent === false) : ?>
                    <img class="message__avatar" src="<?= $this->escape($this->avatarUrl($contact->getAvatar())) ?>"
                         width="32" height="32" alt="">
<?php endif; ?>
                    <time datetime="<?= $this->escape($message->getSentAt()?->format('c')) ?>"><?= $this->escape($this->messageDate($message->getSentAt())) ?></time>
                </p>
                <p class="message__content"><?= nl2br($this->escape($message->getContent())) ?></p>
            </li>
<?php endforeach; ?>
        </ol>
<?php endif; ?>

        <form class="thread__form" action="<?= $this->escape($this->url('/messages/' . $contact->getId())) ?>" method="post">
            <label class="sr-only" for="message-content">Votre message à <?= $this->escape($contact->getUsername()) ?></label>
            <textarea class="form__input thread__input" id="message-content" name="content" rows="2"
                      maxlength="2000" placeholder="Tapez votre message ici" required
<?php if ($error !== '') : ?>
                      aria-invalid="true" aria-describedby="message-content-error"
<?php endif; ?>
                      ><?= $this->escape($content) ?></textarea>
            <button class="btn btn--primary" type="submit">Envoyer</button>
<?php if ($error !== '') : ?>
            <p class="form__error" id="message-content-error"><?= $this->escape($error) ?></p>
<?php endif; ?>
        </form>
<?php endif; ?>
    </section>
</div>
