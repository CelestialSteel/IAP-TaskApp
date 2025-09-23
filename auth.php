<?php
// Authentication configuration

$auth_config = array(
    'session_timeout' => 30 * 60, // Session timeout in seconds (30 minutes)
    'remember_me_duration' => 7 * 24 * 60 * 60, // "Remember Me" duration in seconds (7 days)
    'max_login_attempts' => 5, // Maximum login attempts before lockout
    'lockout_duration' => 15 * 60, // Lockout duration in seconds (15 minutes)
    'password_reset_token_expiry' => 1 * 60 * 60, // Password reset token expiry time in seconds (1 hour)
    );

  class auth{

private function replaceTemplateVars(string $template, array $vars): string {
    foreach ($vars as $key => $value) {
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $template = str_replace("{{$key}}", $value, $template);
    }

    return $template;
}

public function signup($lang, $conf, $ObjSendMail) {

    if (!isset($_POST['signup'])) {
        return;
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo $lang['all_fields_required'];
        return;
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        echo $lang['invalid_username'];
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException($lang['invalid_email']);
    }

    if ($password !== $confirm_password) {
        echo $lang['passwords_do_not_match'];
        return;
    }

    if (strlen($password) < 6) {
        echo $lang['password_too_short'];
        return;
    }

    $mysqli = @new mysqli($conf['DB_HOST'], $conf['DB_USER'], $conf['DB_PASS'], $conf['DB_NAME']);
    if ($mysqli->connect_error) {
        http_response_code(500);
        echo 'Database connection failed: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8');
        return;
    }
    $mysqli->set_charset('utf8mb4');

    $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error.';
        $mysqli->close();
        return;
    }
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo $lang['username_or_email_exists'];
        $stmt->close();
        $mysqli->close();
        return;
    }
    $stmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error.';
        $mysqli->close();
        return;
    }
    $stmt->bind_param('sss', $username, $email, $password_hash);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo 'Database error.';
        $stmt->close();
        $mysqli->close();
        return;
    }
    $stmt->close();

    $verification_code = bin2hex(random_bytes(16));
    $stmt = $mysqli->prepare('UPDATE users SET verification_code = ? WHERE email = ?');
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error.';
        $mysqli->close();
        return;
    }
    $stmt->bind_param('ss', $verification_code, $email);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error.';
        $mysqli->close();
        return;
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user ? $user['id'] : null;
    $stmt->close();

    $mysqli->close();

    // Send verification email
   $fromEmail = !empty($conf['site_email']) ? $conf['site_email'] : (!empty($conf['smtp_user']) ? $conf['smtp_user'] : 'no-reply@example.com');
    $fromName = !empty($conf['site_name']) ? $conf['site_name'] : 'Mastermind';
    $mailCont = [
        'from_email' => $fromEmail,
        'from_name'  => $fromName,
        'to_email'   => $email,
        'to_name'    => $username,
        'subject'    => 'Verify your email address',
        'body'       => $lang['verify_email_body'],
        'body_html'  => $lang['verify_email_body_html'],
        'site_name'  => $conf['site_name'],
        'site_url'   => $conf['site_url'],
        'verification_code' => $verification_code,
    ];
    $mailBody = $this->TemplateVarbind($mailCont['body'], $mailCont);
}

public function verifyEmail($lang, $conf) {
    if (!isset($_GET['code']) || empty($_GET['code'])) {
        echo $lang['invalid_verification_link'];
        return false;
    }

    $verification_code = trim($_GET['code']);

    $mysqli = @new mysqli($conf['DB_HOST'], $conf['DB_USER'], $conf['DB_PASS'], $conf['DB_NAME']);
    if ($mysqli->connect_error) {
        http_response_code(500);
        echo 'Database connection failed: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8');
        return false;
    }
    $mysqli->set_charset('utf8mb4');

    $stmt = $mysqli->prepare('SELECT id FROM users WHERE verification_code = ? AND verified = 0');
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error.';
        $mysqli->close();
        return false;
    }

    $stmt->bind_param('s', $verification_code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo $lang['invalid_or_expired_verification_code'];
        $stmt->close();
        $mysqli->close();
        return false;
    }

    $stmt->close();

    $stmt = $mysqli->prepare('UPDATE users SET verified = 1, verification_code = NULL WHERE verification_code = ?');
    if (!$stmt) {
        http_response_code(500);
        echo 'Database error.';
        $mysqli->close();
        return false;
    }

    $stmt->bind_param('s', $verification_code);
    $success = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    if ($success) {
        echo $lang['email_verified_successfully'];
        return true;
    } else {
        echo $lang['verification_failed'];
        return false;
    }
}
}