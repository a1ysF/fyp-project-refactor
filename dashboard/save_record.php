<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login_signup/login.php');
    exit;
}

include '../connection.php';
function generateUniqueRecordId($conn) {
    // Implement your unique record ID generation logic here
    return uniqid('rec_');
}

function generateUniqueRewardId($conn) {
    // Implement your unique reward ID generation logic here
    return uniqid('rew_');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $user_id = $_POST['user_id'];
    $start = $_POST['start'];
    $material_id = $_POST['material_id'];
    $score_input = $_POST['score'];

    // Extract correct answers and total questions
    list($correct_answers, $total_questions) = explode('/', $score_input);

    // Validate and calculate the score percentage
    if (is_numeric($correct_answers) && is_numeric($total_questions) && $total_questions != 0) {
        $score_percentage = ($correct_answers / $total_questions) * 100;
        $score_percentage = round($score_percentage, 2); // Round to 2 decimal places
    } else {
        die("Invalid score input. Please enter in the format correct/total.");
    }

    // Generate unique record_id
    $record_id = generateUniqueRecordId($conn);

    // Set the current date and time for created_at
    $created_at = date('Y-m-d H:i:s');

    // Insert the data into the database
    $sql = "INSERT INTO records (record_id, user_id, material_id, score_percentage, dstart, created_at) VALUES (:record_id, :user_id, :material_id, :score_percentage, :dstart, :created_at)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':record_id', $record_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':material_id', $material_id);
    $stmt->bindParam(':score_percentage', $score_percentage);
    $stmt->bindParam(':dstart', $start);
    $stmt->bindParam(':created_at', $created_at);

    if ($stmt->execute()) {
        // Check if user has a reward_id
        $sql = "SELECT reward_id, points, learning, assignment, quiz FROM rewards WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $reward_id = $result['reward_id'];
        $points = $result['points'];
        $learning = $result['learning'];
        $assignment = $result['assignment'];
        $quiz = $result['quiz'];
        $stmt->closeCursor();

        $additional_points = 100 + intval($score_percentage);

        if ($reward_id) {
            // User has a reward_id, update the existing record
            $sql = "UPDATE rewards SET points = points + :additional_points, learning = learning + IF(LEFT(:material_id, 1) = 'L', 1, 0), assignment = assignment + IF(LEFT(:material_id, 1) = 'A', 1, 0), quiz = quiz + IF(LEFT(:material_id, 1) = 'Q', 1, 0) WHERE reward_id = :reward_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':additional_points', $additional_points);
            $stmt->bindParam(':material_id', $material_id);
            $stmt->bindParam(':reward_id', $reward_id);
            $stmt->execute();
            $stmt->closeCursor();
        } else {
            // User does not have a reward_id, create a new one
            $reward_id = generateUniqueRewardId($conn);
            $learning_increment = (substr($material_id, 0, 1) == 'L') ? 1 : 0;
            $assignment_increment = (substr($material_id, 0, 1) == 'A') ? 1 : 0;
            $quiz_increment = (substr($material_id, 0, 1) == 'Q') ? 1 : 0;

            $sql = "INSERT INTO rewards (reward_id, user_id, points, learning, assignment, quiz) VALUES (:reward_id, :user_id, :points, :learning, :assignment, :quiz)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':reward_id', $reward_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':points', $additional_points);
            $stmt->bindParam(':learning', $learning_increment);
            $stmt->bindParam(':assignment', $assignment_increment);
            $stmt->bindParam(':quiz', $quiz_increment);
            $stmt->execute();
            $stmt->closeCursor();
        }

        // Call the badge check function
        include('badge_check.php');
        checkAndAssignBadges($conn, $user_id);

        // Redirect back to previous page using JavaScript and refresh
        echo "<script>
                history.back();
                window.addEventListener('load', function() {
                    window.location.reload();
                });
              </script>";
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }

    // Close the database connection
    $conn = null;
} else {
    echo "Invalid request method.";
}
?>