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
   
// Check if the user is logged in
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
        header('location: ../../login_signup/login.php');
        exit;
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user_id']);
    header("location: ../../login_signup/login.php");
    exit();
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

// Fetch students in the specific class for badges
$stmt = $conn->prepare("SELECT u.user_id, u.name as student_name, u.username
                        FROM users u
                        INNER JOIN class_users cu ON u.user_id = cu.user_id
                        INNER JOIN class c ON cu.class_id = c.class_id
                        WHERE c.teacher_id = :teacher_id");
$stmt->bindParam(':teacher_id', $userId);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch badges for each student
$students_badges = [];
foreach ($students as $student) {
    $stmt = $conn->prepare("SELECT b.badge_id, b.badge_file
                            FROM badge_users bu
                            INNER JOIN badges b ON bu.badge_id = b.badge_id
                            WHERE bu.user_id = :user_id");
    $stmt->bindParam(':user_id', $student['user_id']);
    $stmt->execute();
    $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $students_badges[] = ['student' => $student, 'badges' => $badges];
}

// Fetch all badges
$stmt = $conn->prepare("SELECT badge_id, badge_file FROM badges");
$stmt->execute();
$all_badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .table img {
            width: 50px;
            height: 50px;
            margin-right: 5px;
        }
    </style>
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
                    <li>
                        <a class="nav-link" href="./material_teach.php">
                            <i class="nc-icon nc-spaceship"></i>
                            <p>Material</p>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="./assignment_teach.php">
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2>My Students Rewards Dashboard</h2>
                                    <p>This page displays all of your student's rewards, including badges and points earned through various materials.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>All Badges <a href="#">View all</a></h6>
                                </div>
                                <div class="card-body">
                                    <div class="badge-counts">
                                        <?php foreach ($all_badges as $badge): ?>
                                            <div class="badge">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($badge['badge_file']); ?>" alt="Badge">
                                                <p>0</p> <!-- Adjust count based on actual data if available -->
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Students Badges Section -->
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">Your students' badges</h6>
                            <hr class="w-100">
                            <div class="table-responsive table-scroll">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">STUDENT NAME</th>
                                            <th scope="col">USERNAME / EMAIL</th>
                                            <th scope="col">BADGES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students_badges as $student_badges): ?>
                                            <tr>
                                                <td><a href="#"><?php echo htmlspecialchars($student_badges['student']['student_name']); ?></a></td>
                                                <td><?php echo htmlspecialchars($student_badges['student']['username']); ?></td>
                                                <td>
                                                    <?php foreach ($student_badges['badges'] as $badge): ?>
                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($badge['badge_file']); ?>" alt="Badge" style="width: 50px; height: 50px; margin-right: 5px;">
                                                    <?php endforeach; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
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
</body>

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
<style>
    .badge img {
    width: 200px; /* Adjust the width as needed */
    height: 200px; /* Adjust the height as needed */
}

.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    margin-bottom: 20px;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 15px;
}

.card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.card-header {
    background: #f7f7f7;
    border-bottom: 1px solid #ddd;
    padding: 10px 15px;
    border-radius: 4px 4px 0 0;
}

.card-header h6 {
    margin: 0;
    font-size: 14px;
}

.card-header a {
    float: right;
    font-size: 12px;
    color: #007bff;
    text-decoration: none;
}

.card-body {
    padding: 15px;
}

.showcase, .badge-counts {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.badge {
    text-align: center;
}

.badge img {
    width: 50px;
    height: 50px;
}

.user-statistics p {
    margin: 5px 0;
}

.points {
    font-weight: bold;
    color: #007bff;
}

.text-center {
    text-align: center;
}

</style>