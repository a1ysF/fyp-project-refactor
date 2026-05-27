<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login_signup/login.php');
    exit;
}

include '../../connection.php';

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $materialID = htmlspecialchars($_POST['materialID']);
    $uploaderID = htmlspecialchars($_POST['uploaderID']);
    $parentID = htmlspecialchars($_POST['parentID']);
    $dateSubmitted = date('Y-m-d H:i:s'); // Current timestamp
    $dateEdited = date('Y-m-d H:i:s'); // Current timestamp
    $type = htmlspecialchars($_POST['type']);
    $unit = htmlspecialchars($_POST['unit']);
    $title = htmlspecialchars($_POST['title']);
    $description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : NULL;
    $filePath = !empty($_POST['fileH5P']) ? htmlspecialchars($_POST['fileH5P']) : NULL;
    $url = !empty($_POST['url']) ? htmlspecialchars($_POST['url']) : NULL;

    // Default image in case no file is uploaded
    $headImage = NULL;

    // Check if a file has been uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // File properties
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileError = $_FILES['image']['error'];
        $fileType = $_FILES['image']['type'];

        // Allowed file types
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $fileExt = explode('.', $_FILES['image']['name']);
        $fileActualExt = strtolower(end($fileExt));

        // Check if the uploaded file is of an allowed type
        if (in_array($fileActualExt, $allowed)) {
            // Check for any errors
            if ($fileError === 0) {
                // Check file size (5MB max in this example)
                if ($fileSize < 5000000000) {
                    // Read the file's binary data
                    $headImage = file_get_contents($fileTmpName);
                } else {
                    echo "File size is too large.";
                    exit();
                }
            } else {
                echo "Error uploading the file.";
                exit();
            }
        } else {
            echo "Invalid file type.";
            exit();
        }
    }

    try {
        // Prepare the SQL statement
        $sql = "INSERT INTO materials (material_id, uploader_id, parent_id, date_submitted, date_edited, type, unit, title, main_img, description, file_path, url)
                VALUES (:materialID, :uploaderID, :parentID, :dateSubmitted, :dateEdited, :type, :unit, :title, :headImage, :description, :filePath, :url)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':materialID', $materialID);
        $stmt->bindParam(':uploaderID', $uploaderID);
        $stmt->bindParam(':parentID', $parentID);
        $stmt->bindParam(':dateSubmitted', $dateSubmitted);
        $stmt->bindParam(':dateEdited', $dateEdited);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':title', $title);
        if ($headImage !== NULL) {
            $stmt->bindParam(':headImage', $headImage, PDO::PARAM_LOB);
        } else {
            $stmt->bindValue(':headImage', NULL, PDO::PARAM_NULL);
        }
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':filePath', $filePath);
        $stmt->bindParam(':url', $url);

        // Execute the statement
        $stmt->execute();
        
        // Popout message
        echo "<script>alert('New record created successfully'); window.location.href='material_teach.php';</script>";
        } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        }

        // Close the connection
        $conn = null;
}




// function splitContent($content) {
//     // Split the content based on a delimiter
//     // Here, we assume the delimiter is a specific string, e.g., "[split]"
//     $parts = explode('[split]', $content);

//     // Initialize variables
//     $headImage = '';
//     $description = '';

//     // Ensure we have two parts
//     if (count($parts) == 2) {
//         $headImage = trim($parts[0]);  // This is expected to be a base64 string
//         $description = trim($parts[1]);
//     } else {
//         // Default to the entire content as description if no split found
//         $description = trim($content);
//     }

//     // Check if headImage is a base64 string and convert it to binary data
//     if (preg_match('/^data:image\/(\w+);base64,/', $headImage, $type)) {
//         // Split the string on comma to remove the mime type part
//         $data = explode(',', $headImage);
//         if (count($data) == 2) {
//             $headImage = base64_decode($data[1]); // Decode it into binary data
//             if ($headImage === false) {
//                 // If decoding fails, handle the error
//                 $headImage = ''; // Or set an error message if needed
//             }
//         }
//     } else {
//         // If headImage is not in expected format, handle accordingly
//         $headImage = '';
//     }

//     return [$headImage, $description];
// }

?>

