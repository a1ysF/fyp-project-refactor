<?php
 
 // Starting the session, to use and
 // store data in session variable
 session_start();
 //echo "User ID from session: " . $_SESSION['user_id']; // Debugging output
 include '../connection.php';
 include '../checkfunc.php';
   
 // If the session variable is empty, this
// means the user is yet to login
// User will be sent to 'login.php' page
// to allow the user to login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../login_signup/login.php');
    exit;
} else {
    $userId = $_SESSION['user_id'];
    //echo "User ID: " . $userId; // Debugging output
    $userData = fetchStudentDataFromDatabase($userId);
    if ($userData === null) {
        session_destroy();
        unset($_SESSION['user_id']);
        echo "You need to log in first.";
        header('location: ../login_signup/login.php');
        exit;
    }
    // Now $userData contains all the data fetched from the database for the logged-in user
}

// Logout button will destroy the session, and
// will unset the session variables
// User will be headed to 'login.php'
// after logging out
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user_id']);
    header("location: ../login_signup/login.php");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to check verification status
function isUserVerified($userId, $conn) {
    $sql = "SELECT verify FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['verify'] == 1;
}

$userId = $_SESSION['user_id']; // Assuming user ID is stored in the session
$isVerified = isUserVerified($userId, $conn);
$email = $userData['email']; // Assuming user's email is available in $userData
$emailDomain = substr(strrchr($email, "@"), 1);
$emailProviderLink = '#';

switch ($emailDomain) {
    case 'gmail.com':
    case 'siswa.ukm.edu.my': // Custom domain that uses Gmail
        $emailProviderLink = 'https://mail.google.com/';
        break;
    case 'yahoo.com':
        $emailProviderLink = 'https://mail.yahoo.com/';
        break;
    case 'outlook.com':
    case 'hotmail.com':
    case 'live.com':
        $emailProviderLink = 'https://outlook.live.com/';
        break;
    default:
        $emailProviderLink = 'https://www.google.com/search?q=login+' . $emailDomain;
        break;
}

// Fetch user_id from session
$user_id = $_SESSION['user_id'];

// Fetch classes the user has joined
$stmt = $conn->prepare("
    SELECT c.class_name, c.class_id, c.class_code, c.teacher_id
    FROM class c
    INNER JOIN class_users cu ON c.class_id = cu.class_id
    WHERE cu.user_id = :user_id
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$classes_joined = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set the current class ID from the query parameter
$current_class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

// Check if the query parameters are set
if (isset($_GET['material_id'])) {
    $material_id = $_GET['material_id'];
    // $class_id = $_GET['class_id'];

    // Debug: Print the received parameters
    // echo "Received Material ID: " . htmlspecialchars($material_id) . "<br>";
    // echo "Received Class ID: " . htmlspecialchars($class_id) . "<br>";

    // Fetch the material details from the database
    $stmt = $conn->prepare("
        SELECT * FROM materials WHERE material_id = :material_id
    ");
    $stmt->bindParam(':material_id', $material_id);
    $stmt->execute();
    $material_details = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch only one row

    // // Fetch the class details from the database
    // $stmt = $conn->prepare("
    //     SELECT class_name
    //     FROM class
    //     WHERE class_id = :class_id
    // ");
    // $stmt->bindParam(':class_id', $class_id);
    // $stmt->execute();
    // $class_details = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "<p>Invalid request. Material ID and Class ID are required.</p>";
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link href="../images/img2-logo.png" rel="icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>CryptoLearn</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/light-bootstrap-dashboard.css?v=2.0.0 " rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="../assets/css/demo.css" rel="stylesheet" />


    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="../fonts/icomoon/style.css">

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/jquery-ui.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">

    <link rel="stylesheet" href="../css/jquery.fancybox.min.css">

    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">

    <link rel="stylesheet" href="../fonts/flaticon/font/flaticon.css">

    <link rel="stylesheet" href="../css/aos.css">

    <link rel="stylesheet" href="../css/style.css">
</head>

<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg " color-on-scroll="500">
        <div class="container-fluid">
            <button href="" class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navigation">
                <ul class="nav navbar-nav mr-auto">
                    <!-- <li class="nav-item">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <i class="nc-icon nc-palette"></i>
                            <span class="d-lg-none">Dashboard</span>
                        </a>
                    </li> -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nc-icon nc-zoom-split"></i>
                            <span class="d-lg-block">&nbsp;Search</span>
                        </a>
                    </li>
                    <li class="dropdown nav-item">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                            <i class="nc-icon nc-planet"></i>
                            <span class="notification" id="notificationCount">0</span>
                            <span class="d-lg-none">Notification</span>
                        </a>
                        <ul class="dropdown-menu" id="notificationList">
                            <a class="dropdown-item" style="font-weight: bold;">Notifications</a>
                            <div class="divider"></div>
                            <?php if (!$isVerified): ?>
                                <a class="dropdown-item notification-item" href="<?php echo $emailProviderLink; ?>" target="_blank">Verify Your Account</a>
                            <?php endif; ?>

                            <!-- Example notifications -->
                            <!-- <a class="dropdown-item notification-item" href="#">Notification 1</a>
                            <a class="dropdown-item notification-item" href="#">Notification 2</a>
                            <a class="dropdown-item notification-item" href="#">Notification 3</a>
                            <a class="dropdown-item notification-item" href="#">Notification 4</a> -->
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    // Count the number of notification items
                                    var notificationItems = document.querySelectorAll('.notification-item');
                                    var notificationCount = notificationItems.length;

                                    // Update the notification count in the span
                                    document.getElementById('notificationCount').textContent = notificationCount;
                                });
                            </script>
                        </ul>
                    </li>
                </ul>

                <div class="site-logo text-center">
                    <a href="dashboardS.php" style="text-decoration: none;">
                        <div class="site-logo mr-auto">
                            <a href="dashboardS.php" style="color: #7871EB;">
                                <img src="uploads/crypto-logo.png" width="" height="50" style="margin-right: 10px;" alt="CryptoLearn Logo">
                                <!-- CryptoLearn -->
                            </a>
                        </div>
                    </a>
                </div>

                <ul class="navbar-nav ml-auto">
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#pablo">
                            <span class="no-icon">Account</span>
                        </a>
                    </li> -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span id="userName"><?php echo htmlspecialchars($userData['name']); ?></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="dashboardS.php">Home</a>
                            <a class="dropdown-item" href="setting_user.php">Settings</a>
                            <a class="dropdown-item" href="#">Help</a>
                            <!-- <a class="dropdown-item" href="#">Something else here</a> -->
                            <div class="divider"></div>
                            <a class="dropdown-item" href="../login_signup/login.php?logout='1'">Log Out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
</header>

<body>
<div class="future-blobs">
            <div class="blob_2">
            <img src="../images/blob_2.svg" alt="Blob 2">
            </div>
            <div class="blob_1">
            <img src="../images/blob_1.svg" alt="Blob 1">
            </div>
        </div>


        <style>

        /* CSS for background blobs */
.future-blobs {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1; /* Ensures blobs are behind other content */
    pointer-events: none; /* Allows clicks to pass through to elements behind */
}

.blob_1,
.blob_2 {
    position: absolute;
    top: 0;
    left: 0;
    width: 50%; /* Adjust size as needed */
    height: auto;
}

.blob_1 img,
.blob_2 img {
    display: block;
    width: 100%;
    height: auto;
}

.blob_1 {
    transform: translate(120%, -50%); /* Adjust position as needed */
}

.blob_2 {
    transform: translate(-20%, 50%); /* Adjust position as needed */
}
    </style>

    
    <div class="wrapper">
        <div class="sidebar" data-color="black"> 
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <!-- MY CLASS section -->
                    <?php if (!empty($classes_joined)): ?>
                        <li class="nav-header">
                            <p>MY CLASS</p>
                        </li>
                        <?php foreach ($classes_joined as $class): ?>
                            <li class="nav-item <?php echo strcasecmp($current_class_id, $class['class_id']) == 0 ? 'active' : ''; ?>">
                                <a class="nav-link" href="./class_generated.php?class_id=<?php echo $class['class_id']; ?>">
                                    <i class="nc-icon nc-paper-2"></i>
                                    <p><?php echo htmlspecialchars($class['class_name']); ?></p>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- MY ACCOUNT section -->
                    <li class="nav-header">
                        <p>MY ACCOUNT</p>
                    </li>
                    <li>
                        <a class="nav-link" href="dashboardS.php">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./rewards.php">
                            <i class="nc-icon nc-backpack"></i>
                            <p>Rewards</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./progress.php">
                            <i class="nc-icon nc-spaceship"></i>
                            <p>Progress</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./myclass.php">
                            <i class="nc-icon nc-chat-round"></i>
                            <p>Join A Class</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

<style>
    .details-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0; /* Add margin to the top and bottom */
    }
    .detail {
        margin: 20px 0; /* Add margin to separate the details */
    }
    .detail span:first-child {
        font-weight: bold;
        display: block;
        margin-bottom: 5px; /* Add margin below the label */
    }
    .date-submitted {
        margin-left: auto; /* Pushes the date submitted to the far right */
    }
    .material-image {
        margin-top: 20px;
        text-align: center; /* Center the image container */
    }
    .material-image img {
        width: 50%; /* Reduce the image size to 50% */
        height: auto; /* Maintain aspect ratio */
        border: 5px solid #000; /* Add a frame with a border */
        border-radius: 10px; /* Optional: rounded corners */
        display: block;
        margin: 0 auto; /* Center the image horizontally */
    }
    .description {
        white-space: pre-wrap; /* Preserve whitespace and line breaks */
    }
    .indented {
        display: block; /* Ensure it takes the full width */
        padding-left: 15px; /* Adjust the value to increase or decrease the indentation */
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">

            <!-- Back Button -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-top: 10px;">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>     

            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title text-center">
                        <?php echo htmlspecialchars($material_details['type']) . " Material " . htmlspecialchars($material_details['material_id']); ?>
                    </h4>
                </div>

                <div class="card-body">

                    <div class="material-image">
                        <?php
                        $src = 'uploads/default.jpg';
                        if ($material_details['main_img'] !== null) {
                            $imageData = base64_encode($material_details['main_img']);
                            $src = 'data:image/jpeg;base64,' . $imageData;
                        } else {
                            $src = '/mnt/data/image.png'; // Path to the uploaded image
                        }
                        ?>
                        <img src="<?php echo $src; ?>" alt="Material Image">
                    </div>

                    <div class="detail">
                        <span>Title:</span>
                        <span>Unit <?php echo htmlspecialchars($material_details['unit']); ?> of <?php echo htmlspecialchars($material_details['title']); ?></span>
                    </div>

                    <div class="detail">
                        <span>Material Description:</span>
                        <span class="description indented" id="descriptionEdit"><?php echo htmlspecialchars($material_details['description']); ?></span>
                    </div>

                    <?php
                        // Assuming $material_details['file_path'] contains the URL wrapped in <p> tags
                        $file_path_with_tags = $material_details['file_path'];

                        // Decode any HTML entities
                        $file_path_with_tags = html_entity_decode($file_path_with_tags);

                        // Strip the <p> tags
                        $file_path = strip_tags($file_path_with_tags);

                        // Alternatively, you can use regex to remove any remaining tags
                        $file_path = preg_replace('/<[^>]+>/', '', $file_path);

                        // Manual approach to remove specific tags
                        $file_path = trim($file_path);
                        $file_path = str_replace(['<p>', '</p>'], '', $file_path);

                        // Verify the output (for debugging purposes)
                        //echo $file_path;

                        $user_id = $userData['user_id']; // Replace with actual user ID
                        ?>
                        <!-- Include jQuery for AJAX calls (if not already included) -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                        <div class="detail">
                            <span>H5P Content:</span>
                            <div class="iframe-container">
                                <iframe id="h5pIframe" src="<?php echo $file_path; ?>/embed" aria-label="Reverse Cipher" width="1088" height="637" frameborder="0" allowfullscreen="allowfullscreen" allow="autoplay *; geolocation *; microphone *; camera *; midi *; encrypted-media *"></iframe>
                                <script src="https://alfatehyusof.h5p.com/js/h5p-resizer.js" charset="UTF-8"></script>
                            </div>
                        </div>
                        <div id="h5pDataContainer"></div> <!-- Container to display fetched data -->

                        <?php
                            // Assuming $material_details['url'] contains the URL potentially wrapped in tags
                            $url_with_tags = $material_details['url'];

                            // Decode any HTML entities
                            $url_with_tags = html_entity_decode($url_with_tags);

                            // Strip the tags
                            $url = strip_tags($url_with_tags);

                            // Alternatively, you can use regex to remove any remaining tags
                            $url = preg_replace('/<[^>]+>/', '', $url);

                            // Manual approach to remove specific tags
                            $url = trim($url);
                            $url = str_replace(['<p>', '</p>'], '', $url);

                            // Verify the output (for debugging purposes)
                            //echo $url;

                            $user_id = $userData['user_id']; // Replace with actual user ID
                        ?>
                        <!-- Include jQuery for AJAX calls (if not already included) -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                        <div class="detail">
                            <span>Additional Content & URL (Links):</span>
                            <div class="iframe-container-small">
                                <iframe id="urlIframe" src="<?php echo $url; ?>" width="540" height="300" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                            </div>
                        </div>
                        <div id="urlDataContainer"></div> <!-- Container to display fetched data -->

                        <script>
                            $(document).ready(function(){
                                // You can add any additional jQuery functionality here
                                // For example, you might want to fetch and display additional data based on the URL
                            });
                        </script>

                        <style>
                            .iframe-container-small {
                                position: relative;
                                width: 100%;
                                max-width: 800px; /* Adjust this value to control the max width */
                                height: 500px; /* Adjust this value to control the height */
                                margin: 0 auto; /* Center the iframe */
                            }
                            .iframe-container-small iframe {
                                width: 100%;
                                height: 100%;
                                border: none;
                            }
                        </style>

                        <!-- Hidden form to submit data -->
                        <form id="hiddenDataForm" action="save_record.php" method="post">
                            <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>"> <!-- Use PHP to set user ID dynamically -->
                            <input type="hidden" name="start" id="start" value="<?php echo date('Y-m-d'); ?>"> <!-- Set current date as start date -->
                            <input type="hidden" name="material_id" id="material_id" value="<?php echo $material_details['material_id']; ?>"> <!-- Fetch material_id from material_details -->

                            <input type="hidden" name="score_percentage" id="score_percentage"> <!-- Hidden field for score percentage -->

                            <label for="score" style="margin-right: 10px;">Score (correct/total):</label>
                            <input type="text" name="score" id="score" style="margin-right: 10px;" placeholder="e.g., 1/7" required> <!-- Input for score -->
                            <button type="submit">Submit</button>
                        </form>

                        <script>
                        function calculateScorePercentage(event) {
                            event.preventDefault();

                            const scoreInput = document.getElementById('score').value;
                            const [correctAnswers, totalQuestions] = scoreInput.split('/').map(Number);

                            if (isNaN(correctAnswers) || isNaN(totalQuestions) || totalQuestions === 0) {
                                alert('Please enter a valid score in the format correct/total, e.g., 1/7.');
                                return;
                            }

                            const scorePercentage = (correctAnswers / totalQuestions) * 100;
                            document.getElementById('score_percentage').value = scorePercentage.toFixed(2);

                            document.getElementById('hiddenDataForm').submit();
                        }
                        </script>
                </div>
            </div>

        </div>
    </div>
    <footer class="footer">
        <div class="container-fluid">
            <nav>
                <p class="copyright text-center">
                    ©
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                    <a href="">Alfateh Yusof</a>
                </p>
            </nav>
        </div>
    </footer>
</div>


<script>
$(document).ready(function() {
    <?php echo $user_id; ?>
    window.addEventListener('message', function(event) {
        // Check the origin of the message to ensure it's from the trusted source
        if (event.origin === "https://alfatehyusof.h5p.com") {
            // Assuming the message data contains the necessary xAPI event details
            if (event.data.event === 'xAPI' && event.data.statement.verb.id === 'http://adlnet.gov/expapi/verbs/completed') {
                const score = event.data.statement.result.score.raw;
                const maxScore = event.data.statement.result.score.max;
                const userId = '<?php echo $user_id; ?>'; // Replace with the actual user ID
                const materialId = '<?php echo $material_details['material_id']; ?>'; // Replace with the actual material ID

                // Display fetched data in the h5pDataContainer
                $('#h5pDataContainer').html(`
                    <p>Score: ${score}</p>
                    <p>Max Score: ${maxScore}</p>
                    <p>Score Percentage: ${(score / maxScore) * 100}%</p>
                    <p>Submit successful!</p>
                `);

                // AJAX call to save the data to the server
                $.ajax({
                    url: 'save_record.php',
                    method: 'POST',
                    data: {
                        user_id: userId,
                        material_id: materialId,
                        score_percentage: (score / maxScore) * 100
                    },
                    success: function(response) {
                        console.log('Score saved successfully:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving score:', error);
                    }
                });
            }
        }
    });
});
</script>


<script>
    function decodeHtmlEntities(encodedString) {
        var element = document.createElement('div');
        element.innerHTML = encodedString;
        return element.textContent;
    }

    function stripHtmlTags(htmlString) {
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlString;
        return tempDiv.textContent || tempDiv.innerText || "";
    }

    document.addEventListener("DOMContentLoaded", function() {
        var descriptionElement = document.getElementById('descriptionEdit');
        var decodedDescription = decodeHtmlEntities(descriptionElement.innerHTML);
        var plainTextDescription = stripHtmlTags(decodedDescription);
        descriptionElement.innerHTML = plainTextDescription;

        var urlElement = document.getElementById('urlEdit');
        var decodedUrl = decodeHtmlEntities(urlElement.innerHTML);
        var plainTextUrl = stripHtmlTags(decodedUrl);
        urlElement.innerHTML = plainTextUrl;
    });
</script>
</body>


<style>
.nav-header {
    text-align: center;
    margin: 50px 0 10px; /* Add more space above the headers */
}

.nav-header p {
    font-size: 16px; /* Increase font size */
    font-weight: bold;
    color: white; /* Change text color to white */
    text-transform: uppercase;
    margin: 0; /* Remove default margin */
}

/* .nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
} */

.nav-link p {
    margin: 0;
}

</style>
<style>
    .footer {
    background-color: #f8f9fa; /* Adjust the background color as needed */
    padding: 10px 0;
}

.footer nav {
    display: flex;
    justify-content: flex-start; /* Align items to the start (left) */
    align-items: center; /* Center items vertically if needed */
}

.footer .copyright {
    margin: 0; /* Remove default margin */
    padding-left: 15px; /* Add padding to the left if needed */
}
</style>

</body>
<!--   Core JS Files   -->
<script src="../assets/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="../assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="../assets/js/core/bootstrap.min.js" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="../assets/js/plugins/bootstrap-switch.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!--  Chartist Plugin  -->
<script src="../assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="../assets/js/light-bootstrap-dashboard.js?v=2.0.0 " type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="../assets/js/demo.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Javascript method's body can be found in assets/js/demos.js
        demo.initDashboardPageCharts();

        //demo.showNotification();

    });
</script>


<script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/jquery.countdown.min.js"></script>
  <script src="js/bootstrap-datepicker.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.fancybox.min.js"></script>
  <script src="js/jquery.sticky.js"></script>

  
  <script src="js/main.js"></script>

</html>
