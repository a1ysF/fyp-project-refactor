<?php

// Include database connection
include 'connection.php';

function fetchTeacherDataFromDatabase($userId) {
    global $conn;

    // Check if the userId starts with "T" and has exactly 5 characters (1 letter + 4 numbers)
    if (preg_match('/^T\d{4}$/', $userId) !== 1) {
        return null; // Return null if the userId does not match the format
    }

    try {
        $sql = "SELECT * FROM users WHERE user_id = :userId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR); // Adjust PDO::PARAM_STR if user_id is a string
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $teacherData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $teacherData;
        } else {
            return null; // Handle case where teacher data is not found
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

function fetchStudentDataFromDatabase($userId) {
    global $conn;

    // Check if the userId starts with "S" and has exactly 5 characters (1 letter + 4 numbers)
    if (preg_match('/^S\d{4}$/', $userId) !== 1) {
        return null; // Return null if the userId does not match the format
    }

    try {
        $sql = "SELECT * FROM users WHERE user_id = :userId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR); // Adjust PDO::PARAM_STR if user_id is a string
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $studentData;
        } else {
            return null; // Handle case where student data is not found
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}



function check_login($con)
{
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];

        $query = "SELECT * FROM users WHERE user_id = '$id' LIMIT 1";
        
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    // Redirect to login
    header("Location: login.php");
    die;
}


function random_num($length)
{

	$text = "";
	if($length < 5)
	{
		$length = 5;
	}

	$len = rand(4,$length);

	for ($i=0; $i < $len; $i++) { 
		# code...

		$text .= rand(0,9);
	}

	return $text;
}

?>