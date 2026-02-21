<?php
/**
 * Authentication Library
 * Handles admin login and session management
 */

class Auth {
    const ADMIN_USERNAME = 'admin';
    const ADMIN_PASSWORD = 'sofaraway';
    const SESSION_KEY = 'aff-shop-admin-auth';

    public static function verifyCredentials($username, $password) {
        return $username === self::ADMIN_USERNAME && $password === self::ADMIN_PASSWORD;
    }

    public static function login() {
        $_SESSION[self::SESSION_KEY] = 'authenticated';
        $_SESSION['admin_login_time'] = time();
        return true;
    }

    public static function logout() {
        unset($_SESSION[self::SESSION_KEY]);
        unset($_SESSION['admin_login_time']);
        return true;
    }

    public static function isLoggedIn() {
        return isset($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] === 'authenticated';
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
    }

    public static function getUsername() {
        return self::ADMIN_USERNAME;
    }
}
?>
