<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Session;
use App\Core\View;
use App\Entity\User;
use App\Manager\UserManager;
use Throwable;

/**
 * Classe commune à tous les contrôleurs.
 * Un contrôleur reçoit une demande, utilise les managers pour récupérer les données,
 * puis demande à la vue d'afficher la page.
 */
abstract class AbstractController
{
    private View $view;
    private ?User $currentUser = null;
    private bool $isCurrentUserLoaded = false;

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
        // Le membre connecté et les messages sont transmis à toutes les pages (header, messages...)
        $html = $this->view->render($template, array_merge($this->createSharedData(), $data));

        // Le HTML est généré avant d'être envoyé : si une erreur survient, aucune page à moitié affichée
        http_response_code($statusCode);
        echo $html;
    }

    /**
     * Redirige le visiteur vers une autre page puis arrête le script.
     *
     * @param string $path Adresse de la page (ex. "/books")
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . $this->view->url($path));
        exit;
    }

    /**
     * Retourne le membre connecté, ou null pour un visiteur.
     */
    protected function currentUser(): ?User
    {
        // Le membre n'est cherché dans la base qu'une seule fois par page
        if ($this->isCurrentUserLoaded === false) {
            $this->isCurrentUserLoaded = true;
            $userId = Session::getUserId();

            if (is_int($userId)) {
                $this->currentUser = (new UserManager())->findById($userId);

                // Le compte n'existe plus : on déconnecte
                if ($this->currentUser === null) {
                    Session::logout();
                }
            }
        }

        return $this->currentUser;
    }

    /**
     * Prépare les données communes à toutes les pages.
     *
     * @return array<string, mixed>
     */
    private function createSharedData(): array
    {
        try {
            return [
                'currentUser' => $this->currentUser(),
                'flashes' => Session::pullFlashes(),
            ];
        } catch (Throwable $exception) {
            // Les pages d'erreur doivent rester affichables quand la base de données est inaccessible
            return ['currentUser' => null, 'flashes' => []];
        }
    }
}
