<?PHP

//  FUTURE: make the following code a function call,
//  FUTURE: return 0 if user not logged in so downstream code can disengage appropriately  
session_start();

//  FUTURE, make the following a class

function ap($a) {
	echo "<pre style=\"font-size: smaller;\">";
	print_r($a);
	echo "</pre>";
	}

function file_log($file) {
	//  FUTURE - THIS IS SLOW, AVOID shell_exec, use native PHP file I/O
	//  FUTURE - provide a diff vs full past state option
	//  append log file with date stamp, followed by current active
	//  file contents (in case a revert is needed for some reason)
	$cmd = 'echo '.date('Y-m-d H:i:s').' >> '.$file.'_log';
	$out = shell_exec($cmd);
	$cmd = 'echo ------------------- >> '.$file.'_log';
	$out = shell_exec($cmd);
	$out = shell_exec("cat ".$file." >> ".$file.'_log');
	//  FUTURE - how could this fail?  ... add parameter to pass back $out
	}

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
					if ((isset($ub['hash'])) && account::password_accept($p, trim($ub['hash']))) {
						//  FUTURE - for now 'password' is backdoor to NO PASSWORD!
						$_SESSION['uid_dg'] = $ua;
						$_SESSION['uid_dg_flgs'] = $ub['flgs'];
						if ($feature_mask & FEATURE_COOK) {
							$exp = time() + $cookie_expire;
							setcookie( "username_dg",          $u, $exp, '/', $cookie_url);
							setcookie( "username_dg_expire", $exp, $exp, '/', $cookie_url);
							//  FUTURE: above cookie setting is moot?  ... need to follow with a request to server?
							}
						return (0);
						}
					return ("bad password");
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
	global $menu_helo, $menu_mark, $menu_full;

	/*  Assumes a form with name=login, type=post is being used.  */
	/*    hint    adjust formatting/presentation of login form
	/*            depends also on javascript login_fields_toggle
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
		if ($hint) {
			if (isset($menu_mark))  //  if no menu string defined, conserve space and skip new line
				$out .= ' '.$menu_mark;
			$out .= "<a href=\"javascript:head_logout();\">logout</a> | ";
			if ($feature_mask & FEATURE_PROFILE)
				$out .= " <a href=\"javascript:head_profile();\">".$_SESSION['username_dg'].'</a>';
			else
				$out .= $_SESSION['username_dg']." ";
			$out .= "\n</form>";
			//  omit mini avatar
			}
		else {
			if ($feature_mask & FEATURE_PROFILE)
				$out .= ' '.(isset($menu_helo) ? $menu_helo : '').'<a href="javascript:head_profile();">'.$_SESSION['username_dg'].'</a>';
			//	$out .= " <a href=\"javascript:head_profile();\">".$_SESSION['username_dg'].'</a>';
			else
				$out .= $_SESSION['username_dg']." ";
			if (isset($menu_mark))  //  if no menu string defined, conserve space and skip new line
				$out .= "<br>".$menu_mark;
			$out .= "<br><a href=\"javascript:head_logout();\">logout</a>";
			$out .= "\n</form>";
			$out .= '<a href="javascript:head_profile();"><img src=/gfx-upload/avatar_'.$_SESSION['username_dg'].'_min.gif style="margin-left: 4px;"></a>';
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
	$out  = "\n<form method=\"POST\" action=\"\" name=\"login\" style=\"margin: 0; padding: 0; display: inline-block; vertical-align: top;\">";
	//  $out  = ... background-color: yellow;\">";

	$out .= "<span id=\"login_lab\"";
//	$out .= isset($msg) ? " style=\"display: none;\">" : '>';
	$out .= isset($msg) ? " style=\"display: none;\">" : (isset($hint) ? '>' : ' style="display: block;">');
	$out .= "<span onClick=\"login_fields_toggle();\">login</span>";
	$out .= isset($hint) ? ' | </span>' : '</span>';

	$out .= "\n<input size=12";
	$out .= " name=\"username_dg\" id=\"username_dg\" style=\"font-size: 10px; border: 1px solid;";
//	$out .= isset($msg) ? '"' : ' display: none;"';
	$out .= isset($msg) ? (isset($hint) ? '"' : ' display: block;"') : ' display: none;"';
	$out .= " value=\"".$defuname."\" onKeyPress=\"detectKeyLogin(event)\"";
	$out .= " onfocus=\"if(this.value == 'username') {this.value = '';}\"";
	$out .= " onblur=\"if(this.value == '') {this.value = 'username';}\">";

	$out .= "<span id=\"menu_lab\"";
	$out .= isset($msg) ? ' style="display: none;" ' : (isset($hint) ? '' : ' style="display: block;"');
	$out .= '>'.$menu_mark.'</span>';

	$out .= "\n<input size=12 type=password";
	$out .= " name=\"password\" id=\"password\" style=\"font-size: 10px; border: 1px solid;";
	$out .= isset($msg) ? (isset($hint) ? '"' : ' display: block;"') : ' display: none;"';
	$out .= " value=\"password\" onKeyDown=\"detectKeyLogin(event)\"";
	$out .= " onfocus=\"if(this.value == 'password') {this.value = '';}\"";
	$out .= " onblur=\"if(this.value == '') {this.value = 'password';}\">";
	$out .= isset($hint) ? ' ' : '';
	if (isset($msg))
		$out .= "<span id=\"msg_err\" style=\"color: #ff0000;\" onClick=\"login_fields_toggle();\">".$msg."</span>";
	else
		$out .= "<span id=\"msg_err\" style=\"display: none;\" onClick=\"login_fields_toggle();\">reset</span>";

	$out .= "<a id=\"signin_lab\" ";
//	$out .= isset($msg) ? "style=\"display: none;\" " : "style=\"display: block;\" ";
	$out .= isset($msg) ? "style=\"display: none;\" " : (isset($hint) ? ' ' : ' style="display: block;" ');
	$out .= "href=\"javascript: formpop('signup');\">signup</a> ";
//	$out .= "<div id=\"login_key\" style=\"display: inline-block;\"><a onClick=\"login_fields_toggle();\"><img0 src=/gfx/login_55x.png style=\"margin-left: 4px;\"></a></div>";

	$out .= "\n</form>";
	if (is_null($hint))
		$out .= '<img src=/gfx-stock/avatar_nologin_min.png style="margin-left: 4px;">';
	return (0);
	}

//  FUTURE ... declare an 'account' class, refactor the following 
const USERACCT_ID =   0;
const USERACCT_HNDL = 1;
const USERACCT_DATE = 2;
const USERACCT_HASH = 3;  //  Doubles as password
const USERACCT_FNAM = 4;
const USERACCT_MAIL = 5;
const USERACCT_FLGS = 6;  //  flags, account type (optional?)

class account {

	public static function password_make($input) {
		/*  return new password hash  */
		//  CITATION
		//  http://php.net/manual/en/faq.passwords.php#faq.passwords.fasthash
		$salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
		$salt = base64_encode($salt);
		$salt = str_replace('+', '.', $salt);
		$salt = '$2y$10$'.$salt.'$';
		//  crypt() prepends result string with critical hash parameters
		$hash = crypt($input, $salt);
//		echo "\n<br>salt: &nbsp; <tt>".$salt."</tt>\n<br>hash: <tt>".$hash.'</tt>';
	//	echo "\n<br>equal: &nbsp; <tt>".$hash." / ".crypt($input, $hash).'</tt>';
	//	if (hash_equals($hash, crypt($input, $hash)))
//		CITATION - not available before PHP 5.5
//		http://php.net/manual/en/function.password-hash.php
//		$hash = password_hash($input, PASSWORD_DEFAULT);
//		if (password_verify($input, $hash))
			return $hash;
	//	else
	//		return false;
		}

	public static function password_accept($input, $hash) {
		/*  indicate if user entered password matches stored hash  */
//		CITATION - not available before PHP 5.5
//		if (password_hash($input, PASSWORD_DEFAULT) == $hash)
		//  crypt() needs reads critical hash parameters from prefix of stored hash
//		echo "\n<br>input: ".$input.", \n<br>hash: ".$hash;
		if ($hash == 'password' || $hash == crypt($input, $hash))
			return true;
		return false;
		}

	public static function get($file, &$users, $sel = NULL, &$raw = NULL) {
		//  build array of ...
		//  if error, ...
		//    $file    account file: ID#, handle
		//    $users   pass in empty array, fill with [ID]['handle'] output
		//    $raw     pass in empty array to collect raw rows (optional)
		//    $sel     NULL, process all records
		//             array of id's, skip non-mathing records
		//  FUTURE: build a user profile class, allow custom (what this is), SQL, Facebook/OpenID support
		$result = false;
		if ($fh = fopen($file, 'r')) {
			$sel_c = ($sel) ? count($sel) : 0;
			while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
				$sel_go = false;  //  true only if id match
				if (is_null($sel) || $sel_go = in_array($data[USERACCT_ID], $sel)) {
					if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
						$va = array('handle' => $data[USERACCT_HNDL]);
						if (isset($data[USERACCT_DATE])) $va['date'] = $data[USERACCT_DATE];
						if (isset($data[USERACCT_HASH])) $va['hash'] = $data[USERACCT_HASH];
						if (isset($data[USERACCT_FNAM])) $va['fnam'] = $data[USERACCT_FNAM];
						if (isset($data[USERACCT_MAIL])) $va['mail'] = $data[USERACCT_MAIL];
						if (isset($data[USERACCT_FLGS])) $va['flgs'] = $data[USERACCT_FLGS];
						$users[$data[USERACCT_ID]] = $va;
						}
					if (!is_null($raw))
						array_push($raw, $data);
					if ($sel_go) {
						//  if select array of id's to match passed in, okay to return early if all id's retrieved
						$sel_c--;
						if ($sel_c < 1)
							break;
						}
					}
				}
			$result = true;
			fclose($fh);
			}
		return $result;
		}

	private static function match(&$users, $id) {
//		echo "\n<br>id: ".$id;
		foreach ($users as $k => $v) {
		//	echo "\n<br>k: ".$k.', '.$id;
			if ($k == $id)
				return true;
			}
		return false;
		}

	public static function replace($file, &$users) {
		//  Walk account list, rewrite matching record with updated values
		//    $file    account file: ID#, handle
		//    $users   pass in empty array, fill with [ID]['handle'] output
		//    return  false if error, true otherwise
		//  if error, ...
		//  FUTURE: build a user profile class, allow custom (what this is), SQL, Facebook/OpenID support
		$result = false;
		$r = 0;
//		echo "\n<br>A";
//ap($users);
//		echo "\n<br>B";
		//  ATTEMPT WRITE LOCK
		if (mt_lock::get($file, $flock)) {  //  adds _lock suffix
			if (isset($user[USERACCT_ID]) &&  $user[USERACCT_ID] > 0)
				$id_rep = $user[USERACCT_ID];
			else
				$id_rep = NULL;
			if ($fr = fopen($file, 'r')) {
				if ($fw = fopen($file.'_w', 'w')) {
					while (($data = fgetcsv($fr, 1000, ",")) !== FALSE ) {
						if ($data[0] == NULL) {
							//  ignore empty lines
							}
						elseif (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
							if ($r == 0) { //  preprepend updated record at start of file
								foreach ($users as $k => $v) {
									$str = $k;
									$i = 1;
									foreach ($v as $kk => $vv) {
										$str .= ', "'.$vv.'"';
										$i++;
										}
									fwrite($fw, $str."\n");  //  FUTURE, check if returns false?
									}
								}
							$r++;
							//  FUTURE - check for/purge stale '*' validation record
							//  CITATION - http://php.net/manual/en/datetime.diff.php
							if (static::match($users, $data[USERACCT_ID]) === FALSE) {
								//  only write rows with non-redundant ID's
								$rec = array();
								if (isset($data[USERACCT_ID]))   $rec['id'] =   $data[USERACCT_ID];
								if (isset($data[USERACCT_HNDL])) $rec['handle'] = $data[USERACCT_HNDL];
								if (isset($data[USERACCT_DATE])) $rec['date'] = $data[USERACCT_DATE];
								if (isset($data[USERACCT_HASH])) $rec['hash'] = $data[USERACCT_HASH];
								if (isset($data[USERACCT_FNAM])) $rec['fnam'] = $data[USERACCT_FNAM];
								if (isset($data[USERACCT_MAIL])) $rec['mail'] = $data[USERACCT_MAIL];
								if (isset($data[USERACCT_FLGS])) $rec['flgs'] = $data[USERACCT_FLGS];
								$i = 0;
								foreach ($rec as $kk => $vv) {
									if ($i > 0) $str .= ', "'.$vv.'"';
									else        $str = $vv;
								//	fwrite($fw, $str);  //  FUTURE, check if returns false?
									$i++;
									}
								fwrite($fw, $str."\n");  //  FUTURE, check if returns false?
								}
			//				if ($r > 200)
			//					break;
							}
						else {  //  try to retain comment rows
							$i = 0;
							foreach ($data as $k => $v) {
								if ($i > 0) $str .= ','.$v;
								else $str = $v;
								$i++;
								}
							fwrite($fw, $str."\n");  //  FUTURE, check if returns false?
							}
						}
					fwrite($fw, "\n");  //  FUTURE, check if returns false?
					$result = true;
					}
				fclose($fw);
				}
			fclose($fr);
			}
		//  FUTURE - log previous file state, or diff
		file_log($file);
		rename($file.'_w', $file);
		//  RELEASE WRITE LOCK
		fclose($flock);
		unlink($file.'_lock');
		return $result;
		}

	}  /*  class account [end]  */

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
				if (isset($data[USERACCT_FLGS])) $va['flgs'] = $data[USERACCT_FLGS];
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
