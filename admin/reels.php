<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('../index.php');
}

// Handle reel deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $reel_id = (int)$_GET['delete'];
    
    // Get reel info first
    $sql = "SELECT video_path FROM reels WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $reel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reel = $result->fetch_assoc();
    
    if ($reel) {
        // Delete file
        if (file_exists($reel['video_path'])) {
            unlink($reel['video_path']);
        }
        
        // Delete from database
        $sql = "DELETE FROM reels WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $reel_id);
        $stmt->execute();
        
        $_SESSION['success'] = "Reel deleted successfully";
    } else {
        $_SESSION['error'] = "Reel not found";
    }
    
    redirect('reels.php');
}

// Get all reels
$sql = "SELECT * FROM reels ORDER BY created_at DESC";
$result = $db->query($sql);
$reels = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>
    <title>Manage Reels | <?php echo SITE_NAME; ?></title>
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
                        <h1>Manage Reels</h1>
                    </div>
                </div>
                <div class="header-right">
                    <a href="add_reel.php" class="auth-button signup-btn">
                        <i class="fas fa-plus"></i> Add Reel
                    </a>
                </div>
            </header>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Likes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reels as $reel): ?>
                        <tr>
                            <td><?php echo $reel['id']; ?></td>
                            <td><?php echo htmlspecialchars($reel['title']); ?></td>
                            <td><?php echo htmlspecialchars($reel['artist']); ?></td>
                            <td><?php echo $reel['likes']; ?></td>
                            <td class="actions">
                                <a href="edit_reel.php?id=<?php echo $reel['id']; ?>" class="action-btn edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="reels.php?delete=<?php echo $reel['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this reel?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="<?php echo SITE_URL . '/' . $reel['video_path']; ?>" class="action-btn view" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>