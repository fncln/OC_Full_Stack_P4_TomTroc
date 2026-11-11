<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Stocke les réglages de l'application (accès à la base de données, mode debug...).
 * Les réglages sont lus depuis le fichier config/config.php.
 */
final class Config
{
    /** @var array<string, mixed> Réglages, indexés par leur nom */
    private static array $values = [];

    /**
     * Charge le fichier de configuration.
     *
     * @throws RuntimeException si le fichier n'existe pas
     */
    public static function load(string $file): void
    {
        if (is_file($file) === false) {
            throw new RuntimeException(
                'Fichier de configuration introuvable : copiez config/config.example.php vers config/config.php.'
            );
        }

        self::$values = array_merge(self::$values, require $file);
    }

    /**
     * Retourne la valeur d'un réglage, ou $default si le réglage n'existe pas.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }

        return $default;
    }

    /**
     * Définit ou remplace la valeur d'un réglage.
     */
    public static function set(string $key, mixed $value): void
    {
        self::$values[$key] = $value;
    }
}
