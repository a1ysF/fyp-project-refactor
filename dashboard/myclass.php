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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="card-title">My Class</h2>
                                    <p class="mb-0">Teachers have access to all of your CryptoLearn data.</p>
                                </div>
                                <p class="mb-0">Your student id is: <?php echo htmlspecialchars($userData['user_id']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <div class="col-md-3 smaller-width">
                                <h6 style="margin-top: 10px" class="align-items-center">Join a class</h6>
                                <button class="btn btn-primary btn-block smaller-width" data-toggle="modal" data-target="#classCodeModal">Enter a class code</button>
                                <!-- <h5 class="mt-4">Add a teacher</h5>
                                <form>
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Email (yourteacher@example.com)">
                                    </div>
                                    <button type="submit" class="btn btn-secondary btn-block">Add a teacher</button>
                                </form> -->
                            </div>
                        </div>
                    </div>
                        <!-- HTML to Display Joined Classes -->
                        <div class="card-body">
                        <h6 style="margin-top: 10px" class="align-items-center">CLASS YOU HAVE JOINED</h6>
                            <div class="row">
                                <?php foreach ($classes_joined as $class): ?>
                                    <div class="col-md-6">
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
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Class Code Entry Modal -->
                        <div class="modal fade" id="classCodeModal" tabindex="-1" role="dialog" aria-labelledby="classCodeModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <!-- Step 1 -->
                                    <div class="modal-body step-1">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="classCodeModalLabel">Enter your class code</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <div class="form-group">
                                                    <label for="classCodeInput" class="sr-only">Class Code</label>
                                                    <input type="text" class="form-control text-center" id="classCodeInput" placeholder="--------" maxlength="8" style="font-family: monospace; letter-spacing: 0.5em;">
                                                </div>
                                                <button type="button" class="btn btn-primary btn-block" id="nextStepButton" disabled>Continue</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="#" class="text-primary" data-dismiss="modal">Back</a>
                                        </div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="modal-body step-2 d-none">
                                        <div class="modal-header">
                                            <h5 class="modal-title">You're joining: <span id="classCodeDisplay"></span></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <button type="button" class="btn btn-primary btn-block" id="joinClassButton">Join This Class <span></span></button>
                                            <button type="button" class="btn btn-secondary btn-block" id="differentClassButton">I'm in a different class</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                            <!-- <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Light Bootstrap Table Heading</h4>
                                    <p class="card-category">Created using Montserrat Font Family</p>
                                </div>
                                <div class="card-body">
                                    <div class="typography-line">
                                        <h1>
                                            <span>Header 1</span>The Life of LB Dashboard </h1>
                                    </div>
                                    <div class="typography-line">
                                        <h2>
                                            <span>Header 2</span>The Life of Light Bootstrap Dashboard </h2>
                                    </div>
                                    <div class="typography-line">
                                        <h3>
                                            <span>Header 3</span>The Life of Light Bootstrap Dashboard </h3>
                                    </div>
                                    <div class="typography-line">
                                        <h4>
                                            <span>Header 4</span>The Life of Light Bootstrap Dashboard </h4>
                                    </div>
                                    <div class="typography-line">
                                        <h5>
                                            <span>Header 5</span>The Life of Light Bootstrap Dashboard </h5>
                                    </div>
                                    <div class="typography-line">
                                        <h6>
                                            <span>Header 6</span>The Life of Light Bootstrap Dashboard </h6>
                                    </div>
                                    <div class="typography-line">
                                        <p>
                                            <span>Paragraph</span>
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus. I think that’s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at.
                                        </p>
                                    </div>
                                    <div class="typography-line">
                                        <span>Quote</span>
                                        <blockquote>
                                            <p class="blockquote blockquote-primary">
                                                "I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus. I think that’s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at."
                                                <br>
                                                <br>
                                                <small>
                                                    - Noaa
                                                </small>
                                            </p>
                                        </blockquote>
                                    </div>
                                    <div class="typography-line">
                                        <span>Muted Text</span>
                                        <p class="text-muted">
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers...
                                        </p>
                                    </div>
                                    <div class="typography-line">
                                        <span>Primary Text</span>
                                        <p class="text-primary">
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers...</p>
                                    </div>
                                    <div class="typography-line">
                                        <span>Info Text</span>
                                        <p class="text-info">
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers... </p>
                                    </div>
                                    <div class="typography-line">
                                        <span>Success Text</span>
                                        <p class="text-success">
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers... </p>
                                    </div>
                                    <div class="typography-line">
                                        <span>Warning Text</span>
                                        <p class="text-warning">
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers...
                                        </p>
                                    </div>
                                    <div class="typography-line">
                                        <span>Danger Text</span>
                                        <p class="text-danger">
                                            I will be the leader of a company that ends up being worth billions of dollars, because I got the answers... </p>
                                    </div>
                                    <div class="typography-line">
                                        <h2>
                                            <span>Small Tag</span>
                                            Header with small subtitle
                                            <br>
                                            <small>Use "small" tag for the headers</small>
                                        </h2>
                                    </div>
                                </div>
                            </div> -->
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
</body>
<!-- Include jQuery, Popper.js, and Bootstrap JS -->
<script src="../assets/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="../assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="../assets/js/core/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/js/plugins/bootstrap-switch.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<script src="../assets/js/plugins/chartist.min.js"></script>
<script src="../assets/js/plugins/bootstrap-notify.js"></script>
<script src="../assets/js/light-bootstrap-dashboard.js?v=2.0.0 " type="text/javascript"></script>
<script src="../assets/js/demo.js"></script>

<!-- Integrated JavaScript with Debugging -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const classCodeInput = document.getElementById('classCodeInput');
    const nextStepButton = document.getElementById('nextStepButton');
    const classCodeDisplay = document.getElementById('classCodeDisplay');
    const classCodeJoinDisplay = document.getElementById('classCodeJoinDisplay');
    const joinClassButton = document.getElementById('joinClassButton');

    classCodeInput.addEventListener('input', function() {
        if (classCodeInput.value.length === 8) {
            nextStepButton.disabled = false;
        } else {
            nextStepButton.disabled = true;
        }
    });

    nextStepButton.addEventListener('click', function() {
        const classCode = classCodeInput.value;
        console.log('Class Code Entered:', classCode); // Debugging
        $.ajax({
            url: 'verify_class_code.php',
            type: 'POST',
            data: { class_code: classCode },
            success: function(response) {
                console.log('AJAX Response:', response); // Debugging
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    console.log('Parsed Result:', response); // Debugging
                    if (response.valid) {
                        classCodeDisplay.textContent = response.class_name;
                        const classCodeSpan = document.createElement('span');
                        classCodeSpan.textContent = classCode;
                        classCodeDisplay.appendChild(document.createElement('br'));
                        classCodeDisplay.appendChild(classCodeSpan);
                        classCodeJoinDisplay.textContent = classCode;
                        document.querySelector('.step-1').classList.add('d-none');
                        document.querySelector('.step-2').classList.remove('d-none');
                    } else {
                        displayInvalidCodeMessage();
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e); // Debugging
                    displayInvalidCodeMessage();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error); // Debugging
                displayInvalidCodeMessage();
            }
        });
    });

    function displayInvalidCodeMessage() {
        let invalidCodeMessage = document.querySelector('.invalid-code-message');
        if (!invalidCodeMessage) {
            invalidCodeMessage = document.createElement('small');
            invalidCodeMessage.textContent = 'Invalid Code';
            invalidCodeMessage.style.color = 'red';
            invalidCodeMessage.classList.add('invalid-code-message', 'd-block', 'text-center', 'mt-2');
            classCodeInput.parentNode.appendChild(invalidCodeMessage);
        }
    }

    document.getElementById('differentClassButton').addEventListener('click', function() {
        classCodeInput.value = '';
        nextStepButton.disabled = true;
        document.querySelector('.step-1').classList.remove('d-none');
        document.querySelector('.step-2').classList.add('d-none');
        const invalidCodeMessage = document.querySelector('.invalid-code-message');
        if (invalidCodeMessage) {
            invalidCodeMessage.remove();
        }
    });

    joinClassButton.addEventListener('click', function() {
        const classCode = classCodeInput.value;
        console.log('Join Class Code:', classCode); // Debugging
        $.ajax({
            url: 'join_class.php',
            type: 'POST',
            data: { class_code: classCode },
            success: function(response) {
                console.log('Join Class Response:', response); // Debugging
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    alert(response.message);
                    if (response.status === 'success') {
                        window.location.reload();
                    } else if (response.status === 'info') {
                        document.querySelector('.step-2').classList.add('d-none');
                        document.querySelector('.step-1').classList.remove('d-none');
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e); // Debugging
                    alert('An error occurred while joining the class.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error); // Debugging
                alert('An error occurred while joining the class.');
            }
        });
    });
});

$(document).ready(function() {
    $('#classCodeInput').on('input', function() {
        var inputVal = $(this).val();
        if (inputVal.length === 8) {
            $('#nextStepButton').prop('disabled', false);
        } else {
            $('#nextStepButton').prop('disabled', true);
        }
    });

    $('#nextStepButton').on('click', function() {
        var classCode = $('#classCodeInput').val();
        console.log('Class Code Entered:', classCode); // Debugging
        $.ajax({
            url: 'verify_class_code.php',
            type: 'POST',
            data: { class_code: classCode },
            success: function(response) {
                console.log('AJAX Response:', response); // Debugging
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    console.log('Parsed Result:', response); // Debugging
                    if (response.valid) {
                        $('#classCodeDisplay').text(response.class_name);
                        const classCodeSpan = $('<span>').text(classCode);
                        $('#classCodeDisplay').append('<br>').append(classCodeSpan);
                        $('#classCodeJoinDisplay').text(classCode);
                        $('.step-1').addClass('d-none');
                        $('.step-2').removeClass('d-none');
                    } else {
                        displayInvalidCodeMessage();
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e); // Debugging
                    displayInvalidCodeMessage();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error); // Debugging
                displayInvalidCodeMessage();
            }
        });
    });

    function displayInvalidCodeMessage() {
        let invalidCodeMessage = document.querySelector('.invalid-code-message');
        if (!invalidCodeMessage) {
            invalidCodeMessage = document.createElement('small');
            invalidCodeMessage.textContent = 'Invalid Code';
            invalidCodeMessage.style.color = 'red';
            invalidCodeMessage.classList.add('invalid-code-message', 'd-block', 'text-center', 'mt-2');
            $('#classCodeInput').parent().append(invalidCodeMessage);
        }
    }

    $('#differentClassButton').on('click', function() {
        $('#classCodeInput').val('');
        $('#nextStepButton').prop('disabled', true);
        $('.step-1').removeClass('d-none');
        $('.step-2').addClass('d-none');
        const invalidCodeMessage = document.querySelector('.invalid-code-message');
        if (invalidCodeMessage) {
            invalidCodeMessage.remove();
        }
    });
});
</script>


</html>
