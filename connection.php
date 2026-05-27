<?php 

// $servername = "lrgs.ftsm.ukm.my";
// $username = "a185640";
// $password = "bigpurplefrog";
// $dbname = "a185640";

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'fyp';
$charset = 'utf8mb4';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"; // Optional: Just to confirm successful connection
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
