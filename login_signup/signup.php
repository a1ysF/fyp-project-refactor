<?php

// Start session to store session variables
session_start();

// Include PDO connection
include '../connection.php';
require '../plugins/admin/phpmailer/vendor/autoload.php'; // Include Composer's autoloader
include '../plugins/admin/send_verification_email.php'; // Include the sendVerificationEmail function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_id = $_POST['user_id'];
    $verify = 0; // Default to not verified
    $token = bin2hex(random_bytes(16)); // Generate a random token

    try {
        if (isset($_POST['age'])) {
            // Student Signup
            $age = intval($_POST['age']);
            $user_type = 'student';

            $sql = "INSERT INTO users (user_id, name, username, email, password, age, user_type, verify, token) VALUES (:user_id, :name, :username, :email, :password, :age, :user_type, :verify, :token)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':user_type', $user_type);
            $stmt->bindParam(':verify', $verify);
            $stmt->bindParam(':token', $token);
        } else {
            // Teacher Signup
            $user_type = 'teacher';

            $sql = "INSERT INTO users (user_id, name, username, email, password, user_type, verify, token) VALUES (:user_id, :name, :username, :email, :password, :user_type, :verify, :token)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':user_type', $user_type);
            $stmt->bindParam(':verify', $verify);
            $stmt->bindParam(':token', $token);
        }

        $stmt->execute();
        echo "New user registered successfully!";
        $_SESSION['user_id'] = $user_id;

        // Handle sending verification email
        handleVerificationEmail($email, $user_id);

        // Redirect user based on type
        if (strpos($user_id, 'T') === 0) {
            header("Location: ../dashboard/dash_teach/dashboardT.php");
        } elseif (strpos($user_id, 'S') === 0) {
            header("Location: ../dashboard/dashboardS.php");
        } else {
            echo "Invalid user type!";
        }
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to handle sending verification email
function handleVerificationEmail($email, $userId) {
    global $conn; // Use the global $conn variable

    $token = bin2hex(random_bytes(16)); // Generate a new token

    // Update the token in the database
    $sql = "UPDATE users SET token = :token WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // Send the verification email
    sendVerificationEmail($email, $token);
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CryptoLearn</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Favicon -->
    <link href="../images/img2-logo.png" rel="icon">
    
    
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="fonts copy/icomoon/style.css">

    <link rel="stylesheet" href="css copy/bootstrap.min.css">
    <link rel="stylesheet" href="css copy/jquery-ui.css">
    <link rel="stylesheet" href="css copy/owl.carousel.min.css">
    <link rel="stylesheet" href="css copy/owl.theme.default.min.css">
    <link rel="stylesheet" href="css copy/owl.theme.default.min.css">

    <link rel="stylesheet" href="css copy/jquery.fancybox.min.css">

    <link rel="stylesheet" href="css copy/bootstrap-datepicker.css">

    <link rel="stylesheet" href="fonts copy/flaticon/font/flaticon.css">

    <link rel="stylesheet" href="css copy/aos.css">

    <link rel="stylesheet" href="css copy/style.css">

    <link href="https://fonts.googleapis.com/css2?family=Interval+Next:wght@400;700&display=swap" rel="stylesheet">
    <!-- <style>
        /* Add the Interval Next font to the relevant elements */
        .site-logo {
            font-family: 'Interval Next Black', sans-serif;
        }
        .site-logo .brand-name {
            font-size: 32px;
            color: #7871EB;
            font-weight: bold;
        }
    </style> -->
</head>
<body>

    <div class="future-blobs">
        <div class="blob_2">
          <img src="images/blob_2.svg" alt="Blob 2">
        </div>
        <div class="blob_1">
          <img src="images/blob_1.svg" alt="Blob 1">
        </div>
    </div>

    <div class="site-logo mr-auto w-100 text-center">
        <a href="../index.html" style="text-decoration: none; display: inline-block;">
            <div class="site-logo mr-auto">
                <a href="../index.html" style = "color: #7871EB;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="-5.0 -10.0 110.0 135.0" fill="#7871EB" style="margin-right: 10px;">
                        <path d="m51.5 76v14c0 0.82812-0.67188 1.5-1.5 1.5s-1.5-0.67188-1.5-1.5v-14c0-0.82812 0.67188-1.5 1.5-1.5s1.5 0.67188 1.5 1.5zm18.5 6.5h-6.5v-6.5c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm-32-8c-0.82812 0-1.5 0.67188-1.5 1.5v6.5h-6.5c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5zm-14-26h-14c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h14c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm0-12h-6.5v-6.5c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm0 24h-8c-0.82812 0-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-6.5h6.5c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm66-12h-14c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h14c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm-14-9h8c0.82812 0 1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v6.5h-6.5c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5zm8 21h-8c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h6.5v6.5c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5zm-54.5 5.5117v-28.023c0-2.4805 2.0195-4.4883 4.4883-4.4883h2.5117v-11.5c0-7.4414 6.0586-13.5 13.5-13.5s13.5 6.0586 13.5 13.5v11.5h2.5117c2.4805 0 4.4883 2.0195 4.4883 4.4883v28.012c0 2.4805-2.0195 4.4883-4.4883 4.4883h-32.023c-2.4688 0.011719-4.4883-2.0078-4.4883-4.4766zm24.5-13.012c0-2.2109-1.7891-4-4-4s-4 1.7891-4 4c0 1.6797 1.0391 3.1094 2.5 3.6992v3.3008c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-3.3008c1.4609-0.58984 2.5-2.0195 2.5-3.6992zm-14.5-19.5h21v-11.5c0-5.7891-4.7109-10.5-10.5-10.5s-10.5 4.7109-10.5 10.5z"/>
                    </svg>CryptoLearn</a>
            </div>
        </a>
    </div>

    
        <!-- Sign up form -->
    <section class="signup">
        <div class="container">


            <div class="signup-buttons text-center mb-4"> 
                    
            </div>

            <div class="signup-buttons text-center mb-4">
                
            </div>


            <div class="signup-buttons text-center mb-4">
                <!-- Buttons for Student and Teacher -->
                <nav class="site-navigation position-relative" role="navigation">
                    <ul class="site-menu main-menu site-menu-dark js-clone-nav mr-auto d-lg-flex m-0 p-0">
                        <li class="cta mb-2">
                            <a href="#" class="btn btn-primary py-2 px-10 student-button" id="studentBtn">Student</a>
                        </li>
                        &nbsp;
                        <li class="cta">
                            <a href="#" class="btn btn-primary py-2 px-10 teacher-button" id="teacherBtn">Teacher</a>
                        </li>
                    </ul>
                </nav>
                <!-- <a href="#" class="d-inline-block d-lg-none site-menu-toggle js-menu-toggle text-black float-right"><span class="icon-menu h3"></span></a> -->
            </div>


            <div class="row">
                <div class="owl-carousel col-12 nonloop-block-14">
                    <!-- Student Signup Form -->
                    <div class="signup-content">
                        <div class="signup-form" id="student-signup-form">
                            <h2 class="form-title">Student Sign up</h2>
                            <!-- Date of Birth Selectors -->
                            <div class="dob-container">
                                <div class="dob-item">
                                    <div class="dropdown-wrapper">
                                        <select id="student-day" name="day" class="dob-dropdown">
                                            <option value="">Day</option>
                                            <script>
                                                for (let i = 1; i <= 31; i++) {
                                                    document.write('<option value="' + i + '">' + i + '</option>');
                                                }
                                            </script>
                                        </select>
                                    </div>
                                </div>
                                <div class="dob-item">
                                    <div class="dropdown-wrapper">
                                        <select id="student-month" name="month" class="dob-dropdown">
                                            <option value="">Month</option>
                                            <option value="1">Jan</option>
                                            <option value="2">Feb</option>
                                            <option value="3">Mar</option>
                                            <option value="4">Apr</option>
                                            <option value="5">May</option>
                                            <option value="6">Jun</option>
                                            <option value="7">Jul</option>
                                            <option value="8">Aug</option>
                                            <option value="9">Sept</option>
                                            <option value="10">Oct</option>
                                            <option value="11">Nov</option>
                                            <option value="12">Dec</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="dob-item">
                                    <div class="dropdown-wrapper">
                                        <select id="student-year" name="year" class="dob-dropdown">
                                            <option value="">Year</option>
                                            <script>
                                                const currentYear = new Date().getFullYear();
                                                for (let i = currentYear; i >= 1900; i--) {
                                                    document.write('<option value="' + i + '">' + i + '</option>');
                                                }
                                            </script>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" class="register-form" id="student-form" action="signup.php">
                                <div class="form-group">
                                    <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                    <input type="text" name="name" id="name" placeholder="Your Name" required />
                                </div>
                                <div class="form-group">
                                    <label for="username"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                    <input type="text" name="username" id="username" placeholder="Your Username" required />
                                </div>
                                <div class="form-group">
                                    <label for="email"><i class="zmdi zmdi-email"></i></label>
                                    <input type="email" name="email" id="student-email" placeholder="Your Email" required />
                                </div>
                                <div class="form-group">
                                    <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                    <input type="password" name="password" id="student-password" placeholder="Password" required />
                                </div>
                                <div class="form-group">
                                    <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                    <input type="password" name="re_pass" id="student-re_pass" placeholder="Repeat your password" required />
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="agree-term" id="student-agree-term" class="agree-term" required />
                                    <label for="student-agree-term" class="label-agree-term">
                                        <span><span></span></span>
                                        I agree all statements in <a href="#" class="term-service">Terms of Service</a>
                                    </label>
                                </div>
                                <div class="form-group form-button">
                                    <input type="submit" name="signup" id="student-signup" class="form-submit" value="Register" />
                                </div>
                                <input type="hidden" name="user_id" id="student-user_id">
                                <input type="hidden" name="age" id="student-age">
                            </form>
                        </div>
                        <div class="signup-image">
                            <figure><img src="images/signup-image.jpg" alt="sign up image"></figure>
                            <a href="login.php" class="signup-image-link">I am already a member</a>
                            &nbsp;
                            &nbsp;
                            &nbsp;
                        </div>
                    </div>




                    <!-- Teacher Signup Form -->
                    <div class="signup-content">
                        <div class="signup-form" id="teacher-signup-form">
                            <h2 class="form-title">Teacher Sign up</h2>
                            <form method="POST" class="register-form" id="teacher-form" action="signup.php">
                                <div class="form-group">
                                    <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                    <input type="text" name="name" id="name" placeholder="Your Name" required />
                                </div>
                                <div class="form-group">
                                    <label for="username"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                    <input type="text" name="username" id="username" placeholder="Your Username" required />
                                </div>
                                <div class="form-group">
                                    <label for="email"><i class="zmdi zmdi-email"></i></label>
                                    <input type="email" name="email" id="teacher-email" placeholder="Your Email" required />
                                </div>
                                <div class="form-group">
                                    <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                    <input type="password" name="password" id="teacher-password" placeholder="Password" required />
                                </div>
                                <div class="form-group">
                                    <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                    <input type="password" name="re_pass" id="teacher-re_pass" placeholder="Repeat your password" required />
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="agree-term" id="teacher-agree-term" class="agree-term" required />
                                    <label for="teacher-agree-term" class="label-agree-term">
                                        <span><span></span></span>
                                        I agree all statements in <a href="#" class="term-service">Terms of Service</a>
                                    </label>
                                </div>
                                <div class="form-group form-button">
                                    <input type="submit" name="signup" id="teacher-signup" class="form-submit" value="Register" />
                                </div>
                                <input type="hidden" name="user_id" id="teacher-user_id">
                            </form>
                        </div>
                        <div class="signup-image">
                            <figure><img src="images/signup-image.jpg" alt="sign up image"></figure>
                            <a href="login.php" class="signup-image-link">I am already a member</a>
                            &nbsp;
                            &nbsp;
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- Include JavaScript files -->
<script src="student_signup.js"></script>
<script src="teacher_signup.js"></script>

<!-- Student || Teacher Form Slide Function -->
<script>
    $(document).ready(function() {
    var owl = $('.owl-carousel');

    owl.owlCarousel({
        items: 1,
        loop: false,
        margin: 10,
        nav: true,
        dots: false,
        autoHeight: true,
        responsiveClass: true
    });

    var urlParams = new URLSearchParams(window.location.search);
    var type = urlParams.get('type');

    if (type === 'student') {
        showStudentForm();
        owl.trigger('to.owl.carousel', [0, 300]);
        $('.student-button').addClass('active');
        $('.teacher-button').removeClass('active');
    } else if (type === 'teacher') {
        owl.trigger('to.owl.carousel', [1, 300]);
        $('.teacher-button').addClass('active');
        $('.student-button').removeClass('active');
    }

    $('.student-button').click(function(e) {
        e.preventDefault();
        showStudentForm();
        owl.trigger('to.owl.carousel', [0, 300]);
        $('.student-button').addClass('active');
        $('.teacher-button').removeClass('active');
    });

    $('.teacher-button').click(function(e) {
        e.preventDefault();
        hideStudentForm();
        owl.trigger('to.owl.carousel', [1, 300]);
        $('.teacher-button').addClass('active');
        $('.student-button').removeClass('active');
    });

    // Function to show student form
    function showStudentForm() {
        $('.dob-container').show();
        $('#student-form').hide();

        if ($('#student-day').val() !== '' && $('student-#month').val() !== '' && $('#student-year').val() !== '') {
        $('#student-form').show();
        }
        else {
            $('#student-form').hide();
        }


        $('.dob-dropdown').off('change').on('change', function() {
            if ($('#student-day').val() && $('#student-month').val() && $('#student-year').val()) {
                $('#student-form').show();
            }
            else {
                $('#student-form').hide();
            }
        });
    }

    // Function to hide student form
    function hideStudentForm() {
        $('.dob-container').hide();
        $('#student-form').hide();
        $('#teacher-form').show();
    }
});
</script>


    </div>

    <style>

        
    .student-button {
        color: #7871EB !important;
        background-color: white !important;
        border: 2px solid #7871EB !important; /* Adding outline */
    }
    .student-button:hover,
    .student-button.active {
        color: white !important;
        background-color: #7871EB !important;
        border-color: #7871EB !important;
    }

    .teacher-button {
        color: #7871EB !important;
        background-color: white !important;
        border: 2px solid #7871EB !important; /* Adding outline */
    }
    .teacher-button:hover,
    .teacher-button.active {
        color: white !important;
        background-color: #7871EB !important;
        border-color: #7871EB !important;
    }


    @media (max-width: 991.98px) {
        .btn {
            display: flex;
            flex-direction: row; /* Align buttons horizontally */
            justify-content: center; /* Center buttons horizontally */
            margin-bottom: 20px; /* Adjust margin bottom as needed */
        }
        
        .btn .site-menu li {
            margin: 0 10px; /* Add space between buttons */
        }
    }

    .signup-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end; /* Aligns buttons to the bottom */
        height: 100%; /* Ensures buttons take full height */
    }

    /* .signup-buttons .site-menu {
        margin-bottom: 0; /* Remove default margin */
        /*display: flex; /* Ensures flex display for the menu */
        /*flex-direction: column; /* Align menu items vertically */
        /*align-items: center; /* Center align the menu items */
    /* }  */

    .signup-buttons .site-menu li {
        list-style: none; /* Removes default list styling */
        margin: 0px 0; /* Add space between buttons vertically */
    }

    .signup-buttons .site-menu li.cta {
        margin-bottom: 0px; /* Remove margin-bottom override */
    }

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

    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>