<?PHP
const DFLAGS_MOBILE =     1;

// Any mobile device (phones or tablets).
if ($detect->isMobile()) {
	$notes = "isMobile";  $dflags = DFLAGS_MOBILE;
	// Any tablet device.
	if (!$detect->isTablet())
		$notes .= ", notTablet"; 
	// Exclude tablets.
	else
		$notes .= ", isTablet"; 
	}
else {
	$notes = "isDesktop";  $dflags = 0;
	}
 
// Check for a specific platform with the help of the magic methods:
if($detect->isiOS())
	$notes .= ", is_iOS";
else if($detect->isAndroidOS())
	$notes .= ", isAndroid"; 
else
	$notes .= ", isOther"; 
?>
