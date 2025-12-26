<?php
require_once __DIR__ . '/env.php';

session_start();

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
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireAuth()
{
    if (!isLoggedIn()) {
        header('Location: ' . $_ENV['CLIENT_URL'] . '/login.php');
        exit();
    }
}

function login($userId, $userData = [])
{
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_data'] = $userData;
    $_SESSION['last_activity'] = time();
}

function logout()
{
    session_unset();
    session_destroy();
}
