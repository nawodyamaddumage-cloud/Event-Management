<?php
require_once 'db_connect.php';
try {
    $adminId = 'admin';
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE student_id = ?');
    $stmt->execute([$adminId]);
    $exists = $stmt->fetchColumn();
    if ($exists) {
        echo "Admin already exists.\n";
    } else {
        $password = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (student_id, first_name, last_name, email, department, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$adminId, 'Site', 'Admin', 'admin@example.com', 'Administration', $password, 'admin']);
        echo "Admin created. student_id=admin password=Admin@123\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
