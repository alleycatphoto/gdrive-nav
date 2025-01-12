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
            $client->setDeveloperKey($_ENV['GOOGLE_API_KEY']);

            $this->service = new Drive($client);
            $this->isSharedDrive = filter_var($_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $this->driveId = $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'];

            error_log("DriveService initialized with:");
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
                $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'];
            }

            error_log("Listing files for folder: " . $folderId);

            $optParams = [
                'pageSize' => 1000,
                'fields' => 'files(id, name, mimeType, thumbnailLink, webViewLink)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'orderBy' => 'folder,name',
                'driveId' => $this->driveId,
                'corpora' => 'drive',
                'q' => "'{$folderId}' in parents and trashed = false"
            ];

            error_log("API Request parameters: " . json_encode($optParams));

            $results = $this->service->files->listFiles($optParams);
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