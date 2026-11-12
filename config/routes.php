<?php

declare(strict_types=1);

use App\Controller\BookController;

/*
 * Routes de l'application : [méthode HTTP, adresse, classe du contrôleur, méthode à appeler].
 * L'adresse "/" (page d'accueil) n'est pas encore déclarée : elle affiche la page 404
 * tant que la page d'accueil n'est pas créée.
 */

return [
    ['GET', '/books', BookController::class, 'index'],
];
