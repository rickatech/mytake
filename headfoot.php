<?PHP
function head_meta() {
	global $art_rec;
	global $brand_t;

	echo "<TITLE>";
	$prefix = session_username_active() ? $prefix = session_username_active()." - " : '';
        echo is_null($art_rec) ? $prefix.$brand_t : $brand_t."- ".$art_rec['caption'][0];
	echo "</TITLE>";
	}

function head_body() {
	global $notes;
	global $login_form;
	global $h0, $h1;
	global $dflags, $debug_mask;
	global $brand_l;

        echo "<div style=\"float: right; text-align: right;\"><span>".$login_form."</span>";
	if ($debug_mask & 1)
        	echo "\n<span>".$notes."</span> <span id='mt_msg'></span>";
        echo "</div>";
        echo "\n".$h0.$brand_l.$h1;
        if (!$dflags & DFLAGS_MOBILE) {  // i.e. not mobile
		echo "\n<div id='main_div'";
		echo "\n  style=\"position: relative; width: 1024px;; background: #FFFFFF; border: solid; margin: auto;\">";
        } else {
		echo "\n<div id='main_div'";
		echo "\n  style=\"position: relative; background: #FFFFFF; border: solid;\">";
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
	echo "<div style=\"text-align: center; font-family: sans-serif;\"><br>about | join | promotions | careers</div>"; 
	}
?>
