<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($eventId <= 0) {
    header('Location: event.php');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $event = null;
}

if (!$event) {
    header('Location: event.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - SLIATE Event Management</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <div class="card" style="padding: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 24px;">
                <div style="flex: 1; min-width: 300px;">
                    <div class="event-category" style="margin-bottom: 14px; display: inline-block; padding: 6px 12px; background-color: #4a238f; border-radius: 8px; color: white; font-weight: bold;">
                        <?php echo htmlspecialchars($event['category']); ?>
                    </div>
                    <h1 style="font-size: 3rem; margin-bottom: 24px;"><?php echo htmlspecialchars($event['title']); ?></h1>
                    
                    <div class="event-info" style="display: flex; flex-direction: column; gap: 12px; color:#cfc0f2; margin-bottom: 32px; font-size: 1.1rem;">
                        <div><strong>📅 Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></div>
                        <div><strong>⏰ Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></div>
                        <div><strong>📍 Location:</strong> <?php echo htmlspecialchars($event['location']); ?></div>
                        <div><strong>👤 Created by:</strong> <?php echo htmlspecialchars($event['created_by']); ?></div>
                    </div>
                </div>

                <?php if (!empty($event['image'])): ?>
                <div style="flex: 1; min-width: 300px; max-width: 500px;">
                    <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style="width: 100%; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                </div>
                <?php endif; ?>
            </div>

            <div style="margin-top: 32px; padding-top: 32px; border-top: 1px solid rgba(255,255,255,0.1);">
                <h2 style="font-size: 1.8rem; margin-bottom: 16px;">About this Event</h2>
                <p style="color: #d3c2ef; font-size: 1.1rem; line-height: 1.8; white-space: pre-line;">
                    <?php echo htmlspecialchars($event['description']); ?>
                </p>
            </div>

            <div style="margin-top: 40px;">
                <a href="event.php" class="btn btn-secondary">Back to Events</a>
            </div>
        </div>
    </main>
</body>
</html>
