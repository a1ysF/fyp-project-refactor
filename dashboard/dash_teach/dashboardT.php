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
session_start();
include '../../connection.php';
include '../../checkfunc.php';

// If the session variable is empty, redirect to login page
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../../login_signup/login.php');
    exit;
} else {
    $userId = $_SESSION['user_id'];
    $userData = fetchTeacherDataFromDatabase($userId);
    if ($userData === null) {
        session_destroy();
        unset($_SESSION['user_id']);
        echo "You need to log in first.";
        header('location: ../../login_signup/login.php');
        exit;
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user_id']);
    header("location: login.php");
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

$userId = $_SESSION['user_id'];
$isVerified = isUserVerified($userId, $conn);
$email = $userData['email'];
$emailDomain = substr(strrchr($email, "@"), 1);
$emailProviderLink = '#';

switch ($emailDomain) {
    case 'gmail.com':
    case 'siswa.ukm.edu.my':
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

// Fetch classes created by the user (teacher)
$stmt = $conn->prepare("SELECT * FROM class WHERE teacher_id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch students, their classes, and their records
$stmt = $conn->prepare("
    SELECT u.user_id, u.name as student_name, u.username, GROUP_CONCAT(DISTINCT c.class_name SEPARATOR ', ') as class_names,
           r.material_id, r.score_percentage, r.created_at, COUNT(r.material_id) as attempts
    FROM users u
    INNER JOIN class_users cu ON u.user_id = cu.user_id
    INNER JOIN class c ON cu.class_id = c.class_id
    LEFT JOIN records r ON u.user_id = r.user_id
    WHERE c.teacher_id = :user_id
    GROUP BY u.user_id, r.material_id
    ORDER BY u.user_id, r.created_at DESC
");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$students_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
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
                    <li class="nav-item active">
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
                    <li>
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
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="font-weight-bold">Welcome Teacher</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card ">
                                <div class="card-header ">
                                    <h6 class="card-category">Introduction to Cryptography</h6>
                                </div>
                                <div class="card-body ">
                                    <div class="video-box">
                                        <iframe src="../uploads/Cryptography - Introduction.mp4" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <button class="btn btn-primary">Get Started</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="align-items-center" style="margin-top: 10px;">CLASS YOU HAVE CREATED</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Display classes created by the teacher -->
                                        <?php foreach ($classes as $class): ?>
                                            <div class="col-md-12">
                                                <div class="class-card">
                                                    <div class="card">
                                                        <ul class="list-group bg-white h-100 align-self-stretch">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <h5 class="m-0"><?php echo htmlspecialchars($class['class_name']); ?></h5>
                                                                </div>
                                                                <div class="">
                                                                    <a href="./class_generated.php?class_id=<?php echo $class['class_id']; ?>" class="btn btn-success btn-sm float-right button-margin">Enter</a>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="font-weight-bold">Your students (<?php echo count($students_records); ?>)</h6>
                                </div>
                                <!-- Students Section -->
                                <div id="students-section" class="content-section">
                                    <hr class="w-100">
                                    <div class="table-responsive table-scroll">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">STUDENT NAME</th>
                                                    <th scope="col">CLASS</th>
                                                    <th scope="col">MATERIAL ID</th>
                                                    <th scope="col">SCORE (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($students_records as $record): ?>
                                                    <tr>
                                                        <td><a href="#"><?php echo htmlspecialchars($record['student_name']); ?></a></td>
                                                        <td><?php echo htmlspecialchars($record['class_names']); ?></td>
                                                        <td><?php echo htmlspecialchars($record['material_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($record['score_percentage']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        .class-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 15px;
        }
        .card-body {
            flex: 1;
        }
        .video-box iframe {
            width: 100%;
            height: 375px;
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

    <!--   -->
    <!-- <div class="fixed-plugin">
    <div class="dropdown show-dropdown">
        <a href="#" data-toggle="dropdown">
            <i class="fa fa-cog fa-2x"> </i>
        </a>

        <ul class="dropdown-menu">
			<li class="header-title"> Sidebar Style</li>
            <li class="adjustments-line">
                <a href="javascript:void(0)" class="switch-trigger">
                    <p>Background Image</p>
                    <label class="switch">
                        <input type="checkbox" data-toggle="switch" checked="" data-on-color="primary" data-off-color="primary"><span class="toggle"></span>
                    </label>
                    <div class="clearfix"></div>
                </a>
            </li>
            <li class="adjustments-line">
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <p>Filters</p>
                    <div class="pull-right">
                        <span class="badge filter badge-black" data-color="black"></span>
                        <span class="badge filter badge-azure" data-color="azure"></span>
                        <span class="badge filter badge-green" data-color="green"></span>
                        <span class="badge filter badge-orange" data-color="orange"></span>
                        <span class="badge filter badge-red" data-color="red"></span>
                        <span class="badge filter badge-purple active" data-color="purple"></span>
                    </div>
                    <div class="clearfix"></div>
                </a>
            </li>
            <li class="header-title">Sidebar Images</li>

            <li class="active">
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="../assets/img/sidebar-1.jpg" alt="" />
                </a>
            </li>
            <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="../assets/img/sidebar-3.jpg" alt="" />
                </a>
            </li>
            <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="..//assets/img/sidebar-4.jpg" alt="" />
                </a>
            </li>
            <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                    <img src="../assets/img/sidebar-5.jpg" alt="" />
                </a>
            </li>

            <li class="button-container">
                <div class="">
                    <a href="http://www.creative-tim.com/product/light-bootstrap-dashboard" target="_blank" class="btn btn-info btn-block btn-fill">Download, it's free!</a>
                </div>
            </li>

            <li class="header-title pro-title text-center">Want more components?</li>

            <li class="button-container">
                <div class="">
                    <a href="http://www.creative-tim.com/product/light-bootstrap-dashboard-pro" target="_blank" class="btn btn-warning btn-block btn-fill">Get The PRO Version!</a>
                </div>
            </li>

            <li class="header-title" id="sharrreTitle">Thank you for sharing!</li>

            <li class="button-container">
				<button id="twitter" class="btn btn-social btn-outline btn-twitter btn-round sharrre"><i class="fa fa-twitter"></i> · 256</button>
                <button id="facebook" class="btn btn-social btn-outline btn-facebook btn-round sharrre"><i class="fa fa-facebook-square"></i> · 426</button>
            </li>
        </ul>
    </div>
</div>
 -->
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
