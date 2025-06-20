<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Please log in to update your profile";
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);
    
    // Validate inputs
    if (empty($name) || empty($username)) {
        $_SESSION['error'] = "Name and username are required";
        redirect('profile.php');
    }
    
    // Check if username is available
    $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username is already taken";
        redirect('profile.php');
    }
    
    // Handle avatar upload
    $avatar_path = null;
    if (!empty($_FILES['avatar']['name'])) {
        $upload = uploadFile($_FILES['avatar'], 'image');
        if ($upload['success']) {
            $avatar_path = $upload['path'];
            
            // Delete old avatar if exists
            $sql = "SELECT avatar FROM users WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
        }
    }
    
    // Update user in database
    if ($avatar_path) {
        $sql = "UPDATE users SET name = ?, username = ?, bio = ?, avatar = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssi", $name, $username, $bio, $avatar_path, $user_id);
    } else {
        $sql = "UPDATE users SET name = ?, username = ?, bio = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssi", $name, $username, $bio, $user_id);
    }
    
    if ($stmt->execute()) {
        // Update session data
        $_SESSION['name'] = $name;
        $_SESSION['username'] = $username;
        if ($avatar_path) {
            $_SESSION['avatar'] = $avatar_path;
        }
        
        $_SESSION['success'] = "Profile updated successfully!";
        redirect('profile.php');
    } else {
        $_SESSION['error'] = "Failed to update profile";
        redirect('profile.php');
    }
} else {
    redirect('profile.php');
}
?>