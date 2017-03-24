<?PHP
//  typically this is a staticly include into index.php
//  used to populate 'signup' portion of home page

namespace MyTake;

function mail_activation($a) {
	//  CITATION: http://php.net/manual/en/function.mail.php
	global $actv_url;

	$to  = $a['email'];
	$subject = 'mytake - account activation';

	// message
	$message = "<html>
<head>
<title>mytake - account activation</title>
</head>
<body>
<p>mytake - account activation</p>

<p style=\"text-align: center;\">Activation code
<br><span style=\"font-size: larger; font-weight: bold; font-family: sans-serif;\">".$a['act_code']."</span></p>
<p>[ <a href=".$actv_url."/profile/?signup&activate&code=".$a['act_code']."&un=".$a['handl'].">click</a> ] to activate your account.</p>

</body>
</html>";
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	// Additional headers
	//  $headers .= 'From: No Reply <noreply@holistik.org>' . "\r\n";
	//  $headers .= 'Reply-To: No Reply <noreply@mytake.zaptech.com>' . "\r\n";
	$headers .= 'Reply-To: No Reply <mytake@zaptech.com>' . "\r\n";
	mail($to, $subject, $message, $headers);
}

