<?PHP
//  typically this is a staticly include into index.php
//  used to populate 'signup' portion of home page

/*  function mail_activation($a) ...
    moved to .../email/activation/php  */

/*  using file_get_contents() purposely insures PHP commands in file are ignored???  */
//$m = file_get_contents($data_dir.'/is/profile');

//echo "\n<div style=\"margin: 0 4px 4px 4px; font-size: larger;\">";
//section_head(NULL, NULL, $m);
//echo '</div>';

if (isset($_GET['activate'])) {
	//  --- RECIEVE ACTIVATION ---  //
	$is_msg_id = 'profile_info_activate';
	echo "\n<!--  ||  SIGNUP ACTIVATION  ||  -->\n<div style=\"padding: 4px;\"><b>Activation</b>";
	$signup = array();
	if (isset($_GET['un']))
		$signup['handl'] = $_GET['un'];
	if (isset($_GET['code']))
		$signup['act_code'] = $_GET['code'];
	$new_act = array('new', $signup['handl'], date('Y-m-d H:i:s'), 'password');

	/*  try to match activation record, replace with new account record  */
	if ($msg = jjj($new_act, $signup))
		echo "\n<p>".$msg."</p>";
	else {
		echo "\n<p>username: ".$signup['handl'];
		echo "\n<br>suggested password: ".$new_act[USERACCT_HASH]." - please make a note of it </p>";
		$b = array();
//		if ($signup['flags'] & 16) {
		if ($signup['flags'] & profile::ACCTYP_PRVACT) {
			$b['profile_address'] = '[no-address]';
			$b['profile_phone'] = '[no-phone]';
			$b['profile_web'] =   '[no-web]';
			$b['profile_zip'] =   '[no-zip]';
			$b['profile_nat'] =   '[no-nation]';
			if (!profile_put($data_dir.'/users/'.$signup['handl'].'_addr', $b))
//				echo "\n<p>Initial address empty</p>";
//			else
				echo "\n<p>There was a problem creating your profile.</p>";
			}
		else {  //  default personal contact
			$b['profile_address'] = '[no-address]';
			$b['profile_phone'] = '[no-phone]';
			$b['profile_zip'] =   '[no-zip]';
			$b['profile_nat'] =   '[no-nation]';
			if (!profile_put($data_dir.'/users/'.$signup['handl'].'_pddr', $b))
//				echo "\n<p>Initial address empty</p>";
//			else
				echo "\n<p>There was a problem creating your profile.</p>";
			}
		$a = array();
		$a['profile_goals'] = '[no-goals]';
		$a['profile_itags'] = '[new-user]';
		$a['profile_img'] =   'newuser_avatar.png';
		$a['profile_about'] = '[no-about]';
		if (profile_put($data_dir.'/users/'.$signup['handl'].'_profile', $a)) {
			//  YYYY
		//	echo "\n<br>new profle record created";
			if (!isset($_SESSION['username_dg'])) {
			//	echo "\n<br>session username unset";
				//  this should attempt to automatically login?
				$_SESSION['username_dg'] = $_GET['un'];
				login_check($_GET['un'], '');
				}
		//	else
		//		echo "\n<br>session username already set";

		//	echo "\n<p>Please complete your profile - stay tuned ".$im."</p>";
			echo "\n<p>Please complete your profile</p>";
			$im = $file_dir.'/avatar_'.$signup['handl'].'_min.gif';
		//	if ($msg = image_make_min($im, '/public/holistik/stage/gfx-stock/newuser_avatar.png', 'png'))
		//	if ($msg = image_make_min($im, '../gfx-stock/newuser_avatar.png', 'png'))
			if ($msg = image_make_min($im, $file_dir.'/newuser_avatar.png', 'png'))
				echo "\n<p>image_make_min: ".$msg."</p>";
			}
		else
			echo "\n<p>There was a problem creating your profile.</p>";
		}
	/*  FUTURE, user is not logged at this point, ask them to do so, also profile link to change password  */
	/*
	  - read in existing user list
	  - if validation row missing, provide display error message, provide retry link
	  - provide form field with suggested password (user can type over it)
	    [ skip this if user was only changing email address ]
	  - backup old user list (strip out current validation row)
	  - if new account
	      - update user list with new row: [new #ID], username, year/month/day, hashed password
	        create user profile file: fname, email
	        display account activated, welcome, please login 
	  - if password reset
	    [ there may be a legimate establish account row, but with password that has been forgotten ]
	      - update user list existing row: ID,        username, year/month/day, hashed password
	        display account password/email address has been updated, welcome back, please login 
	    */
	echo "</div><!--  ||  SIGNUP ACTIVATION  [end]  ||  -->";
	}
else {
	if (isset($_GET['submit'])) {
		$abort = TRUE;
		$signup_form = false;
		$is_msg_id = 'profile_info_validate';
		//  --- RECIEVE FORM SUBMIT, VALIDATION ---  //
		echo "\n<!--  ||  SIGNUP VALIDATION ||  -->\n<div style=\"padding: 4px;\"><b>Validation</b>";
		$signup = array();
		$signup['fname'] = $_POST['fname'];
		$signup['email'] = $_POST['email'];
		$signup['flags'] = 0;
		if (isset($_POST['acctyp'])) {
			if ($_POST['acctyp'] == 'provider')
				$signup['flags'] = 1;
			//	$signup['flags'] = profile::ACCTYP_PROV;
			}
//		$signup['flags'] = (isset($_POST['flags'])) ? $_POST['flags'] : 0;
		//  if signup form omits username, attempt to generate a random one based on fname
		//  FUTURE: this needs to be airtight, enhance to insure random one doesn't also collide
		//  FUTURE: signup form could provide unreasonably long username, truncate it

//		$signup['handl'] = strlen($_POST['handl']) > 0 ? $_POST['handl'] :
//		  substr(trim_all(strtolower($signup['fname'])), 0, 8)
//		  .rand(0,9).rand(0,9).rand(0,9).rand(0,9);

		if (strlen($_POST['handl']) > 0) {
			//  Check for invalid characters in preferred username
			$signup['handl'] = substr(trim_all(strtolower($_POST['handl'])), 0, 16);
			if ($signup['handl'] == $_POST['handl'] && strlen($signup['handl']) > 3)
				$abort = FALSE;
			else
				echo "\n<p>Preferred account username has unsupported characters or length.</p>";
			}
		else {
			$signup['handl'] = 
			  substr(trim_all(strtolower($signup['fname'])), 0, 8)
			  .rand(0,9).rand(0,9).rand(0,9).rand(0,9);
			$abort = FALSE;
			}

		if (!$abort) {
			//  TODO - generate retrievable hashcode  */
			$signup['act_code'] = act_digit().act_digit().act_digit().act_digit().act_digit().act_digit();
//			$new_act = array('*', $signup['handl'], '2015-11-10',$signup['act_code'], $signup['fname'], $signup['email'], $signup['flags']);
			$new_act = array('*', $signup['handl'], date('Y-m-d H:i:s'),
			  $signup['act_code'], $signup['fname'], $signup['email'], $signup['flags']);
			if ($msg = fff($new_act))
				echo "\n<p>".$msg."</p>";
			else {
				echo "\n<p>An account activation email has been send to you, ".$signup['email']."</p>";
				MyTake\mail_activation($signup);  ?>
<!--  http://stage.holistik.org/?signup&activate&code=QOFYOA&un=wizard  -->
<form method="get" action="/profile/">
<input     name="activate" type="hidden">
<input     name="signup"   type="hidden">
<input     name="un"    type="hidden" value="<?PHP  echo $signup['handl'];  ?>">
<input name="code"     type="text">
<input name="submit"   type="submit">
</form>  <?PHP 
				}
			}
		/*
		  - read in existing user list
		  - if reset password check
		    [ there may be a legimate established account row, but with password that has been forgotten ]
		  - if not reset password checked, perform pre-existing check
		    if username provided and not available (display sorry message)
		    or if no username provided, generate one
		  - backup old user list (strip out validation rows > 1 month old)
		    generate activation access token
		    update user list with new row: *, username, year/month/day, token, fname, new email
		    send to OLD email validation URL/link to activate account
		    new email vs new/reset password should be slight different activation code   */
		echo "</div><!--  ||  SIGNUP VALIDATION [end]  ||  -->";
		}
	else {
		$signup_form = true;
		$is_msg_id = 'profile_info_signup';
		}

	//  --- PRESENT SIGNUP FORM ---  //
	if ($signup_form) {  //  === SIGNUP: SHOW FORM ===  // 
		echo "\n<!--  ||  SIGNUP FORM ||  --><div style=\"padding: 4px;\"><b>Signup</b>";  ?>
<!--  p>These Areas of Life are listed in no particular order.&nbsp;
Please adjust their order with most important to you on top, least on the bottom. </p>
  <ul>
  <li>Health
  <li>Navigating Life
  <li>  Body / Mind / Recreation
  <li>Food
  <li>Environment
  <li>Spiritual
  <li>Family and Community
  <li>Career / Business / Money
  </ul  -->

<!--  p>We'll need to confirm your living essence (confirm you are not a SkyNet drone).&nbsp;
Please provide an appropraite email address.&nbsp; You can change it later.&nbsp;
Once confirmed you'll get a chance to choose a password. </p  --> 

<form name="signup" method="post" onsubmit="return signup_form_okay();" action="?signup&submit">
<input     name="fname" size="40"       type="text"> first name / nick name (*)
<br><input name="email" size="40"       type="text" value="<?PHP  echo $_GET['e'];  ?>"> email address (*)
<br><input name="handl" size="40"       type="text"> preferred account username (if left empty one will be generated for you)
<br>Account type:
<?php if(!empty($_GET['type']) && $_GET['type'] == 'provider') {?>
    <input name="acctyp" value="personal" type="radio"> personal
    <input name="acctyp" value="provider" type="radio" checked> practitioner
<?php } else {?>
    <input name="acctyp" value="personal" type="radio" checked> personal
    <input name="acctyp" value="provider" type="radio"> practitioner
<?php }?>

<br><input name="submit" value="submit" type="submit">
<span id="signup_form_msg" style="color: #ff0000;">...</span>
<!--  input value="cancel"        type="submit"  -->
</form>  <?PHP /*  
		  - enter username, fname,and/or email, 
		    fill in form,
		    click here to reset password,
		    click here to change email address (only if logged in)
		    submit form  */
		echo "</div><!--  ||  SIGNUP FORM [end]  ||  -->";
		}  //  === SIGNUP: SHOW FORM [end] ===  // 
	}  //  --- PRESENT SIGNUP FORM [end] ---  ///

echo "\n<script>";
echo "\nif (e = document.getElementById('".$is_msg_id."')) e.style.display = 'block';";
echo "\n</script>";

