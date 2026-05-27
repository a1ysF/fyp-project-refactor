<?php
// Include the database connection file
include '../../connection.php';

// Ensure the content type is set to JSON before any output
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$materialId = $data['material_id'] ?? '';

// Prepare the SQL statement
$sql = "SELECT COUNT(*) FROM materials WHERE material_id = :material_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':material_id', $materialId);
$stmt->execute();
$count = $stmt->fetchColumn();

// Prepare the response
$response = ['exists' => $count > 0];
echo json_encode($response);
?>
