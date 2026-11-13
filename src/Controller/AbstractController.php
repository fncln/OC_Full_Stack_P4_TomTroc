<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;

/**
 * Classe commune à tous les contrôleurs.
 * Un contrôleur reçoit une demande, utilise les managers pour récupérer les données,
 * puis demande à la vue d'afficher la page.
 */
abstract class AbstractController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(dirname(__DIR__, 2) . '/templates');
    }

    /**
     * Affiche une page.
     *
     * @param string               $template   Nom du template (ex. "book/index")
     * @param array<string, mixed> $data       Variables transmises au template
     * @param int                  $statusCode Code HTTP de la réponse (200 = succès, 404 = page introuvable...)
     */
    protected function render(string $template, array $data = [], int $statusCode = 200): void
    {
        // Le HTML est généré avant d'être envoyé : si une erreur survient, aucune page à moitié affichée
        $html = $this->view->render($template, $data);

        http_response_code($statusCode);
        echo $html;
    }
}
