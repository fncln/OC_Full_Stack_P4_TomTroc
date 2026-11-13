<?php

declare(strict_types=1);

use App\Controller\BookController;
use App\Controller\HomeController;

/*
 * Routes de l'application : [méthode HTTP, adresse, classe du contrôleur, méthode à appeler].
 */

return [
    ['GET', '/', HomeController::class, 'index'],
    ['GET', '/books', BookController::class, 'index'],
];
