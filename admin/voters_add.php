<?php
include 'includes/session.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['add'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $plaintext_password = $_POST['password'];  // Keep plaintext password for sending email
    $hashed_password = password_hash($plaintext_password, PASSWORD_DEFAULT);
    $filename = $_FILES['photo']['name'];
    
    if(!empty($filename)){
        move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);    
    }
    
    // Generate voter ID
    $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $voter = substr(str_shuffle($set), 0, 15);

    // Insert into database
    $sql = "INSERT INTO voters (voters_id, password, firstname, lastname, photo,email) VALUES ('$voter', '$hashed_password', '$firstname', '$lastname', '$filename', '$email')";
    if($conn->query($sql)){
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'sahanresturantandcafe@gmail.com';  // SMTP username
            $mail->Password = 'hclk qdaj inxz cqkr';  // SMTP password (App password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom('no-reply@yourdomain.com', 'Voting System');
            $mail->addAddress($email);  // Voter's email
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Your Voter ID and Password';
            $mail->Body    = "Hello $firstname $lastname,<br><br>Your Voter ID: <b>$voter</b><br>Your Password: <b>$plaintext_password</b><br><br>Please keep this information safe.";
            $mail->AltBody = "Hello $firstname $lastname,\n\nYour Voter ID: $voter\nYour Password: $plaintext_password\n\nPlease keep this information safe.";
            
            $mail->send();
            $_SESSION['success'] = 'Voter added successfully and email sent';
        } catch (Exception $e) {
            $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = $conn->error;
    }
} else {
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: voters.php');
?>
