<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

/**
 * Génère le HTML d'une page.
 *
 * Chaque page est un "template" (un fichier PHP du dossier templates/).
 * Le template est inséré dans le gabarit commun layout.php, qui contient le header
 * et le footer : ils sont écrits une seule fois et jamais dupliqués dans les pages.
 */
final class View
{
    /**
     * @param string $templatesDirectory Dossier qui contient les templates
     */
    public function __construct(private string $templatesDirectory)
    {
    }

    /**
     * Génère la page complète (template inséré dans le layout) et la retourne sous forme de texte.
     *
     * @param string               $template Nom du template, sans extension (ex. "book/index")
     * @param array<string, mixed> $data     Variables disponibles dans le template
     */
    public function render(string $template, array $data = []): string
    {
        // Valeurs par défaut, remplacées par celles données par le contrôleur
        $data = array_merge(['title' => 'Tom Troc', 'activePage' => '', 'currentUser' => null, 'flashes' => []], 
        $data
        );

        // 1. On génère d'abord le contenu propre à la page...
        $content = $this->renderFile($template, $data);

        // 2. ...puis on l'insère dans le layout, qui ajoute le header et le footer
        return $this->renderFile('layout', array_merge($data, ['content' => $content]));
    }

    /**
     * Affiche un morceau de page réutilisable (ex. le header ou le footer).
     *
     * @param array<string, mixed> $data
     */
    public function partial(string $name, array $data = []): void
    {
        echo $this->renderFile('partial/' . $name, $data);
    }

    /**
     * Protège une valeur avant de l'afficher dans le HTML (empêche l'injection de code, faille XSS).
     * Toute valeur qui vient de la base de données ou d'un formulaire doit passer par cette méthode.
     */
    public function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Construit l'adresse d'une page ou d'un fichier du site, en tenant compte du dossier
     * dans lequel le site est installé (ex. "/tomtroc").
     */
    public function url(string $path): string
    {
        return (string) Config::get('base_path', '') . '/' . ltrim($path, '/');
    }

    /**
     * Génère l'URL d'un fichier asset (image, CSS, JS) en ciblant 
     * automatiquement le dossier 'assets/' situé dans le dossier public.
     * 
     * @param string $path Le chemin relatif du fichier à partir du dossier assets
     * @return string L'URL complète de la ressource
     */
    public function asset(string $path): string
    {
        // On retire les éventuels slashes superflus au début du chemin passé en paramètre,
        // puis on le concatène proprement après le dossier "assets/"
        return $this->url('assets/' . ltrim($path, '/'));
    }


    /**
     * Exécute un fichier template et retourne le HTML qu'il produit.
     *
     * @param array<string, mixed> $data
     */
    private function renderFile(string $template, array $data): string
    {
        $file = $this->templatesDirectory . '/' . $template . '.php';

        if (is_file($file) === false) {
            throw new RuntimeException('Template introuvable : ' . $template);
        }

        // Crée une variable par clé du tableau : ['books' => [...]] donne la variable $books
        extract($data, EXTR_SKIP);

        // Le HTML produit par le template est mis en mémoire au lieu d'être envoyé directement
        ob_start();

        try {
            require $file;
        } catch (Throwable $exception) {
            ob_end_clean();

            throw $exception;
        }

        return (string) ob_get_clean();
    }
}
