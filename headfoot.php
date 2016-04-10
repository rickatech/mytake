<?PHP
function head_meta_title() {
	global $art_rec;
	global $brand_t;

	echo "<TITLE>";
	$prefix = session_username_active() ? $prefix = session_username_active()." - " : '';
        echo is_null($art_rec) ? $prefix.$brand_t : $brand_t."- ".$art_rec['caption'][0];
	echo "</TITLE>";
	}

function head_body($class = NULL, $pos = NULL) {
	global $notes;
	global $login_form;
	global $h0, $h1;
	global $dflags, $debug_mask;
	global $brand_l;

	$dc = $class ? ' class='.$class : '';
	if (!$pos) {  //  FUTURE - some of below should be a shared function?
		echo "\n<div id=\"login_up\" style=\"position: absolute; right: 0px; top: 0; text-align: right;\">TEST</div>";
		//  position brand and login above / outside of main body div
		echo "<div style=\"float: right; text-align: right;\"><span>".$login_form."</span>";
	        echo "</div>";
	        echo "\n".$h0.$brand_l.$h1;
		}
	echo "\n<div".$dc." id='main_div'";
        if (!$dflags & DFLAGS_MOBILE) {  // i.e. not mobile
		echo "\n  style=\"position: relative; width: ".DESK_WIDTH_MIN."; background: #FFFFFF; border: solid; margin: auto;\">";
        } else {
		echo "\n  style=\"position: relative; background: #FFFFFF; border: solid;\">";
        	}
	if ($pos) {  //  FUTURE - some of below should be a shared function?
		echo "\n<div id=\"login_up\" style=\"position: absolute; right: 0px; top: 0; margin: 4px; text-align: right;\">".$login_form."</div>";
		//  position brand and login above inside, top of main body div
        	echo "\n<div style=\"padding-top: 8px; padding-left: 4px;\">".$h0.$brand_l.$h1."</div>";
		}
	}

function body_lowright () {
	global $version;
	/*  this shows logged in user's thumbnail image and link to their profile
	/*  FUTURE: customize this to be a seperate 'plug-in' PHP file
	/*         to allow this placement/layout to be customized   */
	if (0) {
	echo "<div style=\"position: absolute; right: 0px; bottom: -1px;\"><a";
	echo "\n  href=/rickatech/><img SRC=https://images.zaptech.com/rickatech/dukeanomx.gif";
	echo "\n  BORDER=0 ALT=\"rickatech\"></a></div>";
		}
	echo "\n<div class=version style=\"position: absolute; right: 0px; bottom: 0;\">";
	echo $version."&nbsp;</div>";
	}	

function foot() {
	global $foot, $notes, $debug_mask;

	echo "<div style=\"text-align: center; font-family: sans-serif; margin-top: 0.5em;\">".$foot.'</div>'; 
	if ($debug_mask & DEBUG_DISPSIZE)
		echo "\n<div style=\"position: fixed; bottom: 0; right: 0; background-color: white; text-align: right; font-size: smaller;\">".$notes."<span id='mt_msg'></span></div>";
	}
?>
