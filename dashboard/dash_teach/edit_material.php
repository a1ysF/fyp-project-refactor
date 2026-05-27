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

// function getDescById(){
//     $stmt = $this->dbConn->prepare("SELECY * FROM")
// }

$materials = fetchMaterials($conn, $_SESSION['user_id']);

if (isset($_GET['id'])) {
    $material_id = $_GET['id'];

    // Fetch material details from the database using PDO
    $query = "SELECT * FROM materials WHERE material_id = :material_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':material_id', $material_id, PDO::PARAM_STR);
    $stmt->execute();
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$material) {
        echo "Material not found.";
        exit;
    }

    $parent_id = $material['parent_id'];
    // Fetch material details from the database using PDO
    $query = "SELECT * FROM materials WHERE material_id = :parent_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_STR);
    $stmt->execute();
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);


    // $objDesc->setId($_GET[id]);
    // $objDesc->function


} else {
    echo "No material ID provided.";
    exit;
}

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
	<!-- <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css"> -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
    <!-- <script src="ckeditor/ckeditor.js"></script> -->
	<!-- <script src="ckeditor/adapters/jquery.js"></script> -->

    <!-- <script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/34.2.0/"
        }
    }
    </script> -->
    
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
        <div class="future-blobs">
            <div class="blob_2">
            <img src="../../images/blob_2.svg" alt="Blob 2">
            </div>
            <div class="blob_1">
            <img src="../../images/blob_1.svg" alt="Blob 1">
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
        
                    <!-- Card 2: Material Insertion -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title text-center">Edit Material</h4>
                        </div>
                        <div class="card-body">
                            <form id="materialForm" method="POST" action="update_material.php" enctype="multipart/form-data">
                                <input type="hidden" name="material_id" value="<?php echo $material['material_id']; ?>">

                                <!-- Hidden fields for material ID, uploader ID, and dates -->
                                <div class="form-group" style="display:only;">
                                    <label>Material ID (PK)</label>
                                    <input type="text" class="form-control" name="material_id_display" value="<?php echo $material['material_id']; ?>" readonly>
                                </div>
                                <div class="form-group" style="display:only;">
                                    <label>Uploader ID (FK)</label>
                                    <input type="text" class="form-control" name="uploader_id" value="<?php echo $material['uploader_id']; ?>" readonly>
                                </div>
                                <div class="form-group" style="display:only;">
                                    <label>Date Submitted</label>
                                    <input type="text" class="form-control" name="date_submitted" value="<?php echo $material['date_submitted']; ?>" readonly>
                                </div>

                                <!-- Editable fields -->
                                <div class="form-group">
                                    <label>Type</label>
                                    <select id="type" name="type" class="form-control">
                                        <option value="" disabled>Type</option>
                                        <option value="Learning" <?php if ($material['type'] == 'Learning') echo 'selected'; ?>>Learning</option>
                                        <option value="Assignment" <?php if ($material['type'] == 'Assignment') echo 'selected'; ?>>Assignment</option>
                                        <option value="Quiz" <?php if ($material['type'] == 'Quiz') echo 'selected'; ?>>Quiz</option>
                                    </select>
                                </div>

                                <div id="parentIDRow" <?php if ($material['type'] != 'Assignment' && $material['type'] != 'Quiz') echo 'style="display: none;"'; ?>>
                                    <div class="form-group">
                                        <label>Parent Material</label>
                                        <select id="parentID" name="parentID" class="form-control">
                                            <!-- <option value="" disabled selected>Learning Material</option> -->
                                            <?php
                                            echo "<option value=\"" . htmlspecialchars($parent['material_id']) . "\" $selected>" . htmlspecialchars($parent['unit']) . " - " . htmlspecialchars($parent['title']) . "</option>";
                                            if (isset($materials["error"])) {
                                                echo "<option value=\"\" disabled>{$materials['error']}</option>";
                                            } else {
                                                foreach ($materials as $mat) {
                                                    if ($mat['material_id'] != $parent['material_id']) {
                                                        echo "<option value=\"" . htmlspecialchars($mat['material_id']) . "\">" . htmlspecialchars($mat['unit']) . " - " . htmlspecialchars($mat['title']) . "</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                

                                <div class="form-group" style="display: only;">
                                    <label>Unit</label>
                                    <input type="text" id="unit" name="unit" class="form-control" value="<?php echo $material['unit']; ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" class="form-control" name="title" id="title" value="<?php echo $material['title']; ?>" placeholder="Title">
                                </div>

                                <div class="form-group">
                                    <label>Upload Image</label>
                                    <?php
                                    $src = 'uploads/default.jpg';
                                    if ($material['main_img'] !== null) {
                                        $imageData = base64_encode($material['main_img']);
                                        $src = 'data:image/jpeg;base64,' . $imageData;
                                    } else {
                                        # code...
                                    }
                                    ?>
                                    <img src="<?php echo $src; ?>" alt="Material Image" style="display: block; width: 100px; height: 100px; margin-top: 10px;">
                                    <input type="file" class="form-control" id="image" name="image" style="margin-top: 10px;">
                                </div>

                                    <!-- $text = "hello this is randesh"; -->

                                <div class="editor-container editor-container_classic-editor editor-container_include-block-toolbar" id="editor-container">
                                    <div class="editor-container__editor">
                                        <div class="form-group">
                                            <label>Material Description</label>
                                            <textarea class="form-control" name="descriptionEdit" id="descriptionEdit" rows="4" placeholder="Description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>H5P Content URL</label>
                                            <textarea class="form-control" name="fileH5PEdit" id="fileH5PEdit" rows="4" placeholder="H5P Content URL"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Additional Content & URL (Links)</label>
                                            <textarea class="form-control" name="urlEdit" id="urlEdit" rows="4" placeholder="(Optional)"></textarea>
                                        </div>


                                        <script>
                                            function decodeHtmlEntities(str) {
                                                var txt = document.createElement("textarea");
                                                txt.innerHTML = str;
                                                return txt.value;
                                            }

                                            function stripHtmlTags(html) {
                                                var tempDiv = document.createElement("div");
                                                tempDiv.innerHTML = html;
                                                return tempDiv.textContent || tempDiv.innerText || "";
                                            }

                                            ClassicEditor
                                                .create(document.querySelector('#descriptionEdit'))
                                                .then(editor => {
                                                    var dataFromServer = '<?php echo htmlspecialchars($material['description'], ENT_QUOTES, 'UTF-8'); ?>';
                                                    var decodedData = decodeHtmlEntities(dataFromServer);
                                                    var strippedData = stripHtmlTags(decodedData);
                                                    editor.setData(strippedData);
                                                    window.descriptionEditorInstance = editor;
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });

                                            ClassicEditor
                                                .create(document.querySelector('#fileH5PEdit'))
                                                .then(editor => {
                                                    var dataFromServer = '<?php echo htmlspecialchars($material['file_path'], ENT_QUOTES, 'UTF-8'); ?>';
                                                    var decodedData = decodeHtmlEntities(dataFromServer);
                                                    var strippedData = stripHtmlTags(decodedData);
                                                    editor.setData(strippedData);
                                                    window.fileH5PEditorInstance = editor;
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });

                                            ClassicEditor
                                                .create(document.querySelector('#urlEdit'))
                                                .then(editor => {
                                                    var dataFromServer = '<?php echo htmlspecialchars($material['url'], ENT_QUOTES, 'UTF-8'); ?>';
                                                    var decodedData = decodeHtmlEntities(dataFromServer);
                                                    var strippedData = stripHtmlTags(decodedData);
                                                    editor.setData(strippedData);
                                                    window.urlEditorInstance = editor;
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });

                                            // Function to add additional data to CKEditor
                                            function addDataToEditor(editorInstance, newData) {
                                                if (editorInstance) {
                                                    const currentData = editorInstance.getData();
                                                    const updatedData = currentData + newData;
                                                    editorInstance.setData(updatedData);
                                                }
                                            }

                                            // Example usage: adding additional data
                                            document.addEventListener('DOMContentLoaded', () => {
                                                const newData = '<p></p>';
                                                addDataToEditor(window.descriptionEditorInstance, newData);
                                                addDataToEditor(window.fileH5PEditorInstance, newData);
                                                addDataToEditor(window.urlEditorInstance, newData);
                                            });
                                        </script>

                                        <!-- <div class="form-group">
                                            <label>URL</label>
                                            <input type="text" class="form-control" name="url" id="url" value="<?php echo $material['url']; ?>" placeholder="URL">
                                        </div> -->
                                    </div>
                                </div>

                                <input type="submit" name="updateMaterial" class="btn btn-info btn-fill pull-right" value="Update Material">
                                <div class="clearfix"></div>
                            </form>
                            <iframe name="materialFrame" id="materialFrame" style="width:100%; height:0; border:none; display:none;"></iframe>
                        </div>

                        <!-- <script>src="insert_func.js"</script> temporary-->
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

<!-- <script>
    CKEDITOR.replace('description');
    CKEDITOR.replace('fileH5P');
    CKEDITOR.replace('url');
</script> -->

<!-- <script>
        $(function() {
            // Bootstrap
         

            // Ckeditor standard
            $( 'textarea#fileH5P' ).ckeditor({width:'98%', height: '150px', toolbar: [
				{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
				[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],			// Defines toolbar group without name.
				{ name: 'basicstyles', items: [ 'Bold', 'Italic' ] }
			]});
            $( 'textarea#fileH5P' ).ckeditor({width:'98%', height: '150px'});
        });

        </script> -->

<!-- <script type="importmap">
{
    "imports": {
        "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.js",
        "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.0/"
    }
}
</script>
<script type="module" src="../main.js"></script> -->

    <!-- <script type="module">
        import ClassicEditor from 'ckeditor5/build/ckeditor';

        import {
            AccessibilityHelp,
            Alignment,
            Autoformat,
            AutoImage,
            AutoLink,
            Autosave,
            BalloonToolbar,
            Bold,
            Code,
            CodeBlock,
            Essentials,
            FontBackgroundColor,
            FontColor,
            FontFamily,
            FontSize,
            FullPage,
            GeneralHtmlSupport,
            Heading,
            Highlight,
            HorizontalLine,
            ImageBlock,
            ImageCaption,
            ImageInline,
            ImageInsert,
            ImageInsertViaUrl,
            ImageResize,
            ImageStyle,
            ImageTextAlternative,
            ImageToolbar,
            ImageUpload,
            Indent,
            IndentBlock,
            Italic,
            Link,
            LinkImage,
            List,
            ListProperties,
            Markdown,
            MediaEmbed,
            PageBreak,
            Paragraph,
            PasteFromMarkdownExperimental,
            PasteFromOffice,
            RemoveFormat,
            SelectAll,
            Base64UploadAdapter,
            SourceEditing,
            SpecialCharacters,
            SpecialCharactersArrows,
            SpecialCharactersCurrency,
            SpecialCharactersEssentials,
            SpecialCharactersLatin,
            SpecialCharactersMathematical,
            SpecialCharactersText,
            Strikethrough,
            Table,
            TableCaption,
            TableCellProperties,
            TableColumnResize,
            TableProperties,
            TableToolbar,
            TextTransformation,
            Underline,
            Undo
        } from 'ckeditor5';

        const editorConfig = {
            toolbar: {
                items: [
                    'undo',
                    'redo',
                    '|',
                    'sourceEditing',
                    'selectAll',
                    '|',
                    'heading',
                    '|',
                    'fontSize',
                    'fontFamily',
                    'fontColor',
                    'fontBackgroundColor',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    'code',
                    'removeFormat',
                    '|',
                    'specialCharacters',
                    'horizontalLine',
                    'pageBreak',
                    'link',
                    'insertImage',
                    'mediaEmbed',
                    'insertTable',
                    'highlight',
                    'codeBlock',
                    '|',
                    'alignment',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'indent',
                    'outdent',
                    '|',
                    'accessibilityHelp'
                ],
                shouldNotGroupWhenFull: false
            },
            plugins: [
                AccessibilityHelp,
                Alignment,
                Autoformat,
                AutoImage,
                AutoLink,
                Autosave,
                BalloonToolbar,
                Bold,
                Code,
                CodeBlock,
                Essentials,
                FontBackgroundColor,
                FontColor,
                FontFamily,
                FontSize,
                FullPage,
                GeneralHtmlSupport,
                Heading,
                Highlight,
                HorizontalLine,
                ImageBlock,
                ImageCaption,
                ImageInline,
                ImageInsert,
                ImageInsertViaUrl,
                ImageResize,
                ImageStyle,
                ImageTextAlternative,
                ImageToolbar,
                ImageUpload,
                Indent,
                IndentBlock,
                Italic,
                Link,
                LinkImage,
                List,
                ListProperties,
                Markdown,
                MediaEmbed,
                PageBreak,
                Paragraph,
                PasteFromMarkdownExperimental,
                PasteFromOffice,
                RemoveFormat,
                SelectAll,
                Base64UploadAdapter,
                SourceEditing,
                SpecialCharacters,
                SpecialCharactersArrows,
                SpecialCharactersCurrency,
                SpecialCharactersEssentials,
                SpecialCharactersLatin,
                SpecialCharactersMathematical,
                SpecialCharactersText,
                Strikethrough,
                Table,
                TableCaption,
                TableCellProperties,
                TableColumnResize,
                TableProperties,
                TableToolbar,
                TextTransformation,
                Underline,
                Undo
            ],
            balloonToolbar: ['bold', 'italic', '|', 'link', 'insertImage', '|', 'bulletedList', 'numberedList'],
            fontFamily: {
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            heading: {
                options: [
                    {
                        model: 'paragraph',
                        title: 'Paragraph',
                        class: 'ck-heading_paragraph'
                    },
                    {
                        model: 'heading1',
                        view: 'h1',
                        title: 'Heading 1',
                        class: 'ck-heading_heading1'
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'Heading 2',
                        class: 'ck-heading_heading2'
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'Heading 3',
                        class: 'ck-heading_heading3'
                    },
                    {
                        model: 'heading4',
                        view: 'h4',
                        title: 'Heading 4',
                        class: 'ck-heading_heading4'
                    },
                    {
                        model: 'heading5',
                        view: 'h5',
                        title: 'Heading 5',
                        class: 'ck-heading_heading5'
                    },
                    {
                        model: 'heading6',
                        view: 'h6',
                        title: 'Heading 6',
                        class: 'ck-heading_heading6'
                    }
                ]
            },
            htmlSupport: {
                allow: [
                    {
                        name: /^.*$/,
                        styles: true,
                        attributes: true,
                        classes: true
                    }
                ]
            },
            image: {
                toolbar: [
                    'toggleImageCaption',
                    'imageTextAlternative',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:wrapText',
                    'imageStyle:breakText',
                    '|',
                    'resizeImage'
                ]
            },
            initialData: '',
            link: {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                decorators: {
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            placeholder: '',
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            ClassicEditor
                .create(document.querySelector('#description'), editorConfig)
                .then(editor => {
                    // Set initial data
                    editor.setData('<p>Initial content</p>');

                    // Save the editor instance to a global variable
                    window.editorInstance = editor;
                })
                .catch(error => {
                    console.error(error);
                });

            // Function to add additional data to CKEditor
            window.addDataToEditor = function(newData) {
                if (window.editorInstance) {
                    const currentData = window.editorInstance.getData();
                    const updatedData = currentData + newData;
                    window.editorInstance.setData(updatedData);
                }
            };

            // Example usage: adding additional data
            const newData = '<p>Additional content</p>';
            window.addDataToEditor(newData);
        });
    </script> -->


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
