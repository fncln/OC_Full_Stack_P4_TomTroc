<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Autoloader PSR-4 : charge automatiquement une classe à partir de son namespace,
 * sans avoir à écrire de "require" pour chaque fichier.
 *
 * Exemple : la classe App\Manager\BookManager est chargée depuis le fichier
 * src/Manager/BookManager.php.
 */
final class Autoloader
{
    /**
     * Enregistre l'autoloader auprès de PHP.
     *
     * @param string $namespacePrefix Début commun des namespaces de l'application (ex. "App\")
     * @param string $baseDirectory   Dossier qui correspond à ce préfixe (ex. "/projet/src")
     */
    public static function register(string $namespacePrefix, string $baseDirectory): void
    {
        spl_autoload_register(
            static function (string $className) use ($namespacePrefix, $baseDirectory): void {
                // Les classes qui n'appartiennent pas à l'application ne nous concernent pas
                if (str_starts_with($className, $namespacePrefix) === false) {
                    return;
                }

                // "App\Manager\BookManager" devient "Manager/BookManager"
                $relativeClassName = substr($className, strlen($namespacePrefix));
                $relativePath = str_replace('\\', '/', $relativeClassName);
                $file = $baseDirectory . '/' . $relativePath . '.php';

                if (is_file($file)) {
                    require $file;
                }
            }
        );
    }
}
