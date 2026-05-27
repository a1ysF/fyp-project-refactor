<?php
include '../../connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM class WHERE class_id = :class_id");
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['unique' => false]);
    } else {
        echo json_encode(['unique' => true]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
