<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Composer dependencies not installed. Please run: composer install');
}

require_once __DIR__ . '/vendor/autoload.php';

try {
    // Load environment variables
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Basic routing
    $request = $_SERVER['REQUEST_URI'];
    $path = parse_url($request, PHP_URL_PATH);
    $query = parse_url($request, PHP_URL_QUERY);

    // Parse query parameters
    parse_str($query ?? '', $queryParams);
    $_GET = array_merge($_GET, $queryParams);

    error_log("Request URI: " . $request);
    error_log("Path: " . $path);
    error_log("Query: " . $query);
    error_log("GET params: " . print_r($_GET, true));

    switch ($path) {
        case '/':
            require __DIR__ . '/src/views/browser.php';
            break;
        case '/list':
            require __DIR__ . '/src/controllers/ListController.php';
            break;
        default:
            http_response_code(404);
            echo "404 Not Found: " . htmlspecialchars($path);
            break;
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo "Internal Server Error: " . htmlspecialchars($e->getMessage());
}