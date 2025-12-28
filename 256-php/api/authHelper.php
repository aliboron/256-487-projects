<?php
require_once __DIR__ . '/env.php';

function ensureSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function checkSessionTimeout()
{
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];

        if ($elapsed > $_ENV["SESSION_TIMEOUT"]) {
            logout();
            return false;
        }
    }

    $_SESSION['last_activity'] = time();
    return true;
}

function isLoggedIn()
{
    ensureSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireAuth()
{
    if (!isLoggedIn()) {
        header('Location: ' . $_ENV['CLIENT_URL'] . '/login.php');
        exit();
    }
}

function requireRole($user_role, $required_role)
{
    if ($user_role !== $required_role) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden');
        exit();
    }
}

function login($userId, $userData = [])
{
    ensureSession();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_data'] = $userData;
    $_SESSION['last_activity'] = time();
}

function logout()
{
    ensureSession();
    session_unset();
    session_destroy();
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class Authorize
{
    public function __construct(
        public string $role
    ) {}
}
