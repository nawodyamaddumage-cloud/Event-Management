<?php
session_start();
require_once 'includes/auth.php';
require_role(['admin', 'organizer']);

$errors = [];
$message = null;
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $event_time = trim($_POST['event_time'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $image = trim($_POST['image'] ?? '');


    if ($action === 'create' || $action === 'update') {
        if (!$title || !$description || !$category || !$event_date || !$event_time || !$location) {
            $errors[] = 'All event fields except detail page are required.';
        }

        if (empty($errors)) {
            if ($action === 'create') {
                $stmt = $pdo->prepare('INSERT INTO events (title, description, category, event_date, event_time, location, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$title, $description, $category, $event_date, $event_time, $location, $image, $_SESSION['student_id']]);
                flash('success', 'Event created successfully.');
            } elseif ($action === 'update') {
                $eventId = intval($_POST['event_id'] ?? 0);
                $stmt = $pdo->prepare('UPDATE events SET title = ?, description = ?, category = ?, event_date = ?, event_time = ?, location = ?, image = ? WHERE id = ?');
                $stmt->execute([$title, $description, $category, $event_date, $event_time, $location, $image, $eventId]);
                flash('success', 'Event updated successfully.');
            }
            header('Location: manage_events.php');
            exit();
        }
    }

    if ($action === 'delete') {
        $eventId = intval($_POST['event_id'] ?? 0);
        if ($eventId > 0) {
            $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
            $stmt->execute([$eventId]);
            flash('success', 'Event deleted successfully.');
        }
        header('Location: manage_events.php');
        exit();
    }
}

$success = flash('success');
$currentEdit = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$editId]);
    $currentEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query('SELECT * FROM events ORDER BY event_date DESC, event_time DESC');
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - SLIATE</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <div class="grid grid-2" style="gap: 32px;">
            <section class="card">
                <h2><?php echo $currentEdit ? 'Edit Event' : 'Create Event'; ?></h2>
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
                <form method="POST" action="manage_events.php">
                    <input type="hidden" name="action" value="<?php echo $currentEdit ? 'update' : 'create'; ?>">
                    <?php if ($currentEdit): ?>
                        <input type="hidden" name="event_id" value="<?php echo intval($currentEdit['id']); ?>">
                    <?php endif; ?>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($currentEdit['title'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($currentEdit['category'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($currentEdit['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="event_date">Event Date</label>
                            <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($currentEdit['event_date'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="event_time">Event Time</label>
                            <input type="time" id="event_time" name="event_time" value="<?php echo htmlspecialchars($currentEdit['event_time'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($currentEdit['location'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image URL</label>
                        <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($currentEdit['image'] ?? ''); ?>" placeholder="Optional image path">
                    </div>

                    <button class="btn btn-primary" type="submit"><?php echo $currentEdit ? 'Update Event' : 'Create Event'; ?></button>
                </form>
            </section>

            <section class="card">
                <h2>Existing Events</h2>
                <?php if (empty($events)): ?>
                    <p>No events have been created yet.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo htmlspecialchars($event['event_date']); ?> <?php echo htmlspecialchars($event['event_time']); ?></td>
                                    <td><?php echo htmlspecialchars($event['category']); ?></td>
                                    <td style="display: flex; gap: 10px; flex-wrap: wrap;">
                                        <a class="btn btn-secondary" href="manage_events.php?edit=<?php echo intval($event['id']); ?>">Edit</a>
                                        <form method="POST" action="manage_events.php" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="event_id" value="<?php echo intval($event['id']); ?>">
                                            <button class="btn btn-secondary" type="submit" onclick="return confirm('Delete this event?');">Delete</button>
                                        </form>
                                    </td>
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
