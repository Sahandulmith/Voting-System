<?php
	session_start();
	include 'includes/conn.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';

	if(isset($_POST['login'])){
		$voter = $_POST['voter'];
		$password = $_POST['password'];

		$sql = "SELECT * FROM voters WHERE voters_id = '$voter'";
		$query = $conn->query($sql);

		if($query->num_rows < 1){
			$_SESSION['error'] = 'Cannot find voter with the ID';
		}
		else{
			$row = $query->fetch_assoc();
			if(password_verify($password, $row['password'])){
				$_SESSION['voter'] = $row['id'];

				// Get voter's info
				$email = $row['email'];
				$firstname = $row['firstname'];  // Assuming there are 'firstname' and 'lastname' columns
				$lastname = $row['lastname'];
				
				// Generate current login time
				$login_time = date('Y-m-d H:i:s');  // Current datetime
				
				// Prepare the PHPMailer
				$mail = new PHPMailer(true);
				try {
					// Server settings
					$mail->isSMTP();
					$mail->Host = 'smtp.gmail.com';
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
					$mail->Subject = 'Login Notification';
					$mail->Body    = "Hello $firstname $lastname,<br><br>You have successfully logged into your account on <b>$login_time</b>.<br><br>Best regards,<br>Voting System Team";
					$mail->AltBody = "Hello $firstname $lastname,\n\nYou have successfully logged into your account on $login_time.\n\nBest regards,\nVoting System Team";
					
					// Send the email
					$mail->send();
					$_SESSION['success'] = 'Login successful and email sent';
				} catch (Exception $e) {
					$_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
				}
			}
			else{
				$_SESSION['error'] = 'Incorrect password';
			}
		}
	}
	else{
		$_SESSION['error'] = 'Input voter credentials first';
	}

	header('location: index.php');
?>
