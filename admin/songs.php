<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('../index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $artist = trim($_POST['artist']);
    $album = trim($_POST['album']);
    $genre = trim($_POST['genre']);
    $duration = (int)$_POST['duration'];
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;
    
    // Validate inputs
    if (empty($title) || empty($artist) || empty($duration)) {
        $_SESSION['error'] = "Title, artist and duration are required";
        redirect('add_song.php');
    }
    
    // Handle file uploads
    $audio_path = '';
    $image_path = '';
    
    if (!empty($_FILES['audio_file']['name'])) {
        $audio_upload = uploadFile($_FILES['audio_file'], 'song');
        if (!$audio_upload['success']) {
            $_SESSION['error'] = $audio_upload['message'];
            redirect('add_song.php');
        }
        $audio_path = $audio_upload['path'];
    }
    
    if (!empty($_FILES['image_file']['name'])) {
        $image_upload = uploadFile($_FILES['image_file'], 'image');
        if (!$image_upload['success']) {
            $_SESSION['error'] = $image_upload['message'];
            redirect('add_song.php');
        }
        $image_path = $image_upload['path'];
    }
    
    // Insert into database
    $sql = "INSERT INTO songs (title, artist, album, genre, duration, audio_path, image_path, is_trending) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssssissi", $title, $artist, $album, $genre, $duration, $audio_path, $image_path, $is_trending);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Song added successfully";
        redirect('songs.php');
    } else {
        $_SESSION['error'] = "Failed to add song";
        redirect('add_song.php');
    }
}
?>

<?php include '../includes/header.php'; ?>
    <title>Add Song | <?php echo SITE_NAME; ?></title>
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
                        <h1>Add New Song</h1>
                    </div>
                </div>
            </header>

            <div class="admin-form-container">
                <form action="add_song.php" method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-group">
                        <label for="title">Title*</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="artist">Artist*</label>
                        <input type="text" id="artist" name="artist" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="album">Album</label>
                        <input type="text" id="album" name="album">
                    </div>
                    
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <input type="text" id="genre" name="genre">
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duration (seconds)*</label>
                        <input type="number" id="duration" name="duration" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_trending"> Trending Song
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="audio_file">Audio File* (MP3, M4A)</label>
                        <input type="file" id="audio_file" name="audio_file" accept="audio/*" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_file">Cover Image (JPEG, PNG)</label>
                        <input type="file" id="image_file" name="image_file" accept="image/*">
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Save Song
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>