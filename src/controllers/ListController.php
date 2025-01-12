<?php
namespace App\Controllers;

use App\Services\DriveService;

header('Content-Type: application/json');

try {
    $driveService = new DriveService();
    $folderId = isset($_GET['folder']) ? $_GET['folder'] : null;

    error_log("ListController: Processing request");
    error_log("Folder ID: " . ($folderId ?? 'null'));
    error_log("Is Shared Drive: " . $_ENV['GOOGLE_DRIVE_IS_SHARED']);
    error_log("Drive ID: " . $_ENV['GOOGLE_DRIVE_ROOT_FOLDER']);

    // List files in the requested folder
    $files = $driveService->listFiles($folderId);
    $breadcrumbs = $driveService->getBreadcrumbs($folderId);

    echo json_encode([
        'success' => true,
        'files' => $files,
        'breadcrumbs' => $breadcrumbs,
        'debug' => [
            'request_folder_id' => $folderId,
            'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'],
            'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'],
            'files_count' => count($files),
            'timestamp' => date('c')
        ]
    ]);
} catch (\Exception $e) {
    error_log("Error in ListController: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    $errorResponse = [
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'request_folder_id' => $_GET['folder'] ?? null,
            'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false,
            'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'],
            'timestamp' => date('c'),
            'error_type' => get_class($e)
        ]
    ];

    http_response_code(500);
    echo json_encode($errorResponse);
}