<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gère la session du visiteur : elle permet de garder le membre connecté d'une page à l'autre
 * et d'afficher un message une seule fois (message "flash").
 */
final class Session
{
    /** Clé qui contient l'identifiant du membre connecté */
    private const USER_KEY = 'user_id';

    /** Clé qui contient les messages à afficher à la prochaine page */
    private const FLASH_KEY = 'flash_messages';

    /**
     * Démarre la session, avec un cookie protégé.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Le cookie n'est valable que dans le dossier du site (ex. "/tomtroc")
        $cookiePath = (string) Config::get('base_path', '');

        if ($cookiePath === '') {
            $cookiePath = '/';
        }

        session_set_cookie_params([
            // 0 : le cookie disparaît à la fermeture du navigateur
            'lifetime' => 0,
            'path' => $cookiePath,
            // Envoyé uniquement en HTTPS quand le site est en HTTPS
            'secure' => ($_SERVER['HTTPS'] ?? 'off') === 'on',
            // Inaccessible au JavaScript : protège le cookie en cas de faille XSS
            'httponly' => true,
            // Non envoyé lors d'une requête venant d'un autre site (protection contre le CSRF)
            'samesite' => 'Lax',
        ]);
        session_name('tomtroc_session');
        session_start();
    }

    /**
     * Connecte un membre.
     */
    public static function login(int $userId): void
    {
        // Nouvel identifiant de session à chaque connexion : empêche le vol de session
        session_regenerate_id(true);
        $_SESSION[self::USER_KEY] = $userId;
    }

    /**
     * Retourne l'identifiant du membre connecté, ou null pour un visiteur.
     */
    public static function getUserId(): ?int
    {
        $userId = $_SESSION[self::USER_KEY] ?? null;

        if (is_int($userId)) {
            return $userId;
        }

        return null;
    }

    /**
     * Déconnecte le membre.
     */
    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    /**
     * Ajoute un message à afficher sur la prochaine page.
     *
     * @param string $type    "success" (réussite) ou "error" (erreur)
     * @param string $message Texte du message
     */
    public static function addFlash(string $type, string $message): void
    {
        $_SESSION[self::FLASH_KEY][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Retourne les messages en attente et les supprime : ils ne s'affichent qu'une fois.
     *
     * @return array<int, array{type: string, message: string}>
     */
    public static function pullFlashes(): array
    {
        $flashes = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);

        return $flashes;
    }
}
