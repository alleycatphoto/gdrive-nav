<?php
namespace App\Controllers;

use App\Services\DriveService;

header('Content-Type: application/json');

try {
    $driveService = new DriveService();

    // Get folder ID and search query from parameters
    $folderId = null;
    $searchQuery = null;

    if (isset($_GET['folder']) && !empty($_GET['folder'])) {
        $folderId = trim($_GET['folder']);
    }

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchQuery = trim($_GET['search']);
    }

    error_log("ListController: Processing request");
    error_log("Raw folder parameter: " . print_r($_GET, true));
    error_log("Processed Folder ID: " . ($folderId ?? 'null'));
    error_log("Search Query: " . ($searchQuery ?? 'null'));

    // List files in the requested folder or search results
    $files = $searchQuery ? $driveService->searchFiles($searchQuery, $folderId) : $driveService->listFiles($folderId);
    $breadcrumbs = $driveService->getBreadcrumbs($folderId);

    // Include search status in the response
    $searchStatus = null;
    if ($searchQuery) {
        $searchStatus = [
            'total_folders_searched' => $driveService->getSearchedFoldersCount(),
            'total_files_found' => count($files),
            'search_complete' => true
        ];
    }

    echo json_encode([
        'success' => true,
        'files' => $files,
        'breadcrumbs' => $breadcrumbs,
        'search_status' => $searchStatus,
        'debug' => [
            'request_folder_id' => $folderId,
            'search_query' => $searchQuery,
            'raw_get_params' => $_GET,
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
            'search_query' => $_GET['search'] ?? null,
            'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false,
            'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'],
            'timestamp' => date('c'),
            'error_type' => get_class($e)
        ]
    ];

    http_response_code(500);
    echo json_encode($errorResponse);
}