<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('../index.php');
}

// Handle song deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $song_id = (int)$_GET['delete'];
    
    // Get song info first
    $sql = "SELECT audio_path, image_path FROM songs WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
    
    if ($song) {
        // Delete files
        if (file_exists($song['audio_path'])) {
            unlink($song['audio_path']);
        }
        if (file_exists($song['image_path'])) {
            unlink($song['image_path']);
        }
        
        // Delete from database
        $sql = "DELETE FROM songs WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $song_id);
        $stmt->execute();
        
        $_SESSION['success'] = "Song deleted successfully";
    } else {
        $_SESSION['error'] = "Song not found";
    }
    
    redirect('songs.php');
}

// Get all songs
$sql = "SELECT * FROM songs ORDER BY created_at DESC";
$result = $db->query($sql);
$songs = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>
    <title>Manage Songs | <?php echo SITE_NAME; ?></title>
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
                        <h1>Manage Songs</h1>
                    </div>
                </div>
                <div class="header-right">
                    <a href="add_song.php" class="auth-button signup-btn">
                        <i class="fas fa-plus"></i> Add Song
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
                            <th>Duration</th>
                            <th>Trending</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($songs as $song): ?>
                        <tr>
                            <td><?php echo $song['id']; ?></td>
                            <td><?php echo htmlspecialchars($song['title']); ?></td>
                            <td><?php echo htmlspecialchars($song['artist']); ?></td>
                            <td><?php echo formatDuration($song['duration']); ?></td>
                            <td><?php echo $song['is_trending'] ? 'Yes' : 'No'; ?></td>
                            <td class="actions">
                                <a href="edit_song.php?id=<?php echo $song['id']; ?>" class="action-btn edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="songs.php?delete=<?php echo $song['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this song?')">
                                    <i class="fas fa-trash"></i>
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