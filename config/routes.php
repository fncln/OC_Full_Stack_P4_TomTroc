<?php

declare(strict_types=1);

use App\Controller\BookController;

/*
 * Routes de l'application : [méthode HTTP, adresse, classe du contrôleur, méthode à appeler].
 * Tant que la page d'accueil n'existe pas, l'adresse "/" affiche la liste des livres.
 */

return [
    ['GET', '/', BookController::class, 'index'],
    ['GET', '/books', BookController::class, 'index'],
];
