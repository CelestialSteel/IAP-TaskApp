<?php
// Include the class file
require __DIR__.'/../ClassAutoLoad.php';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check if username and password are not empty
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Add database connection and verification
        $sql = "SELECT * FROM users WHERE username = ?";
        
        // Handle both PDO and mysqli connections
        if ($conf['dbh'] instanceof PDO) {
            $stmt = $conf['dbh']->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conf['dbh']->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        }
        
        // Check if user exists and verify password
        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            // Start session and store user info
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to catalogue page
            header('Location: catalogue.php');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}

$layout->header($conf);
if (isset($error)) {
    echo "<p style='color: red;'>$error</p>";
}
$forms->signin();
$layout->footer($conf);