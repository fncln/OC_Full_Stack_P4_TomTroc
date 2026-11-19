<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\BookController;
use App\Controller\HomeController;

/*
 * Routes de l'application : [méthode HTTP, adresse, classe du contrôleur, méthode à appeler].
 */

return [
    ['GET', '/', HomeController::class, 'index'],
    ['GET', '/books', BookController::class, 'index'],
    ['GET', '/books/{id}', BookController::class, 'show'],
    ['GET', '/register', AuthController::class, 'showRegister'],
    ['POST', '/register', AuthController::class, 'register'],
    ['GET', '/login', AuthController::class, 'showLogin'],
    ['POST', '/login', AuthController::class, 'login'],
    ['POST', '/logout', AuthController::class, 'logout'],
];
