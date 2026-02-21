<?php
/**
 * Utility Functions
 */

function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sendError($message, $statusCode = 400) {
    sendJson(['success' => false, 'error' => $message], $statusCode);
}

function sendSuccess($data = null, $message = 'Success') {
    sendJson(['success' => true, 'data' => $data, 'message' => $message]);
}

function slugify($text) {
    // Convert to lowercase
    $text = strtolower($text);
    // Replace spaces with hyphens
    $text = preg_replace('/\s+/', '-', $text);
    // Remove special characters
    $text = preg_replace('/[^a-z0-9-]/', '', $text);
    // Remove multiple hyphens
    $text = preg_replace('/-+/', '-', $text);
    // Trim hyphens
    $text = trim($text, '-');
    return $text;
}

function getProductSlug($productName, $productId) {
    return slugify($productName) . '-' . $productId;
}

function parseProductSlug($slug) {
    $parts = explode('-', $slug);
    $productId = array_pop($parts);
    $productName = implode('-', $parts);
    return ['name' => $productName, 'id' => $productId];
}

function formatPrice($price, $currency = 'THB') {
    if ($currency === 'THB') {
        return '฿' . number_format($price, 0);
    }
    return number_format($price, 2);
}

function getImageUrl($imageUrl) {
    if (empty($imageUrl)) {
        return '/assets/images/placeholder.svg';
    }
    return $imageUrl;
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function getQueryParam($key, $default = null) {
    return $_GET[$key] ?? $default;
}

function getPostParam($key, $default = null) {
    return $_POST[$key] ?? $default;
}

function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $base = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $base;
}

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

function isPost() {
    return getRequestMethod() === 'POST';
}

function isGet() {
    return getRequestMethod() === 'GET';
}

function isDelete() {
    return getRequestMethod() === 'DELETE';
}

function isPut() {
    return getRequestMethod() === 'PUT';
}
?>
