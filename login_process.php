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
    $student_id = trim($input['student_id'] ?? '');
    $password = trim($input['password'] ?? '');

    if (!empty($student_id) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT student_id, password FROM users WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['student_id'] = $user['student_id'];

                if (is_json_request()) {
                    send_json([
                        'success' => true,
                        'message' => 'Login successful',
                        'student_id' => $user['student_id']
                    ], 200);
                }

                header("Location: dashboard.php");
                exit();
            }

            if (is_json_request()) {
                send_json([
                    'success' => false,
                    'message' => 'Invalid student ID or password.'
                ], 401);
            }

            $_SESSION['error'] = "Invalid student ID or password.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            if (is_json_request()) {
                send_json([
                    'success' => false,
                    'message' => 'Login failed',
                    'error' => $e->getMessage()
                ], 500);
            }

            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: login.php");
            exit();
        }
    }

    if (is_json_request()) {
        send_json([
            'success' => false,
            'message' => 'Please fill in all fields.'
        ], 400);
    }

    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: login.php");
    exit();
}

if (is_json_request()) {
    send_json(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
}

header("Location: login.php");
exit();
?>