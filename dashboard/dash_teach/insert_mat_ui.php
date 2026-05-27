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
 error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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

// include 'fetch_materials.php';
include 'insert_mat.php';
// include '../../script.php';

// echo "Script is running";
function fetchMaterials($conn, $uploaderID) {
    try {
        // Prepare the SQL query to fetch materials of type "Learning"
        $sql = "SELECT material_id, unit, title FROM materials WHERE uploader_id = :uploaderID AND type = :type";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uploaderID', $uploaderID);
        $stmt->bindParam(':type', $type);
        
        $type = 'Learning'; // We're fetching materials of type "Learning"
        
        $stmt->execute();

        // Fetch all materials
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ["error" => "Error: " . $e->getMessage()];
    } catch (Exception $e) {
        return ["error" => "General Error: " . $e->getMessage()];
    }
}

$materials = fetchMaterials($conn, $_SESSION['user_id']);

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="path/to/bootstrap/css/bootstrap.min.css">

    <!-- <script src="https://cdn.ckeditor.com/4.24.0-lts/standard/ckeditor.js"></script> -->
    <!-- <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script> -->
    <link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css">
    
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
        <!-- <div class="main-panel"> -->
            
            <div class="content">
                <div class="container-fluid">


                        <!-- Back Button -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <a href="material_teach.php" class="btn btn-secondary" style="margin-top: 10px;">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>

                    <!-- <textarea name="editor1" id="editor1" rows="10" cols="80">
                    This is my textarea to be replaced with CKEditor 4.
                    </textarea>
                    <script>
                            CKEDITOR.replace( 'editor1' );
                    </script> -->

                    <!-- <div class="col-md-8"> -->
        
                    <!-- Card 2: Material Insertion -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title text-center">Insert Material</h4>
                        </div>
                        <div class="card-body">
                            <form id="materialForm" method="POST" action="insert_mat.php" enctype="multipart/form-data">
                                <!-- Form fields for material insertion -->
                                <div class="form-group" style="display:none;">
                                    <label>Material ID (PK)</label>
                                    <input type="text" class="form-control" name="materialID" placeholder="Material ID" id="materialID">
                                </div>
                                <div class="form-group" style="display:none;">
                                    <label>Uploader ID (FK)</label>
                                    <input type="text" class="form-control" name="uploaderID" value="<?php echo $userId; ?>" placeholder="Uploader ID">
                                </div>
                                <!-- Hidden inputs for dateSubmitted and dateEdited -->
                                <div class="form-group" style="display:none;">
                                    <label>Date Submitted</label>
                                    <input type="date" class="form-control" name="dateSubmitted" placeholder="Date Submitted" id="dateSubmitted">
                                </div>
                                <div class="form-group" style="display:none;">
                                    <label>Date Edited</label>
                                    <input type="date" class="form-control" name="dateEdited" placeholder="Date Edited" id="dateEdited">
                                </div>
                                <!-- Other form fields -->
                                <div class="form-group">
                                    <label>Type</label>
                                    <select id="type" name="type" class="form-control">
                                        <option value="" disabled selected>Type</option>
                                        <option value="Learning">Learning</option>
                                        <option value="Assignment">Assignment</option>
                                        <option value="Quiz">Quiz</option>
                                    </select>
                                </div>
                                <div id="parentIDRow" style="display: none;">
                                    <!-- <div class="col-md-5 pr-1"> -->
                                        <div class="form-group">
                                            <label>Parent Material</label>
                                            <select id="parentID" name="parentID" class="form-control">
                                                <option value="" disabled selected>Learning Material</option>
                                                <?php
                                                if (isset($materials["error"])) {
                                                    echo "<option value=\"\" disabled>{$materials['error']}</option>";
                                                } else {
                                                    foreach ($materials as $material) {
                                                        echo "<option value=\"" . htmlspecialchars($material['material_id']) . "\">" . htmlspecialchars($material['unit']) . " - " . htmlspecialchars($material['title']) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <!-- </div> -->
                                </div>
                                <div class="form-group" style="display: none;">
                                    <label>Unit</label>
                                    <input type="text" id="unit" name="unit" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Upload Image</label>
                                    <input type="file" class="form-control" id="image" name="image" value="">
                                </div>
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" class="form-control" name="title" id="title" placeholder="Title">
                                </div>
                                <div class="editor-container editor-container_classic-editor editor-container_include-block-toolbar" id="editor-container">
                                    <div class="editor-container__editor">
                                    <div class="form-group">
                                            <label>Material Description</label>
                                            <textarea class="form-control" name="description" id="description" rows="4" placeholder="Description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>H5P Content URL</label>
                                            <textarea class="form-control" name="fileH5P" id="fileH5P" rows="4" placeholder="H5P Content URL"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Additional Content & URL (Links)</label>
                                            <textarea class="form-control" name="url" id="url" rows="4" placeholder="(Optional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="submit" name="insertMaterial" class="btn btn-info btn-fill pull-right" value="Insert Material" />
                                <div class="clearfix"></div>
                            </form>
                            <iframe name="materialFrame" id="materialFrame" style="width:100%; height:0; border:none; display:none;"></iframe>
                        </div>

                        <script>
                            document.getElementById('materialForm').addEventListener('submit', async function(event) {
                                event.preventDefault();

                                const typeSelect = document.getElementById('type').value;
                                // const dateSubmittedInput = document.getElementById('dateSubmitted').value;
                                // const dateEditedInput = document.getElementById('dateEdited').value;

                                const selectedType = typeSelect;

                                let prefix = '';

                                switch (selectedType) {
                                    case 'Learning':
                                        prefix = 'L';
                                        break;
                                    case 'Assignment':
                                        prefix = 'A';
                                        break;
                                    case 'Quiz':
                                        prefix = 'Q';
                                        break;
                                    default:
                                        prefix = '';
                                }

                                if (prefix) {
                                    const materialIDInput = await generateUniqueMaterialId(prefix);
                                    document.getElementById('materialID').value = materialIDInput;
                                }

                                // Set the current date and time for dateSubmitted and dateEdited
                                // const currentDateTime = new Date().toISOString();
                                // document.getElementById('dateSubmitted').value = currentDateTime;
                                // document.getElementById('dateEdited').value = currentDateTime;

                                document.getElementById('materialForm').submit();
                            });

                            async function checkMaterialId(materialId) {
                                const response = await fetch('check_material_id.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ material_id: materialId })
                                });
                                const data = await response.json();
                                return data.exists;
                            }

                            async function generateUniqueMaterialId(prefix) {
                                let materialID = '';
                                let exists = true;

                                while (exists) {
                                    const randomNumber = Math.floor(1000 + Math.random() * 9000); // Generates a random 4-digit number
                                    materialID = `${prefix}${randomNumber}`;
                                    exists = await checkMaterialId(materialID);
                                }

                                return materialID;
                            }
                        </script>
                    </div>
                    <!-- </div> -->


                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('#type').change(function () {
                                var type = $(this).val();
                                if (type == 'Assignment' || type == 'Quiz') {
                                    $('#parentIDRow').show();
                                    $('#unit').val('');
                                } else if (type == 'Learning') {
                                    $('#parentIDRow').hide();
                                    $.ajax({
                                        url: 'fetch_highest_unit.php',
                                        type: 'POST',
                                        data: { type: type },
                                        success: function (response) {
                                            var highestUnit = parseFloat(response);
                                            var newUnit = highestUnit + 1;
                                            $('#unit').val(newUnit);
                                        }
                                    });
                                    $('#parentID').val('none'); // Set parentID to none for Learning
                                } else {
                                    $('#parentIDRow').hide();
                                    $('#unit').val('');
                                    $('#parentID').val('none'); // Set parentID to none for other types
                                }
                            });

                            $('#parentID').change(function () {
                                var parentID = $(this).val();
                                if (parentID) {
                                    $.ajax({
                                        url: 'fetch_highest_unit.php',
                                        type: 'POST',
                                        data: { parentID: parentID },
                                        success: function (response) {
                                            var highestUnit = parseFloat(response);
                                            if (isNaN(highestUnit)) {
                                                var newUnit = $('#parentID option:selected').text().split(' ')[0] + ".1";
                                            } else {
                                                var parentUnit = $('#parentID option:selected').text().split(' ')[0];
                                                var newUnit = parentUnit + "." + (highestUnit + 1);
                                            }
                                            $('#unit').val(newUnit);
                                        }
                                    });
                                } else {
                                    $('#unit').val('');
                                }
                            });
                        });
                    </script>
                    
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
        <!-- </div> -->
    </div>
</body>

<script type="importmap">
{
    "imports": {
        "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.js",
        "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.1/"
    }
}
</script>
<script type="module" src="../main.js"></script>


<!-- <script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.0/"
        }
    }
</script>

<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Font
    } from 'ckeditor5';

    ClassicEditor
        .create( document.querySelector( '#combinedContent' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
            ]
        } )
        .then( editor => {
            window.editor = editor;
        } )
        .catch( error => {
            console.error( error );
        } );

        ClassicEditor
        .create( document.querySelector( '#fileH5P' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
            ]
        } )
        .then( editor => {
            window.editor = editor;
        } )
        .catch( error => {
            console.error( error );
        } );

        ClassicEditor
        .create( document.querySelector( '#url' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
            ]
        } )
        .then( editor => {
            window.editor = editor;
        } )
        .catch( error => {
            console.error( error );
        } );
</script> -->

<!-- Initialize CKEditor -->
<!-- <script>
    CKEDITOR.replace('combinedContent');
    CKEDITOR.replace('fileH5P');
    CKEDITOR.replace('url');
</script>

<style>
    .main-container {
        width: 795px;
        margin-left: auto;
        margin-right: auto;
    }
</style> -->


<!--   Core JS Files   -->
<script src="path/to/jquery/jquery.min.js"></script>
<script src="path/to/bootstrap/js/bootstrap.min.js"></script>
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
