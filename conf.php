<?php
// Site timezone
$conf['site_timezone'] = 'AFRICA/NAIROBI';

// Site information
$conf['site_name'] = 'Mastermind';
$conf['site_url'] = 'http://localhost/taskapp';
$conf['site_email'] = 'mastermind@gmail.com';


// Database Constants
$conf['DB_TYPE'] = 'mysqli';
$conf['DB_HOST'] = 'localhost:3307';
$conf['DB_USER'] = 'root';
$conf['DB_PASS'] = '';
$conf['DB_NAME'] = 'taskapp';

// Supported site languages
$conf['supported_languages'] = array('eng', 'fre', 'spa'); //eng, fre


//Email configuration
$conf['mail_type'] = 'smtp'; // mail or smtp
$conf['smtp_host'] = 'smtp.example.com'; // SMTP Host Address
$conf['smtp_user'] = 'example@domain.com'; // SMTP Username
$conf['smtp_pass'] = ''; // SMTP Password
$conf['smtp_port'] = 465; // SMTP Port - 587 for tls, 465 for ssl
$conf['smtp_secure'] = 'ssl'; // Encryption - ssl or tls

//Activation link generator
$conf['activation_key_length'] = 20; // Length of the activation key
$conf['activation_key_lifetime'] = 24 * 60 * 60; // Activation key lifetime in seconds (24 hours)
$activation_key = bin2hex(random_bytes($conf['activation_key_length']));
$expiration_time = time() + $conf['activation_key_lifetime'];

// Generate the activation link
$activation_link = 'http://' . $_SERVER['HTTP_HOST'] . '/activate.php?key=' . $activation_key;

/*// Include the activation key and expiration time in the email
$email_body = "Please click the following link to activate your account: $activation_link\n";
$email_body .= "The activation key will expire at: " . date('Y-m-d H:i:s', $expiration_time) . "\n";
*/
?>