<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/auth.php';
require_login();
require_once 'db_connect.php';

$user = app_get_current_user();
if (!is_array($user)) {
    header('Location: login.php');
    exit();
}
$role = current_role();
$eventCount = 0;
$events = [];
try {
    $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_time ASC');
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $eventCount = count($events);
} catch (PDOException $e) {
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SLIATE Event Management</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <section style="margin-bottom: 32px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 8px;">Welcome back, <?php echo htmlspecialchars($user['first_name'] ?? 'Student'); ?>.</h1>
            <p style="color: #d3c2ef;">Your role is <strong><?php echo htmlspecialchars(ucfirst($role)); ?></strong>. Use the controls below to manage your event activity.</p>
        </section>

        <div class="grid grid-2" style="gap: 24px; margin-bottom: 40px;">
            <section class="card">
                <h2>Quick Actions</h2>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a class="btn btn-primary" href="event.php">Browse Events</a>
                    <?php if (can_manage_events()): ?>
                        <a class="btn btn-secondary" href="manage_events.php">Create or Edit Events</a>
                    <?php endif; ?>
                    <?php if (can_manage_users()): ?>
                        <a class="btn btn-secondary" href="manage_users.php">Manage User Accounts</a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <h2>Event Summary</h2>
                <p style="color: #d3c2ef; margin-bottom: 18px;">There are currently <strong><?php echo htmlspecialchars($eventCount); ?></strong> event<?php echo $eventCount === 1 ? '' : 's'; ?> listed in the portal.</p>
                <?php if (empty($events)): ?>
                    <p style="color:#d3c2ef;">No events are available yet. Use the Create Event page to add new listings.</p>
                <?php else: ?>
                    <ul style="list-style:none; margin:0; padding:0; display:grid; gap: 14px;">
                        <?php foreach (array_slice($events, 0, 5) as $event): ?>
                            <li style="background: rgba(255,255,255,0.04); border-radius: 14px; padding: 16px;">
                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                <div style="color:#cfc0f2; font-size:0.95rem; margin-top:4px;"><?php echo htmlspecialchars($event['event_date']); ?> at <?php echo htmlspecialchars($event['event_time']); ?> — <?php echo htmlspecialchars($event['category']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>

        <section class="card">
            <h2>My Account</h2>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($user['student_id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($role)); ?></p>
        </section>
    </main>
</body>
</html>
