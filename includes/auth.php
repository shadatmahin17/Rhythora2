<?php
require_once 'db.php';
require_once 'functions.php';

// Handle login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            if ($user['is_admin']) {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $_SESSION['error'] = "Invalid email or password";
        }
    } else {
        $_SESSION['error'] = "Invalid email or password";
    }
}

// Handle signup
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        redirect('signup.php');
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters";
        redirect('signup.php');
    }
    
    // Check if email or username exists
    $sql = "SELECT id FROM users WHERE email = ? OR username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email or username already exists";
        redirect('signup.php');
    }
    
    // Create user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $username, $hashed_password);
    
    if ($stmt->execute()) {
        $user_id = $db->getLastInsertId();
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $_SESSION['is_admin'] = false;
        
        redirect('index.php');
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        redirect('signup.php');
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}
?>