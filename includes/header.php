<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/auth.php';

$user = app_get_current_user();
$loggedIn = is_array($user) && !empty($user);
$role = $loggedIn ? ($user['role'] ?? 'guest') : 'guest';
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Get My Events count for students
$myEventsCount = 0;
if ($loggedIn && $role === 'student') {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM student_events WHERE student_id = ?');
        $stmt->execute([$_SESSION['student_id']]);
        $myEventsCount = (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        $myEventsCount = 0;
    }
}

function navLink($href, $label, $currentPage) {
    $active = $currentPage === $href ? 'active' : '';
    return '<a class="nav-link ' . $active . '" href="' . htmlspecialchars($href) . '">' . htmlspecialchars($label) . '</a>';
}
?>
<style>
    .badge-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--color-accent, #ff9900);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 999px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        margin-left: 5px;
        line-height: 1;
        vertical-align: middle;
    }
</style>
<header class="site-header">
    <div class="container header-inner">
        <div class="site-branding">
            <a class="site-title" href="index.php">SLIATE Event Management</a>
            <div class="site-tagline">Sri Lanka Institute of Advanced Technological Education</div>
        </div>

        <nav class="main-nav">
            <?php echo navLink('index.php', 'Home', $currentPage); ?>
            <?php echo navLink('event.php', 'Events', $currentPage); ?>
            <?php if ($loggedIn): ?>
                <a class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    Dashboard<?php if ($myEventsCount > 0): ?><span class="badge-count" id="my-events-badge"><?php echo $myEventsCount; ?></span><?php else: ?><span class="badge-count" id="my-events-badge" style="display:none;">0</span><?php endif; ?>
                </a>
                <?php if (can_manage_events()): ?>
                    <?php echo navLink('manage_events.php', 'Manage Events', $currentPage); ?>
                <?php endif; ?>
                <?php if (can_manage_users()): ?>
                    <?php echo navLink('manage_users.php', 'Manage Users', $currentPage); ?>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="header-actions">
            <?php if ($loggedIn): ?>
                <span class="header-user">Welcome, <?php echo htmlspecialchars($user['first_name'] ?? 'User'); ?> (<?php echo htmlspecialchars($role); ?>)</span>
                <button class="btn btn-secondary" onclick="window.location.href='logout.php'">Logout</button>
            <?php else: ?>
                <div style="display: flex; gap: 10px;">
                    <a class="btn btn-secondary" href="register.php">Register</a>
                    <a class="btn btn-primary" href="login.php">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
