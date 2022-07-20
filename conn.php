<?php
$conn = new mysqli('20.249.72.156', 'Gaurav', 'Abc161966@', 'apsystem');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

?>