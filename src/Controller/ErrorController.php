<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Config;
use Throwable;

/**
 * Affiche les pages d'erreur : page introuvable (404) et erreur du serveur (500).
 */
final class ErrorController extends AbstractController
{
    /**
     * Page affichée quand l'adresse demandée n'existe pas.
     */
    public function notFound(): void
    {
        $this->render('error/404', ['title' => 'Page introuvable'], 404);
    }

    /**
     * Page affichée quand une erreur technique se produit (base de données inaccessible...).
     * Le détail de l'erreur n'est montré qu'en mode debug, jamais aux visiteurs.
     */
    public function serverError(?Throwable $exception = null): void
    {
        $details = '';

        if ($exception instanceof Throwable && Config::get('debug', false) === true) {
            $details = $exception->getMessage();
        }

        $this->render('error/500', ['title' => 'Erreur du serveur', 'details' => $details], 500);
    }
}
