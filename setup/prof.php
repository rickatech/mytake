<?PHP

class profile {  /*  FUTURE - move this class to util.php?  */

	//  these constants must be prefiexed with profile::
	const ACCTYP_PERS =   0;    //  personal account
	const ACCTYP_PROV =   1;    //  practiioner/provider account
	const ACCTYP_MASK = 0xf;    //  allow 16 account types (for now)
	const ACCTYP_ACTIVE = 16;   //  account active (general?)
	const ACCTYP_PRVACT = 16;   //  account active (practiioner?)
	const ACCTYP_COMPLT = 32;   //  account active (practiioner? general?)

	static public $es_success = FALSE;  //  out of band last method success/fail
	static public $un = NULL;           //  username to process cache
	static public $uf = 0;              //  user account flags of user to process cache
	static public $ufn = 'N/A';         //  first name of user to process cache
	static public $umail = 'N/A';       //  email address of user to process cache
	static public $uid = NULL;          //  user id of user to process cache
	static public $urec = NULL;         //  cached user record array - FUTURE, does this supercede above properties?

	static public function edit_submit_wiz($m) {
		//  Primarily called by wizard form, output submit buttons, but only enable appropriate ones
		//    $m    ...
		//  Ideally, profile static class $uf property has already been set
		global $admins;

		if ($m == 'wiz1')
			$s =      "<input    type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
		else
			$s = "<button onclick=\"alert('switch'); return false;\" style=\"margin: 0;\" disabled>Required</button>";

		if ($m == 'wizwld') {
			$s2  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s2 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz2\">";
			$s2 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,ua_wiz_0_0,ua_wiz_0_1,ua_wiz_0_2,ua_wiz_0_3\">";
			}
		else
			$s2 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Contrib</button>";

		if ($m == 'wizhth') {
			$s3  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			if ((profile::$uf & profile::ACCTYP_MASK) == profile::ACCTYP_PROV)
				$s3 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz6\">";
			else
				$s3 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz3\">";
			$s3 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,ua_wiz_0_0,ua_wiz_0_1,ua_wiz_0_2,ua_wiz_0_3\">";
			}
		else
			$s3 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Health</button>";

		if ((profile::$uf & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
		    if ($m == 'wizcls') {
			$s4  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s4 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz4\">";
			$s4 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,ua_wiz_0_0,ua_wiz_0_1,ua_wiz_0_2,ua_wiz_0_3\">";
			}
		    else
			$s4 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Class</button>";

		    if ($m == 'wizspc') {
			$s5  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s5 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz5\">";
			$s5 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,ua_wiz_0_0,ua_wiz_0_1,ua_wiz_0_2,ua_wiz_0_3\">";
			}
		    else
			$s5 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Spectrum</button>";
		    }
		else {
		    if ($m == 'wizprs') {
			$s4  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s4 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz4\">";
			$s4 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,ua_wiz_0_0,ua_wiz_0_1,ua_wiz_0_2,ua_wiz_0_3\">";
			}
		    else
			$s4 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Personality</button>";

		    if ($m == 'wizskg') {
			$s5  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s5 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz5\">";
			$s5 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,ua_wiz_0_0,ua_wiz_0_1,ua_wiz_0_2,ua_wiz_0_3\">";
			}
		    else
			$s5 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Seeking</button>";
		    }

		if ((profile::$uf & profile::ACCTYP_MASK) == profile::ACCTYP_PERS) {
		    if ($m == 'wizsym') {
			$s6  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s6 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz6\">";
			$s6 .= "\n<input     type=\"hidden\" name=\"js_nf\" value=\"act,acctyp,emblem_2\">";
			}
		    else
			$s6 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Symbol</button>";
		    }

		if ($m == 'wizimg') {
			$s7  = "\n<input     type=\"submit\" name=\"submit\" value=\"submit\" style=\"font-weight: bold;\">";
			$s7 .= "\n<input     type=\"hidden\" name=\"act\" value=\"wiz7\">";
			$s7 .= "\n<input     type=\"hidden\" name=\"js_mf\" value=\"act,acctyp,wiz_imagepast,wiz_goals,wiz_about,wiz_itags\">";  //  WWWW file type have special handling  */
			}
		else
			$s7 = "\n<button onclick=\"return false;\" style=\"margin: 0;\" disabled>Profile Image</button>";

//		echo "\n<br style=\"clear: both;\">";
		echo $s;
		echo $s2;
		echo $s3;
		echo $s4;
		echo $s5;
		echo $s6;
		echo $s7;
		if (static::$uf & profile::ACCTYP_ACTIVE)
			echo "\n<button onclick=\"display_toggle('wizard'); return false;\" style=\"margin: 0;\">Finish</button>";
		else
			echo "\n<button onclick=\"display_toggle('wizard'); return false;\" style=\"margin: 0;\" disabled>Finish</button>";

		if (in_array(static::$un, $admins))
			echo " <span style=\"color: grey;\">".(is_null($m) ? 'NULL' : $m).'</span>';
		}

	static public function edit_submit($m = NULL) {  /*  profile  */
		//    $m    NULL, non-wizard
		//          'wiz', AJAX wizard invoked
		//  FUTURE, move to util.php, refactor as a method?
		global $file_dir, $data_dir, $submitmode, $p_flgs, $login_data;
		global $users;  //  FUTURE - pass this in, don't rely on global

		if ($submitmode == 'upload') {
//			ap($_POST);
//			ap($_FILES);
			if (is_null($m)) {
				$w = '';
				$ift = $_FILES['imagefile']['type'];
				$ifn = $_FILES['imagefile']['tmp_name'];
				$ifs = $_FILES['imagefile']['size'];
				}
			else {
				$w = 'wiz_';  //  XXXX
				$ift = $_FILES['photos']['type'][0];
				$ifn = $_FILES['photos']['tmp_name'][0];
				$ifs = $_FILES['photos']['size'][0];
			//	ap($_FILES['photos']['name']);
				}

//			echo       "Name: ".$ifn;
//			echo '\n<br>Size: '.$ifs;
//			echo '\n<br>Type: '.$ift.' / '.exif_imagetype($_FILES['imagefile']['tmp_name']);

			if      ($ift == 'image/jpeg')  $ext = 'jpeg'; 
			else if ($ift == 'image/png')   $ext = 'png'; 
			else $ext = NULL;

//			echo "\n<br>fields: ".$_POST[$w.'goals'];
//			echo ', '.$_POST[$w.'itags'];
//			echo ', '.$_POST[$w.'about'];

			$a = array();
			$a['profile_goals'] = $_POST[$w.'goals'];
			$a['profile_itags'] = $_POST[$w.'itags'];
			$a['profile_about'] = $_POST[$w.'about'];
//if (is_null($m)) {
			if (is_null($ext)) {
				$result = "Unsupported image format or image too large > 2 MBytes";
				//  FUTURE, its okay - perhaps user is updating non-image portion of profile
				//          use the imagepast hidden value, skip copying temp files, update profile
				$a['profile_img'] =  $_POST[$w.'imagepast'];  //  WWWW
				}
			else {
				$dst_file = $file_dir.'/avatar_'.session_username_active().'.'.$ext;
//				echo "\n<br>path upload: ".$file_dir.'/avatar_'.session_username_active().'.'.$ext;
				//  CITATION: http://php.net/manual/en/function.exif-read-data.php
			//	if (copy ($_FILES['imagefile']['tmp_name'], $dst_file)) {
				if (copy ($ifn, $dst_file)) {
			               	$result = "Copy completed";
					$a['profile_img'] =   'avatar_'.session_username_active().'.'.$ext;
					}
				else
					$result = "Could not copy";
				}
			//  FUTURE - make this a shared function with index.php
			if (profile_put($data_dir.'/users/'.session_username_active().'_profile', $a)) {
				if (!is_null($ext)) {
					$im = $file_dir.'/avatar_'.session_username_active().'_min.gif';
				//	if ($e = image_make_min($im, '/gfx-upload/avatar_'.session_username_active().'.'.$ext, $ext))
					if ($e = image_make_min($im, $file_dir.'/avatar_'.session_username_active().'.'.$ext, $ext))
						$result = $e;
					else {
			               		$result = "Update successful";
						static::$es_success = TRUE;
						}
					}
				else {
					$result .= " / Update successful";
					static::$es_success = TRUE;
					}
				}
			else
				$result = "There was a problem updating your profile";
//} else {
//				$result = "Wiz image uplaod - stay tuned";
//}
			}

		else if ($submitmode == 'urep') {
			if ($_POST['acctyp'] != ($p_flgs & profile::ACCTYP_MASK))
				//  Check that past account type request is same as current account type
				$result = 'Profile urep update error - account type mismatch '.$_POST['acctyp'].', '.$p_flgs;
			else {
//ap($_POST);
				$save = array();
				if (is_null($m))
					$w = '';
				else {
					$w = 'wiz_';  //  XXXX
				//	$save_c2 = array();  //  Note - its possible file does not exist for new account
//echo "\n<br>m: ".$m.', '.static::$un;
					lists::get($data_dir.'/users/'.static::$un.'_urep', $save);  //  current values
//echo "\n<br>m: ".$m;
//ap($save);
					}

				$urep = array();
				lists::get($data_dir.'/urep', $urep);
				if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV)
					$attr = array('contrib', 'healthy', 'class', 'spectrum');
				else
					$attr = array('contrib', 'healthy', 'others', 'looking');

//				$save = array();
if     ($m != 'wizsym') {
				if     ($m == 'wizwld') $i = 0;
				elseif ($m == 'wizhth') $i = 1;
				elseif ($m == 'wizprs') $i = 2;
				elseif ($m == 'wizskg') $i = 3; 
				else                    $i = -1;
				if ($i >= 0) {
					$ii  = 'ua_'.$w.'0_';
					for ($j = 0; $j < 4; $j++) {
						$jj  = $ii.$j;
						$save[$attr[$i]][$j] = $_POST[$jj];
						}
					}
				else {
					for ($i = 0; $i < 4; $i++) {
						$ii  = 'ua_'.$w.$i.'_';
						for ($j = 0; $j < 4; $j++) {
							$jj  = $ii.$j;
							$save[$attr[$i]][$j] = $_POST[$jj];
							}
						}
					}
}

				$i = 0;
				foreach ($urep['emblem'] as $k => $v) {
					$ii  = 'emblem_'.$i;
					if (isset($_POST[$ii]) && $_POST[$ii] == $v)
						$save['emblem'][0] = $v;
					$i++;
					}
//ap($save);
				$f = $data_dir.'/users/'.session_username_active().'_urep';
				if (lists::put($f, $save)) {
					$result = 'Profile user representation update successful';
					static::$es_success = TRUE;
					}
				else
					$result = 'There was a problem updating profile user representation';
				}
			}

		else if ($submitmode == 'sector') {
			if ($_POST['acctyp'] != ($p_flgs & profile::ACCTYP_MASK))
				//  Check that past account type request is same as current account type
				$result = 'Profile business sector update error - account type mismatch '.$_POST['acctyp'].', '.$p_flgs;
			else if (isset($_POST['commit_prime']) && !($p_flgs & profile::ACCTYP_PRVACT)) {
				//  FUTURE - perform a count of draft > allowed entitled prime modaliies
				//  Account inactive
				$result = 'Profile inactive, activating prime modality disabled - stay tuned';
				}
			else {
				$save = array();
				if (isset($_POST['commit_prime']))
					$save['primary'][0] = $_POST['primary-draft'];
				else
					$save['primary'][0] = $_POST['primary'];
				$save['primary-draft'][0] =   $_POST['primary-draft'];
				$save['secondary'][0] =       $_POST['secondary'];
				$save['special'][0] =         $_POST['special'];
				//  FUTURE - make nested array less awkward, refactor lists:::put and all invoking code?
				$f = $data_dir.'/users/'.session_username_active().'_spec';
				if (lists::put($f, $save, "# primary x primary-draft x secondary x special\n# key: item_1, item_2"))
					$result = 'Profile business sector update successful';
				else
					$result = 'There was a problem updating profile business sector';
				}
			}

		else if ($submitmode == 'contact') {
		    if (is_null($m)) {
			if ($_POST['acctyp'] != ($p_flgs & profile::ACCTYP_MASK))
				//  Check that past account type request is same as current account type
				$result = 'Profile contact update error - account type mismatch '.$_POST['acctyp'].', '.$p_flgs;
			else {
			//	echo "\n<br>p_flgs: ".$p_flgs.', '.($p_flgs & profile::ACCTYP_MASK);
			//	ap($_POST);
				$a = array();
				$a['profile_address'] = $_POST['address'];
				$a['profile_phone'] = $_POST['phone'];
				$a['profile_zip'] = $_POST['zip'];
				$a['profile_nat'] = $_POST['nat'];
				$a['profile_born'] = $_POST['born'];
				if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
					echo "\n<br>writing practitioner contact";
					$a['profile_web'] = $_POST['web'];
					//  FUTURE - make this a shared function with index.php
					if (profile_put($data_dir.'/users/'.session_username_active().'_addr', $a)) {
						if (profile_put($data_dir.'/users/'.session_username_active().'_addr_j', $a, 'json')) {
							$result .= " / Update contact successful";
							static::$es_success = TRUE;
							}
						}
					if (static::$es_success === FALSE)
						$result = "There was a problem updating your profile contact";
					}
				else {  //  default personal account
				//	echo "\Writing personal contact";
					//  FUTURE - make this a shared function with index.php
					if (profile_put($data_dir.'/users/'.session_username_active().'_pddr', $a)) {
						if (profile_put($data_dir.'/users/'.session_username_active().'_pddr_j', $a, 'json')) {
							$result .= " / Update contact successful";
							static::$es_success = TRUE;
							}
						}
					if (static::$es_success === FALSE)
						$result = "There was a problem updating your profile contact";
					}
				}
			}
		    else {
			$a = array();
			if ($_POST['wiz_acctyp'] != ($p_flgs & profile::ACCTYP_MASK))
				//  Check that past account type request is same as current account type
				$result = 'Profile contact update error - account type mismatch '.$_POST['acctyp'].', '.$p_flgs;
			else {
			//	echo "\n<br>p_flgs: ".$p_flgs.', '.($p_flgs & profile::ACCTYP_MASK);
			//	ap($_POST);
				$a = array();
			//	$a['profile_address'] = $_POST['address'];
			//	$a['profile_phone'] = $_POST['phone'];
				$a['profile_zip'] = $_POST['wiz_zip'];
			//	$a['profile_nat'] = $_POST['nat'];
				$a['profile_born'] = $_POST['wiz_born'];
				if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
					echo "\n<br>writing practitioner contact";
				//	$a['profile_web'] = $_POST['web'];
					//  FUTURE - make this a shared function with index.php
					if (profile_put($data_dir.'/users/'.session_username_active().'_addr', $a)) {
						if (profile_put($data_dir.'/users/'.session_username_active().'_addr_j', $a, 'json')) {
							$result .= " / Update contact successful";
							static::$es_success = TRUE;
							}
						}
					if (static::$es_success === FALSE)
						$result = "There was a problem updating your profile contact";
					}
				else {  //  default personal account
				//	echo "\nWriting personal contact";
					//  FUTURE - make this a shared function with index.php
					if (profile_put($data_dir.'/users/'.session_username_active().'_pddr', $a)) {
						if (profile_put($data_dir.'/users/'.session_username_active().'_pddr_j', $a, 'json')) {
							$result .= " / Update contact successful";
							static::$es_success = TRUE;
							}
						}
					if (static::$es_success === FALSE)
						$result = "There was a problem updating your profile contact";
					}
				}
			}
		    }

		else if ($submitmode == 'settings') {
			//	if ($_POST['acctyp'] != ($p_flgs & profile::ACCTYP_MASK))
			//		//  Check that past account type request is same as current account type
			//		$result = 'Profile contact update error - account type mismatch '.$_POST['acctyp'].', '.$p_flgs;
			//	else {
			//		echo "\n<br>p_flgs: ".$p_flgs.', '.($p_flgs & profile::ACCTYP_MASK);
			if ($_POST['newpswd'] != $_POST['nwcpswd'])
				$result = "New password is not consistent";
			elseif (strlen($_POST['newpswd']) < 6)
				$result = "New password too short";
			else {
include 'email/passwdchng.php';  //  FUTURE - move this to config.php ?
				$file_profiles = $data_dir.'/'.$login_data;
				$r4 = key(static::$urec);
				$r3 = array($r4 => static::$urec[$r4]);
				$r5 = $r3[$r4];
				$r5['hash'] = ($_POST['newpswd'] == 'password')
				  ? $_POST['newpswd']
				  : account::password_make($_POST['newpswd']);
				$r3[$r4] = $r5;
				if ($hash === false)
					$result = "There was a problem updating account password";
				else {
					account::replace($file_profiles, $r3);
					$result = "Account password updated, courtesy notice has been sent to ".$r5['mail'];
					}
				mail_passwdchng($r5['mail']);
				}
			}

		else
			$result = "Undefined submit mode";

		return ($result);
		}

	static public function urep_attr_disp($attr, $a) {
		//  attempt to output a column of well aligned user representation  information
		//  CITATION: https://css-tricks.com/fluid-width-equal-height-columns/
		global $debug_mask, $dflags, $local;

		$w = ' width: 25%;';
		switch ($attr) {
		  case 'contrib':
			$bc = ' background-color:#f14b00;';
			$lg  = '<div style="text-align: center; margin: 1em 0;">[ World ]</div>';
			$out = 'How do you contribute to the world?';
			break;
		  case 'healthy':
			$bc = ' background-color: #cde500;';
			$lg  = '<div style="text-align: center; margin: 1em 0;">[ Health ]</div>';
			$out = 'How do you stay healthy?';
			break;
		  case 'others':
			$bc = ' background-color: #3900a0;';
			$lg  = '<div style="text-align: center; margin: 1em 0;">[&nbsp;Personality&nbsp;]</div>';
			$out = 'How do others describe you?';
			break;
		  case 'looking':
			$bc = ' background-color: #80009a;';
			$lg  = '<div style="text-align: center; margin: 1em 0;">[ Seeking ]</div>';
			$out = 'What are you looking for?';
			break;
		  case 'class':
			$bc = ' background-color: #3900a0;';
			$lg  = '<div style="text-align: center; margin: 1em 0;">[ LOGO ]</div>';
			$out = 'What is your class?';
			break;
		  case 'spectrum':
			$bc = ' background-color: #80009a;';
			$lg  = '<div style="text-align: center; margin: 1em 0;">[ LOGO ]</div>';
			$out = 'What is your spectrum?';
			break;
		  default:
			$bc = ' background-color: lightgrey;';
			$out = 'undefined';
			}
		echo "\n<div style=\"float: left;".$w.$bc."\">";
		echo $lg;
	//	if ($debug_mask & DEBUG_UREP_V3) {  //  browser based elipsis
		if (1 && ($debug_mask & DEBUG_UREP_V2)) {  //  browser based elipsis
		    $oo0 = ' overflow: hidden; white-space: nowrap; text-overflow: clip ellipsis;';
		    $oo1 = '<span style="font-size: smaller;">';
		    $oo2 = '<br>';
		    $oo3 = '</span>';
		} else {  //  origin format
		    $oo0 = '';
		    $oo1 = '<ol style="padding-left: 1.5em;">';
		    $oo2 = '<li>';
		    $oo3 = '</ol>';
		    }
		echo "\n<div style=\"height: 10em; padding: 4px 0 0 4px; background-color: rgba(255,255,255,0.7);".$oo0.'">';
		$out .= "\n".$oo1;
		foreach ($a as $k => $v) {
			$v_t = (isset($local[$v])) ? $local[$v] : $v;
			$out .= "\n".$oo2.$v_t;
			}
		$out .= $oo3;
		echo $out.'</div>';
		echo '</div>';
		}

	static public function edit_form($m = NULL) {  /*  profile  */
		//  Present profile edit form, typically hidden until user clicks to edit.
		//  Default values will be updated dynamically via javascript elsewhere using field name.
//		echo "\n<br>m: ".$m;
		if (is_null($m))
			$w = '';
		else
			$w = 'wiz_';
		echo "<p style=\"margin: 0 0 4px 0;\">Profile image upload </p>";
	//	echo "\n<form style=\"display: inline-block; margin: 0; padding: 0px;\" name=\"form_user_rep\" method=\"post\"";
		echo "\n<form name=\"form_user_rep\" method=\"post\"";
		if (is_null($m)) {
			echo "\n  action=\"?submitmode=upload\" enctype=\"multipart/form-data\">";
			echo "\n<input     type=\"submit\" name=\"".$w."submit\"  value=\"submit\">";
			}
		else {
			//  FUTURE - upon submit, perform an AJAX refresh of the background page section
//			echo "\n  onsubmit=\"mt_post(event, '".$w."imagefile', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { wiz2(); alert('refresh profile in background?');});\">";
			echo "\n  onsubmit=\"mt_post(event, '".$w."imagefile', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { wiz2(); });\">";
			static::edit_submit_wiz($m);
			}
		echo "\n<input type=\"hidden\" name=\"acctyp\" value=\"".($af & profile::ACCTYP_MASK)."\">";

		echo "\n<br><input type=\"file\"   name=\"".$w."imagefile\" maxlength=\"256\" id=\"".$w."imagefile\">";
		echo "\n<br><textarea              name=\"".$w."goals\" style=\"font-size: smaller; font-family: sans-serif;\">...</textarea> questions";  //  javascript below
		echo "\n<br><textarea              name=\"".$w."about\" style=\"font-size: smaller; font-family: sans-serif;\">...</textarea> biography";  //  these values
		echo "\n<br><input type=\"text\"   name=\"".$w."itags\" value=\"...\"> tags";   //  will update
		echo "\n<br><input type=\"hidden\" name=\"".$w."imagepast\" value=\"...\">";  //  these values

//		if (is_null($m))
//			echo "\n<br><input     type=\"submit\" name=\"submit\" value=\"submit\">";
//		else
//			echo "\n<br><input     type=\"submit\" name=\"submit\" value=\"submit\" disabled>";
		echo "\n</form>";
		}

	static public function edit_form_contact($af = 0, $m = NULL, $prev = NULL) {
		//  Present provider contact edit form, typically hidden until user clicks to edit.
		//  Default values will be updated dynamically via javascript elsewhere using field name.
		//    $af     account flags (i.e. personal, provider, ...)
		//    $m      mode
		//            NULL, legcy non-AJAX
		//            'wiz1', AJAX submit
		//    $prev   array of fields values
		$acctyp = $af & profile::ACCTYP_MASK;
		echo "<p style=\"margin: 0;\">Business Contacts </p>";
//		echo "\n<form style=\"display: inline-block; margin: 0; name=\"form_contact\" method=\"post\"";  //  XXXX
		echo "\n<form name=\"form_contact\" method=\"post\"";  //  XXXX
//		echo "\n  action=\"?submitmode=contact\" enctype=\"multipart/form-data\">";
		if ($m == 'wiz1') {
			//  CITATION - http://stackoverflow.com/questions/3384960/want-html-form-submit-to-do-nothing
		//	echo "\n  onsubmit=\"alert('nice'); return false;\">";
		//	echo "\n  onsubmit=\"wiz('nice'); return false;\">";
		//	echo "\n  onsubmit=\"mt_post(event, '', '/rest/wiz1.php', 'wiz_01', function () { alert('w 1'); });\">";
		//	echo "\n  onsubmit=\"mt_post(event, '', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { alert('thx');});\">";
		//	echo "\n  onsubmit=\"alert('2');\">";:x
			echo "\n  onsubmit=\"mt_post(event, '', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { wiz2();});\">";  //  XXXX

			$w = 'wiz_';
		echo     "\n<input                    type=\"hidden\" name=\"act\" value=\"wiz1\">";
		echo     "\n<input                    type=\"hidden\" name=\"js_nf\" value=\"act,".$w."zip,".$w."born,".$w."acctyp\">";  //  XXXX
			}
		else {
			echo "\n  action=\"?submitmode=contact\">";
			$w = '';
			}
		echo     "\n<input id=\"wiz_address\" type=\"text\"   name=\"".$w."address\" value=\"...\"> address";   //  javascript below will update
		echo "\n<br><input id=\"wiz_phone\"   type=\"text\"   name=\"".$w."phone\"   value=\"...\"> telephone";   //  these
		if ($acctyp & profile::ACCTYP_PROV)
		echo "\n<br><input id=\"wiz_web\"     type=\"text\"   name=\"".$w."web\"     value=\"...\"> web";   //  values
		echo "\n<br><input id=\"wiz_zip\"     type=\"text\"   name=\"".$w."zip\"     value=\"...\"> zip code";   //  values
		echo "\n<br><input id=\"wiz_nat\"     type=\"text\"   name=\"".$w."nat\"     value=\"...\"> nation";   //  values
		echo "\n<br><input id=\"wiz_brn\"     type=\"text\"   name=\"".$w."born\"    value=\"...\"> born";   //  values
		echo     "\n<input                    type=\"hidden\" name=\"".$w."acctyp\"  value=\"".$acctyp."\">";
		if (is_null($m))
			echo "\n<br><input                    type=\"submit\" name=\"".$w."submit\"  value=\"submit\">";
		else
			static::edit_submit_wiz($m);
		echo "\n</form>";
		}

	static public function contact_refresh($un, $fn, $pf) {
		//  Dump user contact info to display (typically wihin a hidden div)
		//    $un   usernamei  
		global $data_dir;

		/*  using readfile() purposely insures PHP commands in file are ignored, inline javascript?  */
		//  FUTURE: read this into a buffer, skip outputing sensative information

echo "\n<br><br>PROFILE TYPE: ".$pf;
	       	readfile($data_dir.'/users/'.$un.'_profile');
		if (($pf & profile::ACCTYP_MASK) == profile::ACCTYP_PROV)
	//	if (($p_flgs & profile::ACCTYP_MASK) == profile::ACCTYP_PROV)
	        	readfile($data_dir.'/users/'.$un.'_addr');  //  provider
		else
	        	readfile($data_dir.'/users/'.$un.'_pddr');  //  personal
		echo "\n<span id=\"profile_fname\">".$fn.'</span>';
		echo "\n<span id=\"profile_flags\">".$pf."</span>\n";
		}

	static public function edit_form_sector_list(&$list) {
		//  FUTURE - this function duplicates similar code is neighboting function?
		$i = 0;  $s = '';
		foreach ($list as $k => $v) {
			$s .= ($i < 1) ? $v : ', '.$v;	
			$i++;
			}
		return ($s);
		}

	static public function edit_form_sector(&$sa, $af = 0) {
		//  Present provider business sector edit form, typically hidden until user clicks to edit.
		//  Default values will be updated dynamically via javascript elsewhere using field name.
		//    sa    multi-imensional array of arrays: current, draft, secondary, special
		//    $af     account flags (i.e. personal, provider, ...)
		$acctyp = $af & profile::ACCTYP_MASK;
		echo "<p style=\"margin: 0;\"> Service Catagories </p>";
//		echo "\n<form style=\"display0: inline-block; margin: 4px;\" name=\"form_sector\" method=\"post\"";
		echo "\n<form name=\"form_sector\" method=\"post\"";
//		echo "\n  action=\"?submitmode=sector\" enctype=\"multtipart/form-data\">";
		echo "\n  action=\"?submitmode=sector\">";
		echo     "\n<input type=\"hidden\"   name=\"primary\"  value=\"";
		echo static::edit_form_sector_list($sa['primary'])."\">";
		echo     "\n<input type=\"text\"   name=\"primary_dis\"  value=\"";
		echo static::edit_form_sector_list($sa['primary'])."\" disabled> primary";
		echo "\n<br><input type=\"text\"     name=\"primary-draft\"     value=\"";
		echo static::edit_form_sector_list($sa['primary-draft'])."\"> primary draft";
		echo "\n<br><input type=\"text\" name=\"secondary\" value=\"";
		echo static::edit_form_sector_list($sa['secondary'])."\"> secondary";
		echo "\n<br><input type=\"text\" name=\"special\"   value=\"";
		echo static::edit_form_sector_list($sa['special'])."\"> specialities";
		echo     "\n<input type=\"hidden\" name=\"acctyp\" value=\"".$acctyp."\">";

		echo     "\n<input type=\"submit\" name=\"submit\" value=\"submit\">";
		echo     "\n<input type=\"checkbox\" name=\"commit_prime\"> commit primary";
		echo "\n</form>";
		}

	static public function edit_form_settings() {
		//  Present provider business sector edit form, typically hidden until user clicks to edit.
		//  Default values will be updated dynamically via javascript elsewhere using field name.
		echo "<dl style=\"margin: 0.5em;\"><dt>Change Password ";
	//	echo "\n<form name=\"form_settings\" method=\"post\" style=\"margin: 0;\" ";
		echo "\n<form name=\"form_settings\" method=\"post\" action=\"?submitmode=settings\">";
	//	echo "\n  action=\"?submitmode=settings\">";
		echo "\n<dd><input type=\"password\" name=\"current\"> current";
		echo "\n<dd><input type=\"password\" name=\"newpswd\"> new";
		echo "\n<dd><input type=\"password\" name=\"nwcpswd\"> new confirm";
		echo "\n<dd><input type=\"submit\"   name=\"submit\" value=\"submit\">";
		echo "\n</form></dl>";
		}

	static public function edit_button($c, $x = 4, $y = 8, $m = 'edit') {
		global $edit_urla, $admins;

		$u = session_username_active();
		$out =  '<div style="position: absolute; top: '.$x.'px; right: '.$y.'px;">';
		if (in_array($u, $admins)) {
			if ($c == 'submit_urepe') {
				$out .= '[<a href='.$edit_urla.'urep>urep</a> ';
				$out .= '<a href='.$edit_urla.'users/'.$u.'_urep>debug</a>] ';
				}
			else if ($c == 'submit_image')
				$out .= '[<a href='.$edit_urla.'users/'.$u.'_profile>debug</a>] ';
			else if ($c == 'submit_sector')
				$out .= '[<a href='.$edit_urla.'users/'.$u.'_spec>debug</a>] ';
			}
//		$cl = "alert('clicked: ".$m."');";
		$out .= "<input type=\"button\" onclick=\"display_toggle('".$c."');\" value=\"".$m.'">';
		$out .= '</div>';
		return $out;
		}

	static private function urep_form_out($qa, $w = '') {
		//  Present user representation form, typically hidden until user clicks to edit.
		//  Note, the open and close form tags should be supplied above call to this
		//    $qa    array of arrays [quad1] => (1, 2, 3, 4), [quad2] => (1, 2, 3, 4)
		//    $w     DOM element prefix string,
		//           helps disambiguate this form from similar ones elsewhere on page?
		global $local;  //  FUTURE - make this a public static class property?
		global $debug_mask;

		if ($debug_mask & DEBUG_UREP_V2) {
		    foreach ($qa as $k => $v) {
			echo "\n<div style=\"float: left; margin-right: 4px; font-size: smaller;\">";
			$i = 0;
			foreach ($v as $kk => $vv) {
				echo ($i < 4) ? "\n<span>" : "\n<span style=\"background-color: lightgrey\">";
				if ($i < 5) {
				    echo ($i ? '<br>' : '');

				    echo ($i > 0) ?
				        "<a onclick=\"urep_form_rankmove('".$k."', '".$kk."', '+', '".$w."');\">+</a>" :
				        "<span style=\"color: white;\">+</span>";

				    echo "\n<input type=\"hidden\" name=\"ua_".$w.$k.'_'.$kk."\" value=\"".$vv."\">";
				    $vv_t = (isset($local[$vv])) ? $local[$vv] : $vv;
				    echo "\n<span id=\"ua_".$w.$k.'_'.$kk.'">'.$vv_t.'</span>';

				    if ($i < 4)
				        echo "\n<a onclick=\"urep_form_rankmove('".$k."', '".$kk."', '-', '".$w."');\">--</a>";
				    }
				echo '</span>';
				$i++;
				}

			//  present drop down selector
			$spsp = '';
			echo "\n<br><select name=\"urep_\"".$k." onchange=\"urep_form_rankmv2('".$k."', event.target.value, '".$w."');\">";
			foreach ($v as $kk => $vv) {
		            $vv_t = (isset($local[$vv])) ? $local[$vv] : $vv;
			    echo "\n  <option value=\"".$vv."\">".$vv_t."</option>";  //  selected?
			    $spsp .= "\n  <span id=\"urep_".$k."_".$vv."\" style=\"display: none;\">".$vv_t."</span>";
			    }
			echo "\n</select>";
			echo '</div>';
			//  Output list of hidden spans of localized phrases
			//  FUTURE - json instead?  ... is wizard duplicating this?
			echo $spsp;
			}
		    }
		else {
		    foreach ($qa as $k => $v) {
			echo "\n<div style=\"float: left; margin-right: 4px;\">";
			echo "<p style=\"margin: 0 0 4px; 0;\">".$k." </p>";
			$i = 0;
			foreach ($v as $kk => $vv) {
				//echo "\n<br>k: ".$k.' kk: '.$kk;
				echo ($i < 4) ? "\n<span>" : "\n<span style=\"background-color: lightgrey\">";
				echo ($i ? '<br>' : '');

				echo "<a onclick=\"urep_form_rankmove('".$k."', '".$kk."', '+', '".$w."');\">+</a>";

				echo "\n<input type=\"hidden\" name=\"ua_".$w.$k.'_'.$kk."\" value=\"".$vv."\" size=4>";
				$vv_t = (isset($local[$vv])) ? $local[$vv] : $vv;
				echo "\n<span id=\"ua_".$w.$k.'_'.$kk.'">'.$vv_t.'</span>';

				echo "\n<a onclick=\"urep_form_rankmove('".$k."', '".$kk."', '-', '".$w."');\">--</a>";
				echo '</span>';
				$i++;
				}

			echo "\n<br><select name=\"urep_\"".$k." onchange0=\"shauker_select();\">";
			foreach ($v as $kk => $vv) {
		            $vv_t = (isset($local[$vv])) ? $local[$vv] : $vv;
		            //  selected?
		            echo "\n  <option value=\"".$vv."\">".$vv_t."</option>";
			    }	
			echo "\n</select>";
			echo '</div>';
			}
		    }
		}

	static private function urep_pack_list(&$picks, &$ref) {
		//  Take previous short list, pad it with unpicked values
		//    $picks short list of previous picked list
		//    $ref   reference list of all possible items that can be picked
		//  [return] picked items top, unpicked padded to end
		//           returned by reference
		$list = array();
		if (count($picks) > 0) { 
			foreach ($picks as $k => $v) {
				array_push($list, $v);
				}
			}
		foreach ($ref as $k => $v) {
			if (!in_array($v, $list))
				array_push($list, $v);
			}
		return $list;
		}

	static public function urep_form(&$test_a, &$urep, $af = 0, $w = ' width: 244px;', $m = NULL) {
		//  Output an absolute position urep form 
		//    $test_a array list of current state
		//    $urep   array list of all possible urep values
		//    $af     account flags (i.e. personal, provider, ...)
		//    $w      css width, typically smaller than urep section so it
		//            can be inset over it with borders each side
		//    $m      NULL
		//            'wiz', 'wizwld', 'wizhth', 'wizprs', 'wizskg', 'winsym', ...
		global $debug_mask;

		$acctyp = $af & profile::ACCTYP_MASK;
		if ($m == 'wizwld')
			$pick = 0x01;
		elseif ($m == 'wizhth')
			$pick = 0x02;
		elseif ($m == 'wizprs')
			$pick = 0x04;
		elseif ($m == 'wizskg')
			$pick = 0x08;
		elseif ($m == 'wizsym')
			$pick = 0x40;
		else if (($acctyp & profile::ACCTYP_MASK) == profile::ACCTYP_PROV)
			$pick = 0x33;  //  1 + 2 + 16 + 32
		else
			$pick = 0x4F;  //  1 + 2 +  4 +  8 + 64
//		echo "\n<br><br>m: ".($m ? $m : 'NULL').", type: ".$af.', '.$acctyp.', '.profile::ACCTYP_PERS;  //  XXXX

		if (is_null($m)) {
			//  ZZZZ
			if ($debug_mask & DEBUG_UREP_EDIT) {  //  XXXX - disable workaround needed for stretchy left profile column
				$se  = 'position: absolute; top: 1em; left: 1em; padding: 4px 0 0 4px; border: 1px solid;';
				$se .= $w.' background-color: white; display: none; z-index: 1;';
				}
			else {
				$se  = 'padding: 4px 0 0 4px; border: 1px solid; display: none;';
				}
			echo "\n<div id=\"submit_urepe\" style=\"".$se."\"><!--  hidden urep/emblem edit  -->";
			}
		else
			echo "\n<div id=\"submit_urepe2\"><!--  hidden urep/emblem edit  -->";
		$my_tribute = static::urep_pack_list($test_a['contrib'],  $urep['contrib']);
		$my_health  = static::urep_pack_list($test_a['healthy'],  $urep['healthy']);
		if (($acctyp & profile::ACCTYP_MASK) == profile::ACCTYP_PROV) {
			$pv_class  = static::urep_pack_list($test_a['class'],    $urep['class']);
			$pv_spect  = static::urep_pack_list($test_a['spectrum'], $urep['spectrum']);
			if ($m == 'wizhth')  //  FUTURE - pretty sure this will change once all practioner urep wizard forms are enabled  XXXX
			$wz = " wiz_rest('n/a');";
			else
			$wz = '';
			}
		else {
			$my_other  = static::urep_pack_list($test_a['others'],   $urep['others']);
			$my_look   = static::urep_pack_list($test_a['looking'],  $urep['looking']);
			if ($m == 'wizsym')
			$wz = " wiz_rest('n/a');";
			else
			$wz = '';
			}
	//	echo "\n<form style=\"display: inline-block; margin: 0; padding: 4px;\" name=\"form_user_rep\" method=\"post\"";
		echo "\n<form name=\"form_user_rep\" method=\"post\"";
		if ($m == 'wizwld' ||  $m == 'wizhth' || $m == 'wizprs' ||  $m == 'wizskg' || $m == 'wizcls' || $m == 'wizspc' ||  $m == 'wizsym')
//			echo "\n  onsubmit=\"mt_post(event, '', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { wiz2();});\">";
//		elseif ($m == 'wizsym')
			echo "\n  onsubmit=\"mt_post(event, '', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { wiz2(); ".$wz." });\">";  //  WWWW
	//		echo "\n  onsubmit=\"mt_post(event, '', '/rest/wiz1.php', 'wiz_01_result', function () { }, function () { wiz2(); wiz_rest('n/a'); });\">";  //  WWWW
		else
			echo "\n  action=\"?submitmode=urep\" enctype=\"multipart/form-data\">";
		echo "\n<input type=\"hidden\" name=\"acctyp\" value=\"".$acctyp."\">";

//		ap($test_a);

	if ($pick != 64)  {
//	if (!($m != 'wizsym')) {
//	if (1) {
		echo "\n<div style=\"float: left; margin-right: 4px;\">";
		$fo = array();
		if ($pick &  1) array_push($fo, $my_tribute);
		if ($pick &  2) array_push($fo, $my_health);
		if ($pick &  4) array_push($fo, $my_other);
		if ($pick &  8) array_push($fo, $my_look);
		if ($pick & 16) array_push($fo, $pv_class);
		if ($pick & 32) array_push($fo, $pv_spect);
		if (is_null($m))
			static::urep_form_out($fo);
		else
			static::urep_form_out($fo, 'wiz_');
//		static::urep_form_out(
//		    ($acctyp == profile::ACCTYP_PROV
//		    ? array($my_tribute, $my_health, $pv_class, $pv_spect)
//		    : array($my_tribute, $my_health, $my_other, $my_look)),
//		    $m );
		echo "</div>";
		}

	//	if ($acctyp == profile::ACCTYP_PERS) {  /*  ---  */
		if ($pick & 64) {  /*  ---  */
		echo "\n<div style=\"float: left; margin: 0 4px 4px 0;\">";
//		  echo "\nEmblem";
//		  $emblem = array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight');
		  $i = 0;  $n = count($urep['emblem']);
		  foreach($urep['emblem'] as $k => $v) {
//			if (!($i & 3)) echo "\n<br>";
			echo "\n<div style=\"display: inline-block;\"><img";
		//	echo "\n  src=/gfx-stock/emblem_".$v."_144x.png style=\"width: 36px; padding: 4px;\">";
			echo "\n  src=/gfx-stock/".$v."_144x.png style=\"width: 36px; padding: 4px;\">";
			echo "\n<br><input name=\"emblem_".$k."\" id=\"emblem_".$k."\" value=\"".$v."\"";
			echo "\n  type=\"checkbox\" onclick=\"emblem_ch_sel('".$i."', '".$n."');\"";
			//  check if ...[0] isset first?
			if ($test_a['emblem'][0] == $v) echo ' checked';
			echo '>';
			echo '</div>';
			$i++;
			}
		echo "</div>";
		echo "<div style=\"clear: both;\"></div>";
		}  /*  ---  */

		if (is_null($m))
			echo "\n<br><input     type=\"submit\" name=\"submit\" value=\"submit\">";
		else {
			profile::$uf = $af;  //  FUTURE - move this assign further up?
			static::edit_submit_wiz($m);
			}

//		if (is_null($m))
//			echo "\n<br><input                    type=\"submit\" name=\"".$w."submit\"  value=\"submit\">";
//		else
//			static::edit_submit_wiz($m, $s);

		echo "\n<div style=\"clear: both;\"></div>";  //  ZZZZ helps multi-form from hanging strangely outside boarder?
		echo "\n</form>";
		if (is_null($m)) {
			echo "\n<button onclick=\"display_toggle('submit_urepe');\" style=\"margin: 0;\">Cancel</button>";
			}
		echo "</div><!--  hidden urep/emblem edit [end]  -->";
		}

//	static public function urep_wiz($un, $pf, $pk = 'wiz') {
	static public function wiz_form_next($un, $pf, $pk = 'wiz') {
		//    $un   username?
		//    $pf   account flags
		//    $pk   specific user representation catagory
		global $data_dir;

		if ($pk == 'wizimg') {
			profile::edit_form($pk);
			}
		else {
			$urep_a2 = array();
			lists::get($data_dir.'/urep', $urep_a2);  //  all possible values
			//  Note - its possible file does not exist for new account
			$urep_c2 = array();
			lists::get($data_dir.'/users/'.$un.'_urep', $urep_c2);  //  current values
			profile::urep_form($urep_c2, $urep_a2, $pf, '', $pk);
			}
		}

	}  /*  class profile [end]  */  ?>

