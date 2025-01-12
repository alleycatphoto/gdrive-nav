<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;

class DriveService {
    private $service;
    private $isSharedDrive;

    public function __construct() {
        $client = new Client();
        $client->setAuthConfig($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
        $client->addScope(Drive::DRIVE_READONLY);

        $this->service = new Drive($client);
        $this->isSharedDrive = filter_var($_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function listFiles($folderId = null) {
        try {
            if ($folderId === null) {
                $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'];
            }

            $query = "'$folderId' in parents and trashed=false";
            $options = [
                'q' => $query,
                'pageSize' => 1000,
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'fields' => 'files(id, name, mimeType, thumbnailLink, webViewLink)',
                'orderBy' => 'folder,name',
                'driveId' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'],
                'corpora' => 'drive'
            ];

            $results = $this->service->files->listFiles($options);
            $files = [];

            foreach ($results->getFiles() as $file) {
                $files[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'thumbnailLink' => $file->getThumbnailLink(),
                    'webViewLink' => $file->getWebViewLink(),
                    'isFolder' => $file->getMimeType() === 'application/vnd.google-apps.folder'
                ];
            }

            return $files;
        } catch (\Exception $e) {
            error_log("Error listing files: " . $e->getMessage());
            throw $e;
        }
    }
}