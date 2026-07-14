<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /sliate_project/login.php');
    exit();
}

$student_id = trim($_POST['student_id'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($student_id === '' || $password === '') {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: /sliate_project/login.php');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT student_id, password, role FROM users WHERE student_id = ?');
    $stmt->execute([$student_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Invalid student ID or password.';
        header('Location: /sliate_project/login.php');
        exit();
    }

    session_regenerate_id(true);
    $_SESSION['student_id'] = $user['student_id'];
    $_SESSION['role'] = $user['role'];

    if (in_array($user['role'], ['admin', 'organizer'], true)) {
        header('Location: /sliate_project/manage_events.php');
    } else {
        header('Location: /sliate_project/dashboard.php');
    }
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Login failed. Please try again later.';
    header('Location: /sliate_project/login.php');
    exit();
}
