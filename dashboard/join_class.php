<?php
// Include database connection
include '../connection.php'; // Correct the path to your connection file

// Set the content type to JSON
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An error occurred'];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not logged in");
        }

        $user_id = $_SESSION['user_id'];
        if (!isset($_POST['class_code'])) {
            throw new Exception("Class code not provided");
        }

        $class_code = $_POST['class_code'];

        // Get class_id and class_name from class_code
        $stmt = $conn->prepare("SELECT class_id, class_name FROM class WHERE class_code = :class_code");
        $stmt->bindParam(':class_code', $class_code);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("Invalid class code");
        }

        $class_id = $result['class_id'];
        $class_name = $result['class_name'];

        // Check if the user is already enrolled in the class
        $stmt = $conn->prepare("SELECT COUNT(*) FROM class_users WHERE class_id = :class_id AND user_id = :user_id");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $is_enrolled = $stmt->fetchColumn();

        if ($is_enrolled > 0) {
            $response['status'] = 'info';
            $response['message'] = "You are already enrolled in the class '$class_name'";
        } else {
            // Insert class_id and user_id into class_users table
            $stmt = $conn->prepare("INSERT INTO class_users (class_id, user_id) VALUES (:class_id, :user_id)");
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = "Successfully joined the class '$class_name'";
            } else {
                throw new Exception("Failed to join the class");
            }
        }
    } else {
        throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
