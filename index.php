<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

$featuredEvents = [];
try {
    $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_time ASC LIMIT 6');
    $featuredEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featuredEvents = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLIATE Event Management</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <section class="hero" style="background-color: #2b0e5e; padding: 80px 20px; border-radius: 24px; margin-bottom: 40px; text-align: center; color: white;">
            <h1 style="font-size: 3rem; margin-bottom: 16px;">Welcome to SLIATE Event Management</h1>
            <p style="font-size: 1.1rem; max-width: 760px; margin: 0 auto 24px; color: #dcd2ff;">Browse campus events, manage event listings, and stay connected with the latest student activities.</p>
            <a class="btn btn-primary" href="event.php">Browse Events</a>
        </section>

        <section style="margin-bottom: 40px;">
            <div class="section-title">Featured Events</div>
            <?php if (empty($featuredEvents)): ?>
                <p style="text-align:center; margin-top: 24px; color: #d3c2ef;">No events are available right now. Check back later or login to create one.</p>
            <?php else: ?>
                <div class="events-grid" style="margin-top: 24px;">
                    <?php foreach ($featuredEvents as $event): ?>
                        <article class="card event-card" style="padding: 20px;">
                            <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                            <p style="margin-bottom: 12px; color: #d3c2ef;"><?php echo htmlspecialchars(substr($event['description'], 0, 140)); ?><?php echo strlen($event['description']) > 140 ? '...' : ''; ?></p>
                            <div class="event-info" style="display:flex; gap: 16px; flex-wrap: wrap; color:#b9a4df; margin-bottom: 16px;">
                                <div><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></div>
                                <div><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></div>
                                <div><strong>Category:</strong> <?php echo htmlspecialchars($event['category']); ?></div>
                            </div>
                            <div style="display:flex; gap: 12px; flex-wrap: wrap;">
                                <?php if (!empty($event['detail_page'])): ?>
                                    <a class="btn btn-secondary" href="<?php echo htmlspecialchars($event['detail_page']); ?>">View Details</a>
                                <?php endif; ?>
                                <a class="btn btn-primary" href="event.php">Browse All Events</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="about" style="background: rgba(255,255,255,0.04); padding: 40px; border-radius: 24px;">
            <h2 class="section-title">Why Use This Portal?</h2>
            <p style="max-width: 900px; margin: 0 auto; color: #d3c2ef; line-height: 1.8;">SLIATE Event Management makes it easy for students, organizers, and administrators to share important events, manage participation, and keep campus activity information centralized. Create events, update details, and remove old listings in one place.</p>
        </section>
    </main>
</body>
</html>
