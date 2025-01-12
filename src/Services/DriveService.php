<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;

class DriveService {
    private $service;
    private $isSharedDrive;
    private $driveId;

    public function __construct() {
        try {
            $client = new Client();
            $client->setApplicationName("Drive Browser");
            $client->setScopes([Drive::DRIVE_READONLY]);
            $client->setAccessType('offline');
            $client->setDeveloperKey($_ENV['GOOGLE_API_KEY']);

            // Configure OAuth 2.0 access token
            if (!empty($_ENV['SHARED_DRIVE_ACCESS_TOKEN'])) {
                $client->setAccessToken($_ENV['SHARED_DRIVE_ACCESS_TOKEN']);
            }

            $this->service = new Drive($client);
            $this->isSharedDrive = filter_var($_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $this->driveId = $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'];

            error_log("DriveService initialized with:");
            error_log("API Key: " . substr($_ENV['GOOGLE_API_KEY'], 0, 10) . "...");
            error_log("Drive ID: " . $this->driveId);
            error_log("Is Shared Drive: " . ($this->isSharedDrive ? 'true' : 'false'));
        } catch (\Exception $e) {
            error_log("Error initializing DriveService: " . $e->getMessage());
            throw $e;
        }
    }

    public function listFiles($folderId = null) {
        try {
            if ($folderId === null) {
                $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? $this->driveId;
            }

            error_log("Listing files for folder: " . $folderId);

            $params = [
                'pageSize' => 1000,
                'fields' => 'files(id, name, mimeType, thumbnailLink, webViewLink)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'orderBy' => 'folder,name desc'
            ];

            // Handle shared drive access
            if ($this->isSharedDrive) {
                $params['driveId'] = $this->driveId;
                $params['corpora'] = 'drives';
                $params['q'] = sprintf("'%s' in parents and trashed = false", $folderId);
            } else {
                $params['q'] = sprintf("'%s' in parents and trashed = false", $folderId);
            }

            error_log("API Request parameters: " . json_encode($params));

            $results = $this->service->files->listFiles($params);
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

            error_log("Found " . count($files) . " files");
            return $files;

        } catch (\Exception $e) {
            error_log("Error in listFiles: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}