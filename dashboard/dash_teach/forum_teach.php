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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
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
