<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('../index.php');
}

// Get stats for dashboard
$users_count = 0;
$songs_count = 0;
$reels_count = 0;

$sql = "SELECT COUNT(*) as count FROM users";
$result = $db->query($sql);
if ($result) {
    $users_count = $result->fetch_assoc()['count'];
}

$sql = "SELECT COUNT(*) as count FROM songs";
$result = $db->query($sql);
if ($result) {
    $songs_count = $result->fetch_assoc()['count'];
}

$sql = "SELECT COUNT(*) as count FROM reels";
$result = $db->query($sql);
if ($result) {
    $reels_count = $result->fetch_assoc()['count'];
}
?>

<?php include '../includes/header.php'; ?>
    <title>Admin Dashboard | <?php echo SITE_NAME; ?></title>
</head>
<body>
    <?php include 'admin_nav.php'; ?>

    <div class="main-content">
        <div class="content">
            <header>
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="logo-title">
                        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Rhythora Logo" />
                        <h1>Admin Dashboard</h1>
                    </div>
                </div>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $users_count; ?></div>
                        <div class="stat-label">Users</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $songs_count; ?></div>
                        <div class="stat-label">Songs</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $reels_count; ?></div>
                        <div class="stat-label">Reels</div>
                    </div>
                </div>
            </div>

            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <div class="activity-list">
                    <!-- Recent activity would go here -->
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-text">New user registered</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <!-- More activity items -->
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>