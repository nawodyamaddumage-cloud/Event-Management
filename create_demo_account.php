<?php
require_once 'db_connect.php';

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id  = trim($_POST['student_id'] ?? 'admin');
    $first_name  = trim($_POST['first_name'] ?? 'Admin');
    $last_name   = trim($_POST['last_name'] ?? 'User');
    $email       = trim($_POST['email'] ?? 'admin@sliate.ac.lk');
    $department  = 'Administration';
    $password    = $_POST['password'] ?? '';
    $role        = $_POST['role'] ?? 'admin';

    if (empty($password)) {
        $message = 'Password cannot be empty.';
        $type = 'error';
    } elseif (!in_array($role, ['admin', 'organizer', 'student'], true)) {
        $message = 'Invalid role selected.';
        $type = 'error';
    } else {
        try {
            // Check if already exists
            $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE student_id = ? OR email = ?');
            $check->execute([$student_id, $email]);
            if ($check->fetchColumn() > 0) {
                // Update existing user's role and password
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ?, role = ?, first_name = ?, last_name = ? WHERE student_id = ? OR email = ?');
                $stmt->execute([$hashed, $role, $first_name, $last_name, $student_id, $email]);
                $message = "✅ Existing account updated! Role set to <strong>$role</strong>. You can now log in.";
                $type = 'success';
            } else {
                // Create new
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (student_id, first_name, last_name, email, department, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$student_id, $first_name, $last_name, $email, $department, $hashed, $role]);
                $message = "✅ Account created! Login with Student ID: <strong>$student_id</strong> and the password you set.";
                $type = 'success';
            }
        } catch (PDOException $e) {
            $message = '❌ Database error: ' . htmlspecialchars($e->getMessage());
            $type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Demo Account — SLIATE</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #1e0836;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f0e8ff;
        }
        .card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            padding: 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .warn-banner {
            background: rgba(255, 160, 0, 0.15);
            border: 1px solid rgba(255,160,0,0.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            color: #ffcc80;
            margin-bottom: 28px;
            text-align: center;
        }
        h1 { font-size: 1.8rem; margin-bottom: 6px; }
        .sub { color: #c7b4e1; font-size: 0.95rem; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.9rem; margin-bottom: 6px; color: #d8c0ff; }
        input, select {
            width: 100%;
            padding: 11px 14px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: #f0e8ff;
            font-size: 0.95rem;
        }
        input:focus, select:focus { outline: none; border-color: #ff9900; }
        select option { background: #2a0f4e; }
        .btn {
            width: 100%;
            padding: 13px;
            background: #ff9900;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s;
        }
        .btn:hover { background: #e08800; }
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.95rem;
        }
        .message.success { background: rgba(19,74,23,0.4); border: 1px solid rgba(91,255,128,0.2); color: #a8ffb0; }
        .message.error   { background: rgba(125,0,0,0.35); border: 1px solid rgba(255,107,107,0.25); color: #ffaaaa; }
        .login-link { text-align: center; margin-top: 20px; font-size: 0.9rem; color: #c7b4e1; }
        .login-link a { color: #ff9900; text-decoration: none; font-weight: bold; }
        .divider { border: none; border-top: 1px solid rgba(255,255,255,0.08); margin: 24px 0; }
    </style>
</head>
<body>
<div class="card">
    <div class="warn-banner">⚠️ Demo only — Delete this file before going live</div>
    <h1>Create Account</h1>
    <p class="sub">Set up a demo admin, organizer or student account.</p>

    <?php if ($message): ?>
        <div class="message <?php echo $type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="admin"     <?php echo (($_POST['role'] ?? 'admin') === 'admin'     ? 'selected' : ''); ?>>Admin</option>
                <option value="organizer" <?php echo (($_POST['role'] ?? '') === 'organizer' ? 'selected' : ''); ?>>Organizer</option>
                <option value="student"   <?php echo (($_POST['role'] ?? '') === 'student'   ? 'selected' : ''); ?>>Student</option>
            </select>
        </div>
        <div class="form-group">
            <label for="student_id">Student / User ID</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($_POST['student_id'] ?? 'admin'); ?>" required>
        </div>
        <div style="display:flex; gap:12px;">
            <div class="form-group" style="flex:1">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? 'Admin'); ?>" required>
            </div>
            <div class="form-group" style="flex:1">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? 'User'); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? 'admin@sliate.ac.lk'); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Set a strong password" required>
        </div>
        <button type="submit" class="btn">Create / Update Account</button>
    </form>

    <hr class="divider">
    <div class="login-link">
        Done? <a href="login.php">Go to Login →</a>
    </div>
</div>
</body>
</html>
