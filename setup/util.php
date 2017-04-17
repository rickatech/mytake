<?PHP

function split_byline($a) {
	//  FUTURE - admit it, the schema should make this easier
	$b = explode('|', $a);
	$c = explode(',', $b[1]);
	return (array('title' => trim($b[0]), 'date' => trim($c[0]), 'author' => trim($c[1])));
	}

function section_head($t = NULL, $m = NULL, $sh = NULL) {
	//  consistent page and section header introduction help
	//  $t  pivot id, for a specific pivot page, boxed outline
	//  $m  set and output general section title,
	//      NULL, default to My Pivots
	//  $sh text string, boxed outline
	if ($t) {
		//  output formatted section head for all followed pivots,
		$msg = $t.' - pivot description, why it is important';
		$s = ' border: solid 1px;';
		$img = '<img src="/gfx-stock/pivot_'.$t.'_144x.png" style="float: left; width: 72px; margin: 0;">';
		}
	else if ($sh) {
		//  general top of page introfo sectio
		$msg = $sh;
		$s = ' border: solid 1px;';
		$img = '';
		}
	else {
		//  top of pivot page, bottom of specific pivot section  
		//$msg = ($m ? $m : 'My Pivots');
		$msg = ($m ? $m : '');
		$s = ' background-color: #bfbfbf;';
		$img = '';
		}

	echo "\n<div style=\"padding: 4px;".$s.'">';
	echo "\n<div class0=list_head style=\"overflow: visible; \">".$msg;
//	echo "\n<div class0=list_head style=\"overflow: visible; \">";
//	echo "\n<span style=\"font-weight: bolder;\">".$msg.'</span>';
	if (!$sh)
	echo "\n".$img.' <br style="clear: both;">';
	echo '</div>';
	echo '</div>';
	}

function beta_set() {
	$_SESSION['beta'] = TRUE;
	}

function beta_check($u) {
	//  If BETA mode is enabled (see server side config),
	//  determine if beta needs to be entered/applied for
	//    return    TRUE, flag user for beta code
	//              FALSE, no need for beta code handling
	//  requires session['beta']
	global $beta_file, $foot;
//	global $beta_key, $foot;

	$result = FALSE;
	if (isset($beta_file)) {
//	if ($beta_key === TRUE) {
	//	if ($_GET['beta'] == 'clear') {
		if (isset($_GET['beta'])) {
	//		echo "\n<br> CLEAR ";
			unset($_SESSION['beta']);
			}
	//	echo "\n<br>BETA CHECK";
		if ($u) {
		//	echo "\n<br>Login detected: you may proceed";
			if (!isset($_SESSION['beta']))
				$_SESSION['beta'] = TRUE;
			}
		elseif (isset($_SESSION['beta'])) {
		//	echo "\n<br>Not logged in, but beta session detected: you may proceed";
			}
		else {
//			$result = 'Not logged in, no beta session detected: no soup for you";
//			echo "\n<br>Not logged in, no beta session detected: no soup for you";
			$result = TRUE;
			}
		if (!$u && !$result) {
			$foot .= ' | <a href=?beta=clear>clear beta</a>';
			}
		}
	return($result);
	}

function account_glbs($uid = NULL, $public = NULL) {
	//  Obtain account and other variables for particular account
	//    $uid		NULL (defer to $public)
	//                      specific account record to lookup by uid
	//    $public           NULL (defer to $uid)
	//                      username of public profile being 
	//    return            results are placed in static class profile
	//  FUTURE - WHY IS THIS NOT MOVED TO profile class!!!!!!!
	global $data_dir, $login_data;

	if (is_null($uid) && is_null($public))
		return;
	profile::$uf = 0;
	profile::$ufn = 'N/A';
	profile::$umail = 'N/A';
	$ua = array();
	$file_profiles = $data_dir.'/'.$login_data;
	//  FUTURE - for nosql, nomemcache this can be slow reading in every account,
	//           instead add new method that reads file line by line, stops reading when account found
	if (account::get($file_profiles, $ua, ($public) ? NULL : array($uid))) {  
		if (is_null($public)) {
			profile::$ufn = $ua[$uid]['fnam'];
			if (!is_null($ua[$uid]['flgs'])) profile::$uf = $ua[$uid]['flgs'];
			profile::$umail = $ua[$uid]['mail'];
			profile::$uid = $uid;
			profile::$urec = $ua;
			}
		else {
			//  FUTURE - this won't scale unless get parameter public also includes UID
			//  FUTURE - redis / memcache to the rescue?
			foreach ($ua as $k => $v) {
				//  hunt down familiar name from profile catalog
				if ($v['handle'] == $public) {
					profile::$ufn = $v['fnam'];
					if (!is_null($v['flgs'])) profile::$uf = $v['flgs'];
					if (!is_null($v['mail'])) profile::$umail = $v['mail'];
					profile::$uid = $k;
					profile::$urec = $ua;
					break;
					}
				}
			}
		}
	}


const GALLERY_UID =    0;
const GALLERY_DATE =   1;
const GALLERY_AUTHOR = 2;
const GALLERY_TITLE =  3;
const GALLERY_HTAGS =  4;
const GALLERY_SRC =    5;

const GCAT_NEW = 1;
const GCAT_UPDATE = 2;
const GCAT_DROP = 3;

class gcat {  /*  gallery class  */
	//  collection of tools for updating gallery catalog
	//
	//  sample record: ID, date, author, title, tags, original
	//  0 rickatech_0003,
	//  1 "2016-02-18",
	//  2 rickatech,
	//  3 "Happiness Hack",
	//  4 "supplement, medicine",
	//  5 friend4-e1455649257719.png
	//  FUTURE - move this to mytake,
	//  try and devine a common parent class to power this

	//  BACKGROUND - there are two types of stores
	//  1)  catalog (e.g. artex, includes article ID and attributes)
	//  2)  list (e.g. following, essentially a key followed by a list of members
	//  Both can be represented by flat files for small prototypes and testing,
	//  however larger more advanced deployments will want to have these
	//  handled by a database, ideally with flat file archiving/backup

	static public function get($filename, $tag = NULL, $art = NULL, $usr = NULL) {
		//  Retieve set of gallery catalog rows (ideally a subset of all rows)
		//  return  array of rows
		//          NULL if no rows found or ... none public?
		//  Related: mytake get_map()
		}

	static public function update($file, $cmd, $a) {
		//  Complete rewrite gallery catalog with updated, added, dropped records
		//  file    catalog filename, including full path  
		//  cmd     NEW|UPDATE|DROP
		//  a       new/updated article record array
		//  return  true, successful
		//          false, failed/incomplete
		//  Related: ecat::update() - catalog, exchange::following_update() - list

		//  get lock file
		//  open read file
		//  open write file
		//  close write fil
		//  close read file
		//  cat new records > new file
		//  cat write file >> new file
		//  mv read archive file
		//  mv new read file
		//  close lock file

		$act_done = false;  //  set true when action for command is confirmed
		$result = false;
		if (!mt_lock::get($file, $fh))
			return ($result);  //  can't get file lock, bail

		/*  perform write processing here  */
		$str =  "# username_id, date, author, title, htags, original\n";
		fwrite($fh, $str);

		if ($cmd == GCAT_NEW) {
			//  FUTURE - following code in a small utility function?
			//           in config.php, declate fields, attributes,
			//           pass that in so gcat becomes generic catalog class
			$str  =       $a['id'];
			$str .=  ', '.$a['date'];
			$str .=  ', '.$a['author'];
			$str .= ', "'.$a['title'].'"';
			$str .= ', "'.$a['htags'].'"';
			$str .=  ', '.$a['src'];
			$str .= "\n";
			fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
			$act_done = true;
			}

		if ($fr = fopen($file, 'r')) {
			while (($data = fgetcsv($fr, 1000, ",")) !== FALSE) {
				//  skip past column titles row
				if ($data[0][0] != '#') {
					//  FUTURE - following code in a small utility function (see above)?
					if ($cmd == GCAT_UPDATE && $data[GALLERY_UID] == $a['id']) {
						$act_done = true;
						$str  =       $a['id'];
						$str .=  ', '.$a['date'];
						$str .=  ', '.$a['author'];
						$str .= ', "'.$a['title'].'"';
						$str .= ', "'.$a['htags'].'"';
						$str .=  ', '.$a['src'];
						$str .= "\n";
						fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
						}
					else {
						$i = 0;  $str = '';
						foreach ($data as $k => $v) {
							$str .= ($i < 1) ? '' : ', ';
							$str .= ((in_array($i, gallery::$field_wq)) ? '"'.$v.'"' : trim($v));
							$i++;
							}
						$str .= "\n";
						fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
						}
					}
				}
			if ($fr) fclose($fr);
			}
		else
			echo '<br>could not access gcat';

		mt_lock::release($fh);
		if ($act_done)
			rename($file, $file.'_0');
////		if ($act_done && rename($file.'_lock', $file.'_done'))  /*  testing  */
		if ($act_done && rename($file.'_lock', $file))
			$result = true;
		else {
			unlink($file.'_lock');
			//  FUTURE / SYSTEM - make system log
			echo '<br>gcat::update, could not complete action ';
			//  DANGER, if this file remains all updates are  blocked!!!
			//  FUTURE - add routine check if lock file date is
			//           older than a few minutes, then force delete
			}
		return ($result);
		}
	}

const GALLERY_MODE_CAT =  1;  //  include standard padding
const GALLERY_MODE_FORM = 2;  //  special input checkmark
const GALLERY_MODE_EXCL = 4;  //  authors to exclude, see list array
const GALLERY_MODE_INCL = 8;  //  authors to include, see list aray

class gallery {  /*  gallery class  */

	static public $field_wq = array(GALLERY_TITLE, GALLERY_HTAGS);

	static public function form($c = 'submit_gallery_image') {
		//  Present gallery image edit form, typically hidden until user clicks to edit.
		//  Default values will be updated dynamically via javascript elsewhere using field name.
		//    c     parent HTML display container
		//  requirements
		//    - external to calling this, provide div or appropriate display container
		echo "\n<img src=ghgh id=\"gallery_iprev\" style=\"width: 278px; border: solid 1px;\">";
		echo "\nPlease choose new image to upload to your gallery";
		echo "\n<form style=\"display: inline-block; background-color: grey;\" name=\"form_gallery_image\" method=\"post\"";
		echo "\n  action=\"/?dash&m2=mc&submitmode=upload\" enctype=\"multipart/form-data\">";
		//  CITATION: http://stackoverflow.com/questions/1084925/input-type-file-show-only-button
		echo "\n<input type=\"file\" id=\"imgup1\" name=\"imagefile\"";

//		echo "\n  onchange=\"document.getElementsByName('imgfn1')[0].value = document.getElementById('imgup1').value;\">";
		echo "\n  onchange=\"document.getElementsByName('imgfn1')[0].value = 'default';\">";

		echo "\n<br><input type=\"text\"   name=\"imgfn1\" value=\"...\"> filename";
		echo "\n<br><input type=\"text\"   name=\"imgfno\" hidden readonly>";  //  previous filename
		echo "\n<br><input size=\"12\"     name=\"date_d\" value=\"new\" disabled> date";
		echo     "\n<input                 name=\"date\"   value=\"new\" hidden readonly>";
		echo "\n<br><input type=\"text\"   name=\"itags\"  value=\"...\"> tags";   //  will
		echo "\n<br><input type=\"text\"   name=\"title\"  value=\"...\"> title";  //  update
		echo     "\n<input type=\"hidden\" name=\"imagepast\" value=\"...\">";    //  these
		//  initially mode = new, but clicking on image will change to edit, drop, ...
		echo "\n<br><input name=\"mode_d\" size=\"8\" value=\"new\" disabled>";  //  values?
		echo     "\n<input name=\"mode\"              value=\"new\" hidden readonly>";
		echo     "\n<input name=\"gcatid\"                          hidden readonly>";
		echo "\n<input     type=\"submit\" name=\"submit_gallery\" value=\"submit\">";
		echo "\n</form>";
		echo "\n<button onclick=\"display_toggle('".$c."');\">Cancel</button>";
		}

	static public function edit_submit() {  /*  gallery  */
		//  return  status message (successful or otherwise)
		//  FUTURE  also return a success code?
		//  FUTURE  database flag to switch from flat file to database support
		global $file_dir, $data_dir, $gcat_data;

		if (!($u = session_username_active()))
			return ('please login before posting to gallery');
		$mode = $_POST['mode'];  //  new | edit | drop
		$u = session_username_active();
		$f = $_FILES['imagefile']['name'];

		switch ($mode) {
		  case 'new':  //  NEW
			//  get next seq number
			$seq = seq_next_free($data_dir.'/gallery', $u, GALLERY_UID);
			if ($seq === false)
				return('could not obtain '.$u.' gallery next sequence');

			if (strlen($_FILES['imagefile']['name']) < 1) {
				return('could not obtain new image for gallery');
				}

			$ift = $_FILES['imagefile']['type'];
			echo       "Name: ".$_FILES['imagefile']['tmp_name'];
//			echo '\n<br>Size: '.$_FILES['imagefile']['size'];
//			echo '\n<br>Type: '.$ift.' / '.exif_imagetype($_FILES['imagefile']['tmp_name']);

			if      ($ift == 'image/jpeg')  $ext = 'jpeg'; 
			else if ($ift == 'image/png')   $ext = 'png'; 
			else $ext = NULL;
			if (is_null($ext))
				return ("Unsupported image format or image too large > 2 MBytes");

			$uid = sprintf("%s_%'.04d", $u, $seq);

			if ($_POST['imgfn1'] != 'default') {
				//  echo "\n<p>seq: ".$seq.' file: '.$_FILES['imagefile']['name'].'</p>';
				//  ASAP - if new check that file with name is not already present, append _0, _1 until no collision
				//  if (file_exists($filename)) {  op.png 
				//echo '- - - '.$file_dir.'/gallery/'.$_FILES['imagefile']['name'];
				$f2 = $_POST['imgfn1'];
				if (file_exists($file_dir.'/gallery/'.$f2)) {
					return('file '.$f2.' can not be over written');
					}
				//  FUTURE okay, custom filename is available,
				//  make dure to delete it if new upload with different name, or drop
				$img_file = $f2;
				}
			else
				$img_file = $uid.'.'.$ext;

			//  $result = 'artex post edit successful';
			$result = 'problem inserting into gallery catalog';
			$a = array(
			  'id' => $uid,
			  'date' => date('Y-m-d'),
			  'author' => $u,
			  'title' => $_POST['title'],
			  'htags' => 'none',
			  'src' => $img_file);
			//  create thumbnail
//			copy($file_dir.'/gallery/rickatech_0002_144x.png',
//			     $file_dir.'/gallery/'.$uid.'_144x.png');  //  FUTURE - try catch?
			image_make_min(
			  $file_dir.'/gallery/'.$uid.'_144x.png',
			  $_FILES['imagefile']['tmp_name'],
			  $ext, 144, 144);
			//  move uploaded file
			rename($_FILES['imagefile']['tmp_name'],
			  $file_dir.'/gallery/'.$img_file);  //  FUTURE - try catch?
			//  gcat, update new
			if (gcat::update($gcat_data, GCAT_NEW, $a)) {
				$result = 'gcat new post successful';
				}
			else
				$result = 'problem posting to gcat';
			break;

		  case 'edit':  //  UPDATE
			$result = 'problem updating gallery catalog';
			$uid = $_POST['gcatid'];
			if (strlen($_FILES['imagefile']['name']) < 1) {
				//  image file unchanged
				$img_file = $_POST['imgfn1'];
				}
			else {
				//  image file changed
				$ift = $_FILES['imagefile']['type'];
				if      ($ift == 'image/jpeg')  $ext = 'jpeg'; 
				else if ($ift == 'image/png')   $ext = 'png'; 
				else $ext = NULL;
				if (is_null($ext))
					return ("Unsupported image format or image too large > 2 MBytes");

				if ($_POST['imgfn1'] != 'default' && $_POST['imgfn1'] != $_POST['imgfno']) {
					//  echo "\n<p>seq: ".$seq.' file: '.$_FILES['imagefile']['name'].'</p>';
					//  ASAP - if new check that file with name is not already present, append _0, _1 until no collision
					//  if (file_exists($filename)) {  op.png 
					//echo '- - - '.$file_dir.'/gallery/'.$_FILES['imagefile']['name'];
					$f2 = $_POST['imgfn1'];
					if (file_exists($file_dir.'/gallery/'.$f2)) {
						return('file '.$f2.' can not be over written');
						}
					//  FUTURE okay, custom filename is available,
					//  make sure to delete it if new upload with different name, or drop
					$img_file = $f2;
					}
				else
					$img_file = $uid.'.'.$ext;
				//  'delete' previous source file
				rename(
				  $file_dir.'/gallery/'.$_POST['imgfno'],
				  $file_dir.'/gallery/'.$_POST['imgfno'].'_0');  //  FUTURE - try catch?
				//  create thumbnail
				image_make_min(
				  $file_dir.'/gallery/'.$uid.'_144x.png',
				  $_FILES['imagefile']['tmp_name'],
				  $ext, 144, 144);
//				copy($file_dir.'/gallery/rickatech_0002_144x.png',
//				     $file_dir.'/gallery/'.$uid.'_144x.png');  //  FUTURE - try catch?
				//  move uploaded file
				rename($_FILES['imagefile']['tmp_name'],
				  $file_dir.'/gallery/'.$img_file);  //  FUTURE - try catch?
				}

			$a = array(
			  'id' => $uid,
			  'date' => $_POST['date'],
			  'author' => $u,
			  'title' => $_POST['title'],
			  'htags' => $_POST['itags'],
			  'src' => $img_file);
			//  gcat, update new
			if (gcat::update($gcat_data, GCAT_UPDATE, $a)) {
				$result = 'gcat post update successful';
				}
			else
				$result = 'problem updting post to gcat';
			break;

		//  DROP
		  default:
			$result = 'undefined gallery action '.$mode;
			}
		return ($result);
		}

	static public function dlist($file, $mode = GALLERY_MODE_CAT, $os = 0, $ct = 7, $al = NULL) {  /*  gallery  */
		//  output formatted image gallery
		//  $file    image gallery catalog file to open
		//  $os      offset into gallery catalog to start processing
		//  $ct      maximum image count to process, return when reached special input checkmark
		//  $list    array of author usernames
		//  return   undefined
		$result = false;
		if ($fh = fopen($file, 'r')) {
			//  fgetcsv more actively preserves contents within double quotes :-)
			$r = 0;
			while (($data = fgetcsv($fh, 1000)) !== FALSE) {
				# uid, date, author, title, htags, source_optionali.ext
				if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
					$list = array_map('trim', $data);
					if ($mode & GALLERY_MODE_INCL) {
						foreach($al as $k => $v) {
							if ($v != $list[GALLERY_AUTHOR]) continue 2;
							}
						}
					if ($mode & GALLERY_MODE_EXCL) {
						foreach($al as $k => $v) {
							if ($v == $list[GALLERY_AUTHOR]) continue 2;
							}
						}
					if ($mode & GALLERY_MODE_FORM) {  //  needed by artex::form
						echo "\n<div style=\"display: inline-block; text-align: center;\"><!--  ".$r."  -->";
						echo '<img style="width: 72;" src=/gfx-upload/gallery/'.$list[GALLERY_UID].'_144x.png>';
						echo "<br><input name=\"image_custom\" id=\"image_".$r."\" value=\"".$list[GALLERY_UID];
						echo "\" type=\"checkbox\" onclick=\"artex_form_image('".$r."');\">";
						echo "<!--  ".$r." [end]  --></div>";
						}
					else {
						echo "<img\nstyle=\"width: 72; padding: 0 4px 4px 0;\" src=/gfx-upload/gallery/";
						echo $list[GALLERY_UID]."_144x.png onclick=\"gallery_form_prefill('submit_gallery_image', '".$list[GALLERY_UID]."');\">";
						echo "\n<div style=\"display: none;\">";
						echo "\n<span id=\"".$list[GALLERY_UID]."_date\" style=\"\">".$list[GALLERY_DATE]."</span>";
						echo "\n<span id=\"".$list[GALLERY_UID]."_author\" style=\"\">".$list[GALLERY_AUTHOR]."</span>";
						echo "\n<span id=\"".$list[GALLERY_UID]."_title\" style=\"\">".$list[GALLERY_TITLE]."</span>";
						echo "\n<span id=\"".$list[GALLERY_UID]."_htags\" style=\"\">".$list[GALLERY_HTAGS]."</span>";
						echo "\n<span id=\"".$list[GALLERY_UID]."_src\" style=\"\">".$list[GALLERY_SRC]."</span>";
						echo "\n</div>";
						}
					$r++;
					}
				if ($r >= $ct) break;
				}
			if ($mode & GALLERY_MODE_CAT) {  //  needed by artex::form
				echo "<div style=\"display: inline-block; padding: 0 4px 4px 0; width: 72px; height: 72px; text-align: center; vertical-align: top\"><!--  nav  -->";
				if (session_userid_active()) {
					echo '<< &lt;<br>[';
					echo "<span onclick=\"gallery_form_prefill('submit_gallery_image', 'new');\">create</span>";
					echo ']<br>> >>';
					}
				else
					echo '<< <<br> <br>> >>';
				echo "<!--  nav [end]  --></div>";
				}
			$result = true;
			fclose($fh);
			}
		return $result;
		}

	}  /*  gallery class [end]  */

const EXCH_FOLLOW =    1;
const EXCH_UNFOLLOW =  2;

class exchange {
	/*  In 2016 exchange was renamed symposium, lounge renamed exchange,
	/*  so ambiguity in class name and method/properties should be expected
	/*  until further refactoring is performed.   */

	static public function list_sub($u) {
		//  Output formatted list of accounts a user is following
		//  $u   user to query against
		global $data_dir;

		$af = NULL;
		if (self::following($data_dir.'/following', $af, $u) && count($af) > 0)
			user_list(USERLIST_ALLF, $af, $af);
		}

	static public function following_update($cmd, $usr, $uid, $fol) {
		//  Follow/unfollow an account.
		//  cmd     command/action
		//  usr     username to process
		//  fol     username of account to follow/unfollow
		//          FUTURE shouldn't everythign use UID instead of username?
		//  return: N/A
		//          as there is no way to recover at this call level,
		//          outputing error message is appropriate
		//  output: error message string
		//  related: friends_update()
		global $data_dir;
		echo $fol;

		$file = $data_dir.'/following';
		//  following
		//  if af null, create a new row, $flag new row to insert
		//  else add $fol to row (if not already present), flag existing recard to update
		//
		//  unfollow
		//  if af null, undefined - reprot error
		//  else
		//    if mulitple value row, remove $fol, flag existing recard to update
		//    else single value row, flag existing record to remove
		//
		//  hint
		//  updated/new records shold be placed at beginning of file

		/*  FUTURE, push file/lock open and post close as mytake methods (acat::update)  */
		/*  - - - - - - - - - - - - -  */
		$tries = 0;  //  design for nosql solution that uses nfs style shared storage
		$act_done = false;  //  set true when action for command is confirmed
		$result = false;
		while ($tries < FOPEN_X_RETRIES) {
			if ($fh = fopen($file.'_lock', 'x'))  //  fails if file already exists
				break;
			sleep(rand(0,3));  //  sleep up to three seconds
			$tries++;
			}
		if (!$fh) {
			echo '<br>AFTER '.FOPEN_X_RETRIES;
			echo ' retries, still could not get pivotsub write lock, sorry';
			return ($result);
			}
		/*  - - - - - - - - - - - - -  */

		//  get lock file
		//  open read file
		//  open write file
		//  close write fil
		//  clsoe read file
		//  cat new records > new file
		//  cat write file >> new file
		//  mv read archive file
		//  mv new read file
		//  close lock file

		$list = NULL;
		if ($fr = fopen($file, 'r')) {
			if ($fw = fopen($file.'_w', 'w')) {
				while (($data = fgets($fr, 1000)) !== FALSE) {
					if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
						//  each line has username(uid) prefix demarked by :
						$posa  = strpos($data, '(');
						$posc  = strpos($data, ':');
						$uid  = substr($data, $posa + 1, $posc - $posa - 2);
						$u  = substr($data, 0 , $posa);
						echo "\n<br>".$u.'('.$uid.') == '.$usr;
						if ($usr != $u)
							fwrite($fw, $data);  //  FUTURE - try/catch?
						else {  //  match!
							//  $list = explode(',', substr($data, $posc + 1));
							$list = array_map('trim', explode(',', substr($data, $posc + 1)));
//							foreach ($list as $k => $v)
//								$list[$k] = trim($list[$k]);
//							array_unshift($list, $uid);
//							$ps = $list;
//							$result = true;
//							break;
//							
							}
						}
					}
				fclose($fw);
				}
			fclose($fr);
			}
//		system('cat txt1 txt2 > txt3');
		$okay = false;
		if ($cmd ==  EXCH_FOLLOW) {
			echo "\n<br>FOLLOW";
			if ($list)
				array_push($list, $fol);
			else
				$list = array($fol);
			$okay = true;
			}
		else if ($cmd == EXCH_UNFOLLOW && !is_null($list)) {
			echo "\n<br>UNFOLLOW";
			$key = array_search($fol, $list);
			if ($key !== false) {
				unset($list[$key]);
				}
			else
				$list = NULL;
			$okay = true;
			echo "\n<br>k: ".$key;
			}
		if ($okay) {  //  positive action to be done (i.e. something acutally needs to change)
			if ($fn = fopen($file.'_n', 'w')) {
				$str =  "#\n";
				fwrite($fn, $str);  //  FUTURE - try/catch?
				if ($list) {
					$str = $usr.'('.$uid.'):';
					$spc = ' ';
					foreach($list as $k => $v) {
						$str .= $spc.$v;
						$spc = ', ';
						}
					$str .= "\n";
					echo "\n<pre>".$str.'</pre>';
					//$str = $usr.'(1): '.$fol."\n";
					fwrite($fn, $str);  //  FUTURE - try/catch?
					}
				fclose($fn);
				//  system('cat '.$file.'_n '.$file.'_w > '.$file.'_d');
				exec('cat '.$file.'_n '.$file.'_w > '.$file.'_d');
				//  FUTURE / DANGER - ideally need other parts of code to read lock stall
				//                    to avoid trying to read itemporary 'missing' file
				//                    suggestion for read lock,
				//                    attempt read open,
				//                    if no file check if lock present
				//                    retry until lock file disappears or timeout.
				//  handy for debug: $ watch cat .../following
				//  comment out next line to test
				rename($file, $file.'_0');  rename($file.'_d', $file);
				$act_done = true;

//  file    base file, which needs its contents changed
//  file_w  copy of base file, initially truncated, then rebuilt but skipping record to change
//  file_n  record to change, to be prepended (persists, overwritten after call)
//  file_d  result of prepended record file to rebuilt file (file evaporates after call)
//  file_0  archive of base file, its past state before contents changed
				}
			}

		/*  - - - - - - - - - - - - -  */
		/*  FUTURE, push file/lock open and post close as mytake methods (acat::update)  */
		if ($fh) fclose($fh);
		unlink($file.'_lock');  //  delete lock file
		if ($act_done) {
			echo ' successful';  //  output necessary?
			$result = true;
			}
		else {
			//  FUTURE / SYSTEM - make system log
			echo '<br>exchange::following_update, could not complete action';
			//  DANGER, if this file remains all updates are  blocked!!!
			//  FUTURE - add routine check if lock file date is
			//           older than a few minutes, then force delete
			//           PHP docs suggest upon process end it should be removed, but it can take a few seconds
			}
		/*  - - - - - - - - - - - - -  */
		return $result;
		}

	static public function following($file, &$al, $usr) {
		//  prepare array of a user's exchange subscriptions
		//  $file    file to open
		//  $al      array, list of usernames subscribed to
		//  $usr     username of record to search
		//  return   true if no errors
		//  related: pivot::get_sub
		global $data_dir;

		$result = false;
		if ($fh = fopen($file, 'r')) {
			while (($data = fgets($fh, 1000)) !== FALSE) {
				if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
					//  each line has username(uid) prefix demarked by :
					$posa  = strpos($data, '(');
					$posc  = strpos($data, ':');
					$uid  = substr($data, $posa + 1, $posc - $posa - 2);
					$u  = substr($data, 0 , $posa);
					if ($usr == $u) {
						$list = explode(',', substr($data, $posc + 1));
						foreach ($list as $k => $v) {
							$list[$k] = trim($list[$k]);
							}
						$al = $list;
						$result = true;
						break;
						}
					}
				}
			fclose($fh);
			}
		return $result;
		}

	static public function latest($nlist = 4, $al_a = NULL, $mode = 'exch', $offset = 0, $tags = NULL, $piv = NULL) {  /*  exchange  */
		//  Output formatted exchanges
		//    $nlist  FUTURE - limit record display
		//    $al_a   array of author to match
		//            NULL match all authors
		//    $mode   'exch', 'pivot', ...
		//    $tags   array of tags to match
		//            NULL, be tag agnostic
		//    $piv    pivot id list tightly catcatenated with | (e.g. 'sleep|brain'  (used only when a pivot page want to supply hint on what default pivot to use for creating a new pvito message
		//            NULL, disabled
		//  FUTURE - this method is quite overloaded, push into a parent class?
		global $exch_dir, $ecat_data;
		global $wall_dir, $wcat_data;
		global $pivt_dir, $pcat_data;
		global $pmsg_dir, $mcat_data;
		global $acat_data;
		global $edit_urla;
		global $debug_mask;

		$mode_title = 'Messages';
		$mode_dbg =   $edit_urla.'ecat';
		if ($mode == 'pivot' || isset($_GET['tag'])) {
			$cat = ecat::get($pcat_data, $al_a);
			if (is_null($cat))
				return;  //  nothing to show, skip output
			$cat_cmd = 'artpivt';
			$cat_typ = 'pivt';
			$cat_dir = $pivt_dir;
			$cat_mod = VIBE_MODE_PIVT;
			$mode_dbg =   $edit_urla.'pcat';
			echo "\n<div style=\"padding: 4px; background-color: #3fffbf;\">\n";  //  body
			}
		elseif ($mode == 'wall') {
			$cat = ecat::get($wcat_data, $al_a);
			if (is_null($cat))
				return;  //  nothing to show, skip output
			$cat_cmd = 'artwall';
			$cat_typ = 'wall';
			$cat_dir = $wall_dir;
			$cat_mod = VIBE_MODE_WALL;
			$mode_title = 'Exchange';
			$mode_dbg =   $edit_urla.'wcat';
			echo "\n<div style=\"padding: 4px; background-color: pink;\">\n";  //  body
			}
		elseif ($mode == 'pmsg') {
			$cat = ecat::get($mcat_data, $al_a, NULL, $tags);
			//  FUTURE - use get2?
			if (is_null($cat))
				return;  //  nothing to show, skip output
			$cat_cmd = 'artex-msg';
			$cat_typ = 'pmsg';
			$cat_dir = $pmsg_dir;
			$cat_mod = VIBE_MODE_PMSG;
			$mode_title = 'Messages';
			$mode_dbg =   $edit_urla.'mcat';
			echo "\n<div class=exch_pmsg_color>\n";  //  body
			}
		else {
			$cat = ecat::get($ecat_data, $al_a);
			if (is_null($cat))
				return;  //  nothing to show, skip output
			$cat_cmd = 'artexch';   //  FUTURE - one of these is redundant?
			$cat_typ = 'exch';      //  FUTURE - one of these is redundant?
			$cat_dir = $exch_dir;
			$cat_mod = VIBE_MODE_EXCH;
			$mode_title = 'Symposium';
			echo "\n<div style=\"padding: 4px; background-color: #bfff3f;\">\n";  //  body
			}
		echo "\n<div class=list_head>\n";
		echo "\n<span style=\"font-weight: bolder;\">".$mode_title.'</span>';
		if (session_userid_active())
			echo " [<a href=\"".$mode_dbg.'">debug</a>]';
		echo "</div>";

		$rc = 0;
		$oc = 0;
		foreach ($cat as $k => $v) {
			if ($offset > $oc)
				goto goff; 

			//  echo "\npiv: ".$piv.', '.$v[ECAT_PIVOT];
			if ($cat_typ == 'pivt')
				if ($piv && strstr($v[ECAT_PIVOT], $piv) === FALSE)
					//  FUTURE - $piv made be list, if so then any to any check is needed
					goto goff; 

			//  FUTURE - below is the 'creamy center', push into a view-like util method

			//  FUTURE - goto above can be inline easier if following is refactored into a new method?
			$s = 'clear: both; overflow: hidden; padding: 4px; background-color: white; position: relative; height: 72px;';
			echo "\n<div class=\"cat_rows\" id=\"".$v[ECAT_UID].vvv::efix($cat_mod)."_more\" style=\"".$s."\">";
			if ($cat_typ == 'pmsg') {
			    if (isset($v[ECAT_PM_FR]) && !empty($v[ECAT_PM_FR]))
			        echo "\n<a href=/profile/?public=".$v[ECAT_PM_FR].'><img src=/gfx-upload/avatar_'.$v[ECAT_PM_FR].'_min.gif style="float: right; width: 2em;"></a>';
			    else
			        echo "\n<div style=\"float: right; width: 2em; text-align: center; color: white; height: 2em; background-color: grey;\">W</div>";
			    }
			else
			echo "\n<a href=/profile/?public=".$v[ECAT_AUTHOR].'><img src=/gfx-upload/avatar_'.$v[ECAT_AUTHOR].'_min.gif style="float: right; width: 2em;"></a>';

			echo "\n<div style=\"float: right;\">";
			vvv::show($v[ECAT_UID], $cat_mod);
			echo "\n</div>";

			if ($v[ECAT_IMG] == 'stock')
				echo "<img class=artex_sum src=\"/gfx-stock/drama_144x.png\" align=left style=\"width: 72px; margin: 0;\">";
			else
				echo "<img class=artex_sum src=\"/gfx-upload/gallery/".$v[ECAT_IMG]."_144x.png\" align=left style=\"width: 72px; margin: 0;\">";
			//  title
			echo "<p style=\"margin: 0; font-weight: bolder;\">".$v[ECAT_TITLE].'</p>';

			//  date, author
			echo "<p style=\"margin: 0; font-size: smaller;\">";
			if ($cat_typ == 'pmsg') {
			    if (isset($v[ECAT_PM_FR]) && !empty($v[ECAT_PM_FR]))
			        echo $v[ECAT_DATE].', <a href=/profile/?public='.$v[ECAT_PM_FR].'>'.$v[ECAT_PM_FR].'</a>';
			    else
			        echo $v[ECAT_DATE];
			    }
			else
			    echo $v[ECAT_DATE].', <a href=/profile/?public='.$v[ECAT_AUTHOR].'>'.$v[ECAT_AUTHOR].'</a>';

		//	if (session_username_active() == $v[ECAT_AUTHOR])
			$un = session_username_active();
			if ($un == $v[ECAT_AUTHOR] && (!($cat_typ == 'pmsg') || $v[ECAT_HTG] == 'draft'))
				echo ' [ <a href=/?'.$cat_cmd.'&'.$cat_typ.'='.$v[ECAT_UID].'>edit</a> ]';

			//  show pivot association, only for pivot messages or if logged in as admin
			if ($cat_typ == 'pivt' || ($un && in_array($un, $admins)))
				echo "\n<br>".$v[ECAT_PIVOT];

			if (isset($v[ECAT_ARTID]) && strlen(trim($v[ECAT_ARTID])) > 0)
				echo "\n<br>[ <span style=\"font-weight: bold;\"><a href=/?art=".$v[ECAT_ARTID].'>pivot/article</a></span> ]';

//			echo '</p>';
			if ($cat_typ == 'pmsg') {
			    //  private message: output to, cc, bcc
//			    echo "\n<span style=\"font-size: smaller;\">";
//			    if (isset($v[ECAT_PM_FR]) && !empty($v[ECAT_PM_FR]))
//			        echo "\n<br>from: ".$v[ECAT_PM_FR];
			    if (isset($v[ECAT_PM_TO]) && !empty($v[ECAT_PM_TO]))
			        echo "\n<br>to: ".$v[ECAT_PM_TO];
			    if (isset($v[ECAT_PM_CC]) && !empty($v[ECAT_PM_CC]))
			        echo "\n<br>cc: ".$v[ECAT_PM_CC];
			    if (isset($v[ECAT_PM_BC]) && !empty($v[ECAT_PM_BC]))
			        echo "\n<br>bcc: ".$v[ECAT_PM_BC];
//			    echo "</span>";
			//  echo "\n<a href=/profile/?public=".$v[ECAT_AUTHOR].'><img src=/gfx-upload/avatar_'.
			//    $v[ECAT_AUTHOR]."_min.gif style=\"float0: right; width: 2em;\"></a>\n<br>";
			    }
			echo '</p>';

//			echo "\n<span style=\"font-size: smaller;\">";
			echo "\n<span>";
			//  exchange copy
			if ($debug_mask & DEBUG_JENN)  //  Allow markdown content formatting
			    echo Michelf\Markdown::defaultTransform(file_get_contents($cat_dir.'/'.$v[ECAT_UID]));
			else
			    readfile($cat_dir.'/'.$v[ECAT_UID]);  /*  readfile() insures PHP in file is ignored  */
			echo "</span>";
			//  read more, see comments
			vvv::readmore_seecmnts($v[ECAT_UID], $cat_mod);
			echo "\n</div>";

			//  comment section - hidden initially
			echo "\n<div style=\"clear: both; display: none;\" id=\"".$v[ECAT_UID].vvv::efix($cat_mod)."_cmnt\">";
			lounge::out($cat_typ, $v[ECAT_UID]);
			echo "</div> &nbsp; ";

			$rc++;
			if ($nlist && $rc >= $nlist)
				break;
goff:			$oc++;
			}

//		echo "\n<div class=list_tail>".(session_userid_active() ? '[<a href=/?'.$cat_cmd.'>create!</a>]' : '... ');
		echo "\n<div class=list_tail>".(
		  (session_userid_active() && !($mode == 'pmsg')) ?
//		  '[<a href=/?'.$cat_cmd.'>create</a> A ]' :
		 // '[<a href=/?'.$cat_cmd.'>create</a>'.($piv ? ' '.$piv.' ' : '').']' :
		  '[<a href=/?'.$cat_cmd.($piv ? '&piv='.$piv : '').'>create</a>]' :
		  '... ');
		echo "\n<form style=\"margin: 0; padding: 0;\" method=\"post\" action=\"?search&exch&heading=Symposium Pager\">";
		echo "\n<input type=\"text\"></form>";
		echo "</div>\n";
		echo "\n</div>\n";  //  body
		return;
		}
	}  /*  exchange class [end]  */

const AOL_MAJOR = 0;    //  Major area ID
const AOL_MINOR = 1;    //  Area ID
const AOL_TITLE = 2;    //  Area Title
const AOL_DESC  = 3;    //  Area Description

class pivot {

	//  various tools for manaaging user pivot topic subscriptions


	static public function get_sub($file, &$ps, $usr = NULL) {  //  FUTURE - aren't arrays already passed by reference?
		//  prepare array of users pivot subscriptions
		//  $file    file to open
		//  $ps      array, upon return contains list of friends
		//  $usr     specific user, return array omit other users (1 deep)
		//           NULL, return array representing all users (2 deep)
		//  return   true if usr record found
		//           true if at least one record for all users
		//           false otherwise
		$result = false;
		if ($fh = fopen($file, 'r')) {
			while (($data = fgets($fh, 1000)) !== FALSE) {
				if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
					//  each line has username(uid) prefix demarked by :
					$posa  = strpos($data, '(');
					$posc  = strpos($data, ':');
					$uid  = substr($data, $posa + 1, $posc - $posa - 2);
					$u  = substr($data, 0 , $posa);
					if (is_null($usr)) {
						//  after :, comma seperated list of other users
						$list = explode(',', substr($data, $posc + 1));
						//  trim off leading/trailing whitespace
						foreach ($list as $k => $v)
							$list[$k] = trim($list[$k]);
						//  pushing UID as first element of array
						array_unshift($list, $uid);
						$ps[$u] = $list;
						if (!$result) $result = true;
						}
					else if ($usr == $u) {
						$list = explode(',', substr($data, $posc + 1));
						foreach ($list as $k => $v)
							$list[$k] = trim($list[$k]);
						array_unshift($list, $uid);
						$ps = $list;
						$result = true;
						break;
						}
					}
				}
			fclose($fh);
			}
		return $result;
		}

	static public function aol_update_sub($usr, $cmd, $p) {
		global $data_dir;

		$file = $data_dir.'/pivotsub';

		echo "\n<br>".$file.', '.$cmd;
		/*  FUTURE, push file/lock open and post close as mytake methods (acat::update)  */
		/*  - - - - - - - - - - - - -  */
		$tries = 0;
		$act_done = false;  //  set true when action for command is confirmed
		$result = false;
		while ($tries < FOPEN_X_RETRIES) {
			if ($fh = fopen($file.'_lock', 'x'))  //  fails if file already exists
				break;
			sleep(rand(0,3));  //  sleep up to three seconds
			$tries++;
			}
		if (!$fh) {
			echo '<br>AFTER '.FOPEN_X_RETRIES;
			echo ' retries, still could not get pivotsub write lock, sorry';
			return ($result);
			}
		/*  - - - - - - - - - - - - -  */

		$str =  "# ...\n";
		fwrite($fh, $str);

		if ($fr = fopen($file, 'r')) {
			while (($data = fgets($fr, 1000)) !== FALSE) {
				if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
					//  each line has username(uid) prefix demarked by :
					$posa  = strpos($data, '(');
					$posc  = strpos($data, ':');
					$uid  = substr($data, $posa + 1, $posc - $posa - 2);
					$u  = substr($data, 0 , $posa);
					//  FUTURE - using array_search and implode built in functions
					//           have no tolerance for extra spaces
					if ($usr == $u) {
						$list = explode(',', substr($data, $posc + 1));
						$w = 0;
						if ($cmd == 'drop') {
							$k0 = -1;
							foreach ($list as $k => $v) {
								if (trim($v) == $p) $k0 = $k;
								}
							if ($k0 >= 0) unset($list[$k0]);
							if (($c0 = count($list, 1)) > 0)
								$w = 1;
							$act_done = true;
							echo "drop ".$p;
							}
						else if ($cmd == 'add') {
							array_push($list, $p);
							$act_done = true;
							$w = 1;
							echo "add ".$p;
							}
						if ($w > 0) {
							$str =  $usr.'('.session_userid_active().'): ';
							foreach ($list as $k => $v) {
								if ($w < 1)
								$str .= ', ';
								$str .= trim($v);
								if ($w > 0) $w = 0;
								}
							$str .= "\n";
							fwrite($fh, $str);
							}
						}
					else {
						//  $str =  $data."\n";
						$str =  $data;
						fwrite($fh, $str);
						}
					}
				}
			fclose($fr);
			}
		if (!$act_done && $cmd == 'add') {
			$str =  $usr.'('.session_userid_active().'): '.$p."\n";
			fwrite($fh, $str);
			$act_done = true;
			echo "add ".$p;
			}

		/*  - - - - - - - - - - - - -  */
		/*  FUTURE, push file/lock open and post close as mytake methods (acat::update)  */
		if ($fh) fclose($fh);
		if ($act_done)
			rename($file, $file.'_0');
	//	if ($act_done && rename($file.'_lock', $file.'_done'))  /*  testing  */
		if ($act_done && rename($file.'_lock', $file)) {
			echo ' successful';
			$result = true;
			}
		else {
			unlink($file.'_lock');
			//  FUTURE / SYSTEM - make system log
			echo '<br>pivot::aol_update_sub, could not complete action';
			//  DANGER, if this file remains all updates are  blocked!!!
			//  FUTURE - add routine check if lock file date is
			//           older than a few minutes, then force delete
			}
		/*  - - - - - - - - - - - - -  */
		return $result;
		}

	static public function aol_list($u) {
		//  this does not make enclosing div, best done upstream of call
		global $data_dir;

		$ps = array();
		pivot::get_sub($data_dir.'/pivotsub', $ps, $u);  //  returns 1 deep array
		$aol = array();
		$file_profiles = $data_dir.'/aol';
		if (pivot::aol_get($file_profiles, $aol)) {
			echo "\n<dl style=\"margin: 4px 0 0 0;\">";
			$l = 'xox';
			foreach ($aol as $k => $v) {
				//  FUTURE: sort by major into seperate array?
				if ($v[0] != $l) {
					if ('xox' != $l) echo '&nbsp;</ul>';
					echo "\n<dt>".$v[0].' ';
					}
				$l = $v[0];
				echo "\n<dd><img src=/gfx-stock/pivot_".$v[1]."_144x.png style=\"width: 36px; float: left;\">";
				echo "\n<a href=/?tag=".$v[1].">".$v[2].'</a>';
				echo "\n<br>".$v[3];
				if (session_userid_active() && isset($ps)) {
					$match = false;
					foreach ($ps as $k => $v0) {
						if ($v0 == $v[1]) {
							$match = true;
							break;
							}
						}
					echo '<br><span style="background-color: lightgrey; color: white; font-size: smaller;">&nbsp;';
					if ($match)
						echo '<a href=/?aol&drop='.$v[1].'>un</a>subscribe';
					else
						echo 'un<a href=/?aol&add='.$v[1].'>subscribe</a> ';
					echo '&nbsp;</span>';
					}
				echo "\n<br><br style=\"clear: both;\">";
				}
			echo "</dl>";
			}
		}

	static public function aol_get($file, &$aol) {
		//  $file    ...
		//  $aol     ...
		//  if error, ...
		$result = false;
		if ($fh = fopen($file, 'r')) {
			while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
				if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
	//				$va = array('handle' => $data[USERACCT_HNDL]);
	//				if (isset($data[USERACCT_DATE])) $va['date'] = $data[USERACCT_DATE];
	//				if (isset($data[USERACCT_HASH])) $va['date'] = $data[USERACCT_HASH];
	//				if (isset($data[USERACCT_FNAM])) $va['date'] = $data[USERACCT_FNAM];
	//				if (isset($data[USERACCT_MAIL])) $va['date'] = $data[USERACCT_MAIL];
	//				$users[$data[USERACCT_ID]] = $va;
	//				}
	//			if (!is_null($raw))
					array_push($aol, $data);
					}
				}
			$result = true;
			fclose($fh);
			}
		return $result;
		}

	}

class artex {
	const ECAT_HSTR =      '# ord, id_readable, title, date, author, pivot, image, artid, htags';
	const ECAT_HSTR_PMSG = '# ord, id_readable, title, date, author, pivot, image, artid, htags, from, to, cc, bcc';

	static public $signup_form = false;
	static public $from = NULL;

	static public function cleanse(&$c, $excp = '<br><p><b><i><strong><emphasize>') {
		//  Most form fields should be checked, stripped of malicious content
		//    $c      reference to source content string
		//    $excp   string of catcatenated tags to allow
		//            FALSE, strip all tags
		if ($excp === FALSE)
		    return htmlspecialchars(strip_tags($c));
		else
		    return htmlspecialchars(strip_tags($c, $excp));
		}

	static public function edit_submit() {  /*  artex  */
		//  artex::$username    for private messages, this must be set to from account prior to call    
//			check basic arguments, any errors provide feedback message, repopulate form
//			load existing records
//			if new && aid, check for aid conflict ...
//			if new && no aid, compute aid
//			  - prepare record
//			  - prepare <span> fortified article
//			  - append old record to archive log
//			  - write updated records file
//			  - write new article as aid
//			if edit && no aid, compute aid
//			  - replace record
//			  - prepare <span> fortified article
//			  - append old record to archive log
//			  - append old article to archive log
//			  - write updated records file
//			  - write new article as aid
		global $acat_data, $artc_dir;
		global $ecat_data, $exch_dir;
		global $wcat_data, $wall_dir;
		global $pcat_data, $pivt_dir;
		global $mcat_data, $pmsg_dir;

		if (!($u = session_username_active()))
			return ('please login before posting artex');
//ap($_POST);
//echo "\n<br>ARTEX_TITLE_MIN: ".ARTEX_TITLE_MIN;
		$mode = $_POST['mode'];  //  new | edit

		//  htmlspecialchars and strip tags were added to encode special characters in html and strip any text fields of malicious HTML and PHP tags.
		//  These functions were added to title, exchange, symposium, and article.
		$a = array();
	//	$a['title'] =   htmlspecialchars(strip_tags($_POST['title']));  //  see cleanse();
		$a['title'] =   static::cleanse($_POST['title'], FALSE);
//echo "\n<br>title length: ".strlen($a['title']);
		if (strlen($a['title']) <= ARTEX_TITLE_MIN)
			return ('please provide a longer title');
		$a['author'] =  $u;
		$a['date'] =    $_POST['date'];
		$a['htags'] =   $_POST['htags'];
		//  detect if custom image has changed
		$a['image'] =   (isset($_POST['image_custom'])) ?
		  $_POST['image_custom'] : $_POST['image'];
	//	if ($a['isexch'] = ($_POST['isexch'] == 'enable' ? true : false)) {
		$a['isexch_x'] = ($_POST['isexch_x'] == 'true' ? TRUE : FALSE);
		if ($a['isexch_x']) {
			$a['copyexch'] = static::cleanse($_POST['copyexch']);
		//	$a['copyexch'] = htmlspecialchars(strip_tags($_POST['copyexch'], '<br><p><b><i><strong><emphasize>'));
			$a['eid'] = $_POST['eid'];
			$a['artid'] = $_POST['aid'];
			$a['pivot'] = isset($_POST['pivot']) ? $_POST['pivot'] : '';
			$cat_dir = $exch_dir;
			}
	//	if ($a['iswall'] = ($_POST['iswall'] == 'enable' ? true : false)) {
		$a['iswall_x'] = ($_POST['iswall_x'] == 'true' ? TRUE : FALSE);
		if ($a['iswall_x']) {
			$a['copyexch'] = static::cleanse($_POST['copyexch']);
		//	$a['copyexch'] = htmlspecialchars(strip_tags($_POST['copyexch'], '<br><p><b><i><strong><emphasize>'));
			$a['eid'] = $_POST['eid'];
			$a['artid'] = $_POST['aid'];
			$a['pivot'] = isset($_POST['pivot']) ? $_POST['pivot'] : '';
			$cat_dir = $wall_dir;
			}
	//	if ($a['ispivt'] = ($_POST['ispivt'] == 'enable' ? true : false)) {
		$a['ispivt_x'] = ($_POST['ispivt_x'] == 'true' ? TRUE : FALSE);
		if ($a['ispivt_x']) {
			$a['copyexch'] = static::cleanse($_POST['copyexch']);
		//	$a['copyexch'] = htmlspecialchars(strip_tags($_POST['copyexch'], '<br><p><b><i><strong><emphasize>'));
			$a['eid'] = $_POST['eid'];
			$a['artid'] = $_POST['aid'];
			$a['pivot'] = isset($_POST['pivot']) ? $_POST['pivot'] : '';
			$cat_dir = $pivt_dir;
			}
	//	if ($a['ispmsg'] = ($_POST['ispmsg'] == 'enable' ? true : false)) {
		$a['ispmsg_x'] = ($_POST['ispmsg_x'] == 'true' ? TRUE : FALSE);
		if ($a['ispmsg_x']) {
			$a['copyexch'] = static::cleanse($_POST['copyexch']);
		//	$a['copyexch'] = $_POST['copyexch'];
			$a['eid'] =   $_POST['eid'];
			$a['artid'] = $_POST['aid'];
			$a['pivot'] = isset($_POST['pivot']) ? $_POST['pivot'] : '';
			$a['from'] =  $_POST['from'];
			$a['to'] =    $_POST['to'];
			$a['cc'] =    $_POST['cc'];
			$a['bcc'] =   $_POST['bcc'];
			if ($_POST['ax_submit'] == 'publish')
			    $a['htags'] = 'sent';
			else
			    $a['htags'] = 'draft';
			$cat_dir = $pmsg_dir;
			}
	//	$a['isartc'] =  ($_POST['isartc'] == 'enable' ? true : false);
		$a['isartc_x'] = ($_POST['isartc_x'] == 'true' ? TRUE : FALSE);
		if ($a['isartc_x']) {
			$a['aid'] = $_POST['aid'];
			$a['copyartc'] = static::cleanse($_POST['copyartc']);
			$a['copyexch'] = static::cleanse($_POST['copyexch']);
			$a['pivot'] = $_POST['pivot'];
			//  determine if this should be published or held as draft / ondeck
			if ($_POST['ax_submit'] == 'publish') { //  if ondeck present, remove it
				if (strpos($a['pivot'], 'ondeck') !== false)
					$a['pivot'] = str_replace('|ondeck', '', $a['pivot']);
				}
			else {  //  if ondeck missing, append it
				if (strpos($a['pivot'], 'ondeck') === false)
					$a['pivot'] .= '|ondeck';
				}
			}

		if ($mode == 'new' && $a['isartc_x']) {
			if (strlen($a['aid'])) {
				echo "\n<br> aid is ".$a['aid'];
				//  ... need to make sure aid string is legit
				//  FUTURE - break into util function?
				$s = strtolower(trim($a['aid']));
				$s = trim_all($s, '-');
				if ($a['aid'] != $s)
					return ('error - aid must be just letters and dashes');
				}
			else {
				echo "\n<br> no aid, make it";
				//  ... manufacture aid
				//  FUTURE - break into util function?
				$s = strtolower(trim($a['title']));
				$a['aid'] = trim_all($s, '-');
				}
			//  attempt to fetch any row that may already use aid,
			//  NULL if no match or file does not exist
			acat::get($acat_data, NULL, $a['aid']);
			if (acat::$acat) {
				//  FUTURE - okay if match has different date and author
				return ('error - there is already an article using aid: '.$a['aid']);
				}
			}

		$neither = false;  //  catch if neither exchange or article form is enabled

		if ($a['isexch_x']) {
			$neither = true;
			//  FUTURE - may need span id to match eid?
//			$s = '<span id="excp">'.$a['copyexch']."</span>\n";

			if ($mode == 'new') {
				//  ecat::seq_next(excahneg directory(seq file + exch file), $author)
				//    deduces next value in squence from seq_file
				//    checks if excahneg with that sequence already exists
				//    if not pdate seq feil and grantt the seuqnce number, update 
				//    if exchaneg exists, search through ecat for next unused dseq
				//        update seq file and grant the sequence number, update 
				//  keep track of exchange sequence/user in special file
				//  username_000n
				//  FUTURE - this needs a file lock?
				//  CITATION http://stackoverflow.com/questions/22409780/flock-vs-lockf-on-linux
				$seq = ecat::seq_next($exch_dir, $a['author']);
				if (($seq === false))
					return ('error - could not write symposium seq file: '.
					  $exch_dir.'/'.$a['author'].'_seq');

				$a['eid'] = sprintf("%s_%'.04d", $a['author'], $seq);
				//  return ('error - new exchange coming soon '.$a['eid']);
				}

			$file_e = $cat_dir.'/'.$a['eid'];
			file_log($file_e);
			//  if file does not exist it will be created
//			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $s) === false))
			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $a['copyexch']) === false))
				return ('error - could not update symposium file: '.$a['eid']);
			fclose($fhe);
			if ($mode == 'new') {
				$result = 'isexch: [ write disabled - stay tuned ]';
				//  FUTURE - need to get array of all existing records, then add new record
				if (ecat::update($ecat_data, ECAT_NEW, $a, self::ECAT_HSTR)) {
					$result = 'artexch new post successful';
					}
				else
					$result = 'problem posting to artexch';
				}
			else if ($mode == 'edit') {
				$result = 'isexch: [ write disabled - stay tuned ]';
				if (ecat::update($ecat_data, ECAT_UPDATE, $a, self::ECAT_HSTR)) {
					$result = 'artexch post edit successful';
					}
				else
					$result = 'problem updating artexch';
				}
			else 
				$result = 'undefined artex action '.$mode;
			}

	//	if ($a['iswall']) {
		if ($a['iswall_x']) {
			$neither = true;
			//  FUTURE - may need span id to match eid?
		//	$s = '<span id="excp">'.$a['copyexch']."</span>\n";

			if ($mode == 'new') {
				$seq = ecat::seq_next($cat_dir, $a['author']);
				if (($seq === false))
					return ('error - could not write exchange seq file: '.
					  $cat_dir.'/'.$a['author'].'_seq');

				$a['eid'] = sprintf("%s_%'.04d", $a['author'], $seq);
				}

			$file_e = $cat_dir.'/'.$a['eid'];
//			echo '<br>file_e: '.$file_e;
//			echo '<br>author: '.$a['author'];
//			echo '<br>'.$s.'<br>';
			file_log($file_e);
			//  if file does not exist it will be created
//			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $s) === false))
			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $a['copyexch']) === false))
				return ('error - could not update exchange file: '.$a['eid']);
			fclose($fhe);
			if ($mode == 'new') {
				//  FUTURE - need to get array of all existing records, then add new record
				if (ecat::update($wcat_data, ECAT_NEW, $a, self::ECAT_HSTR)) {
					$result = 'artexch new post successful';
					}
				else
					$result = 'problem posting to artexch';
				}
			else if ($mode == 'edit') {
				if (ecat::update($wcat_data, ECAT_UPDATE, $a, self::ECAT_HSTR)) {
					$result = 'artexch post edit successful';
					}
				else
					$result = 'problem updating artexch';
				}
			else 
				$result = 'undefined artex action '.$mode;
			}

	//	if ($a['ispmsg']) {
		if ($a['ispmsg_x']) {
			$neither = true;
			$send = FALSE;
			//  FUTURE - may need span id to match eid?
//			$s = '<span id="excp">'.$a['copyexch']."</span>\n";

			//  if 'sent', then get new recipient content id, copy content file, then insert sent record in catalog

			if ($mode == 'new') {
			    //  new records require obtaining a unique eid
			    $seq = ecat::seq_next($cat_dir, $a['author']);
			    if (($seq === false))
			        return ('error - could not write message seq file: '.$cat_dir.'/'.$a['author'].'_seq');
			    $a['eid'] = sprintf("%s_%'.04d", $a['author'], $seq);
			    }
			$file_e = $cat_dir.'/'.$a['eid'];
//			echo '<br>file_e: '.$file_e;
//			echo '<br>author: '.$a['author'];
//			echo '<br>'.$s.'<br>';
			file_log($file_e);
			//  if file does not exist it will be created
			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $a['copyexch']) === false))  //  !!!!
		//	if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $s) === false))  //  !!!!
				return ('error - could not update message file: '.$a['eid']);
			fclose($fhe);

			$a2 = array(
			    (isset($a['ord']) ? $a['ord'] : '*'),  //  FUTURE - reserved for now as eid is sufficient
			    $a['eid'],
			    $a['title'],
			    $a['date'],
			    $a['author'],
			    $a['pivot'],
			    $a['image'],
			    $a['artid'],
			    $a['htags'],
//			    $a['from'],
			    $a['from'] = static::$from,  //  this assign is harder to spoof
			    $a['to'],
			    $a['cc'],
			    $a['bcc']);
			if ($mode == 'new') {
				//  FUTURE - need to get array of all existing records, then add new record
//				if (ecat::update($pcat_data, ECAT_NEW, $a)) {
				if (ecat::update($mcat_data, ECAT_NEW, $a, self::ECAT_HSTR_PMSG, $a2)) {
					$result = 'artexch new message successful';
			    		if ($a['htags'] == 'sent') $send = TRUE;
					}
				else
					$result = 'problem posting artex message';
				}
			else if ($mode == 'edit') {
				if (ecat::update($mcat_data, ECAT_UPDATE, $a, self::ECAT_HSTR_PMSG, $a2)) {
					$result = 'message post update successful';
			    		if ($a['htags'] == 'sent') $send = TRUE;
					}
				else
					$result = 'problem updating message';
				}
			else
				$result = 'undefined message action '.$mode;
			if ($send) {
			    $a2[8] = $a['htags'] = 'inbox';
	  		    //  new records require obtaining a unique eid
	 		    $seq = ecat::seq_next($cat_dir, $a['to']);
			    if (($seq === false))
			        return ('error - could not write message seq file: '.$cat_dir.'/'.$a['to'].'_seq');
			    $a2[1] = $a['eid'] = sprintf("%s_%'.04d", $a['to'], $seq);
			    $a2[4] = $a['author'] = $a['to'];
//			    $a2[9] = $a['from'] = static::$from;  //  this assign is harder to spoof
			    $file_e2 = $cat_dir.'/'.$a['eid'];
			    if (!copy($file_e, $file_e2))
			        return ('error - could not write sent message file: '.$file_e2);
//  echo 'a'; ap($a);
//  echo 'a2'; ap($a2);
			    if (ecat::update($mcat_data, ECAT_NEW, $a, self::ECAT_HSTR_PMSG, $a2))
			        $result = 'artexch new sent message successful';
			    else
			        $result = 'problem posting artex sent message';
			    }
			}

		if ($a['ispivt_x']) {
			$neither = true;
			if ($mode == 'new') {
			    //  new records require obtaining a unique eid
			    $seq = ecat::seq_next($cat_dir, $a['author']);
			    if (($seq === false))
			        return ('error - could not write message seq file: '.$cat_dir.'/'.$a['author'].'_seq');
			    $a['eid'] = sprintf("%s_%'.04d", $a['author'], $seq);
			    }
			$file_e = $cat_dir.'/'.$a['eid'];
			echo '<br>file_e: '.$file_e;
			echo '<br>author: '.$a['author'];
			echo '<br>'.$s.'<br>';
			file_log($file_e);
			//  if file does not exist it will be created
	//		if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $s) === false))  //  !!!!
			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $a['copyexch']) === false))
				return ('error - could not update message file: '.$a['eid']);
			fclose($fhe);

			$a2 = array(
			    (isset($a['ord']) ? $a['ord'] : '*'),  //  FUTURE - reserved for now as eid is sufficient
			    $a['eid'],
			    $a['title'],
			    $a['date'],
			    $a['author'],
			    $a['pivot'],
			    $a['image'],
			    $a['artid'],
			    $a['htags']);

		//	return ('error - no dice');

			if ($mode == 'new') {
				//  FUTURE - need to get array of all existing records, then add new record
				if (ecat::update($pcat_data, ECAT_NEW, $a, self::ECAT_HSTR, $a2)) {
					$result = 'pivot new post successful';
					}
				else
					$result = 'problem posting new pivot message';
				}
			else if ($mode == 'edit') {
				if (ecat::update($pcat_data, ECAT_UPDATE, $a, self::ECAT_HSTR, $a2)) {
					$result = 'pivot post edit successful';
					}
				else
					$result = 'problem updating pivot message';
				}
			else 
				$result = 'undefined pivot action '.$mode;
			}
		if ($a['ispivt']) {
			$neither = true;
			//  FUTURE - may need span id to match eid?
//			$s = '<span id="excp">'.$a['copyexch']."</span>\n";

			if ($mode == 'new') {
				//  ecat::seq_next(excahneg directory(seq file + exch file), $author)
				//    deduces next value in squence from seq_file
				//    checks if excahneg with that sequence already exists
				//    if not pdate seq feil and grantt the seuqnce number, update 
				//    if exchaneg exists, search through ecat for next unused dseq
				//        update seq file and grant the sequence number, update 
				//  keep track of exchange sequence/user in special file
				//  username_000n
				//  FUTURE - this needs a file lock?
				//  CITATION http://stackoverflow.com/questions/22409780/flock-vs-lockf-on-linux
				$seq = ecat::seq_next($cat_dir, $a['author']);
				return ('error - new pivot messages coming soon '.$seq);
				if (($seq === false))
					return ('error - could not write exchange seq file: '.
					  $cat_dir.'/'.$a['author'].'_seq');

				$a['eid'] = sprintf("%s_%'.04d", $a['author'], $seq);
				//  return ('error - new exchange coming soon '.$a['eid']);
				}

			$file_e = $cat_dir.'/'.$a['eid'];
//			echo '<br>file_e: '.$file_e;
//			echo '<br>author: '.$a['author'];
//			echo '<br>'.$s.'<br>';
			file_log($file_e);
			//  if file does not exist it will be created
//			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $s) === false))
			if ((($fhe = fopen($file_e, 'w')) === false) || (fwrite($fhe, $a['copyexch']) === false))
				return ('error - could not update pivot message file: '.$a['eid']);
			fclose($fhe);
			if ($mode == 'new') {
				//  FUTURE - need to get array of all existing records, then add new record
				if (ecat::update($pcat_data, ECAT_NEW, $a, self::ECAT_HSTR)) {
					$result = 'artexch new post successful';
					}
				else
					$result = 'problem posting to artexch';
				}
			else if ($mode == 'edit') {
				if (ecat::update($pcat_data, ECAT_UPDATE, $a, self::ECAT_HSTR)) {
					$result = 'artexch post edit successful';
					}
				else
					$result = 'problem updating artexch';
				}
			else 
				$result = 'undefined artex action '.$mode;
			}

		if ($a['isartc_x']) {
			//  output article content to file, filename = aid
//			$s = '<span id="copy">'.$a['copyartc']."</span>\n";
			$file_a = $artc_dir.'/'.$a['aid'];
			//  !!! FUTURE - add archive previous to log step here
			if ($fha = fopen($file_a, 'w')) {  //  if file does not exist it will be created
				//  FUTURE - error -> system_log?
			//	fwrite($fha, $s);
				fwrite($fha, $a['copyartc']);
				echo "\n<br>Article body  updated";
				}

			//  output article summary content to file, filename = aid_sum
			$file_a = $artc_dir.'/'.$a['aid'].'_sum';
			//  !!! FUTURE - add archive previous to log step here
			if ($fha = fopen($file_a, 'w')) {  //  if file does not exist it will be created
				//  FUTURE - error -> system_log?
				fwrite($fha, $a['copyexch']);
				echo "\n<br>Article summary updated";
				}

			if ($mode == 'new') {
				//  FUTURE - need to get array of all existing records, then add new record
			//	if (acat::write($acat_data, array($a))) {
				if (acat::update($acat_data, ACAT_NEW, $a)) {
					$result = 'artex new post successful';
					}
				else
					$result = 'problem posting to artex';
				}
			else if ($mode == 'edit') {
				if (acat::update($acat_data, ACAT_UPDATE, $a)) {
					$result = 'artex post edit successful';
					}
				else
					$result = 'problem updating artex';
				}
			else 
				$result = 'undefined artex action '.$mode;
			$neither = true;
			}

		if (!$neither)
				$result = 'no artex section selected';

		//  FUTURE: if there is an error, how to persist form data and represent form?
		return ($result);
		}

	static public function form($m, $artex = NULL) {  /*  artex  */
		//  output form for editing/creating Article + Exchange combination
		//    m     art | exch | wall | pivt | pmsg
		//    artex uid, article   must also match author and create date
		//          uid, exchange  authorname_000x
		//          NULL,          create new
		global $data_dir, $edit_urla;
		global $acat_data, $artc_dir;
		global $ecat_data, $exch_dir;
		global $wcat_data, $wall_dir;
		global $pcat_data, $pivt_dir;
		global $mcat_data, $pmsg_dir;
		global $admins;

		$edit = false;
		$v = array();
		if ($m == 'exch') {
			if ($artex) {
				//  $ecat = get_map($ecat_data);
				ecat::get2($ecat_data, NULL, $artex);
				if (!ecat::$ecat || !isset(ecat::$ecat[0]))
					//  FUTURE - okay if match has different date and author
					echo 'error - could not find symposium matching eid: '.$artex;
				else 
					$edit = true;
				}
			}
		elseif ($m == 'wall') {
			if ($artex) {
				ecat::get2($wcat_data, NULL, $artex);
				if (!ecat::$ecat || !isset(ecat::$ecat[0]))
					//  FUTURE - okay if match has different date and author
					echo 'error - could not find exchange matching eid: '.$artex;
				else 
					$edit = true;
				}
			}
		elseif ($m == 'pmsg') {
			$copy =  'replace this with your private message text';
			if ($artex) {
				ecat::get2($mcat_data, NULL, $artex);
				if (!ecat::$ecat || !isset(ecat::$ecat[0]))
					//  FUTURE - okay if match has different date and author
					echo 'error - could not find pivot message matching eid: '.$artex;
				else 
					$edit = true;
				}
			}
		elseif ($m == 'pivt') {
			$copy =  'replace this with your pivot message text';
			if ($artex) {
				ecat::get2($pcat_data, NULL, $artex);
				if (!ecat::$ecat || !isset(ecat::$ecat[0]))
					//  FUTURE - okay if match has different date and author
					echo 'error - could not find pivot message matching eid: '.$artex;
				else 
					$edit = true;
				}
			}
		else {
			if ($artex) {
				acat::get($acat_data, NULL, $artex);
				if (!acat::$acat || !isset(acat::$acat[0]))
					//  FUTURE - okay if match has different date and author
					echo 'error - could not find article matching aid: '.$artex;
				else
					$edit = true;
				}
			}

		if ($edit && ($m == 'exch' || $m == 'wall' || $m == 'pivt' || $m == 'pmsg')) {
			$v['eid'] =   ecat::$ecat[0][ECAT_UID];
			$v['title'] = ecat::$ecat[0][ECAT_TITLE];
			$v['date'] =  ecat::$ecat[0][ECAT_DATE];
			$v['xxx'] =  ecat::$ecat[0][ECAT_IMG];
			if (isset(ecat::$ecat[0][ECAT_ARTID]))
				$v['aid'] =   ecat::$ecat[0][ECAT_ARTID];
			if (isset(ecat::$ecat[0][ECAT_PIVOT]))
			    $v['p0'] = ecat::$ecat[0][ECAT_PIVOT];
			$v['mode'] =  'edit';
			if ($m == 'pmsg') {
			    $v['htags'] =ecat::$ecat[0][ECAT_HTG];
			    $v['from'] = ecat::$ecat[0][ECAT_PM_FR];
			    $v['to'] =   ecat::$ecat[0][ECAT_PM_TO];
			    $v['cc'] =   ecat::$ecat[0][ECAT_PM_CC];
			    $v['bcc'] =  ecat::$ecat[0][ECAT_PM_BC];
			    }
			}
		if ($edit && $m == 'art') {
			$v['aid'] =   acat::$acat[0][CONTENT_UID];
			//  FUTURE need to stip ondeck off end, only keep 1st
			$v['p0'] =    acat::$acat[0][CONTENT_TAG];
			$b = split_byline(acat::$acat[0][CONTENT_BYL]);
			$v['xxx'] =   acat::$acat[0][CONTENT_IMG];
			$v['title'] = $b['title'];
			$v['date'] =  $b['date'];
			$v['mode'] =  'edit';
			}
		else  {
			//  get here if creating new article, or new/edit message
			if (!isset($v['eid']))
				$v['eid'] =   '';
			if (!isset($v['aid']))
				$v['aid'] =   '';
			if ($_GET['piv']) $v['p0'] = $_GET['piv'];  //  typically only for new message
			elseif (!isset($v['p0'])) $v['p0'] = 'undefined';
			//  $v['p0'] =    'health';
			if (!isset($v['title']))
				$v['title'] = '';
			if (!isset($v['date']))
				$v['date'] =  date('Y-m-d');  //  date('Y-m-d H:i:s');
			if (!isset($v['mode']))
				$v['mode'] =  'new';
			if (!isset($v['xxx']))
				$v['xxx'] =  'stock';
			if ($m == 'pmsg') {
			    $v['htags'] = 'draft';
			    if ($_GET['to']) $v['to'] = $_GET['to'];
			//  $v['from'] = ecat::$ecat[0][ECAT_PM_FR];
			    }
			}
//  echo "\n<br>v: "; ap($v);  ?>

<div style="padding: 4px;"><!--  ||  ARTEX FORM ||  -->
<!--m      name="artex" method="post" onsubmit="return signup_form_okay();"  -->
<form      name="artex" method="post"
           action="?submitmode=artexch" enctype="multipart/form-data">

<div style="font-weight: bolder;">Contribute!</div>
<dl style="margin: 0;">
<dd><?PHP
		echo '<input name="title"  size0="40" value="'.$v['title'].'" placeholder="change me" maxlength="'.ARTEX_TITLE_MAX.'"> title (*)';
		echo '<input name="date_d" size="10" value="'.$v['date'].'" disabled> ';
		echo '<input name="date"   size="10" value="'.$v['date'].'" hidden readonly> ';
		echo '<input name="mode_d" size="8"  value="'.$v['mode'].'" disabled> ';
		echo '<input name="mode"   size="8"  value="'.$v['mode'].'" hidden readonly> ';

		echo "\n<dd><input name=\"pivot\"   size0=\"40\" value=\"".$v['p0'].'"> pivot (*)';
		echo "\n<dd><input name=\"htags_d\" size0=\"40\" value=\"".$v['htags'].'" disabled> hash tags (optional)';
		echo '<input name="htags"  size="40" value="'.$v['htags'].'" hidden readonly>';

		echo "<dd><input name=\"image_d\" size0=\"28\"      value=\"".$v['xxx']."\" disabled> custom";
		echo "<input name=\"image\"                value=\"".$v['xxx']."\" hidden readonly>";
		$tags = array_map('trim', explode('|', $v['p0']));
		echo   "\n<div style=\"display: inline-block;\">";
		if ($m == 'art')
			$sa = 'pivot_'.$tags[0];
		else
			$sa = 'drama';
		echo   "\n<dd><div style=\"display: inline-block; text-align: center;\"><!--  ST  -->";
		echo   "<img src=\"/gfx-stock/".$sa."_144x.png\" style=\"width: 72px; margin: 0;\">";
		echo   "<br><input name=\"image_custom\" id=\"image_stock\" type=\"checkbox\" value=\"stock\" onclick=\"artex_form_image('s');\" ".($v['xxx'] == 'stock' ? ' checked' : '').'> stock';
		echo   "<!--  ST [end]  --></div>";

		if ($v['xxx'] != 'stock') {
			echo   "\n<div style=\"display: inline-block; text-align: center;\"><!--  CU  -->";
			echo   "<img src=\"/gfx-upload/gallery/".$v['xxx']."_144x.png\" align=left style=\"width: 72px; margin: 0;\">";
			echo   "\n<br> current";
			echo   "<!--  CU [end]  --></div>";
			}

		$un = session_username_active();
		gallery::dlist($data_dir.'/gallery', GALLERY_MODE_FORM | GALLERY_MODE_INCL, 0, 4, array($un));

		echo "\n<dd>";
		echo 'from: <input name="from_d" size="8"      value="'.$v['from'].'" disabled>';
		echo '<input name="from"                       value="'.$v['from'].'" hidden readonly>';
		echo '&nbsp; to: <input name="to_d" size="8"   value="'.$v['to'].'" disabled>';
		echo '<input name="to"                         value="'.$v['to'].'" hidden readonly>';
		echo '&nbsp; cc: <input name="cc_d" size="8"   value="'.$v['cc'].'" disabled>';
		echo '<input name="cc"              i          value="'.$v['cc'].'" hidden readonly>';
		echo '&nbsp; bcc: <input name="bcc_d" size="8" value="'.$v['bcc'].'" disabled>';
		echo '<input name="bcc"                        value="'.$v['bcc'].'" hidden readonly>';
		echo "</div>\n</dl>";

		$art_on_x =  'false';
		$exch_on_x = 'false';
		$wall_on_x = 'false';
		$pivt_on_x = 'false';
		$pmsg_on_x = 'false';
		if ($m == 'exch') {
			$art_on =  '';
			$exch_on = ' checked disabled';
			$exch_on_x =  'true';
			$wall_on =  '';
			$pivt_on =  '';
			$pmsg_on =  '';
			$smry_on =  '';
			$art_sl =  'display: none;';
			$exch_sl = '';
			}
		elseif ($m == 'wall') {
			$art_on =  '';
			$exch_on = '';
			$wall_on =  ' checked disabled';
			$wall_on_x =  'true';
			$pivt_on =  '';
			$pmsg_on =  '';
			$smry_on =  '';
			$art_sl =  'display: none;';
			$exch_sl = '';
			}
		elseif ($m == 'pivt') {
			$art_on =  '';
			$exch_on = '';
			$wall_on =  '';
			$pivt_on =  ' checked disabled';
			$pivt_on_x =  'true';
			$pmsg_on =  '';
			$smry_on =  '';
			$art_sl =  'display: none;';
			$exch_sl = '';
			}
		elseif ($m == 'pmsg') {
			$art_on =  '';
			$exch_on = '';
			$wall_on =  '';
			$pivt_on =  '';
			$pmsg_on =  ' checked disabled';
			$pmsg_on_x =  'true';
			$smry_on =  '';
			$art_sl =  'display: none;';
			$exch_sl = '';
			}
//		elseif ($m == 'smry') {
//			$art_on =  '';
//			$exch_on = '';
//			$wall_on =  '';
//			$pivt_on =  '';
//			$pmsg_on =  '';
//			$smry_on =  ' checked';
//			$art_sl =  'margin: 0; display: none;';
//			$exch_sl = 'margin: 0;';
//			}
		else {
			$art_on =  ' checked disabled';
			$art_on_x =  'true';
			$exch_on = '';
			$wall_on =  '';
			$pivt_on =  '';
			$pmsg_on =  '';
//			$smry_on =  '';
			$smry_on =  ' checked disabled';
			$art_sl =  '';
//			$exch_sl = 'margin: 0; display: none;';
			$exch_sl = '';
			}

		echo "\n<div style=\"font-weight: bolder;".
		    (($exch_on_x == 'true' || in_array($un, $admins)) ? '' : ' display: none;')."\">Symposium";
		echo "\n<input   name=\"isexch\" type=\"checkbox\" value=\"enable\" id=\"isexch\" onclick=\"artex_form_toggle('exch');\"".$exch_on.">";
		echo "\n<input   name=\"isexch_x\" type=\"hidden\" value=\"".$exch_on_x.'" id="isexch_x"></div>';

		echo "\n<div style=\"font-weight: bolder;".
		    (($wall_on_x == 'true' || in_array($un, $admins)) ? '' : ' display: none;')."\">Exchange";
		echo "\n<input   name=\"iswall\" type=\"checkbox\" value=\"enable\" id=\"iswall\" onclick=\"artex_form_toggle('wall');\"".$wall_on.">";
		echo "\n<input   name=\"iswall_x\" type=\"hidden\" value=\"".$wall_on_x.'" id="iswall_x"></div>';

		echo "\n<div style=\"font-weight: bolder;".
		    (($pivt_on_x == 'true' || in_array($un, $admins)) ? '' : ' display: none;')."\">Pivot Messages";
		echo "\n<input   name=\"ispivt\" type=\"checkbox\" value=\"enable\" id=\"ispivt\" onclick=\"artex_form_toggle('pivt');\"".$pivt_on.">";
		echo "\n<input   name=\"ispivt_x\" type=\"hidden\" value=\"".$pivt_on_x.'" id="ispivt_x"></div>';
//		echo "\n<div style=\"font-weight: bolder;\">Pivot Messages";
//		echo "\n<input   name=\"ispivt\" type=\"checkbox\" disabled value=\"enable\" id=\"ispivt\" onclick=\"artex_form_toggle('pivt');\"".$pivt_on."></div>";

		echo "\n<div style=\"font-weight: bolder;".
		    (($pmsg_on_x == 'true' || in_array($un, $admins)) ? '' : ' display: none;')."\">Private Messages";
		echo "\n<input   name=\"ispmsg\" type=\"checkbox\" value=\"enable\" id=\"ispmsg\" onclick=\"artex_form_toggle('pmsg');\"".$pmsg_on.">";
		echo "\n<input   name=\"ispmsg_x\" type=\"hidden\" value=\"".$pmsg_on_x.'" id="ispmsg_x"></div>';

		echo "\n<div style=\"font-weight: bolder;".
		    (($art_on_x == 'true' || in_array($un, $admins)) ? '' : ' display: none;')."\">Summary";
		echo "\n<input   name=\"issmry\" type=\"checkbox\" value=\"enable\" id=\"issmry\" onclick=\"artex_form_toggle('smry');\"".$smry_on."></div>";

		echo "\n<dl class=artex_fm style=\"".$exch_sl."\"   id=\"dlexch\"><dd>";
		// A character counter was placed above the textarea for exchange and symposium in a div.
		// The textarea for exchange and symposium now has a placeholder instead of a default value.
		// The JavaScript below the textarea runs the functionality for character counter.
		echo "\n<div id=\"textarea_count\"></div>";
		echo "\n<TEXTAREA  name=\"copyexch\" id=\"copyexch\" class=artex_fm COLS0=72 ROWS=8 WRAP=VIRTUAL";
		echo "\n  placeholder=\"replace this with your text\" maxlength=\"".ARTEX_MESSAGE_MAX."\">";
		echo $copy.'</TEXTAREA></dl>';  ?>
<script>
<?PHP  echo "\ntext_max = ".ARTEX_MESSAGE_MAX.';';  ?>
msgc = document.getElementById('copyexch');
msgc_tac = document.getElementById('textarea_count');
msgc.onkeyup = (function() {
	//  nothing displays until typing starts
	msgc_tac.innerHTML = text_max - msgc.value.length + ' characters remaining';
});
</script><?PHP
		echo "\n<div style=\"font-weight: bolder;".
		    (($art_on_x == 'true' || in_array($un, $admins)) ? '' : ' display: none;')."\">Article";
		echo "\n<input   name=\"isartc\" type=\"checkbox\" value=\"enable\" id=\"isartc\" onclick=\"artex_form_toggle('artc');\"".$art_on.'>';
		echo "\n<input   name=\"isartc_x\" type=\"hidden\" value=\"".$art_on_x.'" id="isartc_x"></div>';
		echo "\n<dl class=artex_fm style=\"".$art_sl."\"  id=\"dlartc\">";  ?>
<dd><?PHP
//		echo '<input name="pivot" size0="40" value="'.$v['p0'].'"> pivot (*)';	?>
<!-- A character counter was placed above the article textarea in a div.  The article textarea has a placeholder instead of a default value now.
The JavaScript below the textarea executes the functionality of the character counter for article textarea. -->
<div id="artc_textarea_count"></div>
<!--  http://stackoverflow.com/questions/271067/how-can-i-make-a-textarea-100-width-without-overflowing-when-padding-is-present  -->
<TEXTAREA  name="copyartc" id="copyartc" class=artex_fm ROWS=8
           WRAP=VIRTUAL placeholder="replace this with your text here"  <?PHP  echo
          'maxlength='.ARTEX_FULLART_MAX.'>'.$copy_artc;  ?></TEXTAREA>
<script>
//  no var prefix means these are global
<?PHP  echo "\ntext_max_art = ".ARTEX_FULLART_MAX.";";  ?>
artc_tac = document.getElementById('artc_textarea_count');
artc = document.getElementById('copyartc');
artc.onkeyup = (function() {
	//  nothing displays until typing starts
	artc_tac.innerHTML = text_max_art - artc.value.length + ' characters remaining';
});
</script>
		   
<dd><?PHP
		echo "\n<input name=\"aid\" size0=\"40\" ".($edit ? ' value="'.$v['aid'].'" readonly' : '').'> '.$v['aid'];
//		echo "\n<input name=\"eid\" size=\"40\" ".($edit ? ' value="'.$v['eid'].'" readonly' : '').'> ';
		echo "\n<input name=\"eid\" size0=\"40\" ".($edit ? ' value="'.$v['eid'].'" readonly' : '').' hidden> ';  ?>
article id (optional, will be auto generated otherwise)
</dl>

<br><input name="ax_submit" value="publish" type="submit">
<input     name="ax_submit" value="save draft" type="submit">
<?PHP		if ($edit) echo " [&nbsp;<A href=".$edit_urla.'articles/'.$v['aid'].">debug</a>&nbsp;]";  ?>
</form><?PHP
		echo "\n<div id=\"copyprev\" style=\"display: none;\">";
		/*  hidden div containing previous content spans,
		/*  javascript will copy appropriate value into form above  */
		if ($edit && $m == 'art') {
			/*  using readfile() purposely insures PHP commands in file are ignored  */
			echo       "\n<div id=\"artc_sm\">";
			readfile($artc_dir.'/'.$v['aid'].'_sum');
			echo "</div>\n<div id=\"artc_cp\">";
			readfile($artc_dir.'/'.$v['aid']);
			echo '</div>';
			//  inline javascript, execute immediately after hidden div load completes
			echo "\n<script>\nartex_form_postload();\n</script>\n";
			}
		if ($edit && $m == 'exch') {
			/*  using readfile() purposely insures PHP commands in file are ignored  */
			echo "\n<div id='excp_pl'>";
			readfile($exch_dir.'/'.$v['eid']);
			echo '</div>';
			echo "\n<script>\nartexch_form_postload();\n</script>\n";
			}
		if ($edit && $m == 'wall') {
// echo "\n<br>Yo! ".$wall_dir.'/'.$v['eid'];
			/*  using readfile() purposely insures PHP commands in file are ignored  */
			echo "\n<div id='excp_pl'>";
			readfile($wall_dir.'/'.$v['eid']);
			echo '</div>';
			echo "\n<script>\nartexch_form_postload();\n</script>\n";
			}
		if ($edit && $m == 'pivt') {
			/*  using readfile() purposely insures PHP commands in file are ignored  */
			echo "\n<div id='excp_pl'>";
			readfile($pivt_dir.'/'.$v['eid']);
			echo '</div>';
			echo "\n<script>\nartexch_form_postload();\n</script>\n";
			}
		if ($edit && $m == 'pmsg') {
//echo "\n<br>Yo! ".$pmsg_dir.'/'.$v['eid'];
			/*  using readfile() purposely insures PHP commands in file are ignored  */
			echo "\n<div id='excp_pl'>";
			readfile($pmsg_dir.'/'.$v['eid']);
			echo '</div>';
			echo "\n<script>\nartexch_form_postload();\n</script>\n";
			}

		//  FUTURE - specific element ID's should NOT be part of content in data store
		//           instead a dynamic wrapper with an ID should be performed - Rick
		//  FUTURE - for private messages, use tags in catalog to designate sent, inbox, archive, ...

		echo "\n</div>";
		echo "\n</div>\n";
		}

	static public function latest($nlist = 4, $tag = NULL, $art = NULL, $usr = NULL, $lab = NULL, $offset = 0) {  /*  artex  */
		//  output formated article list
		//    $nlist  FUTURE, number of items to list
		//          NULL all?
		//    ...
		//    $Lab    custom label
		//          'skip', repress head and tail divs
		//    $piv    pivot id list tightly catcatenated with | (e.g. 'sleep|brain'  (used only when a pivot page want to supply hint on what default pivot to use for creating a new pvito message
		//            NULL, disabled
		global $acat_data, $artc_dir, $edit_urla, $debug_mask;

		$data = get_map($acat_data, $tag, $art, $usr);
		if (is_null($data) || sizeof($data) < 1) {
			if ($tag != 'ondeck')  //  even if no list returned, need to show create button
				return;
			$nd = true;  //  no data returned
			}
		else
			$nd = false; //  data returned
		if (isset($lab) && $lab == 'skip')
			$hf = false;
		else
			$hf = true;

		echo "\n<div class=list_body>\n";
		//  without overflow: hidden, spacing is weird

		if ($hf) {
			echo "\n<div class=list_head>\n";
			if (!isset($_GET['tag'])) {
				//  if specific pivto page, already has an information
				//  header so suppress redundant article list title
				if (!is_null($lab))
					echo "<B>".$lab."</B>\n";
				elseif (is_null($tag))
					echo "<B>Latest</B>\n";
				elseif ($tag == 'ondeck')
					echo "<B>On Deck</B>\n";
				else {
					echo "<B><a href=?tag=".$tag.">";  //  FUTURE: omit link if superfluous
					if ($tag == 'music')  echo 'Tunes';
					else if ($tag == 'book')   echo 'Read';
					else if ($tag == 'film')   echo 'Filcks';
					else if ($tag == 'ondeck') echo 'On Deck';
					else echo $tag;
					echo "</a></B>\n";
					}
				}
			else
				echo '<B>'.$tag.'</B>';  //  typically get here only from pivot page
			echo "\n<span style=\"font-weight: bolder;\"> - Articles</span>";
			if (session_userid_active())
				echo " [<a href=\"".$edit_urla."acat\">debug</a>]";
			echo "</div>";
			}

		if (!$nd) {  /*  nd  */
			$rc = 0;
			$oc = 0;
			foreach($data as $da) {
				if ($offset > $oc)
					goto artc_off; 
				//  FUTURE - art_rec, make unpacking a util function!!!
				$da_tag = explode('|', $da[CONTENT_TAG]);
				$da_cap = explode('|', $da[CONTENT_BYL]);
				$byl = split_byline($da[CONTENT_BYL]);
				if (isset($da[1])) {
					/*  FUTURE: make this an ajax call  */
					if (isset($da[5]) && strlen($da[5]) > 0) {
						$a0 = "<a class=exref\n  href=\"".$da[CONTENT_URL]."\">";
						$a1 = "</a>";  }
					else {
						$a0 = "<b><a\n   href=\"/?art=".$da[CONTENT_UID]."\">";
						$a1 = "</a></b>";  }
					}
				else {
					$a0 ='';  $a1 = '';  }
				//  without overflow: hidden, spacing is weird
				$s = 'clear: both; overflow: hidden; padding: 4px; background-color: white; position: relative; height: 72px;';
				echo "\n<div class=\"cat_rows\" id=\"".$da[CONTENT_UID]."_more\" style=\"".$s."\">";

				//  FUTURE - make following vvv code shared between exch and art?
				echo "\n<a href=/profile/?public=".$byl['author'].'><img src=/gfx-upload/avatar_'.$byl['author'].'_min.gif style="float: right; width: 2em;"></a>';

				echo "\n<div style=\"float: right;\">";
				vvv::show($da[CONTENT_UID], VIBE_MODE_ARTC);
				echo "\n</div>";

				echo $a0;
				if ($da[4] == 'stock')
					echo "<img class=artex_sum src=\"/gfx-stock/pivot_".$da_tag[0]."_144x.png\" align=left style=\"width: 72px; margin: 0;\">".$a1;
				else
					echo "<img class=artex_sum src=\"/gfx-upload/gallery/".$da[CONTENT_IMG]."_144x.png\" align=left style=\"width: 72px; margin: 0;\">".$a1;
				//  title
				echo "\n<p style=\"margin: 0;\">".$a0.$byl['title'].$a1;
				//  date, author
				echo "<span style=\"font-size: smaller;\">\n  <br>";
				echo $byl['date'].', <a href=profile/?public='.$byl['author'].'>'.$byl['author'].'</a>';
				if ($byl['author'] == session_username_active())
					//  echo ' [<a href=>edit</a>]';
					echo ' [<a href=/?artex&art='.$da[CONTENT_UID].'>edit</a>]';
				echo '</span></p>';
				//  pivots/tags
				$f = false; $str = '';
				foreach ($da_tag as $i) {
					if (!$f) $f = true;
					else $str .= ", ";
					$str .= $i;
					}
				if ($f) echo "\n  <p style=\"font-size: smaller; margin: 0;\">".$str."</p>";

				//  We are output article summary here
		if ($debug_mask & DEBUG_JENN) {  //  Allow markdown content formatting
		    if (($str = file_get_contents($artc_dir.'/'.$da[1].'_sum')) === FALSE)
		        system_log('Could not open article file - '.$artc_dir.'/'.$da[1].'_sum');
		    else
		        echo Michelf\Markdown::defaultTransform($str);
		    }
		elseif (readfile($artc_dir.'/'.$da[1].'_sum') === false)
		    system_log('Could not open article file - '.$artc_dir.'/'.$da[1].'_sum');  //  ZZZZ

				//  read more, see comments
				vvv::readmore_seecmnts($da[CONTENT_UID]);
				echo "</div>";

				//  comment section - hidden initially
				echo "\n<div style=\"clear: both; display: none;\" id=\"".$da[CONTENT_UID]."_cmnt\">";
				lounge::out('artc', $da[CONTENT_UID]);
				echo "</div> &nbsp; ";
				$rc++;
				if ($nlist && $rc >= $nlist)
					break;
artc_off:
				$oc++;
				}
			}  /*  nd  */
		//  without overflow: hidden, spacing is weird, marry this + title above = 72px;
		if ($hf) {
			if (is_null($tag) || $tag == 'ondeck')  $piv = '';
			else $piv = '&piv='.$tag;
			echo "\n<div class=list_tail>".
			  (session_userid_active() ? '[<a href=/?artex'.$piv.'>create</a>]' : '... ');
			echo "\n<form style=\"margin: 0; padding: 0;\" method=\"post\" action=\"?search&artc&heading=ArticlePager\">";
			echo "\n<input type=\"text\"></form>";
			echo "</div>\n";
			}

		echo "\n</div>\n";
		}

	static public function article_out($artrec) {
		global $dflags;
		global $edit_url, $edit_urla, $artc_dir;
		global $debug_mask;

		if (is_null($artrec))
			return;

		echo "\n<div style=\"margin: 4px;\">";
		echo "<div style=\"float: right;\">";
		$f = false; $str = '';
		foreach ($artrec['tags'] as $i) {
			if (!$f) $f = true;
			else $str .= " | ";
			$str .= $i;
			}
		if ($f) echo "\n".$str;
		if (!$dflags & DFLAGS_MOBILE) {  // i.e. not mobile
			echo " [&nbsp;<A href=".$edit_urla.$artrec['article']."&test=1234>edit</a>&nbsp;]";
			}
		echo "</div>";

		if (isset($artrec['caption'][1])) {
			$are = explode(',', $artrec['caption'][1]);
			$date =   trim($are[0]);
			$author = trim($are[1]);
			$author = '<a href=/profile/?public='.$author.'>'.$author.'</a>';
			}
		else {
			$date =   '[no date]';
			$author = '[no author]';
			}
		echo "<div style=\"float: right;\">";
                
		vvv::show($artrec['article'], VIBE_MODE_ARTC);
		echo "\n</div>";

		echo "<p style=\"margin-top: 0;\">".(isset($artrec['caption'][0]) ? '<b>'.$artrec['caption'][0].'</b>' : '');
		echo "<span style=\"color: grey;\"><br>".$date.', '.$author.'</span></p>';
		/*  using readfile() purposely insures PHP commands in file are ignored  */
		if ($artrec['img'] != 'stock') {
			echo "<img src=/gfx-upload/gallery/".$artrec['img']."_144x.png style=\"float: right; margin-bottom: 4px;\">";
			}
		if ($debug_mask & DEBUG_JENN) {  //  Allow markdown content formatting
		    if (($str = file_get_contents($artc_dir.'/'.$artrec['article'])) === FALSE)
		        system_log('Could not open article file - '.$artc_dir.'/'.$artrec['article']);
		    else
		        echo Michelf\Markdown::defaultTransform($str);
		    }
		elseif (readfile($artc_dir.'/'.$artrec['article']) === false)
		    system_log('Could not open article file - '.$artc_dir.'/'.$artrec['article']);  //  ZZZZ
		echo '</div>';
		}

	}  //  artex [ end]

function trim_all($str, $rep = NULL) {
	/*  eleminate all white space from string  */
	/*  str   string to cleanse
	/*  rep   NULL eliminate whitespace
	/*        string to replace white space,
	/*        runs of whitespace will trigger single replace  */
	//  CITATION: http://pageconfig.com/post/remove-undesired-characters-with-trim_all-php
	//  http://stackoverflow.com/questions/659025/how-to-remove-non-alphanumeric-characters
	//  FUTURE: this needs unit test asap!
        //  $what   = "\\x00..\\x20";  //  all white-spaces, control chars, and numbers
        $what   = "^A-Za-z0-9";    //  strip anything but alphanumerics
	$s = $rep ? $rep : '';
//	return trim(preg_replace("/[".$what."]+/", $s, $str), $what);
	return trim(preg_replace("/[".$what."]+/", $s, $str));
	}

function act_digit() {
	/*  return single character in range '0' - '9' / 'A' - 'Z'  */
	$v = rand(0,35);
	return chr((($v > 9) ? $v + 55 : $v + 48));
	}

function system_log($msg, $file = NULL) {
	global $syslog;

	//  for non-trivial events, append message to system log
	if ($fh = fopen(($file ? $file : $syslog), 'a')) {
		fwrite($fh, date('Y-m-d H:i:s').' '.$msg."\n");  //  FUTURE, check if returns false, try/catch?
		//  if ($fh) fclose($fh);
		fclose($fh);
		}
	}

function handle_edit_submit($callback) {
	/*  Output a div that includes results of POST form submit  */
	/*  callback  reference to function that will provide
	/*            appropriate POST submit processing  */
	//  FUTURE: allow AJAX submit so user doesn't leave page
	echo "\n<p style=\"font-size: smaller;\">";
	$result = $callback();
	echo "Result: ".$result;
       	/*  future: go back to edit page?  ... unset($Submit) ?  */
        unset($_POST['submit']);  //  FUTURE: move this to callback (as some callbacks can skip)
        /*  after page loads, the temporary file is automatically discarded  */
	}


//  FUTURE - refactor the following into an 'account' class

function ppp(&$a, $e, $u, $dp = 1) {
	/*  purge stale activation rows that match email address,
	/*  or (TODO FUTURE) date in the past by ... days  */
	/*  $e      email address
	/*  $u      username
	/*  return  true     username available  */
	/*          false    username not available  */
	//  echo "\n<br>".$e.', '.$dp;
	foreach ($a as $k => $v) {
		//  echo "\n<br>".$k.' => '.$v;
		if (strcmp($v[USERACCT_ID],'*') === 0) {
			//  activation records start with *
			if (strcmp($e, $v[USERACCT_MAIL]) === 0) {
				/*  found activation record matching passed in email address, purge  */
				unset($a[$k]);
				}
			else if (strcmp($u, $v[USERACCT_HNDL]) === 0) {
				/*  found activation record matching passed in username, purge  */
				unset($a[$k]);
				}
			else if (0) {
				/*  FUTURE: check for activation records older than ... days  */
				} 
			}
		else if ($v[0][0] != '#') {
			/*  skip comment rows that start with #  */
			if (strcmp($u, $v[USERACCT_HNDL]) === 0) {
				/*  activation username collides with another  */
				echo "\n<br>".$v[USERACCT_HNDL].', already claimed';
				return (false);
				}
			if (strcmp($e, $v[USERACCT_MAIL]) === 0) {
				/*  activation email collides with another  */
				echo "\n<br>".$v[USERACCT_MAIL].', already claimed';
				return (false);
				}
			}
		}
	return (true);
	}

function fff($na, $f = NULL) {
	/*  check user accounts, add new account activation row  */
	/*  return 0 if new activation row added successfully
	/*  otherwise return an error string  */
	/*    na    new account/record
	/*    f     alternate profile file to use (e.g. beta, ...)  */
	/*          For beta program, okay to reapply (existing email match can be ignored)
	/*  FUTURE - account class?  */
	global $data_dir, $login_data;

	if (0) {
		//  db/sql - say tuned
		}
	else {  //  cowboy file (tm)
		$u_raw = array();
		$users = array();
		$file = ($f ? $f : $data_dir.'/'.$login_data);
		if (get_user_profiles($file, $users, $u_raw)) {
			/*  purge stale account activation records  */
			if (!ppp($u_raw, $na[USERACCT_MAIL], $na[USERACCT_HNDL])) {
				if (!$f)  //  beta program - okay to reapply, don't return on match
					return ("username '".$na[USERACCT_HNDL]."' or '".$na[USERACCT_MAIL].
					  "' is already being used by someone else");
				}
			array_push($u_raw, $na);
			if (!put_user_profiles_raw($file, $u_raw))
				return ("could not update profiles file ".$file);
			return (0);
			}	
		else
			return ("no profiles file ".$file );
		}
	}


function qqq(&$a, &$s) {
	/*  locate matching activation row, purge it
	/*  $a      raw user profile array
	/*  $s      username
	/*          'id'        returned
	/*          'handl'     passed in
	/*          'act_code'  passed in
	/*          'beta'      passed in, ONLY for special beta programs
	/*          'fname'     returned
	/*          'email'     returned
	/*  return  true        activation record found and matches  */
	/*          false       activation mismatch/not found  */
	$max_id = 0;
	if (isset($s['beta']))
		$result = true;  //  ONLY for special beta programs, skip matching but find max_id
	else
		$result = false;
//	echo "\n<br>result: ".($result ? 'TRUE' : 'FALSE');
	foreach ($a as $k => $v) {
		//echo "\n<br>".$k.' => '.$v;
		if (!$result && strcmp($v[0],'*') === 0) {
			//  activation records start with *
			if (strcmp($s['handl'], $v[USERACCT_HNDL]) === 0 && strcmp($s['act_code'], $v[USERACCT_HASH]) === 0) {
			// (strcmp($u, $v[USERACCT_HNDL]) === 0 && strcmp($c, $v[USERACCT_HASH]) === 0) {
				/*  found activation record matching username  */
				if (isset($v[USERACCT_FNAM])) $s['fname'] = $v[USERACCT_FNAM];
				if (isset($v[USERACCT_MAIL])) $s['email'] = $v[USERACCT_MAIL];
				if (isset($v[USERACCT_FLGS])) $s['flags'] = $v[USERACCT_FLGS];
				$result = true;
				unset($a[$k]);
				}
			else if (0) {
				/*  FUTURE: check for activation records older than ... days  */
				} 
			}
		/*  skip comment rows that start with #  */
		if ($v[0][0] != '#') {
			/*  attempt to find greatest row ID value, pass this back somehow  */
			if ($max_id < $v[USERACCT_ID]) $max_id = $v[USERACCT_ID];
			}
		}
	$s['id'] = $max_id + 1;
	return ($result);
	}

class account_util {
	}  /*  class - account_util [end]  */

function jjj($na, $s, $f = NULL) {
	/*  Accept validation, finalize creating new user account.
	/*  Note: often special products are added to account at this point   */
	/*
	/*  check user accounts, match activation record  */
	/*  return 0 if activation successful
	/*  otherwise return an error string  */
	global $data_dir, $login_data;

	if (0) {
		//  db/sql - say tuned
		}
	else {
		$u_raw = array();
		$users = array();
		$file = ($f ? $f : $data_dir.'/'.$login_data);
		if (get_user_profiles($file, $users, $u_raw)) {
			/*  check if matching activation record present  */
			// (!qqq($u_raw, $s['act_code'], $s['handl']))
			/*  qqq should locate and drop initial signup row, update s account record  */
			if (!qqq($u_raw, $s))
				return ("username '".$s['handl']."' activation code not found");

			/*  CODE NOW  */
			$na[USERACCT_ID] =   $s['id'];
			$na[USERACCT_FNAM] = $s['fname'];
			$na[USERACCT_MAIL] = $s['email'];

			//  FUTURE how to use profile_ACCTYP ... if profile class not included?
			$na[USERACCT_FLGS] = $s['flags'];  //  DON'T ACTIVATE HERE, instead activate in wizard or when account has minimum profile setup
			array_push($u_raw, $na);
			if (!put_user_profiles_raw($file, $u_raw))
				return ("could not update user profiles file");
			if ($f)
				return (0);
			//  only perform entitlements for formal account profile operations
			if (($s['flags'] & 15) == 1) {
				$t = products::prod_add_ent(
				  $data_dir.'/products/active',
				  array('provider-alpha1'),
				  $s['id'],
				  $s['handl'],
				  'signup automatic');
				if ($t) echo $t;  //	return ("could not add entitlement");
				}
			return (0);
			}	
		else
			return ("no user profiles file");
		}
	}


//  FUTURE - refactor the following into an 'profile' class
//           rename profile_put / arrayfile_put ?
//  NOT, this is a general loader function!!!

/*  prototype  */
/*  handle      username  */

function profile_get($file, $pr) {
	}

function profile_put($file, &$pr, $mode = NULL) {
	/*  pr    profile record array  */
	/*  mode  NULL,   use classic CBF
	/*        'json', use json format
	/*
	/*        img, goals, itags, about  */

	//  append log file with current account file contents (in case a revert is needed for some reason)
	$cmd = 'echo '.date('Y-m-d H:i:s').' >> '.$file.'_log';
	$out = shell_exec($cmd);
	$cmd = 'echo ------------------- >> '.$file.'_log';
	$out = shell_exec($cmd);
	$out = shell_exec("cat ".$file." >> ".$file.'_log');
	//  FUTURE - above can be replaced with mytake file_log() call
	$result = false;
	if ($fh = fopen($file, 'w')) {
		if ($mode) {
			fwrite($fh, "{");  //  FUTURE, check if returns false, try/catch?
			$sc = '';
			foreach ($pr as $k => $v) {
				$str =  $sc."\n\"".$k.'":"'.$v.'"';
				fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
				$sc = ',';
				}
			fwrite($fh, "\n}\n");  //  FUTURE, check if returns false, try/catch?
			$result = true;
			}
		else {
			foreach ($pr as $k => $v) {
				$str =  '<span id="'.$k.'">'.$v."</span>\n";
				fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
				}
			$result = true;
			}
		if ($fh) fclose($fh);
		}
	return $result;
	}


const USERLIST_ALL =  0;        //  show all users
const USERLIST_CURF = 1;        //  current friends
const USERLIST_REQF = 2;        //  requests to be friend
const USERLIST_ALLF = 3;        //  show all users, pass in friends list as $a
                                //  FUTURE, now using $fl instead, revert to USERLIST_ALL?
const USERLIST_PIVOT = 4;       //  determine list recomended users for a given pivot
				//  FUTURE - likely best to determine the list before calling this
const USERLIST_PROV = 8;        //  bit flag - only show providers
const USERLIST_RECM = 16;       //  bit flag - only recommeded providers
const USERLIST_REC_DP1 = 32;    //  bit flag - only recommeded providers, drop 1
const USERLIST_REC_DP2 = 64;    //  bit flag - only recommeded providers, drop 2

function user_list($pf = USERLIST_ALLF, $a = NULL, $fl = NULL, $al = NULL, $il = NULL) {
	//  output tightly spaced user list including
	//  mini-image, nickname, and link to profile page
	//    pf presentaion flag
	//    a  array of members to match against
	//    fl array of friends, needed to present better interaction links
	//    al array of pending accepts, needed to present better interaction links
	//    il array of pending invites, needed to present better interaction links
	//  FUTURE: add n to list, o offset into list, s search term, f sort flags
	//  FUTURE: move this to mytake/session.php, add callback?
	global $data_dir, $login_data, $admins;

	$f = $pf & 0x7;  //  $f is only lower three bits, $pf retains all bits
	$u_raw = array();
	$users = array();
	$file_profiles = $data_dir.'/'.$login_data;
	if (get_user_profiles($file_profiles, $users, $u_raw)) {
		$i = 0;
		$un = session_username_active();
		//  FUTURE - this function likely should become a class method,
		//  and the $un could then be class property set before invoking function
		foreach ($u_raw as $k => $v) {
			if (isset($v[0][0]) && $v[0][0] != '#') {  //  skip comment row
				if (!is_null($a) && $f != USERLIST_ALLF) {
					$m = false;
					foreach ($a as $mk => $mv) {
						$mv2 =  trim($mv,'"');
						if (strcmp($mv2, $v[USERACCT_HNDL]) === 0) {
							$m = true;
							break;
							}
						}
					}
				else
					$m = true;
				if (isset($fl)) {
					//  constraint list passed in, skip output if not on list
					if (!in_array($v[USERACCT_HNDL], $fl))
						$m = false;
					}
				if ($m && $v[USERACCT_ID] == '*')  //  skip past temporary, unconfirmed accounts
					$m = false;
				if ($m && ($pf & USERLIST_PROV) && (($v[USERACCT_FLGS] & 1) == 0))
					$m = false;
				if ($m) {
					if ($i > 0)
						echo "\n<div style=\"height: 4px; clear: both;\"></div>";
					else {
						if (($pf & USERLIST_RECM) == 0 && ($f != USERLIST_PIVOT) && !session_userid_active()) {
							echo "\n<div style=\"clear: both;\">please login to search users</div>";
							break;
							}
						}
					$bdg_adm = '';  //  admin badge
					if (isset($v[USERACCT_HNDL])) {     //  min image
					    if (in_array($v[USERACCT_HNDL], $admins)) $bdg_adm = '(A)';  //  check for admin
					    echo "<img src=\"/gfx-upload/avatar_".$v[USERACCT_HNDL]."_min.gif\" style=\"float: left; margin-right: 4px;\">";
					    }
					if (isset($v[USERACCT_FNAM]))       //  nickname
						echo $v[USERACCT_FNAM].' ';
					echo $bdg_adm.' ';                  //  show admin badge
					if ($un && in_array($un, $admins)) {
					    if (isset($v[USERACCT_FLGS]))   //  if 'admin' show account flags
					        echo '('.$v[USERACCT_FLGS].')';
					    }
					if (isset($v[USERACCT_HNDL]))       //  username
						echo "\n<br><a href=/profile/?public=".$v[USERACCT_HNDL].'>'.$v[USERACCT_HNDL].'</a>';

					//  SHOW ONLY FRIENDS
					if ($f == USERLIST_CURF && (($pf & USERLIST_RECM) == 0))        //  current friends
						echo "\n<br>(<a href=/?friends&forget=".$v[USERACCT_HNDL].">-</a>)";
					//  SHOW ONLY FRIEND REQUESTS
					elseif ($f == USERLIST_REQF && (($pf & USERLIST_RECM) == 0)) {  //  outstanding friend invites
						echo "\n<br>(<a href=/?friends&accept=".$v[USERACCT_HNDL].">+</a>)";
						echo " (<a href=/?friends&snub=".$v[USERACCT_HNDL].">-</a>)";
						}
					elseif ($pf == (USERLIST_REQF | USERLIST_RECM)) {     //  outstanding endorse invites
						echo "\n<br>(<a href=/?friends&accept-rec=".$v[USERACCT_HNDL].">+</a>)";
						echo " (<a href=/?friends&snub-rec=".$v[USERACCT_HNDL].">x</a>)";
						}

					elseif ($pf == (USERLIST_CURF | USERLIST_RECM | USERLIST_REC_DP1)) {     //  endorse drop 1
						echo "\n<br>(<a href=/?friends&drop-rec=".$v[USERACCT_HNDL].">x</a>)";
						}
					elseif ($pf == (USERLIST_CURF | USERLIST_RECM | USERLIST_REC_DP2)) {     //  endorse drop 2
						echo "\n<br>(<a href=/?friends&forget-rec=".$v[USERACCT_HNDL].">-</a>)";
						}

					//  SHOW ALL - if not logged in/not self/already friend, withhold showing invite
					elseif (session_userid_active()) {                     //  is logged iin
						echo "\n<br>";
						if (!is_null($al) && in_array($v[USERACCT_HNDL], $al))           //  pending accept
							echo '(p)';
						elseif (!is_null($il) && in_array($v[USERACCT_HNDL], $il))      //  pending invite
							echo '(w)';
						elseif ($un != $v[USERACCT_HNDL]) {  //  exclude self
							if (!isset($a) || !in_array($v[USERACCT_HNDL], $a))
								//  if friends list doesn't exist invite all, otherwise, exclude existing friends
								echo '(<a href=/?friends&invite='.$v[USERACCT_HNDL].'>i</a>)';
							}
						}
					$i++;
					}
				}
			}
		return (0);
		}	
	else
		return ("no user profiles file");
	}


const MAKEMIN_AVTR = 1;
const MAKEMIN_GALL = 2;

function image_make_min($file, $src = NULL, $t = NULL, $w = 55, $h = 55) {
	//  generate a 55x55 pixel avatar image,
	//  if no src file then synthesize an image from scratch
	//  return: NULL          successful
	//          text string  error condition
	//  if (!extension_loaded('gd')) echo "\n<p>no gd library :-/</p>";
	//  else ap(gd_info());
	if (!is_null($src)) {
		// Get new sizes
//echo "\n<br>src: ".$src;
		list($width, $height) = getimagesize($src);
//		echo "\n<br>".$src.', '.$width.', '.$height.', ';
		if ($t == 'png')
			$om = imagecreatefrompng($src);  //  FUTURE, call fails?
		else if ($t == 'jpeg')
			$om = imagecreatefromjpeg($src);
		else
			return ('unsupport image format');
		// Load
		$im = imagecreatetruecolor($w, $h);
		// Resize
		imagecopyresized($im, $om, 0, 0, 0, 0, $w, $h, $width, $height);
		imagedestroy($om);
		}
	else {
		// Create a new image instance
		$im = imagecreatetruecolor($w, $h);
		// Make the background white
		imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, 0xFFFFFF);
		// Draw a text string on the image
		imagestring($im, 0, 40, 20, 'GD Library', 0xFFBA00);
//		// Save the image as a GIF
//		imagegif($im, $file);
//		imagedestroy($im);
		}
	// Save the image as a GIF
	imagegif($im, $file);
	imagedestroy($im);
	return (NULL);
	}

function body_menu_x($m, $m2 = false) {
        /*  OBSOLETE, see panel/menu.php  */

	//  m   menu to show selected
	//      if tag get parameter, consider pivot selected    
	//      undefined, hilight no menu
	//      NULL, highlight home
	//  m2  2nd menu, only present for home?
	//	false, omit displaying entirely
	global $debug_mask, $admins, $edit_urla;

	if ($debug_mask & DEBUG_MENU_BG) {
		$ds = 'men2';
		$ss = 'me2o';
		}
	else {
		$ds = 'menu';
		$ss = 'meno';
		}

	$is_edit = NULL;
	$u = session_username_active();
	echo "<div style=\"margin-left: 4px;\">";

	echo "\n<div class=".$ds.'>';
	if ($m)  //  HOME
		echo '<a class=menu href=/>home</a>';
	else {
		echo "<span class=men2>home</span>";
		$is_edit = 'home';
		}
	echo '</div>';

	echo "\n<div class=".$ds.'>';
	if ($m == 'aol' || isset($_GET['tag']) || isset($_GET['art'])) {  //  PIVOT
		if (isset($_GET['tag']) || isset($_GET['art']))
			echo "<a class=men2 href=/?aol>pivot</a>";
		else
			echo "<span class=men2>pivot</span>";
		}
	else
		echo "<a class=menu href=/?aol>pivot</a>";
	echo '</div>';

	echo "\n<div class=".$ds.'>';
	if ($u) {  //  DASHBOARD <- HELP <- CONNECT
		if ($m == 'dash') {
			echo "<span class=men2>dashboard</span>";
			$is_edit = 'dash';
			}
		else
			echo "<a class=menu href=/?dash>dashboard</a>";
		}
	else
		echo "<span class=".$ss.'>dashboard</span>';
	echo '</div>';

	echo "\n<div class=".$ds.'>';
	if ($m == 'providers')  //  PROVIDERS
		echo "<span class=men2>practitioners</span>";
	else
		echo ($u ?
		  '<a class=menu href=/?providers>practitioners</a>' :
		  '<span class='.$ss.'>practitioners</span>');
	echo '</div>';

	echo "\n<div class=".$ds.'>';
	if ($m == 'search')  //  SEARCH
		echo "<span class=men2>search</span>";
	else
		echo ($u ?
		  '<a class=menu href=/?search>search</a>' :
		  '<span class='.$ss.'>search</span>');
	echo '</div>';

	echo "\n<input type=\"text\" size=12 style=\"height: 17px; display: inline-block;\"".(($u) ? '' : ' disabled')."> ";

	if ($m == 'profile') {
		$is_edit = 'profile';
		}

//	echo "\n | logout";
//	echo "\n | sign up";

	if ($m2 !== false) {
		//  2nd menu, for now only dashboard menu needs this
		$m2a = '<span style="color: grey;">';  $m2b = '</span>';
		$m2_o  = "\n<div style=\"margin: 0 0 4px 4px; font-weight: bolder;\">";

		//  NULL, show dashboard active
		$m2_o .= is_null($m2) ?  'My&nbsp;News' :
		  $m2a.'<a class=m2 href=/?dash>My&nbsp;News</a>'.$m2b;
		$m2_o .= ' &nbsp; ';

		//  show my content active, but only if logged in
		$m2_o .= ($u) ? (($m2 == 'mc') ? 'My&nbsp;Content' :
		  $m2a.'<a class=m2 href=/?dash&m2=mc>My&nbsp;Content</a>'.$m2b) :
		  $m2a.'My&nbsp;Content'.$m2b;
		$m2_o .= ' &nbsp; ';
		if ($m2 == 'mc') $is_edit = 'dash_mc';

		//  show my messagese, but only if logged in
		$m2_o .= ($u) ? (($m2 == 'ms') ? 'My&nbsp;Messages' :
		  $m2a.'<a class=m2 href=/?dash&m2=ms>My&nbsp;Messages</a>'.$m2b) :
		  $m2a.'My&nbsp;Messages'.$m2b;
		$m2_o .= ' &nbsp; ';
		if ($m2 == 'ms') $is_edit = 'dash_ms';

		//  show my connections active, but only if logged in
		$m2_o .= ($u) ? (($m2 == 'mf') ? 'My&nbsp;Connections' :
		  $m2a.'<a class=m2 href=/?dash&m2=mf>My&nbsp;Connections</a>'.$m2b) :
		  $m2a.'My&nbsp;Connections'.$m2b;
		if ($m2 == 'mf') $is_edit = 'dash_mf';

		//  Admins only
		if ($u && in_array($u, $admins)) {
			$m2_o .= ' &nbsp; ';
			$m2_o .= ($m2 == 'ad') ? 'Admin' :
			  $m2a.'<a class=m2 href=/?dash&m2=ad>Admin</a>'.$m2b;
			$m2_o .= ' &nbsp; ';
			if ($m2 == 'ad') $is_edit = 'dash_ad';
			}

		$m2_o .= '</div>';
		}
	else
		$m2_o = '';

	if ($is_edit && $u) {
		if (in_array($u, $admins)) {
			echo "\n<div class=".$ds.'>';
			//$mh .= ' [ <a href='.$edit_urla.'is/home>info</a> ]';
			echo '<a href='.$edit_urla.'is/'.$is_edit.'>info</a>';
			if ($is_edit == 'home')
				echo ' <a href='.$edit_urla.'is/'.$is_edit.'_car>car</a>';
			echo '</div>';
			}
		}

	echo "</div>";


	echo $m2_o;
	}

//  FUTURE - refactor the following into a 'lounge' class

const LOUNGE_DATE = 0;    //  datestamp
const LOUNGE_HNDL = 1;    //  author username
const LOUNGE_ADNC = 2;    //  audience
const LOUNGE_TOPRIV = 3;  //  audience
const LOUNGE_MSG  = 4;    //  message

const LOUNGE_DEF_FR = 0;  //  friends
const LOUNGE_DEF_PV = 1;  //  pivot / param
const LOUNGE_DEF_PB = 2;  //  public
const LOUNGE_DEF_PR = 3;  //  private / param
const LOUNGE_DEF_EX = 4;  //  symbosium / asset id
const LOUNGE_DEF_AR = 5;  //  article / asset id
const LOUNGE_DEF_WL = 6;  //  dashwall exchange / asset id

class lounge {

	static public function out($who, $id = NULL, $fl = NULL, $gowide = FALSE) {
		//    who     standard lounge: 'public', 'friends' - drawn from a sinle common datastore
		//            asset based lounge: 'exch', 'artc', wall, pivt - drawn from dedicated datastore
		//    id      NULL, include header, constrain width?
		//            exch/artc asset id to show, omit header
		//            
		//    fl      array list of friends,
		//            omit outputing content from users not on list
		global $lounge_dir, $edit_urla, $exch_dir, $artc_dir, $wall_dir, $pivt_dir, $admins;

		$un = session_username_active();
			$adm = '';
		if (is_null($id)) {
			if ($un && in_array($un, $admins))
				$adm = "[<a href=\"".$edit_urla."lounge/lounge\">debug</a>]";  //  admins only
			echo "\n\n<div id=\"lounge\" style=\"padding: 4px; width0: 272px; background-color: grey;\">";
			echo "\n<!--  lounge  -->";
			echo "\n<div class=list_head>\n<span style=\"font-weight: bolder;\">Lounge</span> ".$adm;
			echo '</div>';
			}
		else {
			echo "\n\n<div id=\"lounge\" style=\"padding: 4px; background-color: grey;\">";
			}
		echo "\n<div style=\"background-color: white; clear; both; overflow: hidden;\"><!--  Lounge White  -->";
		if (session_userid_active()) {
			//  present new comment form, only if active user
			if ($who == 'public')
				lounge::edit_form(LOUNGE_DEF_PB);
			elseif ($who == 'exch') {
				if ($un && in_array($un, $admins))
					$adm = "<a href=\"".$edit_urla."exchange/vvv/".$id."_vc\">debug</a>";  //  admins only
				lounge::edit_form(LOUNGE_DEF_EX, $id);
				}
			elseif ($who == 'artc') {
				if ($un && in_array($un, $admins))
					$adm = "<a href=\"".$edit_urla."articles/vvv/".$id."_vc\">debug</a>";  //  admins only
				lounge::edit_form(LOUNGE_DEF_AR, $id);
				}
			elseif ($who == 'wall') {
				if ($un && in_array($un, $admins))
					$adm = "<a href=\"".$edit_urla."wall/vvv/".$id."_vc\">debug</a>";  //  admins only
				lounge::edit_form(LOUNGE_DEF_WL, $id);
				}
			elseif ($who == 'pivt') {
				if ($un && in_array($un, $admins))
					$adm = "<a href=\"".$edit_urla."pivot/vvv/".$id."_vc\">debug</a>";  //  admins only
				lounge::edit_form(LOUNGE_DEF_PV, $id);
				}
			else
				lounge::edit_form(LOUNGE_DEF_FR);
			}
		$o_a = array();	//  raw lounge has most recent last, new array allows custom order
		$l_a = array();
		if ($id && $who == 'exch') {
			$file_lounge = $exch_dir.'/vvv/'.$id.'_vc';
			$sh = '';
			}
		elseif ($id && $who == 'artc') {
			$file_lounge = $artc_dir.'/vvv/'.$id.'_vc';
			$sh = '';
			}
		elseif ($id && $who == 'wall') {
			$file_lounge = $wall_dir.'/vvv/'.$id.'_vc';
			$sh = '_e';
			}
		elseif ($id && $who == 'pivt') {
			$file_lounge = $pivt_dir.'/vvv/'.$id.'_vc';
			$sh = '_p';
			}
		else
			$file_lounge = $lounge_dir.'/lounge';
		$ct = 0;
//		echo "\n<br>file_lounge: ".$file_lounge;
		if (static::get($file_lounge, $l_a)) {
			$fr = ($who == 'friends') ? $fl : NULL;
			foreach ($l_a as $k => $v) {
				//  raw lounge has most recent last, new array allows custom order
				if ($v[LOUNGE_ADNC] == $who) {
					if (is_null($fr) ||						//  match any for public audience
					//  $un == $v[LOUNGE_HNDL] ||	//  match user's own
					    in_array($v[LOUNGE_HNDL], $fl[$un])) {                      //  match friend audience
						array_splice($o_a, 0, 0, array($v));	//  insert at start of array
						$ct++;
						}
					}
				elseif ('exch' == $who || 'artc' == $who || 'wall' == $who || 'pivt' == $who) {
					$ct++;
			 		array_splice($o_a, 0, 0, array($v));	//  insert at start of array
					}
				}
			}	
		//  FUTURE - following can output comment count,
		//           however opening count file and using that value is better  to handle high comment counts
		/*  echo "\n<br>vocal count: <span id=\"".$id."_cmct\">".$ct.'</span>';  */
	
		reset($o_a);
		foreach ($o_a as $k => $v) {
		//	echo "\n<div style=\"margin-top: 4px;\">";
			echo "\n<div style=\"padding: 4px 4px 0 4px; clear: left;\">";
			echo "\n<img src=\"/gfx-upload/avatar_".$v[LOUNGE_HNDL].'_min.gif" style="float: left; width: 2em; margin-right: 4px;">';
			echo "<a href=/profile/?public=".$v[LOUNGE_HNDL].'>'.$v[LOUNGE_HNDL].'</a>';
			echo "\n<br><span style=\"font-style: italic; font-size: smaller;\">".$v[LOUNGE_MSG].'</span></div>';
			//echo "\n<div style=\"clear: left;\"></div>";
			}
		echo "\n<div style=\"font-size: smaller; padding: 4px;\">click for more";
		if ($id)
			echo "\n<div style=\"float: right;\">".$adm." <span onclick=\"display_toggle('".$id.$sh."_cmnt');\">hide</span></div>";
		echo '</div>';

		echo "\n<!--  Lounge White [end]  --></div>";
		echo "\n<!--  lounge [end] -->\n</div>";
		}

	static private function edit_form($def = LOUNGE_DEF_FR, $param = NULL) {  /*  lounge  */
		//  Present profile edit form, typically hidden until user clicks to edit.
		//  Default values will be updated dynamically via javascript elsewhere using field name.
		//    def   [see const above]
		//    param not needed for public, friends
		//  echo $def.' / '.$param;
		if ($def == LOUNGE_DEF_EX) {
			echo "\n<form style=\"display: inline-block; margin: 0; width: 100%; background-color: grey;\" name=\"form_lounge\" method=\"post\"";
			echo "\n  action=\"?submitmode=exchcmnt\">";
			echo "\n<input type=\"text\"   name=\"fl_aud\"    value=\"exchcmnt\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_param\"  value=\"".$param."\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_msg\"    value=\"message\" style=\"width: 100%\" ";
			echo "\n  id=\"".$param."_msg\" onKeyPress=\"if (event.keyCode == 13) vocal('".$param."', ".VIBE_MODE_EXCH.");\">";
			}
		elseif ($def == LOUNGE_DEF_WL) {
			echo "\n<form style=\"display: inline-block; margin: 0; width: 100%; background-color: grey;\" name=\"form_lounge\" method=\"post\"";
			echo "\n  action=\"?submitmode=exchcmnt\">";
			echo "\n<input type=\"text\"   name=\"fl_aud\"    value=\"exchcmnt\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_param\"  value=\"".$param."\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_msg\"    value=\"message\" style=\"width: 100%\" ";
			echo "\n  id=\"".$param."_e_msg\" onKeyPress=\"if (event.keyCode == 13) vocal('".$param."', ".VIBE_MODE_WALL.");\">";
			}
		elseif ($def == LOUNGE_DEF_PV) {
			echo "\n<form style=\"display: inline-block; margin: 0; width: 100%; background-color: grey;\" name=\"form_lounge\" method=\"post\"";
			echo "\n  action=\"?submitmode=exchcmnt\">";
			echo "\n<input type=\"text\"   name=\"fl_aud\"    value=\"exchcmnt\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_param\"  value=\"".$param."\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_msg\"    value=\"message\" style=\"width: 100%\" ";
			echo "\n  id=\"".$param."_p_msg\" onKeyPress=\"if (event.keyCode == 13) vocal('".$param."', ".VIBE_MODE_PIVT.");\">";
			}
		elseif ($def == LOUNGE_DEF_AR) {
			echo "\n<form style=\"display: inline-block; margin: 0; width: 100%; background-color: grey;\" name=\"form_lounge\" method=\"post\"";
			echo "\n  action=\"?submitmode=artccmnt\">";
			echo "\n<input type=\"text\"   name=\"fl_aud\"    value=\"artccmnt\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_param\"  value=\"".$param."\" hidden>";
	//		echo "\n<input type=\"submit\" name=\"fl_submit\" value=\"submit\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_msg\"    value=\"message\" style=\"width: 100%\" ";
		//	echo "\n  id=\"".$param."_msg\" onKeyPress=\"if (event.keyCode == 13) alert('yo');\">";
			echo "\n  id=\"".$param."_msg\" onKeyPress=\"if (event.keyCode == 13) vocal('".$param."', ".VIBE_MODE_ARTC.");\">";
			}
		else {
			echo "\n<form style=\"display: inline-block; margin: 0; width: 100%; background-color: grey;\" name=\"form_lounge\" method=\"post\"";
			echo "\n  action=\"?submitmode=lounge\" enctype=\"multipart/form-data\">";
			$d_fr = $d_pv = $d_pb = ' disabled';
			$s_fr = $def == 0 ? ' selected' : ''; if ($def == 0) $d_fr = '';
			$s_pv = $def == 1 ? ' selected' : ''; if ($def == 1) $d_pv = '';
			$s_pb = $def == 2 ? ' selected' : ''; if ($def == 2) $d_pb = '';
			echo "\n<select id=\"fl_aud\"    name=\"fl_aud\"  onclick=\"alert('audience');\">";
			echo "\n<option text=\"friends\" id=\"fl_aud_fr\" value=\"friends\"".$s_fr.$d_fr.'>friends</option>';
		//	echo "\n<option text=\"pivot\"   id=\"fl_aud_pv\" value=\"pivot\"".$s_pv.$d_pv.'>pivot</option>';
			echo "\n<option text=\"public\"  id=\"fl_aud_pb\" value=\"public\"".$s_pb.$d_pb.'>public</option>';
			echo "\n</select>\n";
			echo "\n<input type=\"submit\" name=\"fl_submit\" value=\"submit\" hidden>";
			echo "\n<input type=\"text\"   name=\"fl_msg\"    value=\"message\" style=\"width: 100%\" id=\"".$param."_msg\">";
			}
//		echo "\n<input type=\"submit\" name=\"fl_submit\" value=\"submit\" hidden>";
//		echo "\n<input type=\"text\"   name=\"fl_msg\"    value=\"message\" style=\"width: 100%\" id=\"".$param."_msg\">";
	//	echo "\n<input type=\"text\"   name=\"fl_msg_ds\" value=\"message\" style=\"width: 100%; display: none;\" disabled>";
		echo "\n</form>";
		}

	static public function edit_submit() {  /*  lounge  */
		//  return  message string describing result
		global $lounge_dir, $exch_dir, $wall_dir, $pivt_dir, $artc_dir;

		if (!($u = session_username_active()))
			return ('please login before posting to lounge');
		if (!isset($_POST['fl_aud']))
			return ('unspecified audience');
		$a = array();
		$a['frm'] = $u;
		$a['prm'] = $_POST['fl_param'];
		$a['msg'] = $_POST['fl_msg'];
		if (($a['aud'] = $_POST['fl_aud']) == 'exchcmnt')
			$file_lounge = $exch_dir.'/vvv/'.$a['prm'].'_vc';
		elseif ($a['aud'] == 'wallcmnt')
			$file_lounge = $wall_dir.'/vvv/'.$a['prm'].'_vc';
		elseif ($a['aud'] == 'pivtcmnt')
			$file_lounge = $pivt_dir.'/vvv/'.$a['prm'].'_vc';
		elseif ($a['aud'] == 'artccmnt')
			$file_lounge = $artc_dir.'/vvv/'.$a['prm'].'_vc';
		else
			$file_lounge = $lounge_dir.'/lounge';

		echo "\n".$a['aud'];
		echo ', '.$a['prm'];
		echo ', '.$file_lounge;
		echo "\n<br>".$a['msg'];

		if (static::put($file_lounge, '"'.$a['frm'].'", "'.$a['aud'].'", "'.$a['prm'].'", "'.$a['msg'].'"')) {
			$result = 'lounge post successful';
			if     ($a['aud'] == 'exchcmnt') {
				//  FUTURE - wrap a lock around this?
				vvv::vocal($a['prm'], 'exchrt', $voct);
				$voct++;
				vvv::vocal($a['prm'], 'exchwt', $voct);
				}
			elseif  ($a['aud'] == 'wallcmnt') {
				//  FUTURE - wrap a lock around this?
				vvv::vocal($a['prm'], 'wallrt', $voct);
				$voct++;
				vvv::vocal($a['prm'], 'wallwt', $voct);
				}
			elseif  ($a['aud'] == 'pivtcmnt') {
				//  FUTURE - wrap a lock around this?
				vvv::vocal($a['prm'], 'pivtrt', $voct);
				$voct++;
				vvv::vocal($a['prm'], 'pivtwt', $voct);
				}
			elseif ($a['aud'] == 'artccmnt') {
				//  FUTURE - wrap a lock around this?
				vvv::vocal($a['prm'], 'artcrt', $voct);
				$voct++;
				vvv::vocal($a['prm'], 'artcwt', $voct);
				}
			}
		else
			$result = 'problem posting to lounge';
		return ($result);
		}

	static private function get($file, &$lounge) {
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
//					$va = array('handle' => $data[USERACCT_HNDL]);
//					if (isset($data[USERACCT_DATE])) $va['date'] = $data[USERACCT_DATE];
//					if (isset($data[USERACCT_HASH])) $va['date'] = $data[USERACCT_HASH];
//					if (isset($data[USERACCT_FNAM])) $va['date'] = $data[USERACCT_FNAM];
//					if (isset($data[USERACCT_MAIL])) $va['date'] = $data[USERACCT_MAIL];
//					$users[$data[USERACCT_ID]] = $va;
//					}
//				if (!is_null($raw))
					array_push($lounge, $data);
					}
				}
			$result = true;
			fclose($fh);
			}
		return $result;
		}

	static private function put($file, $str) {
		//  append log file with current lounge file contents (in case a revert is needed for some reason)
		$d = date('Y-m-d H:i:s');
		$cmd = 'echo '.$d.' >> '.$file.'_log';
		$out = shell_exec($cmd);
		$cmd = 'echo ------------------- >> '.$file.'_log';
		$out = shell_exec($cmd);
		$out = shell_exec("cat ".$file." >> ".$file.'_log');

		$result = false;
		//  now append lounge message line to end of file
		if ($fh = fopen($file, 'a')) {  //  Append!
			fwrite($fh, $d.', '.$str."\n");  //  FUTURE, check if returns false, try/catch?
			$result = true;
			if ($fh) fclose($fh);
			}
		return $result;
		}

	}  /*  lounge vvv [end]  */

//  FUTURE - refactor the following into a 'friends' class

class social {
    /*  As of 2016 there are three social modes
    /*
    /*             symmetry     approve
    /*             ------------ --------
    /*  friends   |symetrical  |required
    /*  follow    |asymmetrical|none
    /*  recommend |asymetrical |required  */
    /*
    /*  cb file - friends (should this use leading user(uid): f1, f2, ...?)
    /*    - friends
    /*      friend1: friend2, firend3
    /*      friend2: friend1
    /*      friend3: friend1
    /*    - friends invite
    /*      friend2: friend3  */
    /*  cb file - following
    /*    - following
    /*      user1(1): user3
    /*      user3(1): user2, user3
    /*  cb file - recommend / endorse
    /*    - recinvite
    /*      user2(2): user3
    /*    - recommend, who a providers recommends
    /*      user1(1): user3
    /*    - recomended, who has recommended a provider
    /*      user3(3): user1
*/
    }

class endorse extends social {

    const INVITE =   1;
    const ACCEPT =  2;
    const FORGET = 4;
    const SNUB =  8;
    const DROP = 16;

    //  exchange::following_update()
    //  friends_update()

    static public function update($cmd, $un, $uid, $ue) {
	//    $cmd  command/action
	//    $un   username to process
	//    $uid  unique id of user to process (correpsondes to username)
	//    $ue   username of friend to invite
	//  return: N/A
	//          as there is no way to recover at this call level,
	//          outputing error message is appropriate
	//  output: error message string
	//  FUTURE shouldn't everything use UID instead of username?
	global $data_dir, $login_data;

//	echo "\n<br>".$cmd.' / '.$un.' / '.$uid.' / '.$ue;
	if (is_null($un) || is_null($ue)) {
		echo "invalid endorse update";
		return;
		}
	if ($un == $ue) {
		echo "endorse self undefined .";
		return;
		}

	$is_cmd = FALSE;

	//  FUTURE - assert mt_lock() here, but make sure to free it, try/catch?

	$nv = array();
	$file_i = $data_dir.'/recminvt';  //  FUTURE - filename in config
	if (!lists::get($file_i, $nv)) {
		echo "problem updating endorse data";
		return;
		}

	if ($cmd & (static::ACCEPT | static::FORGET | static::DROP)) { 
		$fr = array();  //  this is who user recommends
		$file_f = $data_dir.'/recommend';  //  FUTURE - filename in config
		if (!lists::get($file_f, $fr)) {
			echo "problem accessing endorse data .";
			return;
			}
//		}
//	if ($cmd & (static::ACCEPT | static::FORGET)) { 
		$fd = array();  //  this is recommendations toward a user
		$file_d = $data_dir.'/recmended';  //  FUTURE - filename in config
		if (!lists::get($file_d, $fd)) {
			echo "problem accessing endorsee data ..";
			return;
			}
		}

	if ($cmd & static::ACCEPT) {
// see below	$is_cmd = TRUE;  
		$i = 0;
		if (isset($fr[$ue])) {
			$a = $fr[$ue];
			if (!in_array($un, $a)) {
				array_push($a, $un);
				$i++;
				}
			}
		else {
			$a = array($un);      $i++;
			}
		$fr[$ue] = $a;

//  endorsements are NOT symetrical
		if (isset($fd[$un])) {
			$a = $fd[$un];
			if (!in_array($ue, $a)) {
				array_push($a, $ue);
				$i++;
				}
			}
		else {
			$a = array($ue);      $i++;
			}
		$fd[$un] = $a;

		if ($i < 1)
			echo "endorse already accepted";
		else if (lists::put($file_f, $fr, '# list by who a particular practioner has endorsed')) {
			//  recommend file updated
			if (lists::put($file_d, $fd, '# list by who has endorsed a particular practioner')) {
				//  recommendee file updated
				echo 'recoommend/endorse accept successful';
				}
			else {
				echo 'problem with endorse accept';
				return;
				}
			}
		else {
			echo 'problem with endorse accept .';
			return;
			}
		}

	if ($cmd & (static::SNUB | static::ACCEPT)) {
		//  got here because need to clear away invite state
		$is_cmd = TRUE;
		$i = 0;
		if (isset($nv[$un])) {
			foreach ($nv[$un] as $k => $v) {
				if ($v == $ue) {
					unset($nv[$un][$k]);
					$i++;
					}
				}
			if (count($nv[$un]) < 1)
				//  if all invites cleared, remove row entirely
				unset($nv[$un]);
			}
		if ($i < 1) 
			echo 'no matching endorse invite to reject found';
		else {
			if (lists::put($file_i, $nv, '# endorsement invitations'))
				echo 'recommend/endorse invite successfully rejected';
			else
				echo 'problem updating endorse invite';
			}
		}

	if ($cmd & static::FORGET) {
		//  check both who pract has endorsed list, and endorsees of pract list, then restore petition
		$is_cmd = TRUE;
		$i = 0;
		foreach ($fd as $k => $v) {
			if ($k == $un) {
				foreach ($v as $k2 => $v2) {
					if ($v2 == $ue) {
						unset($fd[$k][$k2]);
						$i++;
						}
					}
				}
			}
		if ($i < 1) 
			echo 'no matching endorse to forget found';
		else {
			//  if all friends cleared, remove row entirely
			if (isset($fd[$un]) && count($fd[$un]) < 1)
				unset($fd[$un]);
			if (!(lists::put($file_d, $fd, '# list by who has endorsed a particular practioner'))) {
				echo 'problem forgetting endorsement';
				return;
				}
			}

		$i = 0;
		foreach ($fr as $k => $v) {
			if ($k == $ue) {
				foreach ($v as $k2 => $v2) {
					if ($v2 == $un) {
						unset($fr[$k][$k2]);
						$i++;
						}
					}
				}
			}
		if ($i < 1) 
			echo 'no matching endorse to forget found .';
		else {
			//  if all friends cleared, remove row entirely
			if (isset($fr[$ue]) && count($fr[$ue]) < 1)
				unset($fr[$ue]);
			if (!(lists::put($file_f, $fr, '# list by who a particular practioner has endorsed'))) {
				echo 'problem forgetting endorsement .';
				return;
				}
			}

		if (isset($nv[$un])) {
			$a = $nv[$un];
			if (in_array($ue, $a)) {
				echo ", pending endorsement petition already be made";
				return;
				}
			array_push($a, $ue);
			}
		else
			$a = array($ue);
		$nv[$un] = $a;
		if (lists::put($file_i, $nv, '# endorsement invitations'))
			echo ', endorsement petition successful';
		else
			echo ', problem sending endorsement petition';
		}

	if ($cmd & static::DROP) {
		//  check both who pract has endorsed list, and endorsees of pract list
		$is_cmd = TRUE;
		$i = 0;
		foreach ($fr as $k => $v) {
			if ($k == $un) {
				foreach ($v as $k2 => $v2) {
					if ($v2 == $ue) {
						unset($fr[$k][$k2]);
						$i++;
						}
					}
				}
			}
//		if (isset($fd[$ue]) && count($fd[$ue]) < 1)
//			unset($fd[$ue]);
		if ($i < 1) 
			echo 'no matching endorse to drop found';
		else {
			//  if all friends cleared, remove row entirely
			if (isset($fr[$un]) && count($fr[$un]) < 1)
				unset($fr[$un]);
			if (!(lists::put($file_f, $fr, '# list by who a particular practioner has endorsed'))) {
				echo 'problem with endorsement drop';
				return;
				}
			echo 'recommendation successfully dropped, ';
			}

		$i = 0;
		foreach ($fd as $k => $v) {
			if ($k == $ue) {
				foreach ($v as $k2 => $v2) {
					if ($v2 == $un) {
						unset($fd[$k][$k2]);
						$i++;
						}
					}
				}
			}
		if ($i < 1) 
			echo 'no matching endorse to drop found .';
		else {
			//  if all friends cleared, remove row entirely
			if (isset($fd[$ue]) && count($fd[$ue]) < 1)
				unset($fd[$ue]);
			if (!(lists::put($file_d, $fd, '# list by who a particular practioner has endorsed'))) {
				echo 'problem with endorsement drop';
				return;
				}
			echo 'recommenation successfully dropped';
			}
		}

	if ($cmd & static::INVITE) {
		$is_cmd = TRUE;
//		if ($un == $ue) {
//			echo "endorse self undefined .";
//			return;
//			}
		if (isset($nv[$ue])) {
			$a = $nv[$ue];
			if (in_array($un, $a)) {
				echo "endorse has already be made";
				return;
				}
			array_push($a, $un);
			}
		else
			$a = array($un);
		$nv[$ue] = $a;
		//  FUTURE - should this be wrapped by mt_lock()?
		if (lists::put($file_i, $nv, '# endorsement invitations')) {
			echo 'recommendation petition request submitted';
			}
		else
			echo 'problem sending endorsement request';
		}

	if (!$is_cmd)
		echo 'undefined endorsement update';
        }

    }

//const FRIENDS_... = 0;	//  timestamp

const FRIENDS_UPDATE_INVITE =    1;
const FRIENDS_UPDATE_SNUB =     2;
const FRIENDS_UPDATE_ACCEPT =  4;
const FRIENDS_UPDATE_FORGET = 8;

function friends_update($cmd, $un, $uf) {
	//  cmd     command/action
	//  un      username to process
	//  uf      username of friend to invite
	//          FUTURE shouldn't everythign use UID instead of username?
	//  return: N/A
	//          as there is no way to recover at this call level,
	//          outputing error message is appropriate
	//  output: error message string
	//
	//  Related
	//    - exchange::following_update()
	global $data_dir, $login_data;

	if (is_null($un)) {
		echo "invalid friends update";
		return;
		}
	if ($cmd & (FRIENDS_UPDATE_ACCEPT | FRIENDS_UPDATE_FORGET)) { 
		$fr = array();
		$file_f = $data_dir.'/friends';  //  FUTURE - filename in config
		if (!lists::get($file_f, $fr)) {
			echo "problem updating friends data";
			return;
			}
		}
	$nv = array();
	$file_i = $data_dir.'/invites';  //  FUTURE - filename in config
	if (!lists::get($file_i, $nv)) {
		echo "problem updating invite data";
		return;
		}
	if ($cmd & FRIENDS_UPDATE_ACCEPT) {
		$i = 0;
		if (isset($fr[$uf])) {
			$a = $fr[$uf];
			if (!in_array($un, $a)) {
				array_push($a, $un);
				$i++;
				}
			}
		else {
			$a = array($un);      $i++;
			}
		$fr[$uf] = $a;
		if (isset($fr[$un])) {
			$a = $fr[$un];
			if (!in_array($uf, $a)) {
				array_push($a, $uf);
				$i++;
				}
			}
		else {
			$a = array($uf);      $i++;
			}
		$fr[$un] = $a;
		if ($i < 1)
			echo "friend already accepted";
		else if (lists::put($file_f, $fr))
			echo 'friend accept successful';
		else {
			echo 'problem with friend accept';
			return;
			}
		goto snub;  //  OMG, a goto, but seriously, accept needs follup snub to hide original invite
		//  FUTURE: consider switch/case fall through instead of goto?
		}
	else if ($cmd & FRIENDS_UPDATE_SNUB) {
snub:		$i = 0;  //  OMG, a goto label, but clear and clean in this case.
		if (isset($nv[$un])) {
			foreach ($nv[$un] as $k => $v) {
				if ($v == $uf) {
					unset($nv[$un][$k]);
					$i++;
					}
				}
			if (count($nv[$un]) < 1)
				//  if all invites cleared, remove row entirely
				unset($nv[$un]);
			}
		if ($i < 1) 
			echo 'no matching invite to reject found';
		else {
			if (lists::put($file_i, $nv))
				echo 'friends invite successfully rejected';
			else
				echo 'problem updating friend invite';
			}
		}
	else if ($cmd & FRIENDS_UPDATE_FORGET) {
		$i = 0;
		foreach ($fr as $k => $v) {
			if ($k == $uf) {
				foreach ($v as $k2 => $v2) {
					if ($v2 == $un) {
						unset($fr[$k][$k2]);
						$i++;
						}
					}
				}
			if ($k == $un) {
				foreach ($v as $k2 => $v2) {
					if ($v2 == $uf) {
						unset($fr[$k][$k2]);
						$i++;
						}
					}
				}
			}
		//  if all friends cleared, remove row entirely
		if (isset($fr[$un]) && count($fr[$un]) < 1)
			unset($fr[$un]);
		if (isset($fr[$uf]) && count($fr[$uf]) < 1)
			unset($fr[$uf]);
		if ($i < 1) 
			echo 'no matching friend to forget found';
		else {
			if (lists::put($file_f, $fr)) {
				echo 'friend successfully forgotten';
				if (isset($nv[$un])) {
					$a = $nv[$un];
					if (in_array($uf, $a)) {
						echo ", invite has already be made";
						return;
						}
					array_push($a, $uf);
					}
				else
					$a = array($uf);
				$nv[$un] = $a;
				if (lists::put($file_i, $nv)) {
					echo ', friends invitation successful';
					}
				else
					echo ', problem sending friend invite';
				}
			else
				echo 'problem with friend forget';
			}
		}
	else if ($cmd & FRIENDS_UPDATE_INVITE) {
		if ($un == $uf) {
			echo "invite self undefined";
			return;
			}
		if (isset($nv[$uf])) {
			$a = $nv[$uf];
			if (in_array($un, $a)) {
				echo "invite has already be made";
				return;
				}
			array_push($a, $un);
			}
		else
			$a = array($un);
		$nv[$uf] = $a;
		if (lists::put($file_i, $nv)) {
			echo 'friends invitation successful';
			}
		else
			echo 'problem sending friend invite';
		}
	else {
		echo 'undefined friends update';
		}
	}

function friends_list($mode, $u, $flgs = 0) {
	//  Generic listing of members of a 'friends' list.  Friends behavior is mutually agreed,
	//  so for every friend list there should be a corresponding invite list.  Typically
	//  there is more than one list for different kinds of friend relationships.
	//    mode  friends
	//          invite
	//          recommend
	//          recmended
	//          recminvt
	//    u     obtain friends list for this username
	//    flgs  additional USERLIST_... flags
	//  return: 0       successfully read/processsed friends data
	//                  output formatted friends list
	//          string  error message, problem processing friends data
	//                  no output is rendered
	//  FUTURE: add n to list, o offset into list, s search term, f sort flags
	global $data_dir, $login_data;

	if (0) {
		//  db/sql - say tuned
		}
	else {
		$fr = array();
		$file_profiles = $data_dir.'/'.$mode;
		if ($mode == 'friends')
			$f = USERLIST_CURF;
		else if ($mode == 'invites')
			$f = USERLIST_REQF;
		else if ($mode == 'recminvt')
			$f = USERLIST_REQF | USERLIST_RECM | $flgs;
		else if ($mode == 'recommend')
			$f = USERLIST_CURF | USERLIST_RECM | $flgs;
		else if ($mode == 'recmended')
			$f = USERLIST_CURF | USERLIST_RECM | $flgs;
		else
			$f = USERLIST_ALL;
		if (lists::get($file_profiles, $fr)) {
			//  friends lists for all user now in $fr
			//  FUTURE - this may use a lot of memory for long friends lists,
			//           instead read line one at a time and break on match?
			foreach ($fr as $k => $v) {
				if ($k == $u) {
				    user_list($f, $v);
				    //  okay, we can stop now as there should only be one friends record/username
				    break;
				    }
				}
			return (0);
			}	
		else
			return ("no user profiles file");
		}  
	}

//function friends_put($file, $fr) {
	//  prepare array of all friends lists
	//  $file    file to open - can be friends or invites
	//  $fr      array, upon return contains list of friends
	//  if error, ...
//	echo '<br>friends_put:';
//	ap($fr);
//	$result = false;
//	file_log($file);  //  why can't this be done after fopen?
//	if ($fh = fopen($file, 'w')) {

//		fwrite($fh, "#\n");  //  FUTURE, check if returns false, try/catch?
//		foreach ($fr as $k => $v) {
//			$str = $k.':';
//			$d = ' ';
//			foreach ($v as $k2 => $v2) {
//				$str .= $d.$v2;
//				$d = ', ';
//				}
//			$str .= "\n";
//			fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
//			}

//		$result = true;
//		if ($fh) fclose($fh);
//		}
//	return $result;
//	}

//function friends_get($file, &$fr) {  //  FUTURE - aren't arrays already passed by reference?
	//  prepare array of all friends lists
	//  $file    file to open - can be friends or invites
	//  $fr      array, upon return contains list of friends
	//  if error, ...
//	$result = false;
//	if ($fh = fopen($file, 'r')) {
//		while (($data = fgets($fh, 1000)) !== FALSE) {
//			if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
				//  each line has username prefix demarked by :
//				$pos  = strpos($data, ':');
//				$u    = substr($data, 0 , $pos);
				//  after :, comma seperated list of other users
//				$list = explode(',', substr($data, $pos + 1));
				//  trim off leading/trailing whitespace
//				foreach ($list as $k => $v)
//					$list[$k] = trim($list[$k]);
//				$fr[$u] = $list;
//				}
//			}
//		$result = true;
//		fclose($fh);
//		}
//	return $result;
//	}

class products {

	static function legit($a, &$is = NULL) {
		//  check array list of product, return true if all are legitimate
		//    a    array list of pid's to check
		//    is   shadow array list, is_stackable state (optional)
		global $products;

		foreach ($a as $k => $v) {
			$ps2 = false;
			foreach ($products as $k2 => $v2) {
				if (!is_null($is))  /*  build/return is_stackable array  */
					if (!isset($is[$k2])) $is[$k2] = $k2[2];
				if ($k2 == $v) {
					$ps2 = true;
					break;
					}
				}
			if (!$ps2)
				return (false);
			}
		return (true);
		}

	static public function prod_add_ent($f, $a, $uid, $unm, $note = '[no note]') {
		//  attempt to entitle a user with a product
		//    f
		//    a       array list of 1 or more products to add
		//    uid
		//    unm
		//    note
		//  return    0 successfull
		//            'error message'
		global $data_dir, $products;

		//  validate entitlement/product matches row from catalog
		$a_is = array();  //  prepare shadow array list products is stackable = t/f
		if (!static::legit($a, $a_is))
			return 'invalid product id';
		//  get lock file
		//  open original file
		$f_old = $data_dir.'/products/active';
		//  open new file
		$f_new = $data_dir.'/products/active_new';
		//  write new record to new file
		//  read row from origina, determine
		//    if appropriate write row to new file
		//    or if made obsolete y new row skip
		//  close new file
		//  close original file
                //  log original
		//  mv new -> original
		//  drop lock file
		//  append product transaction log
		$result = false;
		if ($fo = fopen($f_old, 'r')) {
			echo "\n<br>read okay";
			}
		else
			return 'could not open active products catalog';
		if ($fn = fopen($f_new, 'w')) {
			echo "\n<br>write okay";
			fwrite($fn, "# date-time, product code, user id, user account\n");  //  FUTURE, check if returns false, try/catch?
			$dt = date('Y-m-d H:i:s');
			$al = '';
			foreach ($a as $k => $v) {
				$str = $dt.', '.$v.', '.$uid.', '.$unm."\n";
			//	$al .= 'echo '.$dt.', '.$v.', '.$uid.', '.$unm.', add, \"signup automatic\" >> '.$data_dir.'/products/transactions';
				$al .= 'echo '.$dt.', '.$v.', '.$uid.', '.$unm.', add, \"'.$note.'\" >> '.$data_dir.'/products/transactions';
				fwrite($fn, $str);  //  FUTURE, check if returns false, try/catch?
				}
			$overstack = false;
			while (($data = fgets($fo, 1000)) !== FALSE) {
				if ($data[0] === '#')  //  skip if comment, #
					continue;
				$list = explode(',', $data);
				$t = trim($list[1]);
				//  if past product has same user and pid, and is not stackable - abort!!!
				if ($uid == $list[2] && isset($products[$t][1])) {
					if (!$products[$t][1]) {
						echo ' not stackable';
						foreach ($a as $k2 => $v2) {
							if ($v2 == $t) {
								$overstack = true;
								break;
								}
							}
						}
					}
				if ($overstack)
					break;
				$str = sprintf("%s, %s, %s, %s\n", $list[0], trim($list[1]), trim($list[2]), trim($list[3]));
				fwrite($fn, $str);  //  FUTURE, check if returns false, try/catch?
				}
			fclose($fn);
echo "<pre>".$al."</pre>";

			if (!$overstack) {
				$out = shell_exec($al);
				rename($f_new, $f_old);
				}
			}
		fclose($fo);
		return (($overstack) ? 
		  'product over stack attempt detected, aborting' :
		  0);
		}

	static public function prod_chk_ent($f, $a, $uid) {
		/*  Check if a given user has certain entitlements/products  */
		/*    f     active product/entitlements file
		/*    a     array of entitlements to check for
		/*    uid   user to lookup
		*/
		$pl = array();
		lists::get($f, $pl, 'product');
		$result = NULL;
		foreach ($a as $v) {
			if (isset($pl[$v][$uid]))
				$result = (($result) ? $result.', '.$v : $v);
			}
		return ($result);
		} 

	}  /*  class products [end]  */

const VIBE_MODE_EXCH = 1;
const VIBE_MODE_ARTC = 2;
const VIBE_MODE_WALL = 3;
const VIBE_MODE_PIVT = 4;
const VIBE_MODE_PMSG = 5;

class vvv {
	static public function show($usr, $m) {
		//  ...
		//    $usr  ...
		//    $m    EXCH, ARTC, ... (see const above)
//	echo "\n<span style=\"font-size: smaller;\"><br>".$usr."</span>";
		if ($m == VIBE_MODE_ARTC) {
			$sh = '';  //  FUTURE - for now
			static::vocal($usr, 'artcrt', $cm);
			//  only show views count for articles
			$vimg = 'vvv_36.png';
			static::view($usr, 'get', $vct);
			}
		elseif ($m == VIBE_MODE_PIVT) {
			$sh = '_p';
			static::vocal($usr, 'pivtrt', $cm);
			$vimg = 'vv_36.png';
			$vct = '';
			}
		elseif ($m == VIBE_MODE_WALL) {
			$sh = '_e';
			static::vocal($usr, 'wallrt', $cm);
			$vimg = 'vv_36.png';
			$vct = '';
			}
		else {
			$sh = '';  //  FUTURE - for now
			static::vocal($usr, 'exchrt', $cm);
			$vimg = 'vv_36.png';
			$vct = '';
			}
//		echo "\n<br>m: ".$m;
		if (session_userid_active()) {
			static::vibe($usr, 'chk', $ct, $m);  //  ct passed reference
			if ($ct > 0) {
				$t = 1;
				$b = 'unvibe';
				}
			else {
				$t = 0;
				$b = 'vibe';
				if ($ct) $ct = -$ct; 
				}
			echo "\n<span id=\"".$usr.$sh."_vu\">".$vct.'</span>';
			echo "\n<span id=\"".$usr.$sh."_vb\" onclick=\"vibe('".$usr."', ".$m.");\">".$b.'</span>';
			echo "\n<span id=\"".$usr.$sh."_vbct\">".$ct."</span>";
			echo "\n(<span id=\"".$usr.$sh."_vbcm\">".$cm."</span>)";
			echo "\n<span style=\"display: none;\" id=\"".$usr.$sh."_vbbt\">".$t."</span>";
			echo "\n<img style=\"height: 24px; margin-right: 1em;\" src=/gfx-stock/".$vimg." onclick=\"vibe('".$usr."', ".$m.");\">";
			//hacking in sharing functionality for articles
			if ($m == VIBE_MODE_ARTC) {
				static::share_email("art=".$usr);
				}
			}
		else {
			static::vibe($usr, 'get', $ct, $m);
			echo "\n<span id=\"".$usr.$sh."_vu\">".$vct.'</span>';
			echo "\n<span id=\"".$usr.$sh."_vb\" style=\"color: bfbfbf;\">vibe</span>";
			echo "\n<span id=\"".$usr.$sh."_vbct\">".$ct."</span>";
			echo "\n<span id=\"".$usr.$sh."_vbcm\">(".$cm.")</span>";
			echo "\n<span style=\"display: none;\" id=\"".$usr.$sh."_vbbt\"></span>";
			echo "\n<img style=\"height: 24px; margin-right: 1em;\" src=/gfx-stock/".$vimg.">";
                        if ($m == VIBE_MODE_ARTC) {
				static::share_email("art=".$usr);
				}
			}
		}

	/* share_email
	 * Inserts the share email object to trigger share email modal on click
	 * js is in base.js and css is in base.css
         * Added: Josh N 8/2/2016 
	 */
	static function share_email($link){
            //heredoc code below. DO NOT INDENT.
            /*need to find a better place to put this.
             *want to make it aware of the element you are working with so we
             * don't affect other popups hiding on the same page.
             * TODO: add the page url as an input field so you can share it
             */
echo <<<EOD
<div id="shareEmailPopup">
    <span class="close" onclick="hide_share_popup()">x</span>
    <div id="shareEmailFields">
        <form method="POST" action="rest/share.php" id="shareEmailForm" onsubmit="share_email_submit(event)">
            <input name="toEmail" placeholder="Recipient's Email" type="text">
            <input name="fromEmail" placeholder="Your Email" type="text">
            <input name="type" value="shareEmail" type="hidden">
            <input name="link" value=$link type="hidden">
            <input type="submit" id="shareEmailSubmit" value="send">
        </form>
        <div id="shareEmailError"></div>
    </div>
 </div>
EOD;
                //add actual email button.
                echo "\<img style=\"height: 24px; margin-right: 1em;\" src=/gfx-stock/email-icon-small.png onclick=\"show_share_popup()\">";
                   
		}

	static public function fpath($m) {
		global $artc_dir, $exch_dir, $wall_dir, $pivt_dir;;

		if ($m == VIBE_MODE_EXCH)
			return ($exch_dir.'/vvv/');
		elseif ($m == VIBE_MODE_WALL)
			return ($wall_dir.'/vvv/');
		elseif ($m == VIBE_MODE_PIVT)
			return ($pivt_dir.'/vvv/');
		elseif ($m == VIBE_MODE_ARTC)
			return ($artc_dir.'/vvv/');
		return ('path_not_found');
		}

	static public function efix($m) {
		//  Many DOM/javascript elements may have the same id if
		//  there is no way to seperate different kinds of message lists.
		//    return  ''   EXCH, ARTC
		//            '_e' WALL
		//            '_p' PIVT
		if ($m == VIBE_MODE_WALL)
			return ('_e');
		elseif ($m == VIBE_MODE_PIVT)
			return ('_p');
		return ('');
		}

	static public function vibe($aeid, $act, &$ct, $mode) {
		/*  implied that vibe actions are against current logged in user
		/*   aeid   artexch asset id
		/*   act    action to perform (get, chk, vibe, unvibe)
		/*   ct     > 0 aggregate count (returned by reference)
		/*          < 0 aggregate count, no like already from current user (returned by reference)
		/*  return  0        successful
		/*          message, error condition  */
		/*  FUTURE - add mysql / s3 / ... support  */
		global $uid;

		$return = 'undefined';
		$fn_ct = static::fpath($mode).$aeid.'_ct';
//		echo "\n<span style=\"font-size: smaller;\"><br>fn_ct: ".$fn_ct.'</span>';
		switch ($act) {
		  case 'chk':  /*  check if there is already a like from current user  */
			$return = 'vibe check error/warning';
			$fn_vir = static::fpath($mode).$aeid.'_vi';
//		echo "\n<span style=\"font-size: smaller;\"><br>fn_ct: ".$fn_vir.'</span>';
			$ct_t = 0;  $f = false;
			$u_id = session_userid_active();
			if ($u_id && ($f_vir = fopen($fn_vir, 'r')) !== FALSE) {
				while (($da = fgetcsv($f_vir, 256)) !== FALSE) {
					$ct_t++;
					//  if user record also found later in file, skip over it
					if ($u_id == $da[0])
						$f = true;
					//  FUTURE - break here, get ct from other file?
					}
				fclose($f_vir);
				$return = 0;
				//  you vibed, positive ct, you haven't vibed negative ct
				}
			$ct = ($f) ? $ct_t : -$ct_t;
//		echo "\n<span style=\"font-size: smaller;\"><br>ct: ".$ct.'</span>';
			//  if no vi file, then assume no likes (faux error condition)
			break;

		  case 'get':  /*  just get count, typically when no one is logged in  */
			//  attempt to open ct file
			$ct = 0;
			if ($f_ct = fopen($fn_ct, 'r')) {
				if (($data = fgets($f_ct, 1000)) !== FALSE) {
					$ct = trim($data);
					$return = 0;
					}
				else
					$return = 'could not read count value';
				fclose($f_ct);
				}
			else {
				$return = 'could not open asset vibe count file';
				}
			break;

		  case 'init':  /*  force init with one record of current user  */
			echo "\n<br>init";
			$return = 'init error/warning';
			$fn_vi = static::fpath($mode).$aeid.'_vi';
			$u_id = session_userid_active();
			$u_nm = session_username_active();
			if ($u_id && $f_vi = fopen($fn_vi, 'w')) {
				$str = $u_id.", ".$u_nm."\n";
				fwrite($f_vi, $str);  //  FUTURE - try/catch?
				$ct = 1;
				if ($f_ct = fopen($fn_ct, 'w')) {
					$str = $ct."\n";
					fwrite($f_ct, $str);  //  FUTURE - try/catch?
						$return = 0;
					fclose($f_ct);
					}
				fclose($f_vi);
				}
			break;

		  case 'pluck':  //  drop user record, avoid duplicates
			$pluck = true;
			echo "\n<br>pluck";
			$return = 'pluck error/warning';
			//  fall through

		  case 'insert':  //  insert user record at beginning of file, avoid duplicates
			if (!isset($pluck)) {
			//	echo "\n<br>insert 2";
				$return = 'insert error/warning';
				}
			$fn_vir = static::fpath($mode).$aeid.'_vi';
			$fn_viw = static::fpath($mode).$aeid.'_vi_w';
			$u_id = session_userid_active();
			$u_nm = session_username_active();
			if ($u_id && $f_vir = fopen($fn_vir, 'r')) {
				if ($f_viw = fopen($fn_viw, 'w')) {
					if (isset($pluck))
						$ct = 0;
					else {
						$s = $u_id.", ".$u_nm."\n";
						fwrite($f_viw, $s);  //  FUTURE - try/catch?
						$ct = 1;
						}
					while (($da = fgetcsv($f_vir, 256)) !== FALSE) {
						//  if user record also found later in file, skip over it
						if ($u_id != $da[0]) {
							$s = $da[0].', '.trim($da[1])."\n";
							fwrite($f_viw, $s);  //  FUTURE - try/catch?
							$ct++;
							}
						}
					fclose($f_viw);
					}
				fclose($f_vir);
				if ($f_ct = fopen($fn_ct, 'w')) {
					$s = $ct."\n";
					fwrite($f_ct, $s);  //  FUTURE - try/catch?
					fclose($f_ct);
					rename($fn_viw, $fn_vir);  //  FUTURE - try/catch?
					$return = 0;
					}
				}
			break;

		  case 'free':  /*  clear all vibes  */
			echo "\n<br>free";
			$return = 'free error/warning';
			$fn_vi = static::fpath($mode).$aeid.'_vi';
			$fn_ct = static::fpath($mode).$aeid.'_ct';
			$u_id = session_userid_active();
			if ($u_id && unlink($fn_ct) && unlink($fn_vi)) 
				$return = 0;
			break;

		  case 'vibe':  //  add vibe
			$t = static::vibe($aeid, 'get', $ct_g, $mode);
			echo "\n<br>ct : ".($ct ? $t : $ct_g);
			if ($ct_g == 0) {
				//  if no ct file, then quick write ct and vi files
				echo "\n<br>init / may be okay - ".$t;
				$return = static::vibe($aeid, 'init', $ct_g, $mode);
				}
			else {
				//  insert/overwrite ct file, insert/overwrite vi file
				$return = static::vibe($aeid, 'insert', $ct_g, $mode);
				}
			break;

		  case 'unvb':  //  remove vibe
			$t = static::vibe($aeid, 'get', $ct_g, $mode);
			echo "\n<br>ct : ".($ct ? $t : $ct_g);
			if ($ct_g > 1) {
				//  pluck record, decrement ct
				$return = static::vibe($aeid, 'pluck', $ct_g, $mode);
				}
			else {
				if ($ct_g == 1) {
					//  delete both ct and vi
					$return = static::vibe($aeid, 'free', $ct_g, $mode);
					}
				else
					$return = 0;
				}
			break;

		  default:
			}
		return ($return);
		}
	
	static public function vocal($aeid, $act, &$ct) {
		/*   aeid   artexch asset id
		/*   act    action to perform (get, chk, vibe, unvibe)
		/*   ct     > 0 aggregate count (returned by reference)
		/*          < 0 aggregate count, no like already from current user (returned by reference)
		/*  return  0        successful
		/*          message, error condition  */
		$return = 'undefined';
		if     ($act == 'exchrt' || $act == 'exchwt')
			$fn_ct = static::fpath(VIBE_MODE_EXCH).$aeid.'_voct';
		elseif ($act == 'artcrt' || $act == 'artcwt')
			$fn_ct = static::fpath(VIBE_MODE_ARTC).$aeid.'_voct';
		elseif ($act == 'wallrt' || $act == 'wallwt')
			$fn_ct = static::fpath(VIBE_MODE_WALL).$aeid.'_voct';
		elseif ($act == 'pivtrt' || $act == 'pivtwt')
			$fn_ct = static::fpath(VIBE_MODE_PIVT).$aeid.'_voct';
		switch ($act) {
		  case 'pivtrt':
		  case 'wallrt':
		  case 'exchrt':
		  case 'artcrt':  /*  get vocal count  */
			//  attempt to open ct file
			$ct = 0;
			if ($f_ct = fopen($fn_ct, 'r')) {
				if (($data = fgets($f_ct, 1000)) !== FALSE) {
					$ct = trim($data);
					$return = 0;
					}
				else
					$return = 'could not read count value';
				fclose($f_ct);
				}
			else
				$return = 'could not open asset vocal count file';
			break;

		  case 'pivtwt':
		  case 'wallwt':
		  case 'exchwt':
		  case 'artcwt':  /*  write vocal count  */
			if ($f_ct = fopen($fn_ct, 'w')) {
				$str = $ct."\n";
				fwrite($f_ct, $str);  //  FUTURE - try/catch?
					$return = 0;
				fclose($f_ct);
				}
			break;
			}

		return ($return);
		}

	static public function view($aeid, $act, &$ct) {
		/*  implied that view actions are against current logged in user
		/*   aeid   artexch asset id
		/*   act    action to perform (get, chk, vibe, unvibe)
		/*   ct     > 0 aggregate count (returned by reference)
		/*          < 0 aggregate count, no like already from current user (returned by reference)
		/*  return  0        successful
		/*          message, error condition  */
		/*  FUTURE - add mysql / s3 / ... support  */
		global $uid;

		$return = 'undefined';
		$fn_cu = vvv::fpath(VIBE_MODE_ARTC).$aeid.'_cu';
	//	echo ' ['.$aeid.', '.$act.', '.$ct.'] ';
		switch ($act) {
		  case 'get':  /*  just get count, typically when no one is logged in  */
			//  attempt to open ct file
			$ct = 0;
			if ($f_ct = fopen($fn_cu, 'r')) {
				if (($data = fgets($f_ct, 1000)) !== FALSE) {
					$ct = trim($data);
					$return = 0;
					}
				else
					$return = 'could not read count value';
				fclose($f_ct);
				}
			else
				$return = 'could not open asset view count file';
			break;

		  case 'init':  /*  force init with one record of current user  */
		//	echo "\n<br>init";
			$return = 'init error/warning';
			$fn_vu = vvv::fpath(VIBE_MODE_ARTC).$aeid.'_vu';
			$u_id = session_userid_active();
			$u_nm = session_username_active();
			if ($u_id && $f_vu = fopen($fn_vu, 'w')) {
				$str = $u_id.", ".$u_nm."\n";
				fwrite($f_vu, $str);  //  FUTURE - try/catch?
				$ct = 1;
				if ($f_cu = fopen($fn_cu, 'w')) {
					$str = $ct."\n";
					fwrite($f_cu, $str);  //  FUTURE - try/catch?
						$return = 0;
					fclose($f_cu);
					}
				fclose($f_vu);
				}
			break;

		  case 'insert':  //  insert user record at beginning of file, avoid duplicates
		//	echo "\n<br>insert";
			$return = 'insert error/warning';
			$fn_vir = vvv::fpath(VIBE_MODE_ARTC).$aeid.'_vu';
			$fn_viw = vvv::fpath(VIBE_MODE_ARTC).$aeid.'_vu_w';
			$u_id = session_userid_active();
			$u_nm = session_username_active();
			if ($u_id && $f_vir = fopen($fn_vir, 'r')) {
				if ($f_viw = fopen($fn_viw, 'w')) {
					if (isset($pluck))
						$ct = 0;
					else {
						$s = $u_id.", ".$u_nm."\n";
						fwrite($f_viw, $s);  //  FUTURE - try/catch?
						$ct = 1;
						}
					while (($da = fgetcsv($f_vir, 256)) !== FALSE) {
						//  if user record also found later in file, skip over it
						if ($u_id != $da[0]) {
							$s = $da[0].', '.trim($da[1])."\n";
							fwrite($f_viw, $s);  //  FUTURE - try/catch?
							$ct++;
							}
						}
					fclose($f_viw);
					}
				fclose($f_vir);
				if ($f_ct = fopen($fn_cu, 'w')) {
					$s = $ct."\n";
					fwrite($f_ct, $s);  //  FUTURE - try/catch?
					fclose($f_ct);
					rename($fn_viw, $fn_vir);  //  FUTURE - try/catch?
					$return = 0;
					}
				}
			break;

		  case 'view':  //  increment view count
			$t = static::view($aeid, 'get', $ct_g);
		//	echo "\n<br>ct : ".($ct ? $t : $ct_g);
			if ($ct_g == 0) {
				//  if no ct file, then quick write ct and vi files
				echo "\n<br>init / may be okay - ".$t;
				$return = static::view($aeid, 'init', $ct_g);
				}
			else {
				//  insert/overwrite ct file, insert/overwrite vi file
				$return = static::view($aeid, 'insert', $ct_g);
				}
			$ct = $ct_g;
			break;

		  default:
			}
		return ($return);
		}

	static public function readmore_seecmnts($uid, $m = VIBE_MODE_EXCH) {
		$sh = static::efix($m);

		//  for exch and artc summary lists, provide the read more | see comments action links
		$s = 'position: absolute; bottom: 0; right: 0; background-color: white; font-size: smaller;';
		echo "\n<div style=\"".$s."\">";

		echo "\n<div id=\"".$uid.$sh."_mor-\">";
		echo "&nbsp;<a onclick=\"more('".$uid.$sh."');\">see comments</a> | ";
		echo "<a onclick=\"more('".$uid.$sh."');\">read more</a>&nbsp;</div>";

		echo "\n<div id=\"".$uid.$sh."_mor+\" style=\"display: none;\">";

//		echo "&nbsp;<a onclick=\"vocal('".$uid."', 0);\">reload</a>";
//		echo " | <a onclick=\"more('".$uid."');\">read less</a>&nbsp;</div>";
		echo "&nbsp;<a onclick=\"more('".$uid.$sh."');\">read less</a>&nbsp;</div>";
		echo '</div>';
		}

	}  /*  class vvv [end]  */
?>
