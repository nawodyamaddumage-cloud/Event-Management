<?php
session_start();
require_once 'includes/auth.php';
require_role(['admin', 'organizer']);

$errors = [];
$message = null;
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = trim($_POST['role'] ?? 'student');

    if (!$first_name || !$last_name || !$email || !$student_id || !$department || !$password || !$confirm_password) {
        $errors[] = 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($role === 'organizer' && current_role() !== 'admin') {
        $errors[] = 'Only administrators may create organizer accounts.';
    }

    if (!in_array($role, ['student', 'organizer', 'admin'], true)) {
        $role = 'student';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ? OR student_id = ?');
        $stmt->execute([$email, $student_id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'A user with this email or student ID already exists.';
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (student_id, first_name, last_name, email, department, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$student_id, $first_name, $last_name, $email, $department, $hashed_password, $role]);
        flash('success', 'User account created successfully.');
        header('Location: manage_users.php');
        exit();
    }
}

$success = flash('success');
$userQuery = $pdo->query('SELECT student_id, first_name, last_name, email, department, role, created_at FROM users ORDER BY created_at DESC');
$users = $userQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - SLIATE</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <div class="grid grid-2" style="gap: 32px;">
            <section class="card">
                <h2>Create New Account</h2>
                <?php if ($success): ?>
                    <div class="message success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="message error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="manage_users.php">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="" <?php echo (empty($_POST['department']) ? 'selected' : ''); ?>>Select Department</option>
                            <option value="hndit" <?php echo (($_POST['department'] ?? '') === 'hndit' ? 'selected' : ''); ?>>Higher National Diploma in Information Technology</option>
                            <option value="hnda" <?php echo (($_POST['department'] ?? '') === 'hnda' ? 'selected' : ''); ?>>Higher National Diploma in Accountancy</option>
                            <option value="hnde" <?php echo (($_POST['department'] ?? '') === 'hnde' ? 'selected' : ''); ?>>Higher National Diploma in English</option>
                        </select>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="student" <?php echo (($_POST['role'] ?? '') === 'student') ? 'selected' : ''; ?>>Student</option>
                            <?php if (current_role() === 'admin'): ?>
                                <option value="organizer" <?php echo (($_POST['role'] ?? '') === 'organizer') ? 'selected' : ''; ?>>Organizer</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Create Account</button>
                </form>
            </section>

            <section class="card">
                <h2>Existing Accounts</h2>
                <?php if (empty($users)): ?>
                    <p>No user accounts found.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['department']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
