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
$isStudent = ($role === 'student');

$eventCount = 0;
$events = [];
$myEvents = [];
$myEventCount = 0;

if ($isStudent) {
    // Fetch student's saved events
    try {
        $stmt = $pdo->prepare(
            'SELECT e.* FROM events e
             INNER JOIN student_events se ON e.id = se.event_id
             WHERE se.student_id = ?
             ORDER BY e.event_date ASC, e.event_time ASC'
        );
        $stmt->execute([$_SESSION['student_id']]);
        $myEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $myEventCount = count($myEvents);
    } catch (PDOException $e) {
        $myEvents = [];
    }
} else {
    // Fetch event summary for admin/organizer
    try {
        $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_time ASC');
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $eventCount = count($events);
    } catch (PDOException $e) {
        $events = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SLIATE Event Management</title>
    <link rel="stylesheet" href="CSS/global.css">
    <style>
        .my-events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }
        .my-event-card {
            background: var(--color-surface-strong);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .my-event-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
        }
        .my-event-card .meta {
            font-size: 0.9rem;
            color: var(--color-text-muted);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .my-event-card .actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
            padding-top: 10px;
        }
        .remove-btn {
            background: rgba(200, 30, 30, 0.18);
            color: #ff7070;
            border: 1px solid rgba(255, 100, 100, 0.25);
            border-radius: var(--radius-pill);
            padding: 7px 16px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .remove-btn:hover {
            background: rgba(200, 30, 30, 0.35);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-text-muted);
        }
        .empty-state p {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
    </style>
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

            <?php if (!$isStudent): ?>
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
            <?php endif; ?>
        </div>

        <?php if ($isStudent): ?>
        <section class="card" style="margin-bottom: 40px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <h2>My Events <span style="font-size: 1rem; font-weight: 400; color: var(--color-text-muted);">(<?php echo $myEventCount; ?>)</span></h2>
                <a href="event.php" class="btn btn-primary" style="font-size: 0.9rem;">Browse &amp; Add Events</a>
            </div>

            <?php if (empty($myEvents)): ?>
                <div class="empty-state">
                    <p>You haven't saved any events yet.</p>
                    <a href="event.php" class="btn btn-primary">Browse Events</a>
                </div>
            <?php else: ?>
                <div class="my-events-grid">
                    <?php foreach ($myEvents as $ev): ?>
                        <div class="my-event-card" id="my-event-card-<?php echo $ev['id']; ?>">
                            <div class="event-category" style="font-size: 0.8rem;"><?php echo htmlspecialchars($ev['category']); ?></div>
                            <h3><?php echo htmlspecialchars($ev['title']); ?></h3>
                            <div class="meta">
                                <span>📅 <?php echo htmlspecialchars($ev['event_date']); ?></span>
                                <span>⏰ <?php echo htmlspecialchars($ev['event_time']); ?></span>
                                <span>📍 <?php echo htmlspecialchars($ev['location']); ?></span>
                            </div>
                            <div class="actions">
                                <a href="event_details.php?id=<?php echo $ev['id']; ?>" class="btn btn-secondary" style="font-size: 0.85rem; padding: 7px 14px;">View Details</a>
                                <button class="remove-btn" onclick="removeEvent(<?php echo $ev['id']; ?>, this)">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <section class="card">
            <h2>My Account</h2>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($user['student_id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($role)); ?></p>
        </section>
    </main>

    <?php if ($isStudent): ?>
    <script>
        function removeEvent(eventId, btn) {
            if (!confirm('Remove this event from your list?')) return;
            btn.disabled = true;
            btn.textContent = 'Removing…';

            fetch('my_events_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=remove&event_id=' + eventId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById('my-event-card-' + eventId);
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Update the badge in header
                        updateHeaderBadge(data.count);
                        // Update the section heading count
                        const heading = document.querySelector('section.card h2');
                        if (heading) heading.innerHTML = 'My Events <span style="font-size: 1rem; font-weight: 400; color: var(--color-text-muted);">(' + data.count + ')</span>';
                        // Show empty state if no more events
                        if (data.count === 0) {
                            const grid = document.querySelector('.my-events-grid');
                            if (grid) grid.outerHTML = `<div class="empty-state"><p>You haven't saved any events yet.</p><a href="event.php" class="btn btn-primary">Browse Events</a></div>`;
                        }
                    }, 300);
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Remove';
            });
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
