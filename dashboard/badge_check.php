<?php
function assignBadge($conn, $user_id, $badge_id) {
    // Check if the badge is already assigned to the user
    $sql = "SELECT badge_id FROM badge_users WHERE user_id = :user_id AND badge_id = :badge_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':badge_id', $badge_id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        // Badge is not assigned yet, insert the badge
        $stmt->closeCursor();
        $sql = "INSERT INTO badge_users (user_id, badge_id) VALUES (:user_id, :badge_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':badge_id', $badge_id);
        $stmt->execute();
        // $stmt->closeCursor();
        echo "Badge $badge_id assigned to user $user_id.<br>";
    } else {
        //echo "User $user_id already has badge $badge_id.<br>";
        $stmt->closeCursor();
    }
}

function checkAndAssignBadges($conn, $user_id) {
    // Get user rewards data
    $sql = "SELECT learning, assignment, quiz, points FROM rewards WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $learning = $result['learning'];
    $assignment = $result['assignment'];
    $quiz = $result['quiz'];
    $points = $result['points'];

    // Check conditions and assign badges
    if ($learning >= 1) {
        assignBadge($conn, $user_id, 'material1');
    }
    if ($learning >= 5) {
        assignBadge($conn, $user_id, 'learning5');
    }
    if ($assignment >= 5) {
        assignBadge($conn, $user_id, 'assignment5');
    }
    if ($quiz >= 5) {
        assignBadge($conn, $user_id, 'quiz5');
    }
    if ($points >= 500) {
        assignBadge($conn, $user_id, 'points500');
    }
}
?>
