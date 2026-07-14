<?php
require_once 'db_connect.php';

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS events");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    $pdo->exec("CREATE TABLE users (
        student_id VARCHAR(20) PRIMARY KEY NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        department VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('student','organizer','admin') NOT NULL DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100) NOT NULL,
        event_date DATE NOT NULL,
        event_time TIME NOT NULL,
        location VARCHAR(255) NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        detail_page VARCHAR(255) DEFAULT NULL,
        created_by VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(student_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $adminId = 'admin';
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE student_id = ?');
    $stmt->execute([$adminId]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        $password = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (student_id, first_name, last_name, email, department, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$adminId, 'Site', 'Admin', 'admin@example.com', 'Administration', $password, 'admin']);
        echo "Admin account created. Login with student_id=admin and password=Admin@123.<br>";
    } else {
        echo "Admin account already exists.<br>";
    }

    echo "Setup complete.";
} catch (PDOException $e) {
    echo 'Setup failed: ' . htmlspecialchars($e->getMessage());
}
