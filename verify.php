<?php
require __DIR__ . '/ClassAutoLoad.php';

// Initialize authentication class
$auth = new auth();

// Get language file
$lang = include(__DIR__ . '/dict/eng.php');

// Verify the email
$result = $auth->verifyEmail($lang, $conf);

// Redirect after 3 seconds
if ($result) {
    header("refresh:3;url=" . $conf['site_url'] . "/Forms/login.php");
} else {
    header("refresh:3;url=" . $conf['site_url']);
}
?>