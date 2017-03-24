<?PHP
date_default_timezone_set('America/Los_Angeles');  // otherwise PHP warnings
include 'mobile_detect/Mobile_Detect.php';
//  require_once '/Users/fredness/howto/public_html/php/test/Mobile-Detect-2.8.17/Mobile_Detect.php';
$detect = new Mobile_Detect;    //  Include and instantiate the class.
include 'mytake/session.php';  //  FUTURE, rename this core.php?
include "config.php";
include "mytake/detect_dev.php";  //  javascript vars some config.php values
include "markdown/Michelf/Markdown.inc.php";
include "util.php";
//include "mail_0.php";
include "mytake/mytake.php";
include "mytake/headfoot.php";
session_detect();
$login_form = 'login disabled';
login_state($login_form);

/*  FUTURE - profile.index uses $user_p, $user_id make the same?  */
$username = session_username_active();   //  FUTURE - many areas below aren't but could use this
$userid =   session_userid_active();

/*  $submitmode needed for post form submit processing  */
/*  FUTURE - make this a util function?  */
if (isset($_POST['submitmode']))
        $submitmode = $_POST['submitmode'];
else if (isset($_GET['submitmode']))
        $submitmode = $_GET['submitmode'];
else $submitmode = NULL;

//  detect what page mode is being invoked, adjust menu highlights accordingly
if (isset($_GET['search']))
	$invoke = 'search';
else if (isset($_GET['providers']))
	$invoke = 'providers';
else if (isset($_GET['dash']))
	$invoke = 'dash';
else if (isset($_GET['aol']))
	$invoke = 'aol';
else if (isset($_GET['art']))
	$invoke = 'art';
else if (isset($_GET['tag']))
	$invoke = 'tag';
else if (isset($_GET['beta']))
	$invoke = 'beta';
else
	$invoke = NULL;

if (isset($_GET['tag']))
	$tag = $_GET['tag'];

if (isset($_GET['signup']) ||
    isset($_GET['artex']) ||  /*  FUTURE - obsolete?  */
    isset($_GET['artexch']) ||
    $invoke) {
	$h0 = '<a class=brand_l href='.$home_url.'>';  $h1 = '</a>';
	}
else {
	$h0 = '';  $h1 = '';
	}

if (isset($_GET['art'])) {
	$art = $_GET['art'];
	$art_rec = acat::article($art);
	}
else {
	$art = NULL;
	$art_rec = NULL;
	}  ?>

<HTML>
<HEAD><?PHP
	head_meta_title();  ?>
<LINK rel="stylesheet" type="text/css" href="/base.css">
<!--  typically only mobile devices recognize device-width, desktops ignore  -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/JavaScript" src="/mytake/mytake.js"></script><?PHP
dev_detect_head_meta();  ?>
<script type="text/JavaScript" src="/base.js"></script> 
</HEAD>

<BODY><?PHP
	head_body(($dflags & DFLAGS_MOBILE) ? 'main_mob' : 'main', true);  /*  Main DIV  */
//	head_body('light', true);  /*  Main DIV  */
	body_lowright();

	if (isset($_GET['m2']))
		body_menu($invoke, $invoke_2 = $_GET['m2']);
	else {
		$invoke_2 = ($invoke != 'dash') ? false : NULL;
		body_menu($invoke, $invoke_2);
		}  ?>

<!--  form submit processing, all pages may need similar code  -->
<!--  edit submit results here  --><?PHP
	/*  Post Submit Processing  */
	/*  FUTURE - this logic (callbacks, inline divs, and all) seems overly complicated  */  ?>
<div id="submit_image_results" style="border: solid; border-color: yellow; position: relative; margin: 0 4px; display: none;">
<button style="margin: 0; position: absolute; top: 8px; right: 8px; display: inline-block;"
  onclick="display_toggle('submit_image_results');">dismiss</button><?PHP
	$t = true;
	if ($submitmode == "lounge" && isset($_POST['fl_submit']))      //  dashboard lounge
	//	handle_edit_submit(lounge_edit_submit);
		handle_edit_submit(array('lounge', 'edit_submit'));     //  AKA lounge::edit_submit
	elseif ($submitmode == "exchcmnt" && isset($_POST['fl_submit']))//  exchange comment
		handle_edit_submit(array('lounge', 'edit_submit'));     //  AKA lounge::edit_submit
	elseif ($submitmode == "artccmnt" && isset($_POST['fl_submit']))//  article comment
		handle_edit_submit(array('lounge', 'edit_submit'));     //  AKA lounge::edit_submit
	elseif ($submitmode == "artexch" && isset($_POST['ax_submit'])) {
		artex::$from = $username;  //  assigning from this way is harder to spoof
		handle_edit_submit(array('artex', 'edit_submit'));      //  AKA artex::edit_submit
		}
	elseif ($submitmode == "upload" && isset($_POST['submit_gallery'])) {
		echo 'submit_gallery';
		handle_edit_submit(array('gallery', 'edit_submit'));    //  AKA gallery::edit_submit
		}
	elseif (($is = isset($_GET['friends'])) && isset($_GET['accept'])) {
		echo "\nResult: ";
		friends_update(FRIENDS_UPDATE_ACCEPT, session_username_active(), $_GET['accept']);
		}
	elseif ( $is                            && isset($_GET['invite'])) {
		echo "\nResult: ";
		friends_update(FRIENDS_UPDATE_INVITE, session_username_active(), $_GET['invite']);
		}
	elseif ( $is                            && isset($_GET['forget'])) {
		echo "\nResult: ";
		friends_update(FRIENDS_UPDATE_FORGET, session_username_active(), $_GET['forget']);
		}
	elseif ( $is                            && isset($_GET['snub'])) {
		echo "\nResult: ";
		friends_update(FRIENDS_UPDATE_SNUB,   session_username_active(), $_GET['snub']);
		}
	/*  Symposium / follow actions  */
	elseif ( $is                            && isset($_GET['follow'])) {
		echo "\nResult: ";
		exchange::following_update(EXCH_FOLLOW,
		  session_username_active(), session_userid_active(), $_GET['follow']);
		}
	elseif ( $is                            && isset($_GET['unfollow'])) {
		echo "\nResult: ";
		exchange::following_update(EXCH_UNFOLLOW,
		  session_username_active(), session_userid_active(), $_GET['unfollow']);
		}
	/*  Practioner Recommend actions  */
	elseif ( $is                            && isset($_GET['accept-rec'])) {
		echo "\nResult: ";
		endorse::update(endorse::ACCEPT, $username, $userid, $_GET['accept-rec']);
		}
	elseif ( $is                            && isset($_GET['invite-rec'])) {
		echo "\nResult: ";
		endorse::update(endorse::INVITE, $username, $userid, $_GET['invite-rec']);
		}
	elseif ( $is                            && isset($_GET['drop-rec'])) {
		echo "\nResult: ";
		endorse::update(endorse::DROP, $username, $userid, $_GET['drop-rec']);
		}
	elseif ( $is                            && isset($_GET['forget-rec'])) {
		echo "\nResult: ";
		endorse::update(endorse::FORGET, $username, $userid, $_GET['forget-rec']);
		}
	elseif ( $is                            && isset($_GET['snub-rec'])) {
		echo "\nResult: ";
		endorse::update(endorse::SNUB,  $username, $userid, $_GET['snub-rec']);
		}
	/*  ...  */
	elseif (isset($_GET['aol']) && isset($_GET['add'])) {
		pivot::aol_update_sub(session_username_active(), 'add', $_GET['add']);
		}
	elseif (isset($_GET['aol']) && isset($_GET['drop'])) {
		pivot::aol_update_sub(session_username_active(), 'drop', $_GET['drop']);
		}
	else {
		$t = false;
		}
	if ($t)
		echo "\n<script>\ndisplay_toggle('submit_image_results');\n</script>\n";  ?>
</div>
<!--  edit submit results here [end]  -->
<!--  form submit processing, all pages may need similar code [end]  -->

<?PHP
//	$username = session_username_active();

//	$need_beta_code = FALSE;
//	if ($beta === TRUE) {
//		echo "\n<br>BETA CHECK";
//		if ($username) {
//			echo "\n<br>Login detected: you may proceed";
//			if (!isset($_SESSION['beta']))
//				$_SESSION['beta'] = TRUE;
//			}
//		elseif (isset($_SESSION['beta']))
//			echo "\n<br>Not logged in, but beta session detected: you may proceed";
//		else {
//			echo "\n<br>Not logged in, no beta session detected: no soup for you";
//			$need_beta_code = TRUE;
//			}
//		}
//	$need_beta_code = beta_check($username);

	/*  === MODE: PROMPT FOR BETA CODE ===  */
	if (beta_check($username)) {
		include 'email/beta.php';
		include 'panel/beta_static.php';
		}

	/*  === MODE: SPECIFIC TAG/PIVOT DISPLAY ===  */
	elseif (isset($tag)) {  
		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include 'panel/pivot_static.php';
		}

	/*  === MODE: PROVIDERS ===  */
	else if (isset($_GET['providers'])) {
		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include 'panel/providers_static.php';
		}

	/*  === MODE: SEARCH ===  */
	else if (isset($_GET['search'])) {
		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include 'panel/search_static.php';
		}

	/*  === MODE: FRIENDS ===  */
	else if (($username) && isset($_GET['friends'])) {
		echo "\n<div style=\"margin: 0 4px 4px 4px; font-size: larger;\">";
		section_head(NULL, NULL, '<p style="margin-top: 0; font-weight: lighter;">Check which friends can see your lounge posts.&nbsp; See which authors you are following, and more!</p>');
		echo '</div>';

		echo "\n\n<div class=exch_float>\n<!--  friends  -->";
		echo "\nFellow Sticers [ dbg: ";
		echo "\n<a href=\"".$edit_urla."friends\">friends</a> |";
		echo "\n<a href=\"".$edit_urla."invites\">invites</a> ]\n<br>";
		friends_list('friends', $username);
		echo "\n<div style=\"clear: both;\"></div>";
		echo "\n<div>Pending Friend Requests \n<br>";
		friends_list('invites', $username);  echo '</div>';
		echo "\n<!--  friends [end]  -->\n</div>";

		echo "\n\n<div class=exch_float>\n<!--  following  -->";
		echo "\nFollowing ";
		echo "[ <a href=\"".$edit_urla."following\">debug</a> ]\n<br>";
//		exchange::dlist_sub($username);
		exchange::list_sub($username);
		echo "\n<!--  following [end]  -->\n</div>";
//		echo "\n<div style=\"clear: both;\">EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE</div>";
		}

	/*  === MODE: AREAS OF LIFE ===  */
	else if (isset($_GET['aol'])) {
		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include 'panel/aol_static.php';
		}

	/*  === MODE: BRAND TEST DISPLAY ===  */
	else if (isset($_GET['brand'])) {
		//  special test page, show all brand options fron config
		//  FUTURE - remove this?
		$i = 0;
		echo "<div style=\"background-color: brown;\">";
		foreach($brand_ab as $b) {
			if ($i) echo "\n<br>";
			echo "\n<div style=\"display: inline-block;\">".$i++.": </div><div style=\"display: inline-block;\">".$b."</div>";
			}
		echo "</div>";
		$i = 0;
		foreach($brand_ab as $b) {
			if ($i) echo "\n<br>";
			echo "\n<div style=\"display: inline-block;\">".$i++.": </div><div style=\"display: inline-block;\">".$b."</div>";
			}
		}

	/*  === MODE: ARTEX ===  */
	elseif (session_userid_active() && isset($_GET['artex'])) {
		artex::$signup_form = true;
		if (artex::$signup_form)
			artex::form('art',  isset($_GET['art']) ? $_GET['art'] : NULL);
		}

	elseif (session_userid_active() && isset($_GET['artexch'])) {
		artex::$signup_form = true;
		if (artex::$signup_form)
			artex::form('exch', isset($_GET['exch']) ? $_GET['exch'] : NULL);
		}

	elseif (session_userid_active() && isset($_GET['artwall'])) {
		artex::$signup_form = true;
		if (artex::$signup_form)
			artex::form('wall', isset($_GET['wall']) ? $_GET['wall'] : NULL);
		}

	elseif (session_userid_active() && isset($_GET['artpivt'])) {
		artex::$signup_form = true;
		if (artex::$signup_form)
			artex::form('pivt', isset($_GET['pivt']) ? $_GET['pivt'] : NULL);
		}
	elseif (($debug_mask & DEBUG_MSG_DRCT) && session_userid_active() && isset($_GET['artex-msg'])) {
		artex::$signup_form = true;  //  FUTURE - why set this then compare it?
		if (artex::$signup_form)
			artex::form('pmsg', isset($_GET['pmsg']) ? $_GET['pmsg'] : NULL);
		//	artex::form('pmsg', isset($_GET['to']) ? $_GET['to'] : NULL);
		}

	/*  === MODE: ARTICLE DISPLAY ===  */
	else if (isset($art) && isset($art_rec)) {
		vvv::view($art, 'view', $ct);  //  this triggers view increment
		$art_rec['ct'] = $ct;
//	ap($art_rec);
		artex::article_out($art_rec);
		}

	/*  === MODE: SIGNUP ===  */
	else if (isset($_GET['signup'])) {
		/*  using file_get_contents() purposely insures PHP commands in file are ignored???  */
		$m = file_get_contents($data_dir.'/is/profile');

		echo "\n<div style=\"margin: 0 4px 4px 4px; font-size: larger;\">";
		section_head(NULL, NULL, $m);
		echo '</div>';

		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include 'email/activation.php';
		include 'panel/signup_static.php';
		}

	/*  === MODE: DASHBOARD ===  */
	else if (isset($_GET['dash'])) {
		switch ($invoke_2) {
		  case 'mc':
			include 'panel/content_static.php';
			break;
		  case 'mf':
			include 'prof.php';  //  only to obtain const values?
			include 'panel/connections_static.php';
			break;
		  case 'ms':
			include 'panel/messages_static.php';
			break;
		  case 'ad':
			include 'panel/admin_static.php';
			break;
		  default:
			//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
			include 'panel/dashboard_static.php';
			}
		}

	/*  === MODE: HOME (default) ===  */
	else {  /*  --- MAIN SUMMARY DISPLAY ---  */
		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include 'panel_home_static.php';
		}  /*  --- MAIN SUMMARY DISPLAY [end] ---  */  ?>

<!--  keep long run inline content from bleeding below footer  -->
<div style="clear: both;"></div>
</div><!--  Main DIV [end]  -->

<?PHP
	include 'panel/footer_static.php';
	if (isset($keys['ga'])) {  ?>
<script type="text/JavaScript" src="/ga.js"></script>
	<?PHP  }  ?>
</BODY>
</HTML>

