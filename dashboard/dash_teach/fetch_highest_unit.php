<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || (!isset($_POST['parentID']) && !isset($_POST['type']))) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include '../../connection.php';

$uploaderID = $_SESSION['user_id'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['parentID'])) {
        $parentID = $_POST['parentID'];

        // Fetch the parent unit
        $sql = "SELECT unit FROM materials WHERE uploader_id = :uploaderID AND material_id = :parentID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uploaderID', $uploaderID);
        $stmt->bindParam(':parentID', $parentID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $parentUnit = $result['unit'];

            // Fetch the highest sub-unit for the parent unit
            $sql = "SELECT MAX(CAST(SUBSTRING(unit, LENGTH(:parentUnit) + 2) AS UNSIGNED)) AS max_sub_unit
                    FROM materials
                    WHERE uploader_id = :uploaderID AND unit LIKE :unitPattern";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':uploaderID', $uploaderID);
            $stmt->bindParam(':parentUnit', $parentUnit);
            $unitPattern = $parentUnit . ".%";
            $stmt->bindParam(':unitPattern', $unitPattern);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $maxSubUnit = isset($result['max_sub_unit']) ? $result['max_sub_unit'] : 0;
            echo $maxSubUnit;
        } else {
            echo 0;
        }
    } else if (isset($_POST['type']) && $_POST['type'] == 'Learning') {
        // Fetch the highest unit for "Learning"
        $sql = "SELECT MAX(CAST(unit AS UNSIGNED)) AS max_unit
                FROM materials
                WHERE uploader_id = :uploaderID AND type = :type";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uploaderID', $uploaderID);
        $stmt->bindParam(':type', $type);
        $type = 'Learning';
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxUnit = isset($result['max_unit']) ? $result['max_unit'] : 0;
        echo $maxUnit;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
