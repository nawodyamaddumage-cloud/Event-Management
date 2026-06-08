<?php
session_start();
require_once 'db_connect.php';

function is_json_request() {
    return !empty($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
}

function send_json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = is_json_request() ? json_decode(file_get_contents('php://input'), true) : $_POST;
    $first_name = trim($input['first_name'] ?? '');
    $last_name = trim($input['last_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $student_id = trim($input['student_id'] ?? '');
    $department = $input['department'] ?? '';
    $password = $input['password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';
    $terms = !empty($input['terms']);

    $errors = [];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($department) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (!$terms) {
        $errors[] = "You must agree to the Terms of Service.";
    }

    if (!empty($errors)) {
        if (is_json_request()) {
            send_json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        $_SESSION['errors'] = $errors;
        header("Location: register.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR student_id = ?");
        $stmt->execute([$email, $student_id]);

        if ($stmt->fetchColumn() > 0) {
            if (is_json_request()) {
                send_json([
                    'success' => false,
                    'message' => 'Email or student ID already exists.'
                ], 409);
            }

            $_SESSION['errors'] = ["Email or student ID already exists."];
            header("Location: register.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (student_id, first_name, last_name, email, department, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $first_name, $last_name, $email, $department, $hashed_password]);

        if (is_json_request()) {
            send_json([
                'success' => true,
                'message' => 'Registration successful. Please log in.'
            ], 201);
        }

        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        if (is_json_request()) {
            send_json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }

        $_SESSION['errors'] = ["Error: " . $e->getMessage()];
        header("Location: register.php");
        exit();
    }
}

if (is_json_request()) {
    send_json(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
}

header("Location: register.php");
exit();
?>