<?php

declare(strict_types=1);

use App\Controller\ErrorController;
use App\Core\Autoloader;
use App\Core\Config;
use App\Core\Router;
use App\Core\Session;

/*
 * Point d'entrée unique du site : toutes les pages passent par ce fichier.
 */

$projectDirectory = dirname(__DIR__);

// 1. L'autoloader est le seul fichier chargé à la main : il charge ensuite toutes les classes
require_once $projectDirectory . '/src/Core/Autoloader.php';
Autoloader::register('App\\', $projectDirectory . '/src');

try {
    // 2. Réglages de l'application
    $basePath = Router::detectBasePath($_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI']);
    Config::set('base_path', $basePath);
    Config::load($projectDirectory . '/config/config.php');

    // Le détail des erreurs PHP n'est affiché qu'en mode debug
    if (Config::get('debug', false) === true) {
        ini_set('display_errors', '1');
    } else {
        ini_set('display_errors', '0');
    }

    // La session permet de garder le membre connecté d'une page à l'autre
    Session::start();

    // 3. Déclaration des routes, puis exécution de celle qui correspond à l'adresse demandée
    $router = new Router($basePath);

    foreach (require $projectDirectory . '/config/routes.php' as $route) {
        $router->add($route[0], $route[1], $route[2], $route[3]);
    }

    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $exception) {
    // L'erreur est enregistrée dans les logs d'Apache, le visiteur voit une page d'erreur propre
    error_log((string) $exception);
    (new ErrorController())->serverError($exception);
}
