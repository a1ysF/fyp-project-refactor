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

// // Fetch records data based on filter
// $filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
// $filterQuery = '';

// if ($filter != 'All') {
//     $filterQuery = ' AND m.type = :filter';
// }

// $stmt = $conn->prepare("
//     SELECT 
//         c.class_name,
//         r.material_id,
//         m.type AS material_type,
//         r.created_at,
//         r.score_percentage,
//         TIMESTAMPDIFF(MINUTE, r.dstart, r.created_at) AS time_diff
//     FROM records r
//     INNER JOIN materials m ON r.material_id = m.material_id
//     INNER JOIN class_users cu ON r.user_id = cu.user_id
//     INNER JOIN class c ON cu.class_id = c.class_id
//     WHERE r.user_id = :user_id $filterQuery
//     ORDER BY r.created_at DESC
// ");

// $stmt->bindParam(':user_id', $user_id);
// if ($filter != 'All') {
//     $stmt->bindParam(':filter', $filter);
// }
// $stmt->execute();
// $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Initialize total times
// $total_exercise_minutes = 0;
// $total_learning_minutes = 0;

// // Calculate total times
// foreach ($records as $record) {
//     if ($record['material_type'] == 'Learning') {
//         $total_learning_minutes += $record['time_diff'];
//     } else {
//         $total_exercise_minutes += $record['time_diff'];
//     }
// }

// Fetch badges for the user
$badgeStmt = $conn->prepare("
    SELECT b.badge_file
    FROM badge_users bu
    INNER JOIN badges b ON bu.badge_id = b.badge_id
    WHERE bu.user_id = :user_id
");
$badgeStmt->bindParam(':user_id', $user_id);
$badgeStmt->execute();
$user_badges = $badgeStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Fetch rewards for the user
$rewardStmt = $conn->prepare("
    SELECT points, learning, assignment, quiz
    FROM rewards
    WHERE user_id = :user_id
");
$rewardStmt->bindParam(':user_id', $user_id);
$rewardStmt->execute();
$user_rewards = $rewardStmt->fetch(PDO::FETCH_ASSOC) ?: ['points' => 0, 'learning' => 0, 'assignment' => 0, 'quiz' => 0];

// Fetch all badges
$allBadgesStmt = $conn->prepare("SELECT badge_id, badge_file FROM badges");
$allBadgesStmt->execute();
$all_badges = $allBadgesStmt->fetchAll(PDO::FETCH_ASSOC);

$total_materials_completed = $user_rewards['learning'] + $user_rewards['assignment'] + $user_rewards['quiz'];

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
                    <li class="nav-item active">
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

            <div class="content">
                <div class="container-fluid">

                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>MY REWARDS</h5>
                                </div>
                                <div class="card-body">
                                    take from table badge_users
                                    <h5>My Badges</h5>
                                    <div class="showcase">
                                        <div class="badge"><img src="path/to/shield1.png" alt="Shield 1"></div>
                                        <div class="badge"><img src="path/to/shield2.png" alt="Shield 2"></div>
                                        <div class="badge"><img src="path/to/shield3.png" alt="Shield 3"></div>
                                        <div class="badge"><img src="path/to/shield4.png" alt="Shield 4"></div>
                                    </div>
                                    <div class="user-statistics"> 
                                        take from table rewards
                                        <p>Energy points earned: <span class="points">88,075</span></p>
                                        <p>Material completed: 75</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>All Badges<a href="#">View all</a></h6>
                                </div>
                                <div class="card-body">
                                    <div class="badge-counts">
                                        <div class="badge"><img src="path/to/badge1.png" alt="Badge 1"><p>0</p></div>
                                        <div class="badge"><img src="path/to/badge2.png" alt="Badge 2"><p>0</p></div>
                                        <div class="badge"><img src="path/to/badge3.png" alt="Badge 3"><p>0</p></div>
                                        <div class="badge"><img src="path/to/badge4.png" alt="Badge 4"><p>2</p></div>
                                        <div class="badge"><img src="path/to/badge5.png" alt="Badge 5"><p>6</p></div>
                                        <div class="badge"><img src="path/to/badge6.png" alt="Badge 6"><p>46</p></div>
                                    </div>
                                    <div class="text-center">
                                        <a href="#">Check for new badges and avatars</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h2>My Rewards Dashboard</h2>
                            <p>This page displays your rewards, including badges and points earned through various materials.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>MY REWARDS</h5>
            </div>
            <div class="card-body">
                <h5>My Badges</h5>
                <div class="showcase">
                    <?php foreach ($user_badges as $badge): ?>
                        <div class="badge">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($badge['badge_file']); ?>" alt="Badge" onclick="zoomBadge(this)">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="user-statistics"> 
                    <p>Energy points earned: <span class="points"><?php echo $user_rewards['points']; ?></span></p>
                    <p>Materials completed: <?php echo $total_materials_completed; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6>All Badges <a href="#">View all</a></h6>
            </div>
            <div class="card-body">
                <div class="badge-counts">
                    <?php foreach ($all_badges as $badge): ?>
                        <div class="badge">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($badge['badge_file']); ?>" alt="Badge" onclick="zoomBadge(this)">
                            <p>0</p> <!-- Adjust count based on actual data if available -->
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for zooming badge -->
<div id="badgeModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
    function zoomBadge(img) {
        var modal = document.getElementById("badgeModal");
        var modalImg = document.getElementById("modalImage");
        modal.style.display = "block";
        modalImg.src = img.src;
    }

    function closeModal() {
        var modal = document.getElementById("badgeModal");
        modal.style.display = "none";
    }
</script>

<style>
    .badge img {
        cursor: pointer;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 40%;
        max-width: 700px;
    }

    .close {
        position: absolute;
        top: 50px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    @media only screen and (max-width: 700px) {
        .modal-content {
            width: 100%;
        }
    }
</style>


<!-- <script>
document.getElementById('materialFilter').addEventListener('change', function() {
    const materialType = this.value;
    window.location.href = `?filter=${materialType}`;
});
</script> -->

<style>
    .badge img {
    width: 200px; /* Adjust the width as needed */
    height: 200px; /* Adjust the height as needed */
}
    body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    color: #333;
    margin: 0;
    padding: 0;
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
    .card-title {
    font-size: 1.5em; /* Adjust the font size */
}

.filters {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.filters select {
    margin-right: 10px;
}

.stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background-color: #f4f4f4;
}

.fa-play {
    margin-right: 5px;
}

</style>
                    </div>
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
