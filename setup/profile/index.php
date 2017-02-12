<?PHP
date_default_timezone_set('America/Los_Angeles');  // otherwise PHP warnings - FUTURE, move to config.php?
include_once '../autoloader.php';
include '../mobile_detect/Mobile_Detect.php';
//  require_once '/Users/fredness/howto/public_html/php/test/Mobile-Detect-2.8.17/Mobile_Detect.php';
$detect = new Mobile_Detect;    //  Include and instantiate the class.
include '../mytake/session.php';  //  FUTURE, rename this core.php?
include "../config.php";
include "../mytake/detect_dev.php";  //  javascript vars some config.php values
include "../markdown/Michelf/Markdown.inc.php";
include "../util.php";
include '../local.php';  //  FUTURE - move this to config.php ?
include "../prof.php";
include "../mytake/mytake.php";
include "../mytake/headfoot.php";
session_detect();
$login_form = 'login disabled';

	/*  === MODE: SIGNUP ===  YYYY  */
	if (isset($_GET['signup'])) {
		ob_start();
		//  FUTURE - place this in a hidden div, use AJAX to pull dynamically
		include '../email/activation.php';
		include '../panel/signup_static.php';
		$out1 = ob_get_contents();
		ob_end_clean();
		}
	else $out1 = '';

login_state($login_form);

$h0 = '<a class=brand_l href='.$home_url.'>';  $h1 = '</a>';  //  main brand link to home

/*  $submitmode needed for post form submit processing  */
/*  FUTURE - make this a util function?  */
if (isset($_POST['submitmode']))
        $submitmode = $_POST['submitmode'];
else if (isset($_GET['submitmode']))
        $submitmode = $_GET['submitmode'];
else $submitmode = NULL;

//  detect what page mode is being invoked, adjust menu highlights accordingly
//if (isset($_GET['search']))
	$invoke = 'profile';

$user_p = session_username_active();  //  FUTURE - many areas below aren't but could use this
$user_id = session_userid_active();

if (isset($_GET['public']))
	$public = $_GET['public'];  //  show public facing profile information
else
	$public = NULL;

//include '../local.php';  //  FUTURE - move this to config.php ?
//include "../prof.php";  /*  class profile [end]  */  ?>

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
//	head_body('light', true);  /*  Main DIV  */

	head_body(($dflags & DFLAGS_MOBILE) ? 'main_mob' : 'main', true);  /*  Main DIV  */
//	head_body('main', true);  /*  Main DIV  */
	body_lowright();  /*  version absoulte positioning  */

	//  notification icons - test pattern for now
//	echo "\n<div style=\"float: right;\">&odash; &#9899; &cir;</div>";
	echo "\n<div style=\"float: right;\">&odash; &#9679; &cir;</div>";
	//  output menus
	//  FUTURE, insert a fold that cuts cleanly beneath both left logo/branch and right login/profile cluster
	body_menu($invoke);

	//  FUTURE - move into commong function for index.php, .../profile/ to call?
	$dbout = "\n<p style=\"margin: 0; font-size: smaller;\">";
	if (is_null($user_p)) {
		$dbout .= "no user logged in"; 
		$dbout .= (is_null($public) ? " - not sure what information to show" : " - showing public profile of ".$public);
		if ($public) $profile = $public; else $profile = NULL;
		}
	else {
		$dbout .= "User: ".$user_p." is logged in";
		$dbout .= " - showing ".(is_null($public) ? "private profile" : "public profile of <b>".$public).'</b>';
		if ($public) $profile = $public; else $profile = $user_p;
		}
	$dbout .= " </p>";

if (1) {

	//  normal login sets username before even checking it is okay,
	//  then if checks out, uid and flags sessions are set
	//		$_SESSION['username_dg'] = $_GET['username_dg'];
	//function login_check($u, $p) {  //  YYYY

	if (is_null($user_p) && is_null($public)) {
		echo "\n<div style=\"display: table; height: 10em; margin: 0 auto;\">";
		if (isset($_GET['signup']))
			//  get here if wrong activation code entered during signup
			echo $out1;  //  
		else
		echo "\n<p style=\"vertical-align: middle; display: table-cell;\">Nothing to see here, please sign in.</p></div>";
		//  CITATION: http://stackoverflow.com/questions/1900017/is-goto-in-php-evil
		goto abort;
		}
	}

	if (beta_check($username)) {  //  ZZZZ
		echo "\n<br><a href=/>beta information</a>";
		//  echo "\n<br>[ beta code | email submit ]";
		}
else {

	//  XXXX - likely most rest request will need to re-use the following
if (1) {
//	profile::$uid = $user_id;
	account_glbs($user_id, $public);
	$p_flgs = profile::$uf;
	$p_fnam = profile::$ufn;
	$p_mail = profile::$umail;
	}
else {
	$file_profiles = $data_dir.'/'.$login_data;
	$p_fnam = 'N/A';
	$p_flgs = 0;
	$p_mail = 'N/A';
	$users = array();
	//  FUTURE - for nosql, nomemcache this can be slow reading in every account,
	//           instead add new method that reads file line by line, stops reading when account found
	if (account::get($file_profiles, $users, ($public) ? NULL : array($user_id))) {  
		if (is_null($public)) {
			$p_fnam = $users[$user_id]['fnam'];
			if (!is_null($users[$user_id]['flgs'])) $p_flgs = $users[$user_id]['flgs'];
			$p_mail = $users[$user_id]['mail'];
			}
		else {
			//  FUTURE - this won't scale unless get parameter public also includes UID
			//  FUTURE - redis / memcache to the rescue?
			foreach ($users as $k => $v) {
				//  hunt down familiar name from profile catalog
				if ($v['handle'] == $public) {
					$p_fnam = $v['fnam'];
					if (!is_null($v['flgs'])) $p_flgs = $v['flgs'];
					break;
					}
				}
			}
		}
	}
	//  NOTE - at this point $users = account properties of profile to display  ?>

<!--  form submit processing, all pages may need similar code  -->
<!--  edit submit results here  --><?PHP
	/*  Post Submit Processing  */  ?>
<div id="submit_image_results" style="border: solid; border-color: yellow; position: relative; margin: 0 4px; display: none;">
<button style="margin: 0; position: absolute; top: 8px; right: 8px; display: inline-block;"
  onclick="display_toggle('submit_image_results');">dismiss</button><?PHP
	$t = true;
	if ($submitmode == "upload" &&        isset($_POST['submit']))
		handle_edit_submit(array('profile', 'edit_submit'));  //  profile::edit_submit()
	elseif ($submitmode == "urep" &&     isset($_POST['submit']))
		handle_edit_submit(array('profile', 'edit_submit'));
	elseif ($submitmode == "sector" &&   isset($_POST['submit']))
		handle_edit_submit(array('profile', 'edit_submit'));
	elseif ($submitmode == "contact" &&  isset($_POST['submit']))
		handle_edit_submit(array('profile', 'edit_submit'));
	elseif ($submitmode == "settings" && isset($_POST['submit']))
		handle_edit_submit(array('profile', 'edit_submit'));

	elseif ($submitmode == "upload" &&   isset($_POST['submit_gallery']))
		handle_edit_submit(array('gallery', 'edit_submit'));  //  AKA gallery::edit_submit
//	elseif ($submitmode == "lounge" && isset($_POST['fl_submit'])) {
//		handle_edit_submit(lounge_edit_submit);
//		}
	else
		$t = false;
	if ($t)
		echo "\n<script>\ndisplay_toggle('submit_image_results');\n</script>\n";  ?>
</div>
<!--  edit submit results here [end]  -->
<!--  form submit processing, all pages may need similar code [end]  --><?PHP

	/*  using file_get_contents() purposely insures PHP commands in file are ignored???  */
	$m = file_get_contents($data_dir.'/is/profile');
	echo "\n<div style=\"margin: 0 4px 4px 4px; font-size: larger;\">";
	//  FUTURE - make this collapsable, even add a session flag to persistently hide
	section_head(NULL, NULL, $m);
	echo '</div>';  ?>

<div id="prof_contact" style="display: none;"><!--  hidden profile content here  --><?PHP
	//  FUTURE - above div id prefix with uid or username?
	profile::contact_refresh($profile, $p_fnam, $p_flgs);  ?>
<!--  hidden profile content [ end ]  --></div><?PHP

	echo "\n\n<div id=\"acctyp_title\" style=\"padding: 0 4px; position: relative;\">";
	echo "[ PERSONAL vs PRACTITIONER personality snapshot ]</div>";  ?>
<!--  CITATION
http://stackoverflow.com/questions/1260122/expand-a-div-to-take-the-remaining-width  -->

<div style="position: relative;"><!--  PROFILE TOP / ALL COLUMN PARENT / NEW FLOAT ON  -->
<?PHP
	/*  WIZARD  */
	$s  = 'position: absolute; top: 1em; left: 0;';
	if ($public)
		$s .= ' background-color0: blue; width: 100%; display: none; z-index: 1;';
	if (          ($p_flgs & profile::ACCTYP_ACTIVE) == 0)  //  YYYY
		$s .= ' background-color0: blue; width: 100%; z-index: 1;';
	elseif (isset($_GET['signup']))
		$s .= ' background-color0: blue; width: 100%; z-index: 1;';
	else
		$s .= ' background-color0: blue; width: 100%; display: none; z-index: 1;';
	echo "\n<div id=\"wizard\" style=\"".$s."\"><!--  wizard  --><a name=wizard></a>";
	echo "\n<div style=\"float: left;  width: 1em;\">&nbsp;</div>";
	echo "\n<div style=\"float: right; width: 1em;\">&nbsp;</div>";
	if (in_array($user_p, $admins))
		echo "\n<div id=\"wiz_00\" style=\"margin: 0 4px 4px 0; padding: 0.5em; overflow: hidden; background-color: cyan;\">";
	else
		echo "\n<div id=\"wiz_00\" style=\"margin: 0 2px 2px 0; padding: 0.5em; overflow: hidden; background-color: white; border: 2px solid grey;\">";

	if (isset($_GET['signup']))
		$s = $out1;
	else
		$s = 'What if there is long text here, will it wrap or what.&nbsp; Seriously, think about it, does it blow your mind to think about this?';
	echo "\n<div id=\"wiz_01\" style=\"margin: 0 4px 4px 0;\">";
	if (in_array($user_p, $admins))
		echo "\n<div id=\"wiz_01_result\" style=\"background-color: pink;\">".$s.'</div>';
	else
		echo "\n<div id=\"wiz_01_result\">".$s.'</div>';
	if (in_array($user_p, $admins))
		echo "\n<div id=\"wiz_01_body\" style=\"background-color: blue;\">";
	else
		echo "\n<div id=\"wiz_01_body\" style0=\"background-color: blue;\">";
	profile::$un = $user_p;
	profile::$uf = $p_flgs;
	profile::edit_form_contact($p_flgs, 'wiz1');  //  YYYY
	echo ' </div>';
	echo "\n</div>";

	echo "\n</div>";
	echo "<!--  wizard [end]  -->\n</div>";  ?>

<div class="col_left"><!--  COL LEFT  --><?PHP
	/*  --- left pane (desktop) ---  */
	/*  hidden bio, goal, image edit form  */
	echo "\n<div style=\"position: relative;\">"; 
	echo "\n<div id=\"submit_image\" class=prof_edit_pop_a style=\"display: none;\"><!--  hidden profile/image edit  -->";
	echo "\n<div style=\"float: left; margin: 0 4px 4px 0;\">";
	  profile::edit_form();
	  echo "</div>";
	echo "\n<button onclick=\"display_toggle('submit_image');\" style=\"margin: 0;\">Cancel</button>";
	echo "</div><!--  hidden profile/image edit [end]  -->";
	echo "</div>";

	/*  visible content - javascript below will fill this in  */
	echo "\n<div style=\"width: 280px; padding: 0 4px 4px 4px; position: relative;\">";  /*  $l_style  */
//	echo "\n<div style=\"float: left; width: 280px; padding: 0 4px 4px 4px; position: relative;\">";  /*  $l_style  */
//	echo "\n<div style=\"padding: 4px; position: relative; background-color: lightgreen;\">";
	if (!$public)
		echo profile::edit_button('submit_image', 8, 8);
	echo "\n<div id=prf_left>left</div>";
//	if ($user_p && in_array($user_p, $admins))  /*  allow admins to edit protools content  */
//		echo "\n[ <a href=".$edit_urla.'sample_protools>info</a> ]';
//	echo '</div>';
	/*  visible content - conditional social tools bar  */
	if ($user_p && $public) { 
		if ($user_p == $public) {
			$str = $ftr = 'N/A';
			$mtr = 'msg';
			}
		else {
			$mtr = '<a href=/?artex-msg&to='.$public.'>msg</a>';
			$gf = array();
			if (lists::get($data_dir.'/friends', $gf, NULL, $public, $user_p))
				$str = '<a href=/?dash&m2=mf>friend</a>';
			elseif (lists::get($data_dir.'/invites', $gf, NULL, $user_p, $public))
				$str = '<a href=/?friends&accept='.$public.'>accept invite</a>';
			else
				$str = '<a href=/?friends&invite='.$public.'>friend request</a>';

			$af = NULL;
			if (exchange::following($data_dir.'/following', $af, $user_p) &&
			    count($af) > 0 &&
			    in_array($public, $af))
				$ftr = '<a href=/?friends&unfollow='.$public.'>unfllw</a>';
			else
				$ftr = '<a href=/?friends&follow='.$public.'>follow</a>';
			}

		$ctr = lists::get($data_dir.'/friends', $gf, NULL, $public, 'ct');
		if ($ctr === FALSE) $ctr = 0;
		else $ctr = "<a onclick=\"prf_flist();\">".$ctr."</a>";

		//   'margin: 0 4px; height0: 2em; background-image: url(gfx-stock/social_0.png); padding: 0; background-size: cover;';
		$s = 'margin: 0 4px; background-image: url(/gfx-stock/social_0.png); padding: 0; background-size: cover; font-size: smaller; text-align: center;';
		echo "\n<div style=\"".$s."\">";
		echo "<div style=\"width: 20%; display: inline-block; margin: 0;\">[ ".$str.' ]</div>';
		echo "<div style=\"width: 20%; display: inline-block; margin: 0;\">[ ".$ftr.' ]</div>';
//		echo "<div style=\"width: 20%; display: inline-block; margin: 0;\">[ msg ]</div>";
		echo "<div style=\"width: 20%; display: inline-block; margin: 0;\">[ ".$mtr." ]</div>";
		echo "<div style=\"width: 20%; display: inline-block; margin: 0;\">[ ".$ctr." ]</div>";
                /* Share profile by Email
                 * Added by Josh N 8/10/16
                 */
                echo "<div style=\"width: 20%; display: inline-block; margin: 0;\">";
                echo "<img style=\"height: 24px;\" src=/gfx-stock/email-icon-small.png onclick=\"show_share_popup()\">";
                echo "</div>";
                
	//	echo '<div style=\"clear: both;\">&nbsp;</div></div>';
		echo '</div>';
                //Share email popup code, DO NOT INDENT.
                echo <<<EOD
<div id="shareEmailPopup">
    <span class="close" onclick="hide_share_popup()">x</span>
    <div id="shareEmailFields">
        <form method="POST" action="../rest/share.php" id="shareEmailForm" onsubmit="share_email_submit(event)">
            <input name="toEmail" placeholder="Recipient's Email" type="text">
            <input name="fromEmail" placeholder="Your Email" type="text">
            <input name="type" value="shareProfile" type="hidden">
            <input name="link" value=$public type="hidden">
            <input type="submit" id="shareEmailSubmit" value="send">
        </form>
        <div id="shareEmailError"></div>
    </div>
 </div>
EOD;
		}
	$s = 'border: 1px solid; padding: 4px; margin: 4px 0 0 0;;';
	echo "\n<div id=\"cont\" style=\"clear: both; position: relative; ".$s."\">";
//	if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
//		/*  Business contact - providers only  */
//		echo   "\n<div id=\"submit_contact\" class=prof_edit_pop_b style=\"display: none;\"><!--  hidden profile/contact form  -->";
//		echo     "\n<div style=\"float: left; margin: 0 4px 4px 0;\">".profile::edit_form_contact($p_flgs)."</div>";
//		echo     "\n<button onclick=\"display_toggle('submit_contact');\" style=\"margin: 0;\">Cancel</button>";
//		echo   "</div><!--  hidden profile/contact form [end]  -->";

//		if (!$public) echo profile::edit_button('submit_contact', 8, 8);
//		echo "\n<div id=prf_addr>addr</div>";
//		echo '</div>';
//		}
//	else {
//		/*  default - personal account  */
		echo   "\n<div id=\"submit_contact\" class=prof_edit_pop_b style=\"display: none;\"><!--  hidden profile/contact form  -->";
		echo     "\n<div style=\"float: left; margin: 0 4px 4px 0;\">".profile::edit_form_contact($p_flgs)."</div>";
		echo     "\n<button onclick=\"display_toggle('submit_contact');\" style=\"margin: 0;\">Cancel</button>";
		echo   "</div><!--  hidden profile/contact form [end]  -->";

		if (!$public) echo profile::edit_button('submit_contact', 8, 8);
		echo "\n<div id=prf_addr>addr</div>";
		echo '</div>';
//		}
	echo "\n</div>";

//echo "\n<br>".$p_flgs.', '.$user_p;
//	if ($user_p && $public && ($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
	if ($user_p && $public &&
	   ($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV &&
	   ($_SESSION['uid_dg_flgs'] & profile::ACCTYP_MASK) == profile::ACCTYP_PROV &&
	   $public != $user_p) {

	    /*  Request recommendation - providers only  */
	    $s = 'border: 1px solid; padding: 4px; margin: 0 4px 0 4px';
	    echo "\n<div style=\"text-align: center; position: relative; ".$s."\">";
	    /*  FUTURE - call out to determine if already recommended  */
	    $gr = array();
	    if (lists::get($data_dir.'/recommend', $gr, NULL, $user_p, $public))
//	        echo "[ ENDORSEMENT ACCEPTED ]\n";
	        echo "[ RECOMMEND ACCEPTED ]\n";
	    /*  FUTURE - detect if recommend is pending  */
	    else {
		$l = '/?friends&invite-rec='.$public;
//	        echo "[ <a href=\"".$l."\">SUBMIT ENDORSEMENT</a> ]\n";
	        echo "[ <a href=\"".$l."\">RECOMMEND</a> ]\n";
		}
//	    ap($gr);
	    echo "\n</div>";
	    }
	/*  Recommended - recommendations I have received [ display only for practioners ]  */
	if ($public && ($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
	    echo "\n<div id=\"recmdd\" style=\"margin: 4px; clear: both; position: relative; border: 1px solid;\">";
//	    echo "\n<div style=\"padding: 4px;\" id=prf_recm>".$public." has been endorsed by these practioners</div>";
	    echo "\n<div style=\"padding: 4px;\" id=prf_recm>".$public." has been recommended by these practioners</div>";

	    echo "\n\n<div><!--  friends list  -->";
	    friends_list('recmended', $public);
	    echo "\n<!--  friends list [end]  --></div>";

	    echo "\n<div style=\"clear: both;\"></div>";
	    echo "</div>";
	    }
	/*  --- left pane (desktop) [end] ---  */  ?>
</div><!--  COL LEFT [end]  -->

<div class="col_stretch"><!--  COL STRETCH  -->
<?PHP
	/*  --- right pane (desktop) ---  */
	/*  friends list - hidden initially  */
	echo "\n\n<div id=\"flis\" class=urep style=\"display: none;\"><!--  friends list  -->";
		echo '<div style="position: absolute; top: 8px; right: 8px;">';
		echo "<input type=\"button\" onclick=\"prf_flist();\" value=\"back\">";
		echo '</div>';
//	echo "\n[ friends list ]";
	friends_list('friends', $public);
	echo "\n<!--  friends list [end]  --></div>";

	/*  urep 4x4  */
	echo "\n\n<div id=\"urep\" class=urep><!--  urep 4x4  -->";
	$urep_a = array();
	$urep_c = array();
	//  Note - its possible file does not exist for new account
	lists::get($data_dir.'/users/'.
	  ($public ? $public : session_username_active()).  //  show public vs logged in user
	  '_urep', $urep_c);  //  current values
	lists::get($data_dir.'/urep', $urep_a);  //  all possible values
	profile::urep_form($urep_c, $urep_a, $p_flgs,
//	  ($dflags & DFLAGS_MOBILE) ? ' width: 244px;' : ' width: 532px;');
//	  ' width: auto;');
	  ' margin-right: 1em;');
	if (!$public) echo profile::edit_button('submit_urepe', 4, 8);

//	echo "\n<div style=\"clear: both;\"></div>";  //  ZZZZ - otherwise urep stagger wraps edit form
	echo "\n<div style=\"background-color: grey;\">";
	profile::urep_attr_disp('contrib', array(
	  $urep_c['contrib'][0], $urep_c['contrib'][1], $urep_c['contrib'][2], $urep_c['contrib'][3]
	  ));
	profile::urep_attr_disp('healthy', array(
	  $urep_c['healthy'][0], $urep_c['healthy'][1], $urep_c['healthy'][2], $urep_c['healthy'][3]
	  ));

	if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
		profile::urep_attr_disp('class',    array(
		  $urep_c['class'][0], $urep_c['class'][1], $urep_c['class'][2], $urep_c['class'][3]
		  ));
		profile::urep_attr_disp('spectrum', array(
		  $urep_c['spectrum'][0], $urep_c['spectrum'][1], $urep_c['spectrum'][2], $urep_c['spectrum'][3]
		  ));
		}
	else {
		profile::urep_attr_disp('others', array(
		  $urep_c['others'][0], $urep_c['others'][1], $urep_c['others'][2], $urep_c['others'][3]
		  ));
		profile::urep_attr_disp('looking', array(
		  $urep_c['looking'][0], $urep_c['looking'][1], $urep_c['looking'][2], $urep_c['looking'][3]
		  ));
		}
	echo '</div>';
	echo "\n<!--  urep 4x4 [end]  --></div>";

	if (0 && ($user_p && in_array($user_p, $admins))) {  /*  allow admins to edit protools content  */  ?>
<div
  style="width: 25%; display: inline-block;">A</div><div
  style="width: 25%; display: inline-block;">B</div><div
  style="width: 25%; display: inline-block;">C</div><div
  style="width: 25%; display: inline-block; background-color: blue;">D</div><?PHP
		}

	//  VVVV - moved entitlement report section down near account settings
	if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
		echo "\n<div style=\"clear: both;\"></div>";
		echo "\n<div id=\"entl\" class=prof_entl>";
		/*  Business sector - providers only  */
		$spec = array();
		//  Note - its possible file does not exist for new account
		lists::get($data_dir.'/users/'.
		  ($public ? $public : session_username_active()).  //  show public vs logged in user
		  '_spec', $spec);  //  current values

		if (!$public) echo profile::edit_button('submit_sector', 8, 8);

		echo   "\n<div id=\"submit_sector\" class=prof_edit_pop_b style=\"display: none;\"><!--  hidden profile/sector form  -->";
		echo     "\n<div>".profile::edit_form_sector($spec, $p_flgs).'</div>';
		echo     "\n<button onclick=\"display_toggle('submit_sector');\"\">Cancel</button>";
		echo   "</div><!--  hidden profile/sector form [end]  -->";

		echo "\n<div><span style=\"font-weight: bold;\">Primary sector:</span> ";
		echo profile::edit_form_sector_list($spec['primary'])."</div>";
		echo "\n<div><span style=\"font-weight: bold;\">Secondary:</span> ";
		echo profile::edit_form_sector_list($spec['secondary'])."</div>";
		echo "\n<div><span style=\"font-weight: bold;\">Specialities:</span> ";
		echo profile::edit_form_sector_list($spec['special'])."</div>";

		echo "\n</div>";
		}

	//  show only symposium, what user is following
	echo "\n<div style=\"clear: both;\"></div>";
	echo "\n<div id=\"symp\" style=\"clear0: both; margin: 4px;\">";
	exchange::latest(6, array(($public) ? $public : session_username_active()));
	echo "</div>";

	/*  Questions, tags, symbol  */
	echo "\n<div id=\"qust\" style=\"margin: 4px; clear: both; position: relative; border: 1px solid;\">";
	if (isset($urep_c['emblem'][0]))
	//	echo "<img style=\"\position: absolute; right: 8px; top: 8px; width: 55px;\" src=/gfx-stock/".$urep_c['emblem'][0]."_144x.png>";
		echo "<img style=\"float: right; width: 55px; padding: 4px;\" src=/gfx-stock/".$urep_c['emblem'][0]."_144x.png>";
	else
	//	echo "<div \n  style=\"\position: absolute; right: 8px; top: 8px; text-align: center; padding: 4px; width: 53px; border: 1px solid;\">no<br>image</div>";
		echo "<div \n  style=\"float: right; text-align: center; padding: 4px; width: 53px; border: 1px solid; margin: 4px;\">no<br>image</div>";
//	echo "\n<div style=\"background-color: lightyellow; padding: 4px;\" id=prf_rght>rght</div>";
	echo "\n<div style=\"padding: 4px;\" id=prf_rght>rght</div>";
	echo "\n<div style=\"clear: both;\"></div>";
	echo "</div>";

	/*  Articles  */
	if ($public) { 
	    $s = 'margin: 4px; width0: 100%; vertical-align: top; display0: inline-block; background-color: orange;';
	    $s = 'margin: 4px; vertical-align: top; background-color: orange;';
	    echo "\n<div id=\"pivt\" style=\"".$s."\">";

	    //	section_head(NULL, 'Articles');
	    artex::latest(6, NULL, NULL,
	      $public ? $public : (session_userid_active() ? session_username_active() : 'N/A'),
	      $public ? 'User Content' : 'My Content');
	    //	echo "\n<div style=\"height: 4px;\"></div>";
	    if (!$public && session_userid_active())
		//  skip ondeck display if public profile or no login
		//  FUTURE - consider instead redirect to anonymous dashboard?
		artex::latest(6, 'ondeck');
	    echo '</div>';
	    }

	/*  Recommendations - who I recommended [ display only for practioners ]  */
	if ($public && ($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
	    echo "\n<div id=\"recm\" style=\"margin: 4px; clear: both; position: relative; border: 1px solid;\">";
//	    echo "\n<div style=\"padding: 4px;\" id=prf_recm>".$public." has endorsed these practioners</div>";
	    echo "\n<div style=\"padding: 4px;\" id=prf_recm>".$public." has recommended these practioners</div>";

	    echo "\n\n<div><!--  friends list  -->";
	    friends_list('recommend', $public);
	    echo "\n<!--  friends list [end]  --></div>";

	    echo "\n<div style=\"clear: both;\"></div>";
	    echo "</div>";
	    }

	/*  --- right pane (desktop) [end???] ---  */  ?>
</div><!--  COL STRETCH [end ]  -->
</div><!--  PROFILE TOP / ALL COLUMN PARENT [end]  -->

<!--  without this, tall left column content may descent below footer  -->
<div style="clear: both;"></div><?PHP

//	if ($public) { 
//		}
//	else {
	if (!$public) { 
	 	$s = 'border: 1px solid; padding: 4px; margin: 0 4px 4px 4px;';
		echo "\n<div style=\"position: relative; ".$s."\">";
		echo "Account Settings";
		echo "<dl style=\"margin: 0.5em;\"><dt>Email ";
		echo "\n<dd>".$p_mail;
		echo "\n</dl>";
		profile::edit_form_settings();
	//	echo "\n<button onclick=\"display_toggle('wizard'); scroll(0, 0);\" style=\"margin: 0;\"> I must see the Wizard</button>";
		echo "\n<button onclick=\"display_toggle('wizard'); window.scrollTo(0,0);\" style=\"margin: 0;\"> I must see the Wizard</button>";

                /* Eternal e-vite button and popup
                 * Overload link field for provider vs user option
                 * Added: Josh N 8/12/16
                 * heredoc code below. DO NOT INDENT.
                 */
                
echo <<<EOD
<div id="shareEmailPopup">
    <span class="close" onclick="hide_share_popup()">x</span>
    <div id="shareEmailFields">
        <form method="POST" action="../rest/share.php" id="shareEmailForm" onsubmit="share_email_submit(event)">
            <input name="toEmail" placeholder="Recipient's Email" type="text"><br>
            <input name="type" value="evite" type="hidden">
            Type of user:<br>
            <input name="accountType" value="personal" type="radio" checked>Personal<br>
            <input name="accountType" value="provider" type="radio">Practitioner<br>
            <input type="submit" id="shareEmailSubmit" value="send">
        </form>
        <div id="shareEmailError"></div>
    </div>
 </div>
EOD;
                echo "\<button onclick=\"show_share_popup()\">Invite a friend</button>";
                echo "\n</div>";

		//  VVVV - moved entitlement report section from under urep
		$s = 'border: 1px solid; padding: 4px; margin: 0 4px 4px 4px;';
		echo "\n<div style=\"position: relative; ".$s."\">";
		if ($user_p) {
			echo "\n<div style=\"color: darkgrey;\">";
			echo "\n<span style=\"font-weight: bold;\">Entitlements:</span> ";
			$prod_m = products::prod_chk_ent($data_dir.'/products/active', array('provider-alpha1'), $user_id);
			if (is_null($prod_m))
				echo "no products currently, stay tuned for out store";
			else
				echo $prod_m;
			echo "\n<div style=\"float: right;\"><span style=\" font-weight: bold;\">flags:</span> ";
			echo (($p_flgs & profile::ACCTYP_PRVACT) ? 'Active' : 'Account Inactive').'</div>';
			echo "\n</div>";
			}
		echo "\n</div>";
		//  FUTURE - the following should not be needed to insure margin bottom
		echo "\n<div style=\"clear: both; height: 1px;\"></div>";
		}

}  /*  end beta check  ZZZZ  */

	abort:  ?>

<!--  div style="clear: both;"></div  -->

</div><!--  Main DIV [end]  --><?PHP

	foot();  ?>
</BODY>

<script>
function prf_util0(id, str) {
	if (document.getElementById(id)) {
		str.v = document.getElementById(id).innerHTML;
		return true;
		}
	return false;
	}

function prf_flist() {
	//  for public profile view,
	//  hide elements except profile image and friends list
	display_toggle('urep');  //  BUG: https://github.com/Wholosophy/holistik/issues/67
//	display_toggle('cont');
	display_toggle('entl');
	display_toggle('symp');
	display_toggle('qust');
	display_toggle('pivt');
	display_toggle('flis');
	display_toggle('recm');
	}

function flash_address(m, dst) {  //  WWWW
	/*  flash update various address forms fields  */
	/*    m   mode bitmap flags
	/*        1, 2, 4  */
	var et;
	var prf_addr = '';
	var str = { v : '' };  //  this is 'object' style, which can be passed by reference

	if (prf_util0('profile_address', str)) {
		prf_addr += '<div>address: ' + str.v + '</div>';
	//	if (m & 1) document.getElementsByName('address')[0].value = str.v;  //  update hidden form field
		document.getElementsByName('address')[0].value = str.v;  //  update hidden form field
	//	if ((m & 2) && (et = document.getElementById('wiz_address')))
		if (et = document.getElementById('wiz_address'))
			et.value = str.v;
		}
	if (prf_util0('profile_phone', str)) {
		prf_addr += '<div>phone: ' + str.v + '</div>';
	//	if (m & 1) document.getElementsByName('phone')[0].value = str.v;  //  update hidden form field
		document.getElementsByName('phone')[0].value = str.v;  //  update hidden form field
	//	if ((m & 2) && (et = document.getElementById('wiz_phone')))
		if (et = document.getElementById('wiz_phone'))
			et.value = str.v;
		}
	if (prf_util0('profile_web', str)) {
		prf_addr += '<div>web: ' + str.v + '</div>';
		document.getElementsByName('web')[0].value = str.v;  //  update hidden form field
		if (et = document.getElementById('wiz_web'))
			et.value = str.v;
		}
	if (prf_util0('profile_zip', str)) {
		prf_addr += '<div>zip: ' + str.v + '</div>';
		document.getElementsByName('zip')[0].value = str.v;  //  update hidden form field
		if (et = document.getElementById('wiz_zip'))
			et.value = str.v;
		}
	if (prf_util0('profile_nat', str)) {
		prf_addr += '<div>nation: ' + str.v + '</div>';
		document.getElementsByName('nat')[0].value = str.v;  //  update hidden form field
		if (et = document.getElementById('wiz_nat'))
			et.value = str.v;
		}
	if (prf_util0('profile_born', str)) {
		prf_addr += '<div>born: ' + str.v + '</div>';
		document.getElementsByName('born')[0].value = str.v;  //  update hidden form field
		if (et = document.getElementById('wiz_brn'))
			et.value = str.v;
		}
	document.getElementById(dst).innerHTML = prf_addr;
	}

function flash_profile() {  //  WWWW
	/*  flash update various address forms fields  */
	/*    m   mode bitmap flags
	/*        1, 2, 4  */
	var str = { v : '' };  //  this is 'object' style, which can be passed by reference

	if (prf_util0('profile_img', str))
		document.getElementsByName('wiz_imagepast')[0].value = str.v;
	if (prf_util0('profile_goals', str))
		document.getElementsByName('wiz_goals')[0].value = str.v;
	if (prf_util0('profile_about', str))
		document.getElementsByName('wiz_about')[0].value = str.v;
	if (prf_util0('profile_itags', str))
		document.getElementsByName('wiz_itags')[0].value = str.v;
	}

//  DANGER - do not NOT prepend with var inside a function, it must persist as global.
http_req_wzpf = new XMLHttpRequest();

function wiz_rest(fn) {
	//  Use this function when a profile element needs an AJAX refresh
	//  from backend without having to request entire page reload.
	//    url rest URL to call
	//    fn  function to call upon success AJAX call (optional?)
	//    dv  dv element innerHTML to replace (optional?)
	var url;

	url = '/rest/wiz1.php?act=get&test=1';
	http_req_wzpf.onreadystatechange = function() { vvv_rest(function() {
//		alert(http_req_wzpf.responseText);
		//  FUTURE - combine this function with flash_profile()?
		flash_profile();
//		alert('yeah');
		}, http_req_wzpf); };
//	alert(url);
	http_req_wzpf.open('GET', url, true);
	http_req_wzpf.send(null);
	}

<?PHP	//  Note, during activation additional information sections may also be unhidden  */
	if ($public)
		echo "\nif (e = document.getElementById('profile_info_public')) e.style.display = 'block';";
	else if ($user_p)
		echo "\nif (e = document.getElementById('profile_info_edit')) e.style.display = 'block';";  ?>

//  raw profile content should be loaded into hidden DOM elements,
//  now populate visibable elements with profile content accordingly
var str = { v : '' };  //  this is 'object' style, which can be passed by reference
var prf_left = '';
var acctyp_msg = 'test';
var wiz_reset = true;  //  WWWW, preparing to activate all wizard submit buttons

<?PHP	echo 'var loggedin_username  = '.($user_p ? "'".$user_p."'" : 'null').';';
	echo "\nvar prof_public  = ".($public ? 'true' : 'false').';';  ?>

if (prf_util0('profile_img', str)) {
	//  if no login, the empty profile stub [bog]/users/_profile will be read in if present
	//  active click only if in edit mode
	prf_left += '<img src=/gfx-upload/' + str.v + ' style="width: 100%; height: 280px; margin-bottom: 4px;">';
	document.getElementsByName('imagepast')[0].value = str.v;
	prf_left += '\n<br>';
	}

prf_left += '<b style="font-size: larger;">';
if (prf_util0('profile_fname', str))  prf_left += str.v;
if (prf_util0('profile_flags', str))  {
	prf_left += ' [ ' + str.v + ' ]';
	if ((str.v & 15) == 1) {  /*  FUTURE define 15 as same mask value as PHP  */
		//prf_left += ' *&nbsp;PROVIDER *';
		acctyp_msg = 'Practitioner Profile';
		}
	else
		acctyp_msg = 'Personal Profile';
	}
if (prf_util0('profile_lname', str))  prf_left += ' ' + str.v;
prf_left += '</b>';
if (prf_util0('profile_about', str))  {
	prf_left += '<br><br>' + str.v;
	//  update hidden form field
	document.getElementsByName('about')[0].value = str.v;
	}
document.getElementById('prf_left').innerHTML = prf_left;

if (prof_public)
	acctyp_msg += ' - Public View';
else
	acctyp_msg += ' - <a href=?public=' + loggedin_username + '>Public View</a> | Edit Mode';
//  YYYY
//acctyp_msg += '<button style="margin: 0; position: absolute; top: 0px; right: 8px; display: inline-block;" onclick=display_toggle("acctyp_title");>hide</button>';
//acctyp_msg += ' <span style="color: grey;">(<a href=# style="color: grey;" onclick=display_toggle("acctyp_title");>x</a>)</span>';
//acctyp_msg += '<a style="margin: 0; position: absolute; top: 0px; right: 8px; display: inline-block;" onclick=display_toggle("acctyp_title");>hide</a>';
acctyp_msg += '<a style="margin: 0; position: absolute; top: 0px; right: 8px; display: inline-block;" onclick=display_toggle("acctyp_title");>x</a>';
document.getElementById('acctyp_title').innerHTML = acctyp_msg;

flash_address(3, 'prf_addr');  //  WWWW

var prf_rght = '';
if (prf_util0('profile_goals', str)) {
	//prf_rght += '<br><br>Interests: ' + str.v;
	prf_rght += '<div>' + str.v + '</div>';
	//  update hidden form field
	document.getElementsByName('goals')[0].value = str.v;
	}
if (prf_util0('profile_itags', str)) {
	prf_rght += '<div>' + str.v + '</div>';
	//  update hidden form field
	document.getElementsByName('itags')[0].value = str.v;
	}
document.getElementById('prf_rght').innerHTML = prf_rght;
</script><?PHP
	if (isset($keys['ga'])) {
		//  FUTURE - should thi sbe moved high up on the page?  ?>
<script type="text/JavaScript" src="/ga.js"></script>  <?PHP  }  ?>
</HTML>

