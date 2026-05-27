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

// Get the class_id from the URL
$current_class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

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

// Initialize an empty array to hold the materials
$materials = [];

// Fetch materials created by the teacher and start with "L"
foreach ($classes_joined as $class) {
    $stmt = $conn->prepare("
        SELECT m.material_id, m.title, m.unit
        FROM materials m
        WHERE m.uploader_id = :teacher_id AND m.material_id LIKE 'L%'
    ");
    $stmt->bindParam(':teacher_id', $class['teacher_id']);
    $stmt->execute();
    $materials[$class['class_id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sort the materials by unit in ascending order
    usort($materials[$class['class_id']], function($a, $b) {
        return $a['unit'] <=> $b['unit'];
    });
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
        
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <!-- Title and Button -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="font-weight-bold">Class Modules</h2>
                        </div>
                    </div>
                    <div class="container custom-container">
                        <?php foreach ($classes_joined as $class): ?>
                            <?php if (!empty($materials[$class['class_id']])): ?>
                                <?php foreach ($materials[$class['class_id']] as $material): ?>
                                    <div class="w-100 mb-3">
                                        <div class="course h-100 align-self-stretch">
                                            <div class="class-card">
                                                <div class="class-info d-flex justify-content-between align-items-center">
                                                    <figure class="m-0">
                                                        <!-- <img src="path/to/icon.png" alt="Class Icon" class="img-fluid same-size"> -->
                                                    </figure>
                                                    <div class="class-details">
                                                        <h5 class="m-0">Module <?php echo htmlspecialchars($material['unit']); ?></h5>
                                                        <p>Title: <?php echo htmlspecialchars($material['title']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button class="btn" onclick="location.href='./module_generated.php?material_id=<?php echo $material['material_id']; ?>&class_id=<?php echo $class['class_id']; ?>'">Enter Module</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
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
.custom-container {
    background-color: white;
    padding: 20px;
    font-family: 'Roboto', Arial, sans-serif; /* Apply the imported font */
}

.class-card {
    border: 2px solid #7871EB; /* Purple frame */
    background-color: #7871EB; /* Purple background */
    border-radius: 10px; /* Smooth edges */
    padding: 15px;
    margin-bottom: 20px; /* Space between cards */
    overflow: hidden; /* Ensure child elements don't overflow */
}

.class-info {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.class-info figure {
    margin-right: 15px;
}

.class-details {
    flex: 1;
}

.class-details h5, .class-details p, .class-details a {
    margin: 0;
    color: white; /* Make text white */
    font-weight: 700; /* Make text bold */
}

.class-details h5 a {
    font-weight: 700; /* Make module text bold */
}

.btn {
    margin-left: auto;
    border-radius: 10px; /* Smooth edges for the button */
    color: white; /* Text color for the button */
    background-color: #d3d3d3; /* Light gray background color for the button */
    font-weight: 700; /* Make button text bold */
    border: 2px solid #7871EB; /* Add border to the button */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover effect */
}

.btn:hover {
    background-color: #A3E4A7; /* Light green color when hovered */
    color: white; /* Keep text color white when hovered */
}

.nav-item.active .nav-link {
    background-color: #007bff; /* Example active background color */
    color: white; /* Example active text color */
}
</style>



                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="course bg-white h-100 align-self-stretch">
                                    <figure class="m-0">
                                      <a href="course-single.html"><img src="../assets/img/reverse_cipher.jpg" alt="Image" class="img-fluid same-size"></a>
                                    </figure>
                                    <div class="course-inner-text py-4 px-4">
                                      <h3><a href="#">Reverse Cipher</a></h3>
                                      <p>The Reverse Cipher involves reversing the entire string of text, making it difficult to read until reversed again.</p>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-fill pull-right button-margin">Start</button>
                                    <div class="clearfix"></div>
                                    <div class="d-flex border-top stats">
                                      <div class="py-3 px-4"><span class="icon-users"></span> 2,193 students</div>
                                      <div class="py-3 px-4 w-25 ml-auto border-left"><span class="icon-chat"></span> 2</div>
                                    </div>
                                  </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="course bg-white h-100 align-self-stretch">
                                    <figure class="m-0">
                                      <a href="course-single.html"><img src="../assets/img/caeser_cipher.jpg" alt="Image" class="img-fluid same-size"></a>
                                    </figure>
                                        <div class="course-inner-text py-4 px-4">
                                            <h3><a href="#">Caeser Cipher</a></h3>
                                            <p>The Caesar Cipher shifts each letter in the plaintext by a fixed number of positions down the alphabet.</p>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-fill pull-right button-margin">Start</button>
                                        <div class="clearfix"></div>
                                        <div class="d-flex border-top stats">
                                        <div class="py-3 px-4"><span class="icon-users"></span> 2,193 students</div>
                                        <div class="py-3 px-4 w-25 ml-auto border-left"><span class="icon-chat"></span> 2</div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <nav>
                        <!-- <ul class="footer-menu">
                            <li>
                                <a href="#">
                                    Home
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Company
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Portfolio
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Blog
                                </a>
                            </li>
                        </ul> -->
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
