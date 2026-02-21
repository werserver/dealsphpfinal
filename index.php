<?php
/**
 * Delasof2026 PHP - Main Entry Point
 * XAMPP Compatible Version
 * Standalone PHP application for affiliate shop management
 */

session_start();

// Define base paths
define('BASE_PATH', __DIR__);
define('SRC_PATH', BASE_PATH . '/src');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CSV_PATH', STORAGE_PATH . '/csv');
define('CONFIG_FILE', STORAGE_PATH . '/config.json');

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

// Initialize config
Config::init();

// Handle POST requests for CSV upload and admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePostRequest();
}

// Parse request URI - XAMPP Compatible
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$script_name = ($script_name === '\\') ? '/' : $script_name;

// Get the path relative to this script
if (strpos($request_uri, $script_name) === 0) {
    $path = substr($request_uri, strlen($script_name));
} else {
    $path = $request_uri;
}

$path = '/' . ltrim($path, '/');

// Handle page requests
$page = 'home';
$params = [];

// Parse path and extract page/params
if ($path !== '/' && $path !== '/index.php') {
    $parts = array_filter(explode('/', $path));
    $page = array_shift($parts) ?: 'home';
    $params = $parts;
}

// Handle special routes
if ($page === 'logout') {
    Auth::logout();
    header('Location: ' . getBaseUrl());
    exit;
}

// Render page
$page_file = SRC_PATH . '/pages/' . $page . '.php';

if (file_exists($page_file)) {
    require_once $page_file;
} else {
    http_response_code(404);
    require_once SRC_PATH . '/pages/404.php';
}

/**
 * Handle POST requests for CSV upload and admin actions
 */
function handlePostRequest() {
    $action = sanitizeInput($_POST['action'] ?? '');
    
    // Check admin authentication for sensitive operations
    if (in_array($action, ['upload_csv', 'delete_csv', 'save_config'])) {
        if (!Auth::isLoggedIn()) {
            $_SESSION['error'] = 'ต้องเข้าสู่ระบบก่อน';
            header('Location: ' . getBaseUrl() . '/admin');
            exit;
        }
    }
    
    switch ($action) {
        case 'login':
            handleLogin();
            break;
        
        case 'upload_csv':
            handleCsvUpload();
            break;
        
        case 'delete_csv':
            handleCsvDelete();
            break;
        
        case 'save_config':
            handleConfigSave();
            break;
        
        default:
            $_SESSION['error'] = 'ไม่รู้จักการดำเนินการนี้';
            header('Location: ' . getBaseUrl() . '/admin');
            break;
    }
}

/**
 * Handle admin login
 */
function handleLogin() {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    
    if (Auth::login($username, $password)) {
        $_SESSION['success'] = 'เข้าสู่ระบบสำเร็จ';
        header('Location: ' . getBaseUrl() . '/admin');
    } else {
        $_SESSION['error'] = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        header('Location: ' . getBaseUrl() . '/admin');
    }
    exit;
}

/**
 * Handle CSV file upload
 */
function handleCsvUpload() {
    if (!isset($_FILES['csv_file'])) {
        $_SESSION['error'] = 'ไม่มีไฟล์ที่เลือก';
        header('Location: ' . getBaseUrl() . '/admin');
        exit;
    }
    
    $file = $_FILES['csv_file'];
    $category = sanitizeInput($_POST['category'] ?? 'สินค้าแนะนำ');
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัพโหลด';
        header('Location: ' . getBaseUrl() . '/admin');
        exit;
    }
    
    // Check file type
    if ($file['type'] !== 'text/csv' && $file['type'] !== 'application/vnd.ms-excel') {
        $_SESSION['error'] = 'ต้องเป็นไฟล์ CSV เท่านั้น';
        header('Location: ' . getBaseUrl() . '/admin');
        exit;
    }
    
    // Check file size (max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        $_SESSION['error'] = 'ไฟล์ใหญ่เกินไป (สูงสุด 10MB)';
        header('Location: ' . getBaseUrl() . '/admin');
        exit;
    }
    
    // Create CSV directory if not exists
    if (!is_dir(CSV_PATH)) {
        mkdir(CSV_PATH, 0755, true);
    }
    
    // Save file
    $filename = $category . '.csv';
    $filepath = CSV_PATH . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $_SESSION['success'] = 'อัพโหลด CSV สำเร็จ';
    } else {
        $_SESSION['error'] = 'ไม่สามารถบันทึกไฟล์ได้';
    }
    
    header('Location: ' . getBaseUrl() . '/admin');
    exit;
}

/**
 * Handle CSV file deletion
 */
function handleCsvDelete() {
    $category = sanitizeInput($_POST['category'] ?? '');
    
    if (empty($category)) {
        $_SESSION['error'] = 'ไม่ได้ระบุหมวดหมู่';
        header('Location: ' . getBaseUrl() . '/admin');
        exit;
    }
    
    $filepath = CSV_PATH . '/' . $category . '.csv';
    
    if (file_exists($filepath) && unlink($filepath)) {
        $_SESSION['success'] = 'ลบไฟล์สำเร็จ';
    } else {
        $_SESSION['error'] = 'ไม่สามารถลบไฟล์ได้';
    }
    
    header('Location: ' . getBaseUrl() . '/admin');
    exit;
}

/**
 * Handle configuration save
 */
function handleConfigSave() {
    $config = Config::get();
    
    // Update configuration
    if (isset($_POST['site_name'])) {
        $config['siteName'] = sanitizeInput($_POST['site_name']);
    }
    
    if (isset($_POST['cloaking_token'])) {
        $config['cloakingToken'] = sanitizeInput($_POST['cloaking_token']);
    }
    
    if (isset($_POST['cloaking_base_url'])) {
        $config['cloakingBaseUrl'] = sanitizeInput($_POST['cloaking_base_url']);
    }
    
    if (isset($_POST['enable_flash_sale'])) {
        $config['enableFlashSale'] = true;
    } else {
        $config['enableFlashSale'] = false;
    }
    
    if (isset($_POST['enable_ai_reviews'])) {
        $config['enableAiReviews'] = true;
    } else {
        $config['enableAiReviews'] = false;
    }
    
    // Save configuration
    if (Config::save($config)) {
        $_SESSION['success'] = 'บันทึกการตั้งค่าสำเร็จ';
    } else {
        $_SESSION['error'] = 'ไม่สามารถบันทึกการตั้งค่าได้';
    }
    
    header('Location: ' . getBaseUrl() . '/admin');
    exit;
}
?>
