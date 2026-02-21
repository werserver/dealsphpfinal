<?php
/**
 * Authentication API Endpoints
 */

function handleLogin() {
    if (!isPost()) {
        return sendError('Method not allowed', 405);
    }

    $input = getJsonInput();
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (Auth::verifyCredentials($username, $password)) {
        Auth::login();
        sendSuccess(['username' => Auth::getUsername()], 'Login successful');
    } else {
        sendError('Invalid credentials', 401);
    }
}

function handleLogout() {
    Auth::logout();
    sendSuccess(null, 'Logged out successfully');
}
?>
