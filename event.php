<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';
require_once 'includes/auth.php';

$categoryFilter = $_GET['category'] ?? '';
$allowedCategories = ['Workshops', 'Cultural', 'Sports', 'Tech Talks'];

// Check if logged-in user is a student
$isStudent = false;
$savedEventIds = [];
if (isset($_SESSION['student_id'])) {
    $isStudent = (current_role() === 'student');
    if ($isStudent) {
        try {
            $stmt = $pdo->prepare('SELECT event_id FROM student_events WHERE student_id = ?');
            $stmt->execute([$_SESSION['student_id']]);
            $savedEventIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            $savedEventIds = [];
        }
    }
}

$events = [];
try {
    if (in_array($categoryFilter, $allowedCategories)) {
        $stmt = $pdo->prepare('SELECT * FROM events WHERE category = ? ORDER BY event_date ASC, event_time ASC');
        $stmt->execute([$categoryFilter]);
    } else {
        $categoryFilter = '';
        $placeholders = implode(',', array_fill(0, count($allowedCategories), '?'));
        $stmt = $pdo->prepare("SELECT * FROM events WHERE category IN ($placeholders) ORDER BY event_date ASC, event_time ASC");
        $stmt->execute($allowedCategories);
    }
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
    <style>
        .save-btn {
            border: none;
            border-radius: var(--radius-pill);
            padding: 7px 16px;
            font-size: 0.85rem;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }
        .save-btn.saved {
            background: rgba(200, 30, 30, 0.18);
            color: #ff7070;
            border: 1px solid rgba(255, 100, 100, 0.25);
        }
        .save-btn.unsaved {
            background: rgba(255, 153, 0, 0.15);
            color: var(--color-accent-text);
            border: 1px solid rgba(255, 153, 0, 0.3);
        }
        .save-btn:disabled {
            opacity: 0.6;
            cursor: default;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <section style="margin-bottom: 40px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 16px;">All Events</h1>
            <p style="color: #cfc0f2;">Browse all active event listings and learn more about each activity happening at SLIATE.</p>
        </section>

        <div class="category-tabs" style="display: flex; gap: 10px; margin-bottom: 32px; flex-wrap: wrap;">
            <a href="event.php" class="btn <?php echo empty($categoryFilter) ? 'btn-primary' : 'btn-secondary'; ?>">All Events</a>
            <?php foreach ($allowedCategories as $cat): ?>
                <a href="event.php?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $categoryFilter === $cat ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo htmlspecialchars($cat); ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($events)): ?>
            <p style="text-align:center; color:#d3c2ef;">No events found<?php echo $categoryFilter ? ' in this category' : ''; ?>. Please check again later.</p>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <?php $isSaved = in_array($event['id'], $savedEventIds); ?>
                    <article class="card event-card" style="padding: 24px;" id="event-card-<?php echo $event['id']; ?>">
                        <div class="event-category" style="margin-bottom: 14px;"><?php echo htmlspecialchars($event['category']); ?></div>
                        <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                        <p style="margin-bottom: 16px; color: #d3c2ef;"><?php echo htmlspecialchars(substr($event['description'], 0, 160)); ?><?php echo strlen($event['description']) > 160 ? '...' : ''; ?></p>
                        <div class="event-info" style="display: flex; gap: 16px; flex-wrap: wrap; color:#b9a4df; margin-bottom: 18px;">
                            <div><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></div>
                            <div><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></div>
                            <div><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></div>
                        </div>
                        <div style="display:flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                            <a class="btn btn-secondary" href="event_details.php?id=<?php echo htmlspecialchars($event['id']); ?>">View Details</a>
                            <span class="badge">Created by <?php echo htmlspecialchars($event['created_by']); ?></span>
                            <?php if ($isStudent): ?>
                                <button
                                    class="save-btn <?php echo $isSaved ? 'saved' : 'unsaved'; ?>"
                                    data-event-id="<?php echo $event['id']; ?>"
                                    data-saved="<?php echo $isSaved ? '1' : '0'; ?>"
                                    onclick="toggleSave(this)"
                                >
                                    <?php echo $isSaved ? '✕ Remove from My Events' : '+ Add to My Events'; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php if ($isStudent): ?>
    <script>
        function toggleSave(btn) {
            const eventId = btn.dataset.eventId;
            const isSaved = btn.dataset.saved === '1';
            const action = isSaved ? 'remove' : 'add';

            btn.disabled = true;

            fetch('my_events_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=' + action + '&event_id=' + eventId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const nowSaved = action === 'add';
                    btn.dataset.saved = nowSaved ? '1' : '0';
                    btn.className = 'save-btn ' + (nowSaved ? 'saved' : 'unsaved');
                    btn.textContent = nowSaved ? '✕ Remove from My Events' : '+ Add to My Events';
                    updateHeaderBadge(data.count);
                }
                btn.disabled = false;
            })
            .catch(() => { btn.disabled = false; });
        }

        function updateHeaderBadge(count) {
            const badge = document.getElementById('my-events-badge');
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        }
    </script>
    <?php endif; ?>
</body>
</html>
