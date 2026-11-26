<?php

declare(strict_types=1);

use App\Controller\AccountController;
use App\Controller\AuthController;
use App\Controller\BookController;
use App\Controller\HomeController;
use App\Controller\MemberController;
use App\Controller\MessageController;

/*
 * Routes de l'application : [méthode HTTP, adresse, classe du contrôleur, méthode à appeler].
 */

return [
    ['GET', '/', HomeController::class, 'index'],
    ['GET', '/books', BookController::class, 'index'],
    ['GET', '/books/new', BookController::class, 'create'],
    ['POST', '/books/new', BookController::class, 'store'],
    ['GET', '/books/{id}', BookController::class, 'show'],
    ['GET', '/books/{id}/edit', BookController::class, 'edit'],
    ['POST', '/books/{id}/edit', BookController::class, 'update'],
    ['GET', '/books/{id}/delete', BookController::class, 'confirmDelete'],
    ['POST', '/books/{id}/delete', BookController::class, 'delete'],
    ['GET', '/members/{id}', MemberController::class, 'show'],
        ['GET', '/messages', MessageController::class, 'index'],
    ['GET', '/messages/{id}', MessageController::class, 'show'],
    ['POST', '/messages/{id}', MessageController::class, 'send'],
    ['GET', '/register', AuthController::class, 'showRegister'],
    ['POST', '/register', AuthController::class, 'register'],
    ['GET', '/login', AuthController::class, 'showLogin'],
    ['POST', '/login', AuthController::class, 'login'],
    ['POST', '/logout', AuthController::class, 'logout'],
    ['GET', '/account', AccountController::class, 'index'],
    ['POST', '/account/profile', AccountController::class, 'updateProfile'],
];
