<?php
include '../connection.php';
echo 'JSON functions are available.';
header('Content-Type: application/json');

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

if (isset($data['user_id'])) {
    $user_id = $conn->real_escape_string($data['user_id']);
    $sql = "SELECT 1 FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    echo json_encode(['exists' => $result->num_rows > 0]);
    $conn->close();
} else {
    echo json_encode(['exists' => false, 'error' => 'No user_id received']);
}
?>
