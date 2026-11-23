<?php

declare(strict_types=1);

namespace App\Controller\AbstractController;

use finfo;
use InvalidArgumentException;

/**
 * Enregistre les images envoyées par les membres (photos de livres et photos de profil).
 *
 * Ce qui vient du visiteur n'est jamais cru : le nom du fichier et le type annoncés par le navigateur sont ignorés.
 * Le vrai type est lu dans le contenu du fichier, et le fichier reçoit un nom aléatoire.
 */
final class ImageUploader
{
    /** Taille maximale d'une image : 2 Mo */
    private const MAX_SIZE_IN_BYTES = 2097152;

    /** Types d'image acceptés, avec l'extension donnée au fichier enregistré */
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /** Début du nom des fichiers envoyés par les membres (les images de démonstration n'ont pas ce début) */
    private const FILE_PREFIX = 'upload-';

    /**
     * @param string $uploadDirectory Dossier qui contient les images (ex. ".../public/uploads")
     */
    public function __construct(private string $uploadDirectory)
    {
    }

    /**
     * Enregistre l'image envoyée dans un sous-dossier ("books" ou "avatars").
     *
     * @param array<string, mixed>|null $file   Le fichier reçu, tel que PHP le donne dans $_FILES
     * @param string                    $folder Sous-dossier de destination
     *
     * @return string|null Nom du fichier enregistré, ou null si aucun fichier n'a été choisi
     *
     * @throws InvalidArgumentException avec un message que l'on peut montrer au membre
     */
    public function upload(?array $file, string $folder): ?string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $this->checkUploadError((int) $file['error']);

        $temporaryPath = (string) ($file['tmp_name'] ?? '');

        // Vérifie que le fichier vient bien d'un envoi de formulaire, et pas d'un chemin choisi par le visiteur
        if (is_uploaded_file($temporaryPath) === false) {
            throw new InvalidArgumentException('L\'envoi de l\'image a échoué. Merci de réessayer.');
        }

        if ((int) $file['size'] > self::MAX_SIZE_IN_BYTES) {
            throw new InvalidArgumentException('L\'image est trop lourde : 2 Mo au maximum.');
        }

        $extension = $this->findExtension($temporaryPath);
        $directory = $this->uploadDirectory . '/' . $folder;

        if (is_dir($directory) === false && mkdir($directory, 0755, true) === false) {
            throw new InvalidArgumentException('L\'image n\'a pas pu être enregistrée sur le serveur.');
        }

        // Nom aléatoire : impossible à deviner et impossible d'écraser l'image d'un autre membre
        $fileName = self::FILE_PREFIX . bin2hex(random_bytes(16)) . '.' . $extension;

        if (move_uploaded_file($temporaryPath, $directory . '/' . $fileName) === false) {
            throw new InvalidArgumentException('L\'image n\'a pas pu être enregistrée sur le serveur.');
        }

        return $fileName;
    }

    /**
     * Supprime une image enregistrée par upload().
     * Les autres fichiers (images de démonstration) ne sont jamais supprimés.
     */
    public function delete(string $folder, ?string $fileName): void
    {
        if (is_string($fileName) === false || preg_match('/^upload-[a-f0-9]{32}\.(jpg|png|webp)$/', $fileName) === 0) {
            return;
        }

        $file = $this->uploadDirectory . '/' . $folder . '/' . $fileName;

        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Transforme le code d'erreur de PHP en message compréhensible.
     */
    private function checkUploadError(int $errorCode): void
    {
        if ($errorCode === UPLOAD_ERR_OK) {
            return;
        }

        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
            throw new InvalidArgumentException('L\'image est trop lourde : 2 Mo au maximum.');
        }

        throw new InvalidArgumentException('L\'envoi de l\'image a échoué. Merci de réessayer.');
    }

    /**
     * Lit le vrai type du fichier dans son contenu et retourne l'extension correspondante.
     *
     * @throws InvalidArgumentException si le fichier n'est pas une image JPEG, PNG ou WebP
     */
    private function findExtension(string $path): string
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        $isAllowedType = is_string($mimeType) && array_key_exists($mimeType, self::ALLOWED_TYPES);

        // getimagesize() vérifie en plus que le contenu est une vraie image
        if ($isAllowedType === false || getimagesize($path) === false) {
            throw new InvalidArgumentException('Le fichier doit être une image JPEG, PNG ou WebP.');
        }

        return self::ALLOWED_TYPES[$mimeType];
    }
}
