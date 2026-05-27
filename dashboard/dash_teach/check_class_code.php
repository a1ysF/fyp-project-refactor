<?php
include '../../connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_code = $_POST['class_code'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM class WHERE class_code = :class_code");
    $stmt->bindParam(':class_code', $class_code);
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
