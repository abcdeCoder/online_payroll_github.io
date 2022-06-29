<?php
$conn = new mysqli('remotemysql.com', 'nOkcCY6dDe', 'vm4bpSk5rp', 'nOkcCY6dDe');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

?>