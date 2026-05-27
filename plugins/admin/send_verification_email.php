<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/autoload.php'; // Include Composer's autoloader

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alfatehyusof@gmail.com'; // Your Gmail address
        $mail->Password = 'bjnr ixhn yftw lihs'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('alfatehyusof@gmail.com', 'CryptoLearn - Support');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify your email address';

        // HTML Email Body
        $mail->Body = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    padding: 20px 0;
                }
                .header img {
                    width: 50px;
                }
                .content {
                    text-align: center;
                    padding: 20px;
                }
                .content h2 {
                    font-size: 20px;
                    margin-bottom: 20px;
                }
                    .content h1 {
                    font-size: 25px;
                    margin-bottom: 20px;
                }
                .content p {
                    font-size: 16px;
                    margin-bottom: 20px;
                }
                .content a {
                    display: inline-block;
                    background-color: #4292DC;
                    color: #ffffff;
                    padding: 10px 20px;
                    border-radius: 5px;
                    text-decoration: none;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    font-size: 12px;
                    color: #777777;
                }
                .footer a {
                    color: #007bff;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>CryptoLearn Verification</h1>
                </div>
                <div class="content">
                    <h2>Confirm it\'s you</h2>
                    <p>Thanks for using CryptoLearn. To confirm it\'s you, please verify your email.</p>
                    <a href="http://localhost/example1/login_signup/verify.php?token=' . $token . '">Verify your email</a>
                </div>
                <div class="footer">
                    <p>What\'s CryptoLearn?</p>
                    <p>CryptoLearn is a safe way to learn and trade cryptocurrencies. Learn more.</p>
                    <p>Need support? <a href="mailto:support@cryptolearn.com">Email us</a> or <a href="https://cryptolearn.com/support">visit our website</a>.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Example usage
$email = 'user@example.com';
$token = bin2hex(random_bytes(16)); // Example token generation
sendVerificationEmail($email, $token);
?>
