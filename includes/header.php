<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/auth.php';

$user = app_get_current_user();
$loggedIn = is_array($user) && !empty($user);
$role = $loggedIn ? ($user['role'] ?? 'guest') : 'guest';
$currentPage = basename($_SERVER['SCRIPT_NAME']);

function navLink($href, $label, $currentPage) {
    $active = $currentPage === $href ? 'active' : '';
    return '<a class="nav-link ' . $active . '" href="' . htmlspecialchars($href) . '">' . htmlspecialchars($label) . '</a>';
}
?>
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
                <?php echo navLink('dashboard.php', 'Dashboard', $currentPage); ?>
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
                <a class="btn btn-primary" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
