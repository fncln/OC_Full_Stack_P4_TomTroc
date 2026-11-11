<?php

declare(strict_types=1);

/*
 * Modèle de configuration.
 * Copiez ce fichier vers config/config.php puis adaptez les valeurs.
 * config/config.php n'est pas versionné : ne jamais publier de vrais identifiants.
 */

return [
    // Accès à la base de données (valeurs par défaut de XAMPP)
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'tomtroc',
    'db_user' => 'root',
    'db_password' => '',

    // true uniquement en développement : affiche le détail des erreurs
    'debug' => false,
];
