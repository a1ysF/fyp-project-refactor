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
 include '../../connection.php';
 include '../../checkfunc.php';
   
 error_reporting(E_ALL);
ini_set('display_errors', 1);
 // If the session variable is empty, this 
 // means the user is yet to login
 // User will be sent to 'login.php' page
 // to allow the user to login
 if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../../login_signup/login.php');
    exit;
} else {
   $userId = $_SESSION['user_id'];
   //echo "User ID: " . $userId; // Debugging output
   $userData = fetchTeacherDataFromDatabase($userId);
   if ($userData === null) {
       session_destroy();
       unset($_SESSION['user_id']);
       echo "You need to log in first.";
       header('location: ../../login_signup/login.php');
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
    header("location: ../../login_signup/login.php");
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

if (isset($_SESSION['user_id'])) {
    $uploader_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM materials WHERE uploader_id = :uploader_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':uploader_id', $uploader_id, PDO::PARAM_STR);
    $stmt->execute();
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $_SESSION['materials'] = $materials;
}

// include 'fetch_materials.php';
 ?>
 
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link href="../../images/img2-logo.png" rel="icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>CryptoLearn</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../assets/css/light-bootstrap-dashboard.css?v=2.0.0 " rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="../../assets/css/demo.css" rel="stylesheet" />


    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="../../fonts/icomoon/style.css">

    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/jquery-ui.css">
    <link rel="stylesheet" href="../../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../../css/owl.theme.default.min.css">

    <link rel="stylesheet" href="../../css/jquery.fancybox.min.css">

    <link rel="stylesheet" href="../../css/bootstrap-datepicker.css">

    <link rel="stylesheet" href="../../fonts/flaticon/font/flaticon.css">

    <link rel="stylesheet" href="../../css/aos.css">

    <link rel="stylesheet" href="../../css/style.css">
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
                    <a href="dashboardT.php" style="text-decoration: none;">
                        <div class="site-logo mr-auto">
                            <a href="dashboardT.php" style="color: #7871EB;">
                                <img src="../uploads/crypto-logo.png" width="" height="50" style="margin-right: 10px;" alt="CryptoLearn Logo">
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
                            <a class="dropdown-item" href="dashboardT.php">Home</a>
                            <a class="dropdown-item" href="../setting_user.php">Settings</a>
                            <a class="dropdown-item" href="#">Help</a>
                            <!-- <a class="dropdown-item" href="#">Something else here</a> -->
                            <div class="divider"></div>
                            <a class="dropdown-item" href="../../login_signup/login.php?logout='1'">Log Out</a>
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
<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    ?>
    <div class="wrapper">
        <div class="sidebar" data-color="black"> 
            <!-- data-image="../assets/img/sidebar-5.jpg" -->
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
        -->
            <div class="sidebar-wrapper">
                <!-- <div class="logo">
                    <a href="http://www.creative-tim.com" class="simple-text">
                        Creative Tim
                    </a>
                    <a href="" class = "simple-text" type="hidden">CryptoLearning</a>
                </div> -->
                <ul class="nav">
                    <li>
                        <a class="nav-link" href="dashboardT.php">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./myclass_teach.php">
                            <i class="nc-icon nc-backpack"></i>
                            <p>Myclass</p>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="./material_teach.php">
                            <i class="nc-icon nc-spaceship"></i>
                            <p>Material</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./rewards_teach.php">
                            <i class="nc-icon nc-paper-2"></i>
                            <p>Rewards</p>
                        </a>
                    </li>
                    <!-- <li>
                        <a class="nav-link" href="./quiz_teach.php">
                            <i class="nc-icon nc-chat-round"></i>
                            <p>Announce & Message</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./forum_teach.php">
                            <i class="nc-icon nc-chat-round"></i>
                            <p>Forum</p>
                        </a>
                    </li> -->
                    <!-- <li>
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
            <div class="content">
                <div class="container-fluid">
                    <!-- Title and Button -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="font-weight-bold">Materials</h2>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['materials']) && count($_SESSION['materials']) > 0): ?>
                        <div class="row mb-4">
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <select id="materialTypeFilter" class="form-control">
                                        <option value="all">All Types</option>
                                        <option value="Learning">Learning</option>
                                        <option value="Assignment">Assignment</option>
                                        <option value="Quiz">Quiz</option>
                                    </select>
                                </div>
                                <div>
                                    <a href="insert_mat_ui.php" class="btn btn-primary">New Material</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($_SESSION['materials'] as $material): ?>
                                <?php
                                // Convert binary image data to base64 if it exists
                                $src = 'uploads/default.jpg'; // Default image path
                                if ($material['main_img'] !== null) {
                                    $imageData = base64_encode($material['main_img']);
                                    $src = 'data:image/jpeg;base64,' . $imageData;
                                }
                                ?>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="course bg-white h-100 align-self-stretch">
                                            <figure class="m-0">
                                                <a href="<?= htmlspecialchars($material['file_path']) ?>"><img src="<?= htmlspecialchars($src) ?>" alt="Image" class="img-fluid same-size"></a>
                                            </figure>
                                            <div class="course-inner-text py-4 px-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h3 class="m-0"><a href="#"><?= htmlspecialchars($material['title']) ?></a></h3>
                                                    <p class="font-weight-bold m-0">Unit: <?= htmlspecialchars($material['unit']) ?></p>
                                                </div>
                                                <?= htmlspecialchars_decode($material['description']) ?>
                                            </div>
                                            <div class="card-footer d-flex justify-content-between align-items-center">
                                                <a href="display_mat_teach.php?id=<?= $material['material_id'] ?>">
                                                    <button type="button" class="btn btn-info float-right button-margin">Start</button>
                                                </a>
                                                <a href="edit_material.php?id=<?= $material['material_id'] ?>" class="btn btn-warning float-right button-margin">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="font-weight-bold">Upload New Material</p>
                            <a href="insert_mat_ui.php" class="btn btn-primary">New Material</a>
                        </div>
                    <?php endif; ?>

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

                        <script>
                            // JavaScript to handle filtering based on material type
                            document.getElementById('materialTypeFilter').addEventListener('change', function() {
                                var selectedType = this.value;
                                // Redirect to the same page with the selected type as a query parameter
                                window.location.href = 'material_teach.php?type=' + selectedType;
                            });
                        </script>
            </div>
        </div>
    </div>
</body>

<style>
.same-size {
    width: 100%;
    height: 200px; /* Adjust the height as needed */
    object-fit: cover;
}

.button-margin {
    margin-right: 20px; /* Adjust the margin-right as needed */
    margin-bottom: 20px; /* Adjust the margin-bottom as needed */
}

.card .img-fluid {
    width: 100%;
    height: 200px; /* Adjust the height as per your design needs */
    object-fit: cover; /* Ensures the image covers the entire space */
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
<script src="../../assets/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="../../assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="../../assets/js/core/bootstrap.min.js" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="../../assets/js/plugins/bootstrap-switch.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!--  Chartist Plugin  -->
<script src="../../assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../../assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="../../assets/js/light-bootstrap-dashboard.js?v=2.0.0 " type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="../../assets/js/demo.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Javascript method's body can be found in assets/js/demos.js
        demo.initDashboardPageCharts();

        //demo.showNotification();

    });
</script>

</html>


<!-- <script src="js/jquery-3.3.1.min.js"></script>
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

</html> -->
