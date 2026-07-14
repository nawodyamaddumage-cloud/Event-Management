<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/auth.php';
require_login();

$student_id = $_SESSION['student_id'];
$action = trim($_POST['action'] ?? '');
$event_id = intval($_POST['event_id'] ?? 0);

if ($event_id <= 0 || !in_array($action, ['add', 'remove'], true)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

// Only students can use My Events
if (current_role() !== 'student') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Only students can save events.']);
    exit();
}

header('Content-Type: application/json');

try {
    if ($action === 'add') {
        $stmt = $pdo->prepare('INSERT IGNORE INTO student_events (student_id, event_id) VALUES (?, ?)');
        $stmt->execute([$student_id, $event_id]);
    } elseif ($action === 'remove') {
        $stmt = $pdo->prepare('DELETE FROM student_events WHERE student_id = ? AND event_id = ?');
        $stmt->execute([$student_id, $event_id]);
    }

    // Get updated count
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM student_events WHERE student_id = ?');
    $countStmt->execute([$student_id]);
    $count = (int) $countStmt->fetchColumn();

    echo json_encode(['success' => true, 'count' => $count, 'action' => $action]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
exit();
