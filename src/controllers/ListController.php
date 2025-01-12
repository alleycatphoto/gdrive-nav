<?php
use App\Services\DriveService;

header('Content-Type: application/json');

try {
    $driveService = new DriveService();
    $folderId = $_GET['folder_id'] ?? $_ENV['GOOGLE_DRIVE_FOLDER_ID'];

    // Debug information
    error_log("Listing files for folder: " . $folderId);
    error_log("Shared Drive ID: " . $_ENV['GOOGLE_DRIVE_ROOT_FOLDER']);

    $files = $driveService->listFiles($folderId);

    echo json_encode([
        'success' => true,
        'files' => $files,
        'debug' => [
            'folder_id' => $folderId,
            'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'],
            'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER']
        ]
    ]);
} catch (\Exception $e) {
    error_log("Error in ListController: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'folder_id' => $_GET['folder_id'] ?? $_ENV['GOOGLE_DRIVE_FOLDER_ID'],
            'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false,
            'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER']
        ]
    ]);
}