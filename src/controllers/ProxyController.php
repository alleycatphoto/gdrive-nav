<?php
namespace App\Controllers;
ini_set('memory_limit', '1024M');
use App\Services\DriveService;

class ProxyController {
    private $driveService;

    public function __construct() {
        $this->driveService = new DriveService();
    }

    public function streamFile($fileId) {
        try {
            // Get file metadata from Drive
            $service = $this->driveService->getService();
            $file = $service->files->get($fileId, [
                'supportsAllDrives' => true,
                'fields' => 'id, name, mimeType, size'
            ]);

            // Get the file content
            $response = $service->files->get($fileId, [
                'alt' => 'media',
                'supportsAllDrives' => true
            ]);

            // Set appropriate headers
            header('Content-Type: ' . $file->getMimeType());
            header('Content-Disposition: inline; filename="' . $file->getName() . '"');
            header('Cache-Control: public, max-age=3600');

            // Stream the file content
            $stream = $response->getBody()->getContents();
            echo $stream;

        } catch (\Exception $e) {
            error_log("Error in ProxyController: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}