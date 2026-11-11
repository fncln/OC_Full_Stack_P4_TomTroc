<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Fournit la connexion à la base de données.
 * La connexion est créée une seule fois, à la première demande, puis réutilisée.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * Retourne la connexion PDO (la crée si elle n'existe pas encore).
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }

        return self::$connection;
    }

    /**
     * Crée la connexion à partir des réglages de config/config.php.
     */
    private static function createConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            (string) Config::get('db_host', '127.0.0.1'),
            (int) Config::get('db_port', 3306),
            (string) Config::get('db_name', 'tomtroc')
        );

        return new PDO(
            $dsn,
            (string) Config::get('db_user', 'root'),
            (string) Config::get('db_password', ''),
            [
                // En cas d'erreur SQL, PDO lance une exception au lieu de l'ignorer
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Chaque ligne est retournée sous forme de tableau associatif
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Les requêtes préparées sont exécutées par MySQL (protection contre l'injection SQL)
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
