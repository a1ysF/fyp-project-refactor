<?php
$servername = getenv('MYSQLHOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: '3306';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '';
$dbname = getenv('MYSQLDATABASE') ?: 'fyp';

$conn = new mysqli($servername, $username, $password, $dbname, (int) $port);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
