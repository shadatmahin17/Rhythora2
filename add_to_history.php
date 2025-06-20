<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn() || !isset($_GET['song_id'])) {
    die(json_encode(['success' => false]));
}

$user_id = $_SESSION['user_id'];
$song_id = (int)$_GET['song_id'];

if (addToHistory($user_id, $song_id)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>