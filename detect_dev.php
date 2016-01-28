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

function dev_detect_head_meta() {
	global $dflags, $debug_mask;

	echo "\n<script>";
	echo "\nvar desk_width = ".DESK_WIDTH_MIN;
	echo "\nvar desk_width_max = ".DESK_WIDTH_MAX;
	echo "\nvar desk_width_pad = ".DESK_WIDTH_PAD;
	echo "\nvar dflags = ".$dflags;
	echo "\nvar debug_mask = ".$debug_mask;
	echo "\nvar FLAG_MOBILE = 1;";
	echo "\n</script>";
	}

?>
