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
    private function TemplateVarbind($template, $vars) {
        foreach ($vars as $key => $value) {
            $template = str_replace('{' . $key . '}', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $template);
        }
    return $template;
  }  

  public function Signup($lang,$conf,$ObjSendMail){

    if(isset($_POST['signup'])){
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Basic validation
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            echo $lang['all_fields_required'];
            return;
        if( !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
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

        // Connect to DB
        $mysqli = @new mysqli($conf['DB_HOST'], $conf['DB_USER'], $conf['DB_PASS'], $conf['DB_NAME']);
        if ($mysqli->connect_error) {
            http_response_code(500);
            echo 'Database connection failed: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8');
            return;
        }
        $mysqli->set_charset('utf8mb4');

        // Check for existing username or email
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

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $mysqli->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
        if (!$stmt) {
            http_response_code(500);
            echo 'Database error.';
            $mysqli->close();
            return;
        }
        $stmt->bind_param('sss', $username, $email, $password_hash);
  }