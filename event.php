<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';
$events = [];
try {
    $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_time ASC');
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - SLIATE Event Management</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <section style="margin-bottom: 40px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 16px;">All Events</h1>
            <p style="color: #cfc0f2;">Browse all active event listings and learn more about each activity happening at SLIATE.</p>
        </section>

        <?php if (empty($events)): ?>
            <p style="text-align:center; color:#d3c2ef;">No events have been created yet. Please check again later.</p>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <article class="card event-card" style="padding: 24px;">
                        <div class="event-category" style="margin-bottom: 14px;"><?php echo htmlspecialchars($event['category']); ?></div>
                        <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                        <p style="margin-bottom: 16px; color: #d3c2ef;"><?php echo htmlspecialchars(substr($event['description'], 0, 160)); ?><?php echo strlen($event['description']) > 160 ? '...' : ''; ?></p>
                        <div class="event-info" style="display: flex; gap: 16px; flex-wrap: wrap; color:#b9a4df; margin-bottom: 18px;">
                            <div><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></div>
                            <div><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></div>
                            <div><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></div>
                        </div>
                        <div style="display:flex; gap: 10px; flex-wrap: wrap;">
                            <a class="btn btn-secondary" href="event_details.php?id=<?php echo htmlspecialchars($event['id']); ?>">View Details</a>
                            <span class="badge">Created by <?php echo htmlspecialchars($event['created_by']); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
