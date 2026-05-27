<form method="post" action="process_form.php">
    <!-- Other form fields -->

    <!-- Student Signup Button -->
    <div class="form-group form-button">
        <input type="submit" name="signup" id="student-signup" class="form-submit" value="Register" />
    </div>

    <!-- Teacher Signup Button -->
    <div class="form-group form-button">
        <input type="submit" name="signup" id="teacher-signup" class="form-submit" value="Register" />
    </div>
</form>


<?php
// Check which button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $button_clicked = $_POST['signup'];

    // Determine user type based on button ID
    if ($button_clicked == 'student-signup') {
        $user_type = 'student';
    } elseif ($button_clicked == 'teacher-signup') {
        $user_type = 'teacher';
    } else {
        // Default fallback if neither button ID matches (though should not happen in this case)
        $user_type = 'unknown';
    }

    // Use $user_type as needed
    echo "User type: $user_type";
} else {
    // Handle case where no button was clicked
    echo "No signup button clicked.";
}
?>
