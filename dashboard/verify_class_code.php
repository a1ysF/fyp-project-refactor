<?php
// Include database connection
include '../connection.php'; // Corrected the path to the connection file

// Set the content type to JSON
header('Content-Type: application/json');

// Disable displaying errors and log them instead
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log'); // Make sure the path is writable

$response = ['valid' => false]; // Default response

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_POST['class_code'])) {
            throw new Exception("Class code not provided");
        }

        $class_code = $_POST['class_code'];

        // Log the received class code
        error_log("Received class code: " . $class_code); 

        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT class_name FROM class WHERE class_code = :class_code");
        $stmt->bindParam(':class_code', $class_code);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Query result: " . json_encode($result)); // Log the query result

        if ($result) {
            $response['valid'] = true;
            $response['class_name'] = $result['class_name'];
        }
    } else {
        throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    // Log the error message if necessary
    error_log($e->getMessage());
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
