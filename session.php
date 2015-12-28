<?PHP

//  FUTURE: make the following code a function call,
//  FUTURE: return 0 if user not logged in so downstream code can disengage appropriately  
session_start();

//  FUTURE, make the following a class

function session_detect() {
	//  FUTURE, only allow if NOT isprod?
	if (isset($_GET['debug'])) {
		if ($_GET['debug'] < 1)
			unset($_SESSION['debug']);
		else
			$_SESSION['debug'] = $_GET['debug'];
		}
	if (!isset($_POST['logout']) && !isset($_GET['logout']) && isset($_SESSION['username_dg'])) {
		/*  preserve session unless logout detected  */
		$mode = 1;
		}
	else if	(isset($_POST['username_dg']) && isset($_POST['password']) && (!isset($_SESSION['username_dg']))) {
		/*  login attempt detected, setup session username_dg  */
		$mode = 2;
		$_SESSION['username_dg'] = $_POST['username_dg'];
		}
	else if (isset($_GET['username_dg']) && isset($_GET['password']) && (!isset($_SESSION['username_dg']))) {
		/*  login attempt detected, setup session username_dg  */
		$mode = 2;
		$_SESSION['username_dg'] = $_GET['username_dg'];
		}
	else {
		$mode = 0;
		}
	if ($mode < 1) {
		/*  logout or inconsistent session detected, clear session username_dg, uid_dg  */
		if (isset($_SESSION['username_dg'])) {
			//  FUTURE: Until cookies work consistently across browsers,
			//  FUTURE: fallback to stuffing last username in a session.
			//  FUTURE: Browser restart won't see this, need a cookies to work for that.
			$_SESSION['username_dg_prev'] = $_SESSION['username_dg'];
			unset($_SESSION['username_dg']);
			}
		if (isset($_SESSION['uid_dg']))
			unset($_SESSION['uid_dg']);
		}
	}

function login_check($u, $p) {
	global $feature_mask;
	global $cookie_expire;
	global $cookie_url;
	global $login_data;
	global $data_dir;

	/*  Check login credentials against subscription database.  */
	/*  return 0 and set $_SESSION['uid_dg'] if username/password accepted
	/*  otherwise return an error string  */
	/*  Future 1: stop storing password in session  */
	/*  Future 2: still using http for page display, have login  */
	/*            use AJAX https call to authentication service  */
	if (0) {  // ***
		$query = "select * from users where login=\"".$u."\"";
		if (!mysql_connect("localhost", "subscribe", "..."))
			return ( "no db login, no db subscription ");
		else {
			if (!mysql_select_db("subscription"))
				return ("db login ok, no db subscription ");
			else {
				if (!$result = mysql_query($query))
					return ("db result empty");
				else if (!$row = mysql_fetch_row($result))
					return ("bad username");
				else if ($row[1] !=  md5($p))
					return ("bad password");
				else {
					$_SESSION['uid_dg'] = $row[3];
					return (0);
					}
				}
			}
		}
	else { // ***
		$user_profiles = array();
		$file_profiles = $data_dir.'/'.$login_data;
		if (get_user_profiles($file_profiles, $user_profiles)) {
			foreach ($user_profiles as $ua => $ub) {
				if ((isset($ub['handle'])) && trim($ub['handle']) == $u) {
					$_SESSION['uid_dg'] = $ua;
					if ($feature_mask & FEATURE_COOK) {
						$exp = time() + $cookie_expire;
						setcookie( "username_dg",          $u, $exp, '/', $cookie_url);
						setcookie( "username_dg_expire", $exp, $exp, '/', $cookie_url);
						//  FUTURE: above cookie setting is moot?  ... need to follow with a request to server?
						}
					return (0);
					}
				}
			return ("bad username");
			}
		else
			return ("no user profiles file");
		} // ***
	}

function session_userid_active() {
	//  Return User ID if user is actively logged in or NULL 
	//    NULL  not active
	//    UID   user login active
	return isset($_SESSION['uid_dg']) ? $_SESSION['uid_dg'] : NULL;
	}

function session_username_active() {
	//  Return username if user is actively logged in or NULL 
	if (session_userid_active()) {
		if (isset($_SESSION['username_dg']))
			return $_SESSION['username_dg'];
		}
	return NULL;
	}

function login_state(&$out, $hint = NULL) {
	global $feature_mask;
	global $cookie_expire;
	global $cookie_url;
	global $menu_mark, $menu_full;

	/*  Assumes a form with name=login, type=post is being used.  */
	/*  return 1  login accepted - output logout  */
	/*  return 0  invalid login - output username, password form  */
	if (isset($_POST['username_dg']) && isset($_POST['password'])) {  // clasic POST form
		unset($_SESSION['uid_dg']);
		/*  login_check will set $_SESSION['uid_dg'] for valid login  */
		if ($msg = login_check($_POST['username_dg'], $_POST['password'])) {
			//  echo "<span style=\"color: #ff0000;\">".$msg."</span> ";
			unset($_SESSION['username_dg']);
			}
		}
	if (isset($_GET['username_dg']) && isset($_GET['password'])) {  // new AJAX form
		unset($_SESSION['uid_dg']);
		/*  login_check will set $_SESSION['uid_dg'] for valid login  */
		if ($msg = login_check($_GET['username_dg'], $_GET['password'])) {
			//  echo "<span style=\"color: #ff0000;\">".$msg."</span> ";
			unset($_SESSION['username_dg']);
			}
		}
	if (isset($_SESSION['uid_dg'])) {
		$out  = "\n<form method=\"POST\" action=\"\" name=\"login\" style=\"display: inline-block; vertical-align: top;\">";
		$out .= "<input name=\"logout\" value=\"yes\" type=\"hidden\">";  //  FUTURE - is this still needed?
		if ($feature_mask & FEATURE_PROFILE)
			$out .= " <a href=\"javascript:head_profile();\">".$_SESSION['username_dg'].'</a>';
		else
			$out .= $_SESSION['username_dg']." ";
		if ($hint) {
			if (isset($menu_mark))  //  if no menu string defined, conserve space and skip new line
				$out .= ' '.$menu_mark;
			$out .= "\n</form>";
			//  omit mini avatar
			}
		else {
			if (isset($menu_mark))  //  if no menu string defined, conserve space and skip new line
				$out .= "<br>".$menu_mark;
			$out .= "<br><a href=\"javascript:head_logout();\">logout</a>";
			$out .= "\n</form>";
			$out .= '<img src=/gfx/avatar_'.$_SESSION['username_dg'].'_min.gif style="margin-left: 4px;">';
			}
		return (1);
		}

	/*  got here - user is logged out  */
	$defuname = 'username';
	if ($feature_mask & FEATURE_COOK) {
		if (isset($_SESSION['username_dg_prev'])) {
			$defuname = $_SESSION['username_dg_prev'];
			$exp = time() + $cookie_expire;
			setcookie( "username_dg",   $defuname, $exp, '/', $cookie_url);
			setcookie( "username_dg_expire", $exp, $exp, '/', $cookie_url);
			//  FUTURE: above cookie setting is moot?  ... need to follow with a request to server?
			}
		else if (isset($_COOKIE['username_dg'])) {
			$defuname = $_COOKIE['username_dg'];
			unset($_SESSION['username_dg_prev']);
			}	
		}

	/*  !!!  $dflags & DFLAGS_MOBILE  */
	$out  = "\n<form method=\"POST\" action=\"\" name=\"login\" style=\"margin: 0; padding: 0; display: inline-block; vertical-align: top; background-color: pink\">";
	//  $out  = ... background-color: yellow;\">";

	$out .= "<span id=\"login_lab\" ";
	$out .= isset($msg) ? "style=\"display: none;\" " : '';
	$out .= "onClick=\"login_fields_toggle();\">login</span>";

	$out .= "\n<input size=12";
	$out .= " name=\"username_dg\" id=\"username_dg\" style=\"font-size: 10px; border: 1px solid;";
	$out .= isset($msg) ? '"' : ' display: none;"';
	$out .= " value=\"".$defuname."\" onKeyPress=\"detectKeyLogin(event)\"";
	$out .= " onfocus=\"if(this.value == 'username') {this.value = '';}\"";
	$out .= " onblur=\"if(this.value == '') {this.value = 'username';}\">";

	$out .= "<span id=\"menu_lab\"";
	$out .= isset($msg) ? ' style="display: none;" ' : '';
	$out .= '>'.$menu_mark.'</span>';

	$out .= "\n<input size=12 type=password";
	$out .= " name=\"password\" id=\"password\" style=\"font-size: 10px; border: 1px solid;";
	$out .= isset($msg) ? '"' : ' display: none;"';
	$out .= " value=\"password\" onKeyDown=\"detectKeyLogin(event)\"";
	$out .= " onfocus=\"if(this.value == 'password') {this.value = '';}\"";
	$out .= " onblur=\"if(this.value == '') {this.value = 'password';}\">";
	if (isset($msg))
		$out .= "<span id=\"msg_err\" style=\"color: #ff0000;\" onClick=\"login_fields_toggle();\">".$msg."</span>";
	else
		$out .= "<span id=\"msg_err\" style=\"display: none;\" onClick=\"login_fields_toggle();\">reset</span>";

	$out .= "<a id=\"signin_lab\" ";
	$out .= isset($msg) ? "style=\"display: none;\" " : '';
	$out .= "href=\"javascript: formpop('signup');\">signup</a> ";
//	$out .= "<div id=\"login_key\" style=\"display: inline-block;\"><a onClick=\"login_fields_toggle();\"><img0 src=/gfx/login_55x.png style=\"margin-left: 4px;\"></a></div>";

	$out .= "\n</form>";
	return (0);
	}

//  FUTURE ... declare an 'account' class, refactor the following 
const USERACCT_ID =   0;
const USERACCT_HNDL = 1;
const USERACCT_DATE = 2;
const USERACCT_HASH = 3;  //  Doubles as password
const USERACCT_FNAM = 4;
const USERACCT_MAIL = 5;

function get_user_profiles($file, &$users, &$raw = NULL) {
	//  $file    account file: ID#, handle
	//  $users   pass in empty array, fill with [ID]['handle'] output
	//  $raw     pass in empty array to collect raw rows (optional)
	//  build array of ...
	//  if error, ...
	//  FUTURE: build a user profile class, allow custom (what this is), SQL, Facebook/OpenID support
	$result = false;
	if ($fh = fopen($file, 'r')) {
		while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
			if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
				$va = array('handle' => $data[USERACCT_HNDL]);
				if (isset($data[USERACCT_DATE])) $va['date'] = $data[USERACCT_DATE];
				if (isset($data[USERACCT_HASH])) $va['hash'] = $data[USERACCT_HASH];
				if (isset($data[USERACCT_FNAM])) $va['fnam'] = $data[USERACCT_FNAM];
				if (isset($data[USERACCT_MAIL])) $va['mail'] = $data[USERACCT_MAIL];
				$users[$data[USERACCT_ID]] = $va;
				}
			if (!is_null($raw))
				array_push($raw, $data);
			}
		$result = true;
		fclose($fh);
		}
	return $result;
	}

function put_user_profiles_raw($file, &$raw) {
	//  $file    account file: ID#, handle, ...
	//  $raw     raw array to overwirte file

	//  append log file with current account file contents (in case a revert is needed for some reason)
	//  FUTURE, fwrite($fp, date('Y-m-d H:i:s'));
	//  FUTURE, fwrite($fp, "\n-------------------\n");
	$output = shell_exec("cat ".$file." >> ".$file."_test_log;");
	$result = false;
	if ($fh = fopen($file, 'w')) {
		foreach ($raw as $k => $v) {
			$i = 0;
			foreach ($v as $kk => $vv) {
				if ($i > 0) $str = ', "'.$vv.'"';
				else        $str = $vv;
				fwrite($fh, $str);  //  FUTURE, check if returns false?
				$i++;
				}
			fwrite($fh, "\n");  //  FUTURE, check if returns false?
			}
		$result = true;
		if ($fh) fclose($fh);
		}
	//  echo "</pre>";
	return $result;
	}
?>
