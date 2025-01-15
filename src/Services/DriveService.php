<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;

class DriveService {
    private $service;
    private $isSharedDrive;
    private $driveId;
    private $defaultFolderId;

    public function __construct() {
        try {
            $client = new Client();
            $client->setApplicationName("Drive Browser");
            $client->setAuthConfig($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
            $client->addScope(Drive::DRIVE_READONLY);

            $this->service = new Drive($client);
            $this->isSharedDrive = filter_var($_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $this->driveId = $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'];
            $this->defaultFolderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? $this->driveId;

            error_log("DriveService initialized with:");
            error_log("Drive ID: " . $this->driveId);
            error_log("Default Folder ID: " . $this->defaultFolderId);
            error_log("Is Shared Drive: " . ($this->isSharedDrive ? 'true' : 'false'));
        } catch (\Exception $e) {
            error_log("Error initializing DriveService: " . $e->getMessage());
            throw $e;
        }
    }

    // Add method to get file metadata
    public function getFileMetadata($fileId) {
        try {
            $file = $this->service->files->get($fileId, [
                'supportsAllDrives' => true,
                'fields' => 'id, name, mimeType, thumbnailLink'
            ]);

            $downloadUrl = "https://drive.usercontent.google.com/download?id=" . $file->getId() . "&export=download&authuser=0";
            $viewUrl = "https://drive.google.com/file/d/" . $file->getId() . "/view";

            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'mimeType' => $file->getMimeType(),
                'thumbnailLink' => $file->getThumbnailLink(),
                'downloadUrl' => $downloadUrl,
                'webViewLink' => $viewUrl
            ];
        } catch (\Exception $e) {
            error_log("Error getting file metadata: " . $e->getMessage());
            return null;
        }
    }

    public function getService() {
        return $this->service;
    }

    public function getBreadcrumbs($folderId) {
        try {
            $breadcrumbs = [];
            $currentId = $folderId;

            while ($currentId && $currentId !== $this->defaultFolderId) {
                $file = $this->service->files->get($currentId, [
                    'supportsAllDrives' => true,
                    'fields' => 'id, name, parents'
                ]);

                array_unshift($breadcrumbs, [
                    'id' => $file->getId(),
                    'name' => $file->getName()
                ]);

                $parents = $file->getParents();
                $currentId = !empty($parents) ? $parents[0] : null;
            }

            array_unshift($breadcrumbs, [
                'id' => $this->defaultFolderId,
                'name' => 'Home'
            ]);

            return $breadcrumbs;
        } catch (\Exception $e) {
            error_log("Error getting breadcrumbs: " . $e->getMessage());
            return [['id' => $this->defaultFolderId, 'name' => 'Home']];
        }
    }

    public function listFiles($folderId = null) {
        try {
            if ($folderId === null || empty($folderId)) {
                $folderId = $this->defaultFolderId;
            }

            error_log("Listing files for folder: " . $folderId);

            $optParams = [
                'pageSize' => 1000,
                'fields' => 'files(id, name, mimeType, thumbnailLink)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => $this->isSharedDrive,
                'orderBy' => 'folder,name',
                'q' => "'$folderId' in parents and trashed = false"
            ];

            if ($this->isSharedDrive) {
                $optParams['driveId'] = $this->driveId;
                $optParams['corpora'] = 'drive';
            }

            error_log("API Request parameters: " . json_encode($optParams));

            $results = $this->service->files->listFiles($optParams);
            $files = [];

            foreach ($results->getFiles() as $file) {
                $downloadUrl = "https://drive.usercontent.google.com/download?id=" . $file->getId() . "&export=download&authuser=0";
                $viewUrl = "https://drive.google.com/file/d/" . $file->getId() . "/view";

                $thumbnailLink = $file->getThumbnailLink();
                $highResThumbnail = $thumbnailLink ? preg_replace('/=s\d+$/', '=s1024', $thumbnailLink) : null;

                $files[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'thumbnailLink' => $thumbnailLink,
                    'highResThumbnail' => $highResThumbnail,
                    'downloadUrl' => $downloadUrl,
                    'webViewLink' => $viewUrl,
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