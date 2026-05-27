<!-- 
=========================================================
 Light Bootstrap Dashboard - v2.0.1
=========================================================

 Product Page: https://www.creative-tim.com/product/light-bootstrap-dashboard
 Copyright 2019 Creative Tim (https://www.creative-tim.com)
 Licensed under MIT (https://github.com/creativetimofficial/light-bootstrap-dashboard/blob/master/LICENSE)

 Coded by Creative Tim

=========================================================

 The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.  -->

 <?php

// Start session to store session variables
session_start();
include '../connection.php';
include '../checkfunc.php';
require '../plugins/admin/phpmailer/vendor/autoload.php'; // Include Composer's autoloader
// include '../plugins/admin/send_verification_email.php'; // Include the sendVerificationEmail function

// If the session variable is empty, this means the user is yet to login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../login_signup/login.php');
    exit;
} else {
    $userId = $_SESSION['user_id'];
    $userData = fetchStudentDataFromDatabase($userId);
    if ($userData === null) {
        session_destroy();
        unset($_SESSION['user_id']);
        echo "You need to log in first.";
        header('location: ../login_signup/login.php');
        exit;
    }
}

// Logout button will destroy the session, and will unset the session variables
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

// Fetch classes the user has joined
$stmt = $conn->prepare("
    SELECT c.class_name, c.class_id
    FROM class c
    INNER JOIN class_users cu ON c.class_id = cu.class_id
    WHERE cu.user_id = :user_id
");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$classes_joined = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
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
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#pablo">
                            <span class="no-icon">Log out</span>
                        </a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
</header>

<body>
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
                            <li>
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
                    <li class="nav-item active">
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
                    <!-- <li>
                        <a class="nav-link" href="./maps.html">
                            <i class="nc-icon nc-pin-3"></i>
                            <p>Maps</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./notifications.html">
                            <i class="nc-icon nc-bell-55"></i>
                            <p>Notifications</p>
                        </a>
                    </li>
                    <li class="nav-item active active-pro">
                        <a class="nav-link active" href="upgrade.html">
                            <i class="nc-icon nc-alien-33"></i>
                            <p>Upgrade to PRO</p>
                        </a>
                    </li> -->
                </ul>
            </div>
        </div>
        
        <div class="main-panel">
            <?php if (!$isVerified): ?>
                <div class="alert alert-info" role="alert">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>Your account is not verified. Please verify your account to enjoy all the features. 
                        <a href="<?php echo $emailProviderLink; ?>" target="_blank">Verify Your Account</a>
                    </span>
                </div>
            <?php endif; ?>
            <div class="content">
            <h2 class="card-title">Welcome to Cryptography</h2>
                <div class="container-fluid">
                    <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-category">Introduction to Cryptography</h6>
                            </div>
                            <div class="card-body">
                                <div class="video-box">
                                    <!-- Embed a video or place a video element here -->
                                    <iframe width="100%" height="375" src="uploads/Cryptography - Introduction.mp4" frameborder="0" allowfullscreen></iframe>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a href="./myclass.php">
                                        <button class="btn btn-primary">Get Started</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-category">Continue where you left off</h6>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title">Reverse Cipher</h5>
                                </div>
                                <div class="card-body">
                                    <div class="course-list">
                                        <div class="course-item">
                                            <div class="course-icon">
                                                <img src="path/to/icon1.png" alt="Icon 1">
                                            </div>
                                            <div class="course-info">
                                                <p>Video 1</p>
                                            </div>
                                        </div>
                                        <div class="course-item">
                                            <div class="course-icon">
                                                <img src="path/to/icon2.png" alt="Icon 2">
                                            </div>
                                            <div class="course-info">
                                                <p>Video 2</p>
                                            </div>
                                        </div>
                                        <div class="course-item">
                                            <div class="course-icon">
                                                <img src="path/to/icon3.png" alt="Icon 3">
                                            </div>
                                            <div class="course-info">
                                                <p>Assesment 1</p>
                                            </div>
                                        </div>
                                        <div class="course-item">
                                            <div class="course-icon">
                                                <img src="path/to/icon4.png" alt="Icon 4">
                                            </div>
                                            <div class="course-info">
                                                <p>Video 3</p>
                                            </div>
                                        </div>
                                        <div class="course-item">
                                            <div class="course-icon">
                                                <img src="path/to/icon5.png" alt="Icon 5">
                                            </div>
                                            <div class="course-info">
                                                <p>Example 1</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <button class="btn btn-primary">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="container">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-category">My Class</h6>
                                </div>
                                <div class="card-body d-flex">
                                    <div class="flex-fill pr-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Learning Material and Quiz</h5>
                                        </div>
                                        <div class="course-list">
                                            <div class="course-item">
                                                <div class="course-icon">
                                                    <img src="path/to/icon1.png" alt="Icon 1">
                                                </div>
                                                <div class="course-info">
                                                    <a href="path/to/learn_more.html">
                                                        <p>Caesar Cipher</p>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="course-item">
                                                <div class="course-icon">
                                                    <img src="path/to/icon2.png" alt="Icon 2">
                                                </div>
                                                <div class="course-info">
                                                    <a href="path/to/extra_questions.html">
                                                        <p>Extra Questions 1</p>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="course-item">
                                                <div class="course-icon">
                                                    <img src="path/to/icon3.png" alt="Icon 3">
                                                </div>
                                                <div class="course-info">
                                                    <a href="path/to/quiz.html">
                                                        <p>Caesar Cipher Quiz</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="vl"></div>
                                    <div class="flex-fill pl-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Announcement & Messages</h5>
                                        </div>
                                        <div class="course-list">
                                            <div class="course-item">
                                                <div class="course-info">
                                                    <a href="path/to/submission_update.html">
                                                        <p>Submission Update</p>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="course-item">
                                                <div class="course-info">
                                                    <a href="path/to/message_teacher_alex.html">
                                                        <p>Message: Teacher Alex</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a href="./.php">
                                        <button class="btn btn-primary">Get Started</button>
                                    </a>
                                </div>
                            </div>
                        </div> -->
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
    </div>



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
.vl {
    border-left: 1px solid #ccc;
    height: 100%;
    margin: 0 15px; /* Adjust margin for spacing */
}

.course-info a {
    text-decoration: none; /* Optional: Remove underline from links */
    color: inherit; /* Optional: Inherit text color */
}

.course-info a:hover {
    text-decoration: underline; /* Optional: Add underline on hover */
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
        // demo.showNotifications();

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

<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> -->

</html>
