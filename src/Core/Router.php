<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\ErrorController;

/**
 * Routeur : point d'entrée unique de l'application.
 * Il associe l'adresse demandée (ex. "/books") à une méthode d'un contrôleur.
 * Toute adresse inconnue affiche la page d'erreur 404.
 */
final class Router
{
    /** @var array<int, array{method: string, path: string, controller: string, action: string}> */
    private array $routes = [];

    /**
     * @param string $basePath Dossier dans lequel le site est installé (ex. "/tomtroc"), vide si à la racine
     */
    public function __construct(private string $basePath)
    {
    }

    /**
     * Déclare une route.
     *
     * @param string $httpMethod      Méthode HTTP : "GET" pour afficher une page, "POST" pour envoyer un formulaire
     * @param string $path            Adresse de la page (ex. "/books")
     * @param string $controllerClass Nom complet de la classe du contrôleur
     * @param string $action          Nom de la méthode du contrôleur à appeler
     */
    public function add(string $httpMethod, string $path, string $controllerClass, string $action): void
    {
        $this->routes[] = [
            'method' => $httpMethod,
            'path' => $path,
            'controller' => $controllerClass,
            'action' => $action,
        ];
    }

    /**
     * Cherche la route qui correspond à la demande et exécute son contrôleur.
     * Si aucune route ne correspond, la page 404 est affichée.
     */
    public function dispatch(string $httpMethod, string $requestUri): void
    {
        $requestedPath = $this->extractPath($requestUri);

        foreach ($this->routes as $route) {
            if ($route['method'] === $httpMethod && $route['path'] === $requestedPath) {
                $controllerClass = $route['controller'];
                $action = $route['action'];

                // Crée le contrôleur puis appelle sa méthode, dont le nom est stocké dans $action
                $controller = new $controllerClass();
                $controller->$action();

                return;
            }
        }

        (new ErrorController())->notFound();
    }

    /**
     * Détermine le dossier dans lequel le site est installé.
     *
     * Exemples : "" quand le site est à la racine, "/tomtroc" avec XAMPP
     * (le fichier .htaccess de la racine redirige alors vers le dossier "public").
     *
     * @param string $scriptName Chemin du script exécuté (ex. "/tomtroc/public/index.php")
     * @param string $requestUri Adresse demandée (ex. "/tomtroc/books")
     */
    public static function detectBasePath(string $scriptName, string $requestUri): string
    {
        $scriptDirectory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        $requestedPath = (string) parse_url($requestUri, PHP_URL_PATH);

        $isPublicFolderHidden = str_ends_with($scriptDirectory, '/public')
            && str_starts_with($requestedPath, $scriptDirectory) === false;

        if ($isPublicFolderHidden) {
            // L'adresse ne contient pas "/public" : on le retire du dossier d'installation
            return substr($scriptDirectory, 0, -strlen('/public'));
        }

        return $scriptDirectory;
    }

    /**
     * Extrait de l'adresse demandée le chemin de la page, sans le dossier d'installation
     * ni les paramètres. Exemple : "/tomtroc/books?q=hygge" devient "/books".
     */
    private function extractPath(string $requestUri): string
    {
        $path = (string) parse_url($requestUri, PHP_URL_PATH);

        if (str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }

        // "/books/" et "/books" désignent la même page
        $path = rtrim($path, '/');

        if ($path === '') {
            return '/';
        }

        return $path;
    }
}
