<?php
/**
 * Delasof2026 PHP - Main Entry Point
 * Handles routing and session management
 */

session_start();

// Define base paths
define('BASE_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', __DIR__);
define('SRC_PATH', BASE_PATH . '/src');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Include core libraries
require_once SRC_PATH . '/lib/config.php';
require_once SRC_PATH . '/lib/auth.php';
require_once SRC_PATH . '/lib/utils.php';
require_once SRC_PATH . '/lib/csv-parser.php';
require_once SRC_PATH . '/lib/url-builder.php';

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', STORAGE_PATH . '/logs/error.log');

// Parse request URI
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$path = substr($request_uri, strlen($base_url));
$path = '/' . ltrim($path, '/');

// Handle API requests
if (strpos($path, '/api/') === 0) {
    header('Content-Type: application/json');
    $api_path = substr($path, 5); // Remove '/api/'
    
    // Route API requests
    if (strpos($api_path, 'auth/login') === 0) {
        require_once SRC_PATH . '/api/auth.php';
        handleLogin();
    } elseif (strpos($api_path, 'auth/logout') === 0) {
        require_once SRC_PATH . '/api/auth.php';
        handleLogout();
    } elseif (strpos($api_path, 'config') === 0) {
        require_once SRC_PATH . '/api/config.php';
        handleConfig();
    } elseif (strpos($api_path, 'products') === 0) {
        require_once SRC_PATH . '/api/products.php';
        handleProducts();
    } elseif (strpos($api_path, 'csv') === 0) {
        require_once SRC_PATH . '/api/csv.php';
        handleCsv();
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'API endpoint not found']);
    }
    exit;
}

// Handle sitemap.xml
if ($path === '/sitemap.xml') {
    header('Content-Type: application/xml');
    require_once SRC_PATH . '/api/sitemap.php';
    generateSitemap();
    exit;
}

// Handle page requests
$page = 'home';
$params = [];

// Parse path and extract page/params
if ($path !== '/') {
    $parts = array_filter(explode('/', $path));
    $page = array_shift($parts) ?: 'home';
    $params = $parts;
}

// Render page
$page_file = SRC_PATH . '/pages/' . $page . '.php';

if (file_exists($page_file)) {
    require_once $page_file;
} else {
    http_response_code(404);
    require_once SRC_PATH . '/pages/404.php';
}
?>
