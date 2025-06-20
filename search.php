<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = trim($_GET['q']);
    $results = searchSongs($query);
    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>