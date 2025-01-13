<?php
// Start session before any output
session_start();

// Enable error reporting only in non-production
if (!filter_var($_ENV['PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

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
    $queryParams = [];
    if ($query) {
        parse_str($query, $queryParams);
    }
    $_GET = array_merge($_GET, $queryParams);

    // Only log in non-production
    if (!filter_var($_ENV['PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN)) {
        error_log("Request URI: " . $request);
        error_log("Path: " . $path);
        error_log("Query: " . $query);
        error_log("GET params: " . print_r($_GET, true));
    }

    // Extract file ID from proxy route
    if (preg_match('/^\/proxy\/([^\/]+)/', $path, $matches)) {
        require_once __DIR__ . '/src/controllers/ProxyController.php';
        $fileId = $matches[1];
        $controller = new \App\Controllers\ProxyController();
        $controller->streamFile($fileId);
        exit;
    }

    switch ($path) {
        case '/':
            require __DIR__ . '/src/views/browser.php';
            break;
        case '/list':
            require __DIR__ . '/src/controllers/ListController.php';
            break;
        case '/auth/login':
            $controller = new \App\Controllers\AuthController();
            $controller->login();
            break;
        case '/auth/logout':
            $controller = new \App\Controllers\AuthController();
            $controller->logout();
            break;
        case '/auth/current-user':
            $controller = new \App\Controllers\AuthController();
            $controller->getCurrentUser();
            break;
        default:
            http_response_code(404);
            echo "404 Not Found: " . htmlspecialchars($path);
            break;
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    if (!filter_var($_ENV['PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN)) {
        echo "Internal Server Error: " . htmlspecialchars($e->getMessage());
    } else {
        echo "Internal Server Error";
    }
}