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

    // Enhanced method to get file metadata with video support and platform-specific data
    public function getFileMetadata($fileId) {
        try {
            $file = $this->service->files->get($fileId, [
                'supportsAllDrives' => true,
                'fields' => 'id, name, mimeType, thumbnailLink, videoMediaMetadata, imageMediaMetadata, parents, description'
            ]);

            $downloadUrl = "https://drive.usercontent.google.com/download?id=" . $file->getId() . "&export=download&authuser=0";
            $viewUrl = "https://drive.google.com/file/d/" . $file->getId() . "/view";

            // Get video metadata if available
            $videoMetadata = $file->getVideoMediaMetadata();
            $dimensions = [];
            if ($videoMetadata) {
                $dimensions = [
                    'width' => $videoMetadata->getWidth(),
                    'height' => $videoMetadata->getHeight(),
                    'durationMillis' => $videoMetadata->getDurationMillis()
                ];
            }

            // Generate different thumbnail sizes
            $thumbnails = $this->generateThumbnailSizes($file->getThumbnailLink());

            // Get image metadata if available
            $imageMetadata = $file->getImageMediaMetadata();
            $imageInfo = [];
            if ($imageMetadata) {
                // Safely get metadata properties
                $imageInfo = [
                    'width' => $imageMetadata->width ?? null,
                    'height' => $imageMetadata->height ?? null,
                    'rotation' => $imageMetadata->rotation ?? null
                ];
            }

            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'mimeType' => $file->getMimeType(),
                'thumbnailLink' => $file->getThumbnailLink(),
                'thumbnails' => $thumbnails,
                'videoMetadata' => $dimensions,
                'imageMetadata' => $imageInfo,
                'downloadUrl' => $downloadUrl,
                'webViewLink' => $viewUrl,
                'parentId' => $file->getParents() ? $file->getParents()[0] : null,
                'description' => $file->getDescription(),
                'socialMetadata' => $this->generateSocialMetadata($file)
            ];
        } catch (\Exception $e) {
            error_log("Error getting file metadata: " . $e->getMessage());
            return null;
        }
    }

    // Generate social media specific metadata
    private function generateSocialMetadata($file) {
        $title = $file->getName();
        $description = $file->getDescription() ?? "Shared via DNA Distribution";
        $mimeType = $file->getMimeType();

        return [
            'facebook' => [
                'title' => $title,
                'description' => $description,
                'type' => $this->getFacebookType($mimeType),
            ],
            'twitter' => [
                'title' => $title,
                'description' => substr($description, 0, 200),
                'card' => $this->getTwitterCardType($mimeType),
            ],
            'linkedin' => [
                'title' => $title,
                'description' => substr($description, 0, 250),
            ],
            'pinterest' => [
                'title' => $title,
                'description' => substr($description, 0, 500),
            ]
        ];
    }

    // Determine Facebook content type
    private function getFacebookType($mimeType) {
        if (strpos($mimeType, 'video/') === 0) {
            return 'video.other';
        } elseif (strpos($mimeType, 'image/') === 0) {
            return 'photo';
        } elseif ($mimeType === 'application/pdf') {
            return 'article';
        }
        return 'website';
    }

    // Determine Twitter card type
    private function getTwitterCardType($mimeType) {
        if (strpos($mimeType, 'video/') === 0) {
            return 'player';
        } elseif (strpos($mimeType, 'image/') === 0) {
            return 'summary_large_image';
        }
        return 'summary';
    }

    // Generate different thumbnail sizes for social platforms
    private function generateThumbnailSizes($thumbnailUrl) {
        if (!$thumbnailUrl) return null;

        // Define thumbnail sizes for different platforms
        $sizes = [
            'facebook' => ['w' => 1200, 'h' => 630],   // Facebook recommended
            'twitter' => ['w' => 1200, 'h' => 675],    // Twitter card
            'linkedin' => ['w' => 1200, 'h' => 627],   // LinkedIn sharing
            'pinterest' => ['w' => 600, 'h' => 900],   // Pinterest optimal
            'thumbnail' => ['w' => 320, 'h' => 180],   // Small preview
            'preview' => ['w' => 1280, 'h' => 720]     // Full preview
        ];

        $thumbnails = [];
        foreach ($sizes as $platform => $dimensions) {
            // Replace size parameter in Google Drive thumbnail URL
            $thumbnails[$platform] = preg_replace(
                '/=s\d+/', 
                "=w{$dimensions['w']}-h{$dimensions['h']}-c", 
                $thumbnailUrl
            );
        }

        return $thumbnails;
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

    public function searchFiles($query, $folderId = null) {
        try {
            if ($folderId === null) {
                $folderId = $this->defaultFolderId;
            }

            // Escape single quotes and special characters
            $escapedQuery = str_replace("'", "\\'", $query);

            // Build the search query using fullText search for comprehensive results
            $searchQuery = "fullText contains '{$escapedQuery}' and trashed = false";

            // If folder ID is provided, restrict search to that folder
            if ($folderId) {
                $searchQuery .= " and '{$folderId}' in parents";
            }

            error_log("Built search query: " . $searchQuery);

            $optParams = [
                'pageSize' => 1000,
                'fields' => 'files(id, name, mimeType, thumbnailLink, parents, webViewLink)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => $this->isSharedDrive,
                'q' => $searchQuery
            ];

            if ($this->isSharedDrive) {
                $optParams['driveId'] = $this->driveId;
                $optParams['corpora'] = 'drive';
            }

            error_log("Search parameters: " . json_encode($optParams));

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

            error_log("Search found " . count($files) . " results");
            return $files;

        } catch (\Exception $e) {
            error_log("Error in searchFiles: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}