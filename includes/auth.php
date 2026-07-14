<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../db_connect.php';

if (!function_exists('app_get_current_user')) {
    function app_get_current_user() {
        static $currentUser = null;
        if ($currentUser !== null) {
            return $currentUser;
        }

        if (!isset($_SESSION['student_id'])) {
            return null;
        }

        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE student_id = ?');
        $stmt->execute([$_SESSION['student_id']]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($currentUser === false || !is_array($currentUser)) {
            $currentUser = null;
            unset($_SESSION['student_id']);
            unset($_SESSION['role']);
        }
        return $currentUser;
    }
}

if (!function_exists('current_role')) {
    function current_role() {
        $user = app_get_current_user();
        return is_array($user) && isset($user['role']) ? $user['role'] : null;
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        if (!isset($_SESSION['student_id'])) {
            header('Location: login.php');
            exit();
        }

        $user = app_get_current_user();
        if (!is_array($user)) {
            header('Location: login.php');
            exit();
        }
    }
}

if (!function_exists('require_role')) {
    function require_role($roles) {
        require_login();
        $role = current_role();
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($role, $allowed, true)) {
            header('Location: dashboard.php');
            exit();
        }
    }
}

function can_manage_events() {
    $role = current_role();
    return in_array($role, ['admin', 'organizer'], true);
}

function can_manage_users() {
    $role = current_role();
    return in_array($role, ['admin', 'organizer'], true);
}

function flash($key, $message = null) {
    if ($message === null) {
        if (isset($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $msg;
        }
        return null;
    }

    $_SESSION[$key] = $message;
}
