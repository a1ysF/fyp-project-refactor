<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../../connection.php';

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login_signup/login.php');
    exit;
}

// Set the response header to JSON
header('Content-Type: application/json');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $class_id = $_POST['class_id'];
    $class_name = $_POST['className'];
    $class_code = $_POST['classCode'];
    $teacher_id = $_POST['teacher_id'];

    try {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO class (class_id, class_name, class_code, teacher_id) VALUES (:class_id, :className, :classCode, :teacher_id)");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':className', $class_name);
        $stmt->bindParam(':classCode', $class_code);
        $stmt->bindParam(':teacher_id', $teacher_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'New class created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error executing statement: ' . implode(":", $stmt->errorInfo())]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }

    // Close the connection
    $conn = null;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
