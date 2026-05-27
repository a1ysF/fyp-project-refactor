<?php
session_start();

include '../connection.php'; // Include your PDO connection script

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = isset($_POST['name']) ? $_POST['name'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($user_name) && !empty($password)) {
        try {
            // Prepare the SQL query to check both username and email
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :name OR email = :name");
            $stmt->bindParam(':name', $user_name); // Bind the same parameter for both username and email
            $stmt->execute();

            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                // Compare passwords (assuming not hashed for demonstration purposes)
                if ($user_data['password'] === $password) {
                    $_SESSION['user_id'] = $user_data['user_id'];

                    // Check if the user is a teacher or a student based on user_id
                    if (strpos($user_data['user_id'], 'T') === 0) {
                        header("Location: ../dashboard/dash_teach/dashboardT.php");
                    } elseif (strpos($user_data['user_id'], 'S') === 0) {
                        header("Location: ../dashboard/dashboardS.php");
                    } else {
                        echo "<script>alert('Invalid user type!'); window.location.href = 'login.php';</script>";
                    }
                    exit;
                } else {
                    echo "<script>alert('Invalid username or password!'); window.location.href = 'login.php';</script>";
                }
            } else {
                echo "<script>alert('User not found!'); window.location.href = 'login.php';</script>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Invalid username or password!'); window.location.href = 'login.php';</script>";
    }
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
    <!-- <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css"> -->

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
            font-family: 'Interval Next', sans-serif;
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
            <!-- <div style="display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="-5.0 -10.0 110.0 135.0" fill="#7871EB" style="margin-right: 10px;">
                    <path d="m51.5 76v14c0 0.82812-0.67188 1.5-1.5 1.5s-1.5-0.67188-1.5-1.5v-14c0-0.82812 0.67188-1.5 1.5-1.5s1.5 0.67188 1.5 1.5zm18.5 6.5h-6.5v-6.5c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm-32-8c-0.82812 0-1.5 0.67188-1.5 1.5v6.5h-6.5c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5zm-14-26h-14c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h14c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm0-12h-6.5v-6.5c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm0 24h-8c-0.82812 0-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-6.5h6.5c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm66-12h-14c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h14c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm-14-9h8c0.82812 0 1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v6.5h-6.5c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5zm8 21h-8c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h6.5v6.5c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5zm-54.5 5.5117v-28.023c0-2.4805 2.0195-4.4883 4.4883-4.4883h2.5117v-11.5c0-7.4414 6.0586-13.5 13.5-13.5s13.5 6.0586 13.5 13.5v11.5h2.5117c2.4805 0 4.4883 2.0195 4.4883 4.4883v28.012c0 2.4805-2.0195 4.4883-4.4883 4.4883h-32.023c-2.4688 0.011719-4.4883-2.0078-4.4883-4.4766zm24.5-13.012c0-2.2109-1.7891-4-4-4s-4 1.7891-4 4c0 1.6797 1.0391 3.1094 2.5 3.6992v3.3008c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-3.3008c1.4609-0.58984 2.5-2.0195 2.5-3.6992zm-14.5-19.5h21v-11.5c0-5.7891-4.7109-10.5-10.5-10.5s-10.5 4.7109-10.5 10.5z"/>
                </svg>
                <span style="font-size: 32px; color: #7871EB; font-weight: bold; font-family: 'Interval Next Black', sans-serif;">CryptoLearn</span>
            </div> -->
            <div class="site-logo mr-auto ">
                <a href="../index.html" style = "color: #7871EB;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="-5.0 -10.0 110.0 135.0" fill="#7871EB" style="margin-right: 10px;">
                        <path d="m51.5 76v14c0 0.82812-0.67188 1.5-1.5 1.5s-1.5-0.67188-1.5-1.5v-14c0-0.82812 0.67188-1.5 1.5-1.5s1.5 0.67188 1.5 1.5zm18.5 6.5h-6.5v-6.5c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm-32-8c-0.82812 0-1.5 0.67188-1.5 1.5v6.5h-6.5c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5zm-14-26h-14c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h14c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm0-12h-6.5v-6.5c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5h8c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm0 24h-8c-0.82812 0-1.5 0.67188-1.5 1.5v8c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-6.5h6.5c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm66-12h-14c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h14c0.82812 0 1.5-0.67188 1.5-1.5s-0.67188-1.5-1.5-1.5zm-14-9h8c0.82812 0 1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5s-1.5 0.67188-1.5 1.5v6.5h-6.5c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5zm8 21h-8c-0.82812 0-1.5 0.67188-1.5 1.5s0.67188 1.5 1.5 1.5h6.5v6.5c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-8c0-0.82812-0.67188-1.5-1.5-1.5zm-54.5 5.5117v-28.023c0-2.4805 2.0195-4.4883 4.4883-4.4883h2.5117v-11.5c0-7.4414 6.0586-13.5 13.5-13.5s13.5 6.0586 13.5 13.5v11.5h2.5117c2.4805 0 4.4883 2.0195 4.4883 4.4883v28.012c0 2.4805-2.0195 4.4883-4.4883 4.4883h-32.023c-2.4688 0.011719-4.4883-2.0078-4.4883-4.4766zm24.5-13.012c0-2.2109-1.7891-4-4-4s-4 1.7891-4 4c0 1.6797 1.0391 3.1094 2.5 3.6992v3.3008c0 0.82812 0.67188 1.5 1.5 1.5s1.5-0.67188 1.5-1.5v-3.3008c1.4609-0.58984 2.5-2.0195 2.5-3.6992zm-14.5-19.5h21v-11.5c0-5.7891-4.7109-10.5-10.5-10.5s-10.5 4.7109-10.5 10.5z"/>
                    </svg>CryptoLearn</a>
            </div>
        </a>
    </div>
        <!-- Sing in  Form -->
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="images/signin-image.jpg" alt="sing up image"></figure>
                        <a href="signup.php?type=student" class="signup-image-link">Create an account</a>
                    </div>

                    <div class="signin-form">
                    <h2 class="form-title">Sign in</h2>
                    <form method="POST" class="register-form" id="login-form">
                        <div class="form-group">
                            <label for="your_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="text" name="name" id="name" placeholder="Your Name or Email"/>
                        </div>
                        <div class="form-group">
                            <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                            <input type="password" name="password" id="password" placeholder="Password"/>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                            <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signin" id="signin" class="form-submit" value="Log in"/>
                        </div>
                    </form>
                        <!-- <div class="social-login">
                            <span class="social-label">Or login with</span>
                            <ul class="socials">
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                            </ul>
                        </div> -->
                    </div>
                </div>
            </div>
        </section>

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


    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>