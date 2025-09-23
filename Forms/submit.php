<?php
require '../ClassAutoLoad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? (string)$_POST['password'] : '';

if ($username === '' || $email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid submission. Please provide a valid username, email address, and password.';
    exit;
}

// Connect to MariaDB/MySQL
$mysqli = @new mysqli($conf['DB_HOST'], $conf['DB_USER'], $conf['DB_PASS'], $conf['DB_NAME']);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8');
    exit;
}
$mysqli->set_charset('utf8mb4');

// Check for existing username or email
$exists = false;
if ($stmt = $mysqli->prepare('SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1')) {
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
}

if ($exists) {
    http_response_code(409);
    echo 'Username or email already exists.';
    $mysqli->close();
    exit;
}

// Hash the password and insert the user
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
if (!($stmt = $mysqli->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)'))) {
    http_response_code(500);
    echo 'Failed to prepare insert statement.';
    $mysqli->close();
    exit;
}
$stmt->bind_param('sss', $username, $email, $passwordHash);
if (!$stmt->execute()) {
    // Handle duplicate keys or other errors
    if ($mysqli->errno === 1062) {
        http_response_code(409);
        echo 'Username or email already exists.';
    } else {
        http_response_code(500);
        echo 'User registration failed.';
    }
    $stmt->close();
    $mysqli->close();
    exit;
}
$stmt->close();

// Build email
$fromEmail = !empty($conf['site_email']) ? $conf['site_email'] : (!empty($conf['smtp_user']) ? $conf['smtp_user'] : 'no-reply@example.com');
$fromName  = !empty($conf['site_name']) ? $conf['site_name'] : 'Mastermind';

$mailCnt = [
    'from_email' => $fromEmail,
    'from_name'  => $fromName,
    'to_email'   => $email,
    'to_name'    => $username,
    'subject'    => 'Enrollment Confirmation - Mastermind',
    'body'       => '<p>Congratulations ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . ', you have successfully enrolled to the Mastermind course!!! Prepare for a nerve-wracking but thought provoking and fulfilling experience. Cannot wait to interract with you. To continue, please click the following link to activate your account: ' . $activation_link . '.<br>The activation key will expire at: ' . date('Y-m-d H:i:s', $expiration_time) . '</p>'
];

// Send email after successful insert
$mailer = isset($ObjSendMail) ? $ObjSendMail : new SendMail();

// Suppress any output during mailing to allow redirect
ob_start();
$mailer->send($mysqli, $mailCnt);
ob_end_clean();

$mysqli->close();

// Redirect to welcome page
header('Location: welcome.php?name=' . urlencode($username));
exit;
