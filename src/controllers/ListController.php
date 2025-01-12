<?php
use App\Services\DriveService;

header('Content-Type: application/json');

try {
    $driveService = new DriveService();
    $folderId = $_GET['folder_id'] ?? $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'];
    $files = $driveService->listFiles($folderId);
    
    echo json_encode([
        'success' => true,
        'files' => $files
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
