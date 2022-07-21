<?php
$conn = new mysqli('sql6.freesqldatabase.com', 'sql6507794', 'RKR8zmYwEL', 'sql6507794');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

?>