<?PHP
//  typically this is a staticly include into index.php
//  used to populate 'signup' portion of home page

function mail_passwdchng($a) {
	//  Send email to user that account password has been changed
	//    a   user email to send to
	//  CITATION: http://php.net/manual/en/function.mail.php
	global $actv_url;

//	$to  = $a['email'];
	$to  = $a;
	$subject = 'wholosophy - account password updated';

	// message
	$message = "<html>
<head>
<title>wholosophy - account password updated</title>
</head>
<body>
<p>wholosophy - account password updated</p>

<p>This is courtesy notice that your <a href=".$actv_url.">wholosophy</a> account password has been updated.</p>

</body>
</html>";
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	// Additional headers
	// $headers .= 'To: rickatech@gmail.com, Rick <rick@zaptech.com>' . "\r\n";
	//  $headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
	//  $headers .= 'From: No Reply <noreply@holistik.org>' . "\r\n";
	$headers .= 'Reply-To: No Reply <noreply@wholosophy.org>' . "\r\n";
	//  $headers .= 'Reply-To: info@zaptech.com' . "\r\n" .

	mail($to, $subject, $message, $headers);
	}

