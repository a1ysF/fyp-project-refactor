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
// Start the session
session_start();

// Include necessary files
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

// Fetch user_id from session
$user_id = $_SESSION['user_id'];

// Check if the required parameters are present in the URL
if (isset($_GET['class_id']) && isset($_GET['user_id'])) {
    // Fetch the class_id and user_id from the URL parameters
    $class_id = $_GET['class_id'];
    $teacher_id = $_GET['user_id'];

    // Fetch classes created by the teacher
    $stmt = $conn->prepare("SELECT * FROM class WHERE teacher_id = :teacher_id AND class_id = :class_id");
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Function to count modules (total modules uploaded by the teacher)
    function countModules($conn, $teacher_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM materials WHERE uploader_id = :teacher_id AND material_id LIKE 'L%'");
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Function to count students in a class
    function countStudents($conn, $class_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM class_users cu JOIN class c ON cu.class_id = c.class_id WHERE c.class_id = :class_id");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Fetch students, their classes, and their records for the specific class_id
    $stmt = $conn->prepare("
        SELECT u.user_id, u.name as student_name, u.username, GROUP_CONCAT(DISTINCT c.class_name SEPARATOR ', ') as class_names,
               r.material_id, r.score_percentage, r.created_at, COUNT(r.material_id) as attempts
        FROM users u
        INNER JOIN class_users cu ON u.user_id = cu.user_id
        INNER JOIN class c ON cu.class_id = c.class_id
        LEFT JOIN records r ON u.user_id = r.user_id
        WHERE c.teacher_id = :teacher_id AND c.class_id = :class_id
        GROUP BY u.user_id, r.material_id
        ORDER BY u.user_id, r.created_at DESC
    ");
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();
    $students_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch students in the specific class for badges
    $stmt = $conn->prepare("SELECT u.user_id, u.name as student_name, u.username
                            FROM users u
                            INNER JOIN class_users cu ON u.user_id = cu.user_id
                            WHERE cu.class_id = :class_id");
    $stmt->bindParam(':class_id', $class_id);
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
} else {
    // Redirect to a default page or show an error if parameters are missing
    header("Location: error_page.php");
    exit();
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
            <div class="sidebar-wrapper">
            <ul class="nav">
                    <li>
                        <a class="nav-link" href="dashboardT.php">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item active">
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

        <!-- Main Content -->
        <div class="main-panel">
            <!-- Header Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs w-100">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" id="classes-link">Classes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="students-link">Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="badges-link">Badges</a>
                        </li>
                    </ul>
                    <!-- Back Button -->
                    <button class="btn btn-secondary ml-auto" onclick="goBack()">Back</button>
                </div>
            </nav>
            <div class="content">
                <div class="content-container">
                    <div class="container-fluid">
                        <!-- material Section -->
                        <div id="classes-section" class="content-section active">
                            <!-- Title and Button
                            <div class="row mb-4">
                                <div class="col-md-12 d-flex justify-content-between align-items-center">
                                    <h6 class="font-weight-bold m-0">Your classes</h6>
                                    <a href="#" class="text-primary" data-toggle="modal" data-target="#newMaterialModal">Add New Class</a>
                                </div>
                            </div>

                            Horizontal line
                            <hr class="w-100">

                            Multi-step Modal
                            <div class="modal fade" id="newMaterialModal" tabindex="-1" role="dialog" aria-labelledby="newMaterialModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form id="newClassForm">
                                            Hidden Inputs
                                            <input type="hidden" id="class_id" name="class_id">
                                            <input type="hidden" id="teacher_id" name="teacher_id" value="<?php echo $userId; ?>">

                                            Step 1
                                            <div class="modal-body step-1">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="newMaterialModalLabel">Add new class</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group mt-2">
                                                        <label for="className">Enter your class name</label>
                                                        <input type="text" class="form-control" id="className" name="className" placeholder="e.g., Cryptography Set 2 2023/2024" maxlength="50" required>
                                                        <small class="form-text text-muted">This class name is what your students will see.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                                                </div>
                                            </div>

                                            Step 2
                                            <div class="modal-body step-2 d-none">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="newMaterialModalLabel">Add new class</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5>Have your students create their own accounts</h5>
                                                    <p>Copy, then email or share this link with your students.</p>
                                                    <div class="form-group">
                                                        <label for="classCode">Class code</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="classCode" name="classCode" readonly>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" id="copyClassCodeBtn">Copy</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p>Or have your students visit <a href="https://www.cryptography.com/join" target="_blank">www.cryptography.com/join</a> and enter your class code <strong id="displayClassCode"></strong>.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" id="prevStep">Back</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> -->

                            <!-- Title and Button -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h2 class="font-weight-bold">Materials</h2>
                                </div>
                            </div>

                            <?php
    // Filter materials uploaded by the current user
    $uploader_id = $_SESSION['user_id'];
    $materials = array_filter($_SESSION['materials'], function($material) use ($uploader_id) {
        return $material['uploader_id'] === $uploader_id;
    });
    ?>

    <?php if (!empty($materials)): ?>
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
            <?php foreach ($materials as $material): ?>
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

                        <!-- Students Section -->
                        <div id="students-section" class="content-section d-none">
                            <h6 class="font-weight-bold">Your students (<?php echo count($students_records); ?>)</h6>
                            <hr class="w-100">
                            <div class="table-responsive table-scroll">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">STUDENT NAME</th>
                                            <th scope="col">USERNAME / EMAIL</th>
                                            <th scope="col">CLASS</th>
                                            <th scope="col">MATERIAL ID</th>
                                            <th scope="col">SCORE (%)</th>
                                            <th scope="col">DATE SUBMITTED</th>
                                            <th scope="col">ATTEMPTS</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students_records as $record): ?>
                                            <tr>
                                                <td><a href="#"><?php echo htmlspecialchars($record['student_name']); ?></a></td>
                                                <td><?php echo htmlspecialchars($record['username']); ?></td>
                                                <td><?php echo htmlspecialchars($record['class_names']); ?></td>
                                                <td><?php echo htmlspecialchars($record['material_id']); ?></td>
                                                <td><?php echo htmlspecialchars($record['score_percentage']); ?></td>
                                                <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($record['attempts']); ?></td>
                                                <td>
                                                    <input type="checkbox">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Students Badges Section -->
                        <div id="badges-section" class="content-section d-none">
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
            <footer class="footer always-bottom">
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

</body>\

<style>
    .table img {
        width: 50px;
        height: 50px;
        margin-right: 5px;
    }
</style>

<style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .main-panel {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .content {
            flex: 1;
            overflow-y: auto;
        }
        .footer {
            flex-shrink: 0;
        }
    </style>

<style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        /* .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        } */
        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
        }
        .always-bottom {
            margin-top: auto;
        }

</style>

<style>
        .nav-tabs .nav-link.active {
    color: #007bff; /* Bootstrap primary color */
    font-weight: bold;
    position: relative;
}

.nav-tabs .nav-link.active::after {
    content: '';
    display: block;
    width: 100%;
    height: 2px;
    background-color: #007bff; /* Bootstrap primary color */
    position: absolute;
    bottom: -4px;
    left: 0;
}

.table-scroll {
    display: block;
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
}

.content-section {
    display: none;
}

.content-section.active {
    display: block;
}

.main-panel {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.content {
    flex: 1;
}

.footer {
    background-color: #f8f9fa;
    padding: 20px 0;
}
    </style>

<style>
    .container {
        max-width: 900px;
        margin: 50px auto;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .class-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 15px;
        background-color: #fff;
    }

    .class-info {
        display: flex;
        align-items: center;
    }

    .class-info img {
        width: 50px;
        height: 50px;
        margin-right: 15px;
        border-radius: 5px;
    }

    .class-details {
        margin-right: 30px;
    }

    .class-details h5 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }

    .class-details p {
        margin: 0;
        font-size: 14px;
    }

    .class-actions {
        display: flex;
        align-items: center;
    }

    .class-actions button {
        margin-right: 10px;
    }

    .btn-primary {
        background-color: #1a73e8;
        border-color: #1a73e8;
    }

    .btn-primary:hover {
        background-color: #1558b0;
        border-color: #1558b0;
    }

    .btn-outline-primary {
        color: #1a73e8;
        border-color: #1a73e8;
    }

    .btn-outline-primary:hover {
    background-color: #1a73e8; /* Blue background color */
    border-color: #1a73e8; /* Optional: you can keep the border color the same as the background color */
}
</style>

<style>
    .promo-card-container {
        background-image: url('img/bg_create_class.jpg'); /* Background image */
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 8px;
        color: #333; /* Text color for better contrast */
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px; /* Adjust based on your design */
    }

    .promo-card {
        background-color: rgba(255, 255, 255, 0.9); /* White background with transparency */
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 100%;
        margin: 20px; /* Add margin to create space inside the container */
    }

    .promo-content {
        max-width: 70%;
    }

    .promo-content h2 {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .promo-content p {
        font-size: 1rem;
        margin-top: 10px;
        margin-bottom: 10px;
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

<!-- jQuery, Popper.js, and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!--  Plugin for Switches -->
<script src="../../assets/js/plugins/bootstrap-switch.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!--  Chartist Plugin  -->
<script src="../../assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../../assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Light Bootstrap Dashboard -->
<script src="../../assets/js/light-bootstrap-dashboard.js?v=2.0.0" type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="../../assets/js/demo.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Dashboard Charts
        demo.initDashboardPageCharts();

        // Function to handle next step in modal
        function handleNextStep() {
            if ($('#className').val() === '') {
                alert('Please enter your class name.');
                return;
            }
            generateUniqueClassIdAndCode();
        }

        // Function to handle previous step in modal
        function handlePrevStep() {
            $('.step-2').addClass('d-none');
            $('.step-1').removeClass('d-none');
        }

        // Event listener for next step button
        $('#nextStep').on('click', function() {
            handleNextStep();
        });

        // Event listener for previous step button
        $('#prevStep').on('click', function() {
            handlePrevStep();
        });

        // Function to copy class code to clipboard
        function copyClassCode() {
            var copyText = document.getElementById("classCode");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            alert("Class code copied: " + copyText.value);
        }

        // Event listener for copy button
        $('#copyClassCodeBtn').on('click', function() {
            copyClassCode();
        });

        // Function to generate class_id
        function generateClassId() {
            var randomNum = Math.floor(1000 + Math.random() * 9000); // Generates a random 4-digit number
            return "C" + randomNum;
        }

        // Function to generate an 8-character uppercase letter class code
        function generateClassCode() {
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            var code = '';
            for (var i = 0; i < 8; i++) {
                code += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return code;
        }

        // Function to check uniqueness of class_id
        function checkClassIdUnique(classId, callback) {
            $.ajax({
                url: 'check_class_id.php',
                type: 'POST',
                data: { class_id: classId },
                dataType: 'json',
                success: function(response) {
                    callback(response.unique);
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' - ' + error);
                }
            });
        }

        // Function to check uniqueness of class_code
        function checkClassCodeUnique(classCode, callback) {
            $.ajax({
                url: 'check_class_code.php',
                type: 'POST',
                data: { class_code: classCode },
                dataType: 'json',
                success: function(response) {
                    callback(response.unique);
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' - ' + error);
                }
            });
        }

        // Function to generate unique class_id and class_code
        function generateUniqueClassIdAndCode() {
            var classId = generateClassId();
            var classCode = generateClassCode();

            checkClassIdUnique(classId, function(isUniqueId) {
                if (isUniqueId) {
                    $('#class_id').val(classId);
                    checkClassCodeUnique(classCode, function(isUniqueCode) {
                        if (isUniqueCode) {
                            $('#classCode').val(classCode);
                            $('#displayClassCode').text(classCode);
                            $('.step-1').addClass('d-none');
                            $('.step-2').removeClass('d-none');
                        } else {
                            generateUniqueClassIdAndCode(); // Retry if class code is not unique
                        }
                    });
                } else {
                    generateUniqueClassIdAndCode(); // Retry if class ID is not unique
                }
            });
        }

        // Set the generated class_id and submit the form when the form is submitted
        $('#newClassForm').on('submit', function(event) {
            event.preventDefault();

            // Ensure unique class_id and class_code are set before submitting
            if ($('#class_id').val() === '' || $('#classCode').val() === '') {
                alert('Class ID and Class Code must be generated first.');
                return;
            }

            // Perform AJAX request to submit form data to crud_class.php
            $.ajax({
                url: 'crud_class.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        // Redirect to myclass_teach.php after a short delay
                        setTimeout(function() {
                            window.location.href = 'myclass_teach.php';
                        }, 2000);
                    } else {
                        alert(response.message);
                    }
                    // Optionally, you can reset the form and modal here
                    $('#newMaterialModal').modal('hide');
                    $('#newClassForm')[0].reset();
                    $('.step-2').addClass('d-none');
                    $('.step-1').removeClass('d-none');
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' - ' + error);
                }
            });
        });
    });
</script>

<!-- JavaScript to Toggle Sections -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Function to handle navigation tab switching
    function handleTabSwitch(sectionId) {
        // Remove active class from all tabs
        document.querySelectorAll('.nav-tabs .nav-link').forEach(function(navLink) {
            navLink.classList.remove('active');
        });

        // Add active class to clicked tab
        document.getElementById(sectionId.split('-')[0] + '-link').classList.add('active');

        // Hide all sections
        document.querySelectorAll('.content-section').forEach(function(contentSection) {
            contentSection.classList.remove('active');
            contentSection.classList.add('d-none');
        });

        // Show the appropriate section
        document.getElementById(sectionId).classList.add('active');
        document.getElementById(sectionId).classList.remove('d-none');
    }

    // Event listeners for navigation tabs
    document.querySelectorAll('.nav-tabs .nav-link').forEach(function(navLink) {
        navLink.addEventListener('click', function() {
            var sectionId = this.id.replace('-link', '-section');
            handleTabSwitch(sectionId);
        });
    });

    // Initialize the first tab as active
    handleTabSwitch("classes-section");
});

// Function to navigate back to the previous page
function goBack() {
    history.back();
}
</script>


</body>

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
