<?php
require __DIR__ . '/../ClassAutoLoad.php';

// Connect to DB (MariaDB/MySQL)
$mysqli = @new mysqli($conf['DB_HOST'], $conf['DB_USER'], $conf['DB_PASS'], $conf['DB_NAME']);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8');
    exit;
}
$mysqli->set_charset('utf8mb4');

// Fetch users in ascending order by username (change to created_at if preferred)
$sql = 'SELECT id, username, email, created_at FROM users ORDER BY username ASC';
$result = $mysqli->query($sql);
if ($result === false) {
    http_response_code(500);
    echo 'Query failed.';
    $mysqli->close();
    exit;
}

// Render
$ObjLayout->header($conf);

echo "<h2>Users (ascending by username)</h2>";
echo "<table border='1' cellpadding='6' cellspacing='0'>";
echo "<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Created</th></tr></thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . (int)$row['id'] . '</td>';
    echo '<td>' . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '<td>' . htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';

$ObjLayout->footer($conf);

$result->free();
$mysqli->close();
