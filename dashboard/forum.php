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

// Fetch user_id from session
$user_id = $_SESSION['user_id'];

// Fetch classes the user has joined
$stmt = $conn->prepare("
    SELECT c.class_name, c.class_id
    FROM class c
    INNER JOIN class_users cu ON c.class_id = cu.class_id
    WHERE cu.user_id = :user_id
");
$stmt->bindParam(':user_id', $user_id);
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style type="text/css">
    body{
margin-top:20px;
color: #1a202c;
text-align: left;
background-color: #e2e8f0;    
}
.inner-wrapper {
position: relative;
height: calc(100vh - 3.5rem);
transition: transform 0.3s;
}
@media (min-width: 992px) {
.sticky-navbar .inner-wrapper {
    height: calc(100vh - 3.5rem - 48px);
}
}

.inner-main,
.inner-sidebar {
position: absolute;
top: 0;
bottom: 0;
display: flex;
flex-direction: column;
}
.inner-sidebar {
left: 0;
width: 235px;
border-right: 1px solid #cbd5e0;
background-color: #fff;
z-index: 1;
}
.inner-main {
right: 0;
left: 235px;
}
.inner-main-footer,
.inner-main-header,
.inner-sidebar-footer,
.inner-sidebar-header {
height: 3.5rem;
border-bottom: 1px solid #cbd5e0;
display: flex;
align-items: center;
padding: 0 1rem;
flex-shrink: 0;
}
.inner-main-body,
.inner-sidebar-body {
padding: 1rem;
overflow-y: auto;
position: relative;
flex: 1 1 auto;
}
.inner-main-body .sticky-top,
.inner-sidebar-body .sticky-top {
z-index: 999;
}
.inner-main-footer,
.inner-main-header {
background-color: #fff;
}
.inner-main-footer,
.inner-sidebar-footer {
border-top: 1px solid #cbd5e0;
border-bottom: 0;
height: auto;
min-height: 3.5rem;
}
@media (max-width: 767.98px) {
.inner-sidebar {
    left: -235px;
}
.inner-main {
    left: 0;
}
.inner-expand .main-body {
    overflow: hidden;
}
.inner-expand .inner-wrapper {
    transform: translate3d(235px, 0, 0);
}
}

.nav .show>.nav-link.nav-link-faded, .nav-link.nav-link-faded.active, .nav-link.nav-link-faded:active, .nav-pills .nav-link.nav-link-faded.active, .navbar-nav .show>.nav-link.nav-link-faded {
color: #3367b5;
background-color: #c9d8f0;
}

.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
color: #fff;
background-color: #467bcb;
}
.nav-link.has-icon {
display: flex;
align-items: center;
}
.nav-link.active {
color: #467bcb;
}
.nav-pills .nav-link {
border-radius: .25rem;
}
.nav-link {
color: #4a5568;
}
.card {
box-shadow: 0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px 0 rgba(0,0,0,.06);
}

.card {
position: relative;
display: flex;
flex-direction: column;
min-width: 0;
word-wrap: break-word;
background-color: #fff;
background-clip: border-box;
border: 0 solid rgba(0,0,0,.125);
border-radius: .25rem;
}

.card-body {
flex: 1 1 auto;
min-height: 1px;
padding: 1rem;
}
</style>

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
                    <li class="nav-item active">
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

        <div class="main-panel">
            
            <div class="content">
                <div class="container-fluid">


                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous" />
<div class="container">
<div class="main-body p-0">
<div class="inner-wrapper">

<div class="inner-sidebar">

<div class="inner-sidebar-header justify-content-center">
<button class="btn btn-primary has-icon btn-block" type="button" data-toggle="modal" data-target="#threadModal">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus mr-2">
<line x1="12" y1="5" x2="12" y2="19"></line>
<line x1="5" y1="12" x2="19" y2="12"></line>
</svg>
NEW DISCUSSION
</button>
</div>


<div class="inner-sidebar-body p-0">
<div class="p-3 h-100" data-simplebar="init">
<div class="simplebar-wrapper" style="margin: -16px;">
<div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div>
<div class="simplebar-mask">
<div class="simplebar-offset" style="right: 0px; bottom: 0px;">
<div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll;">
<div class="simplebar-content" style="padding: 16px;">
<nav class="nav nav-pills nav-gap-y-1 flex-column">
<a href="javascript:void(0)" class="nav-link nav-link-faded has-icon active">All Threads</a>
<a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Popular this week</a>
<a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Popular all time</a>
<a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Solved</a>
<a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Unsolved</a>
<a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">No replies yet</a>
</nav>
</div>
</div>
</div>
</div>
<div class="simplebar-placeholder" style="width: 234px; height: 292px;"></div>
</div>
<div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div>
<div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 151px; display: block; transform: translate3d(0px, 0px, 0px);"></div></div>
</div>
</div>

</div>


<div class="inner-main">

<div class="inner-main-header">
<a class="nav-link nav-icon rounded-circle nav-link-faded mr-3 d-md-none" href="#" data-toggle="inner-sidebar"><i class="material-icons">arrow_forward_ios</i></a>
<select class="custom-select custom-select-sm w-auto mr-1">
<option selected>Latest</option>
<option value="1">Popular</option>
<option value="3">Solved</option>
<option value="3">Unsolved</option>
<option value="3">No Replies Yet</option>
</select>
<span class="input-icon input-icon-sm ml-auto w-auto">
<input type="text" class="form-control form-control-sm bg-gray-200 border-gray-200 shadow-none mb-4 mt-4" placeholder="Search forum" />
</span>
</div>



<div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">Realtime fetching data</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">drewdan</a> replied <span class="text-secondary font-weight-bold">13 minutes ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 19</span>
<span><i class="far fa-comment ml-2"></i> 3</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">Laravel 7 database backup</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">jlrdw</a> replied <span class="text-secondary font-weight-bold">3 hours ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 18</span>
<span><i class="far fa-comment ml-2"></i> 1</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar3.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">Http client post raw content</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">ciungulete</a> replied <span class="text-secondary font-weight-bold">7 hours ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 32</span>
<span><i class="far fa-comment ml-2"></i> 2</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar4.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">Top rated filter not working</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">bugsysha</a> replied <span class="text-secondary font-weight-bold">11 hours ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 49</span>
<span><i class="far fa-comment ml-2"></i> 9</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar5.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">Create a delimiter field</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">jackalds</a> replied <span class="text-secondary font-weight-bold">12 hours ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 65</span>
<span><i class="far fa-comment ml-2"></i> 10</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">One model 4 tables</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">bugsysha</a> replied <span class="text-secondary font-weight-bold">14 hours ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 45</span>
<span><i class="far fa-comment ml-2"></i> 4</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body p-2 p-sm-3">
<div class="media forum-item">
<a href="#" data-toggle="collapse" data-target=".forum-content"><img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="mr-3 rounded-circle" width="50" alt="User" /></a>
<div class="media-body">
<h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body">Auth attempt returns false</a></h6>
<p class="text-secondary">
lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor sit amet
</p>
<p class="text-muted"><a href="javascript:void(0)">michaeloravec</a> replied <span class="text-secondary font-weight-bold">18 hours ago</span></p>
</div>
<div class="text-muted small text-center align-self-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 70</span>
<span><i class="far fa-comment ml-2"></i> 3</span>
</div>
</div>
</div>
</div>
<ul class="pagination pagination-sm pagination-circle justify-content-center mb-0">
<li class="page-item disabled">
<span class="page-link has-icon"><i class="material-icons">chevron_left</i></span>
</li>
<li class="page-item"><a class="page-link" href="javascript:void(0)">1</a></li>
<li class="page-item active"><span class="page-link">2</span></li>
<li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
<li class="page-item">
<a class="page-link has-icon" href="javascript:void(0)"><i class="material-icons">chevron_right</i></a>
</li>
</ul>
</div>


<div class="inner-main-body p-2 p-sm-3 collapse forum-content">
<a href="#" class="btn btn-light btn-sm mb-3 has-icon" data-toggle="collapse" data-target=".forum-content"><i class="fa fa-arrow-left mr-2"></i>Back</a>
<div class="card mb-2">
<div class="card-body">
<div class="media forum-item">
<a href="javascript:void(0)" class="card-link">
<img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="rounded-circle" width="50" alt="User" />
<small class="d-block text-center text-muted">Newbie</small>
</a>
<div class="media-body ml-3">
<a href="javascript:void(0)" class="text-secondary">Mokrani</a>
<small class="text-muted ml-2">1 hour ago</small>
<h5 class="mt-1">Realtime fetching data</h5>
<div class="mt-3 font-size-sm">
<p>Hellooo :)</p>
<p>
I'm newbie with laravel and i want to fetch data from database in realtime for my dashboard anaytics and i found a solution with ajax but it dosen't work if any one have a simple solution it will be
helpful
</p>
<p>Thank</p>
</div>
</div>
<div class="text-muted small text-center">
<span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 19</span>
<span><i class="far fa-comment ml-2"></i> 3</span>
</div>
</div>
</div>
</div>
<div class="card mb-2">
<div class="card-body">
<div class="media forum-item">
<a href="javascript:void(0)" class="card-link">
<img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="rounded-circle" width="50" alt="User" />
<small class="d-block text-center text-muted">Pro</small>
</a>
<div class="media-body ml-3">
<a href="javascript:void(0)" class="text-secondary">drewdan</a>
<small class="text-muted ml-2">1 hour ago</small>
<div class="mt-3 font-size-sm">
<p>What exactly doesn't work with your ajax calls?</p>
<p>Also, WebSockets are a great solution for realtime data on a dashboard. Laravel offers this out of the box using broadcasting</p>
</div>
<button class="btn btn-xs text-muted has-icon"><i class="fa fa-heart" aria-hidden="true"></i>1</button>
<a href="javascript:void(0)" class="text-muted small">Reply</a>
</div>
</div>
</div>
</div>
</div>


</div>

</div>

<div class="modal fade" id="threadModal" tabindex="-1" role="dialog" aria-labelledby="threadModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<form>
<div class="modal-header d-flex align-items-center bg-primary text-white">
<h6 class="modal-title mb-0" id="threadModalLabel">New Discussion</h6>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">×</span>
</button>
</div>
<div class="modal-body">
<div class="form-group">
<label for="threadTitle">Title</label>
<input type="text" class="form-control" id="threadTitle" placeholder="Enter title" autofocus />
</div>
<textarea class="form-control summernote" style="display: none;"></textarea>
<div class="custom-file form-control-sm mt-3" style="max-width: 300px;">
<input type="file" class="custom-file-input" id="customFile" multiple />
<label class="custom-file-label" for="customFile">Attachment</label>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
<button type="button" class="btn btn-primary">Post</button>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
	
</script>


                    <!-- <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">100 Awesome Nucleo Icons</h4>
                                    <p class="card-category">Handcrafted by our friends from
                                        <a href="https://nucleoapp.com/?ref=1712">NucleoApp</a>
                                    </p>
                                </div>
                                <div class="card-body all-icons">
                                    <div class="row">
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-air-baloon"></i>
                                                <p>nc-air-baloon</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-album-2"></i>
                                                <p>nc-album-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-alien-33"></i>
                                                <p>nc-alien-33</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-align-center"></i>
                                                <p>nc-align-center</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-align-left-2"></i>
                                                <p>nc-align-left-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-ambulance"></i>
                                                <p>nc-ambulance</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-android"></i>
                                                <p>nc-android</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-app"></i>
                                                <p>nc-app</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-apple"></i>
                                                <p>nc-apple</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-atom"></i>
                                                <p>nc-atom</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-attach-87"></i>
                                                <p>nc-attach-87</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-audio-92"></i>
                                                <p>nc-audio-92</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-backpack"></i>
                                                <p>nc-backpack</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-badge"></i>
                                                <p>nc-badge</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bag"></i>
                                                <p>nc-bag</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bank"></i>
                                                <p>nc-bank</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-battery-81"></i>
                                                <p>nc-battery-81</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bell-55"></i>
                                                <p>nc-bell-55</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bold"></i>
                                                <p>nc-bold</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bulb-63"></i>
                                                <p>nc-bulb-63</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bullet-list-67"></i>
                                                <p>nc-bullet-list-67</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-bus-front-12"></i>
                                                <p>nc-bus-front-12</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-button-pause"></i>
                                                <p>nc-button-pause</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-button-play"></i>
                                                <p>nc-button-play</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-button-power"></i>
                                                <p>nc-button-power</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-camera-20"></i>
                                                <p>nc-camera-20</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-caps-small"></i>
                                                <p>nc-caps-small</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-cart-simple"></i>
                                                <p>nc-cart-simple</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-cctv"></i>
                                                <p>nc-cctv</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-chart-bar-32"></i>
                                                <p>nc-chart-bar-32</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-chart-pie-35"></i>
                                                <p>nc-chart-pie-35</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-chart-pie-36"></i>
                                                <p>nc-chart-pie-36</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-chart"></i>
                                                <p>nc-chart</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-chat-round"></i>
                                                <p>nc-chat-round</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-check-2"></i>
                                                <p>nc-check-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-circle-09"></i>
                                                <p>nc-circle-09</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-circle"></i>
                                                <p>nc-circle</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-cloud-download-93"></i>
                                                <p>nc-cloud-download-93</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-cloud-upload-94"></i>
                                                <p>nc-cloud-upload-94</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-compass-05"></i>
                                                <p>nc-compass-05</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-controller-modern"></i>
                                                <p>nc-controller-modern</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-credit-card"></i>
                                                <p>nc-credit-card</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-delivery-fast"></i>
                                                <p>nc-delivery-fast</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-email-83"></i>
                                                <p>nc-email-83</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-email-85"></i>
                                                <p>nc-email-85</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-explore-2"></i>
                                                <p>nc-explore-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-fav-remove"></i>
                                                <p>nc-fav-remove</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-favourite-28"></i>
                                                <p>nc-favourite-28</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-globe-2"></i>
                                                <p>nc-globe-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-grid-45"></i>
                                                <p>nc-grid-45</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-headphones-2"></i>
                                                <p>nc-headphones-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-html5"></i>
                                                <p>nc-html5</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-istanbul"></i>
                                                <p>nc-istanbul</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-key-25"></i>
                                                <p>nc-key-25</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-layers-3"></i>
                                                <p>nc-layers-3</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-light-3"></i>
                                                <p>nc-light-3</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-lock-circle-open"></i>
                                                <p>nc-lock-circle-open</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-map-big"></i>
                                                <p>nc-map-big</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-mobile"></i>
                                                <p>nc-mobile</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-money-coins"></i>
                                                <p>nc-money-coins</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-note-03"></i>
                                                <p>nc-note-03</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-notes"></i>
                                                <p>nc-notes</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-notification-70"></i>
                                                <p>nc-notification-70</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-palette"></i>
                                                <p>nc-palette</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-paper-2"></i>
                                                <p>nc-paper-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-pin-3"></i>
                                                <p>nc-pin-3</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-planet"></i>
                                                <p>nc-planet</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-preferences-circle-rotate"></i>
                                                <p>nc-preferences-circle-rotate</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-puzzle-10"></i>
                                                <p>nc-puzzle-10</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-quote"></i>
                                                <p>nc-quote</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-refresh-02"></i>
                                                <p>nc-refresh-02</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-ruler-pencil"></i>
                                                <p>nc-ruler-pencil</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-satisfied"></i>
                                                <p>nc-satisfied</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-scissors"></i>
                                                <p>nc-scissors</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-send"></i>
                                                <p>nc-send</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-settings-90"></i>
                                                <p>nc-settings-90</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-settings-gear-64"></i>
                                                <p>nc-settings-gear-64</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-settings-tool-66"></i>
                                                <p>nc-settings-tool-66</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-simple-add"></i>
                                                <p>nc-simple-add</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-simple-delete"></i>
                                                <p>nc-simple-delete</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-simple-remove"></i>
                                                <p>nc-simple-remove</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-single-02"></i>
                                                <p>nc-single-02</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-single-copy-04"></i>
                                                <p>nc-single-copy-04</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-spaceship"></i>
                                                <p>nc-spaceship</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-square-pin"></i>
                                                <p>nc-square-pin</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-stre-down"></i>
                                                <p>nc-stre-down</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-stre-left"></i>
                                                <p>nc-stre-left</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-stre-right"></i>
                                                <p>nc-stre-right</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-stre-up"></i>
                                                <p>nc-stre-up</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-sun-fog-29"></i>
                                                <p>nc-sun-fog-29</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-support-17"></i>
                                                <p>nc-support-17</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-tablet-2"></i>
                                                <p>nc-tablet-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-tag-content"></i>
                                                <p>nc-tag-content</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-tap-01"></i>
                                                <p>nc-tap-01</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-time-alarm"></i>
                                                <p>nc-time-alarm</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-tv-2"></i>
                                                <p>nc-tv-2</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-umbrella-13"></i>
                                                <p>nc-umbrella-13</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-vector"></i>
                                                <p>nc-vector</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-watch-time"></i>
                                                <p>nc-watch-time</p>
                                            </div>
                                        </div>
                                        <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-6">
                                            <div class="font-icon-detail">
                                                <i class="nc-icon nc-zoom-split"></i>
                                                <p>nc-zoom-split</p>
                                            </div>
                                        </div>
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

</html>
