<?php
require_once 'db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function formatDuration($seconds) {
    $mins = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf("%d:%02d", $mins, $secs);
}

function formatNumber($num) {
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    } elseif ($num >= 1000) {
        return round($num / 1000, 1) . 'K';
    }
    return $num;
}

function getTrendingSongs($limit = 10) {
    global $db;
    $sql = "SELECT * FROM songs WHERE is_trending = TRUE ORDER BY created_at DESC LIMIT ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function searchSongs($query) {
    global $db;
    $search = "%" . $db->escapeString($query) . "%";
    $sql = "SELECT * FROM songs WHERE title LIKE ? OR artist LIKE ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserPlaylists($user_id) {
    global $db;
    $sql = "SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPlaylistSongs($playlist_id) {
    global $db;
    $sql = "SELECT s.* FROM songs s 
            JOIN playlist_songs ps ON s.id = ps.song_id 
            WHERE ps.playlist_id = ? 
            ORDER BY ps.position";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $playlist_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserFavorites($user_id) {
    global $db;
    $sql = "SELECT s.* FROM songs s 
            JOIN user_favorites uf ON s.id = uf.song_id 
            WHERE uf.user_id = ? 
            ORDER BY uf.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserHistory($user_id, $limit = 20) {
    global $db;
    $sql = "SELECT s.* FROM songs s 
            JOIN listening_history lh ON s.id = lh.song_id 
            WHERE lh.user_id = ? 
            ORDER BY lh.played_at DESC 
            LIMIT ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addToHistory($user_id, $song_id) {
    global $db;
    $sql = "INSERT INTO listening_history (user_id, song_id) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $user_id, $song_id);
    return $stmt->execute();
}

function getAllReels() {
    global $db;
    $sql = "SELECT * FROM reels ORDER BY created_at DESC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function uploadFile($file, $type = 'image') {
    $target_dir = '';
    $allowed_types = [];
    
    switch ($type) {
        case 'song':
            $target_dir = SONG_UPLOAD_PATH;
            $allowed_types = ['audio/mpeg', 'audio/mp3', 'audio/m4a'];
            break;
        case 'video':
            $target_dir = VIDEO_UPLOAD_PATH;
            $allowed_types = ['video/mp4', 'video/quicktime'];
            break;
        default: // image
            $target_dir = IMAGE_UPLOAD_PATH;
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_ext;
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'File upload failed'];
    }
}
?>