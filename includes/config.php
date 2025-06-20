<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rhythora');

// Site configuration
define('SITE_URL', 'http://localhost/rhythora');
define('SITE_NAME', 'Rhythora');

// File upload paths
define('SONG_UPLOAD_PATH', 'assets/uploads/songs/');
define('IMAGE_UPLOAD_PATH', 'assets/uploads/images/');
define('VIDEO_UPLOAD_PATH', 'assets/uploads/videos/');

// Start session
session_start();

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>