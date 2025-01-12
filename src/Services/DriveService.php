<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;

class DriveService {
    private $service;

    public function __construct() {
        $client = new Client();
        $client->setAuthConfig($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
        $client->addScope(Drive::DRIVE_READONLY);
        
        $this->service = new Drive($client);
    }

    public function listFiles($folderId = 'root') {
        try {
            $query = "'$folderId' in parents and trashed=false";
            $options = [
                'q' => $query,
                'pageSize' => 1000,
                'fields' => 'files(id, name, mimeType, thumbnailLink, webViewLink)',
                'orderBy' => 'folder,name'
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
