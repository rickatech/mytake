<?PHP

function ap($a) {
	echo "<pre style=\"font-size: smaller;\">";
	print_r($a);
	echo "</pre>";
	}

const CONTENT_ORD = 0;  //  ...
const CONTENT_UID = 1;
const CONTENT_BYL = 2;  //  title | date, author
const CONTENT_TAG = 3;
const CONTENT_IMG = 4;
const CONTENT_URL = 5;
const CONTENT_HTG = 5;  //  hash tags

const FOPEN_X_RETRIES = 3;

const ACAT_NEW = 1;
const ACAT_UPDATE = 2;

class acat {
	//  collection of tools for updating article catalog

	static public $acat = NULL;

	static public function get($file, $tag = NULL, $art = NULL) {
		//  sets public $acat property to result of catalog fetch
		acat::$acat = get_map($file, $tag, $art);
		//  FUTURE - refactor to pull in all records?  ...
		//  then perform filter on in memory array (could be a memory pig)
		}

	static public function article($art = NULL) {
		//  return  array of article attibutes, success
		//          NULL if not found
		global $dflags;
		global $edit_url;
		global $acat_data;
	
		if (!is_null($art) && $da = get_map($acat_data, NULL, $art)) {
			$da_cap = explode('|', $da[0][2]);
			$da_tag = explode('|', $da[0][3]);
			return array(
			  "article" => $art,
			  "caption" => $da_cap,
			  "tags" =>    $da_tag);
			}
		return NULL;
		}

	static public function update($file, $cmd, $a) {
		//  complete rewrite catalog with updated, added, dropped records
		//  file    catalog filename, including full path  
		//  cmd     NEW|UPDATE 
		//  a       new/updated article record array
		//  return  true, successful
		//          false, failed/incomplete
		//
		//  create file exclusive lock on new catalog file, walk the old catalog
		//  drop, update, add, keep rows as appropriate into the new file.
		//  once new file is complete, close original,
		//  Goal - oldest records (according to byline) should be at end of file
		//  FUTURE - fseek may be able to help optimize this?
		//  FUTURE - when database backend, this likely will less involved
		//  check that lock is not present, open with lock new empty catalog file,
		//  fopen($path, 'x', ...), errors if file exists alreadyi, which is fine, retry after delay
		//  if blocked try three more attempts after micro random wait before declaring access error		
		//  add -    place new/fresh record at beginning of file,
		//           spin through origin catalog copying rows over
		//  update - spin through origin catalog copying rows over,
		//           update target record if it comes up (if not report no match, bail)
		//  drop -   spin through origin catalog copying rows over,
		//           omit target record if it comes up (if not report no match, bail)
		//  close original, close new, rename original, rename new.
		//  CITATION - http://www.hackingwithphp.com/8/11/0/locking-files-with-flock
		//  http://stackoverflow.com/questions/13522273/will-flocked-file-be-unlocked-when-the-process-die-unexpectedly
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
			echo ' retries, still could not get artc write lock, sorry';
			return ($result);
			}

		/*  perform write processing here  */
		$o = 1;  //  FUTURE is ordinal needed?  ... should form preserve it?
		$str =  "# ord, id_readable, ...\n";
		fwrite($fh, $str);

		$row = 0;
		if ($cmd == ACAT_NEW) {
			$act_done = true;
				if (isset($a['ord']))
					$o = $a['ord'];
				//  FUTURE - following code in a small utility function?
				$str  = $o;
				$str .= ', "'.$a['aid'].'"';
				$str .= ', "'.$a['title'].'|'.$a['date'].', '.$a['author'].'"';
				$str .= ', "'.$a['pivot'].'"';
				$str .= ', "'.$a['image'].'"';
				$str .= "\n";
				fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
				$o++;
				$row++;
			}

		if ($fr = fopen($file, 'r')) {
			while (($data = fgetcsv($fr, 1000, ",")) !== FALSE) {
				//  skip past column titles row
				if ($data[CONTENT_ORD][0] != '#' && $data[CONTENT_ORD] != 'ID') {
					if ($cmd == ACAT_UPDATE && $data[CONTENT_UID] == $a['aid']) {
						$act_done = true;
						if (isset($a['ord']))
							$o = $a['ord'];
						//  FUTURE - following code in a small utility function?
						$str  = $o;
						$str .= ', "'.$a['aid'].'"';
						$str .= ', "'.$a['title'].'|'.$a['date'].', '.$a['author'].'"';
						$str .= ', "'.$a['pivot'].'"';
						$str .= ', "'.$a['image'].'"';
						$str .= "\n";
						fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
						$o++;
						}
					else {
						$i = 0;  $str = '';
						foreach ($data as $k => $v) {
							$str .= ($i < 1) ? $v : ', "'.$v.'"';
							$i++;
							}
						$str .= "\n";
						fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
						}
					}
				$row++;
				}
			if ($fr) fclose($fr);
			}
		else
			echo '<br>could not access acat';

		if ($fh) fclose($fh);
		if ($act_done)
			rename($file, $file.'_0');
//		if ($act_done && rename($file.'_lock', $file.'_done'))  /*  testing  */
		if ($act_done && rename($file.'_lock', $file))
			$result = true;
		else {
			unlink($file.'_lock');
			//  FUTURE / SYSTEM - make system log
			echo '<br>acat::update, could not complete action';
			//  DANGER, if this file remains all updates are  blocked!!!
			//  FUTURE - add routine check if lock file date is
			//           older than a few minutes, then force delete
			}
		return $result;
		}

	static public function write($file, $c) {
		$result = false;
		//  echo "write::".$file;
//		if ($fh = fopen($file, 'w')) {  //  if file does not exist it will be created
		if ($fh = fopen($file, 'a')) {  //  if file does not exist it will be created
		//  Need a more refined write that places most recent toward file beginning :-/
			//  FUTURE, check if returns false, try/catch?
			$str =  "# ord, id_readable, ...\n";
			fwrite($fh, $str);
			$o = 1;  //  FUTURE is ordinal needed?  ... should form preserve it?
			foreach ($c as $k => $v) {
				echo "\n<br>".$k.' / ';
				if (isset($v['ord']))
					$o = $v['ord'];
	//			ap($v);
				$str  = $o;
				$str .= ', "'.$v['aid'].'"';
				$str .= ', "'.$v['title'].'|'.$v['date'].', '.$v['author'].'"';
				$str .= ', "'.$v['pivot'].'"';
				$str .= ', "'.$v['image'].'"';
				$str .= "\n";
			//	$str =  '<span id="'.$k.'">'.$v."</span>\n";
				echo '<p>'.$str.'</p>';
				fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
				$o++;
				}
			$result = true;
			if ($fh) fclose($fh);
			}
		return $result;
		}

	}  //  acat [end]

//  FUTURE - following can be rolled into a static class (see above)

function get_map($filename, $tag = NULL, $art = NULL, $usr = NULL) {
        //  return  array containing article catalog
	//          NULL if article not found or is ondeck but author is not logged in user
	//  tag     tag to match, NULL match all tags
	//          ignore elements matching 'ondeck' unless specifcally passed
	//  art     article ID to match, NULL match all article ID's
	//          if both art and tag are passed, tag will be ignored
	//  usr     only show content authored by specific user
	//  flg     FUTURE: omit ondeck, filter by content type
	//  FUTURE: all multiple tags, article ID's to be passed in

        $row = 0;
        if ($fh = fopen($filename, 'r')) {
		$ua = is_null(session_userid_active()) ? false : true;
                while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
		    if ($data[CONTENT_ORD][0] != '#' && $data[CONTENT_ORD] != 'ID') {  //  skip past column titles row
			//  FUTURE - $tag == 'ALL', goto addorw directly
			$mu = false;
			$mux = false;
			if (!is_null($usr))
				$usrx = $usr;                        //  usrx = usr passed in
		//	else if ($tag == 'ondeck' && is_null($usr))
			else if ($tag == 'ondeck')
				$usrx = session_username_active();   //  usrx = active logged in user/NULL if tag passed in is ondeck
			else
				$usrx = NULL;
			$da_cap = explode('|', $data[CONTENT_BYL]);  //  FUTURE, seperate date, UID, and username with additional |
			if (!is_null($usrx)) {  //  see if content author matches logged in user
				if (isset($da_cap[1]) && strstr($da_cap[1], $usrx))
					$mu = true;  //  passed in user or ondeck active online user matches byline author
				}
			else if (session_username_active()) {  //  ...
				if (isset($da_cap[1]) && strstr($da_cap[1], session_username_active()))
					$mux = true;  //  active logged in user matches byline author
				}
			$od = false;
			$m = false;
			$ta = explode('|', $data[CONTENT_TAG]);
			foreach($ta as $t) {
				if ($t == 'ondeck')
					$od = true;
				if (!is_null($tag) && $t == $tag)
					$m = true;
				}
			if (!is_null($art)) {
				if ($data[CONTENT_UID] == $art)
				//	$m = true;
					$m = ($od ? $mux : true);  //  return false if ondeck tag and author is not logged in user
				}
			else if (is_null($tag))
				$m = true;
			if (!is_null($usr)) {  //  see if passed in or ondeck usr matches content author
				if ($m) $m = $mu;
				}
			//  only show ondeck items if ondeck tag passed in, and author matches logged in user
			if ($m && $tag != 'ondeck') {
//				$m = $od ? false : true;
				//  okay to show with ondeck if article passed in and logged in user is author
				$m = $od ? (!(is_null($art)) ? $mux : false) : true;
				}
			else if ($m)
				$m = $mu;
			if ($m) {
                                $new_map[$row] = $data;
				$row++;
				}
                        }
		    }
                fclose($fh);
                return ($new_map);
                }
        else {
                echo "<p>fopen read error </p> \n\n";  //  FUTURE, make a log file entry for this?
                return (NULL);
                }
        }

function catalog_latest($nlist = 4, $tag = NULL, $art = NULL, $usr = NULL, $lab = NULL) {
	global $catalog_data;

	$data = get_map($catalog_data, $tag, $art, $usr);
	//  $data = get_map($catalog_data, $tag, $art, session_username_active());
	if (is_null($data) || sizeof($data) < 1) {
		if ($tag != 'ondeck')  //  even if no list returned, need to show create button
			return;
		$nd = true;  //  no data returned
		}
	else
		$nd = false; //  data returned
	echo "\n<div class=list_body>\n";
	//  without overflow: hidden, spacing is weird
	echo "\n<div class=list_head>\n";
	if (!is_null($lab))
		echo "<B>".$lab."</B>\n";
	else if (is_null($tag)) {
		echo "<B>Latest</B>\n";
		}
	else {
		echo "<B><a href=?tag=".$tag.">";  //  FUTURE: omit link if superfluous
		if ($tag == 'music')  echo 'Tunes';
		else if ($tag == 'book')   echo 'Read';
		else if ($tag == 'film')   echo 'Filcks';
		else if ($tag == 'ondeck') echo 'On Deck';
		else echo $tag;
		echo "</a></B>\n";
		}
	echo "</div>";
	if (!$nd) {  /*  nd  */
	foreach($data as $da) {
		$da_tag = explode('|', $da[3]);
		$da_cap = explode('|', $da[2]);
		if (isset($da[1])) {
			/*  FUTURE: make this an ajax call  */
			if (strlen($da[5]) > 0) {
				$a0 = "<a class=exref\n  href=\"".$da[5]."\">";
				$a1 = "</a>";  }
			else {
				$a0 = "<b><a\n   href=\"?art=".$da[1]."\">";
				$a1 = "</a></b>";  }
			}
		else {
			$a0 ='';  $a1 = '';  }
		//  without overflow: hidden, spacing is weird
		echo "\n<div class=\"cat_rows\" style=\"clear: both; overflow: hidden;\">".$a0.
		  "<img src=\"gfx/".$da[4]."\" align=left style=\"width: 72px; margin: 0;\">".$a1;
		echo "<p style=\"margin: 0;\">".(isset($da_cap[0]) ? $a0.$da_cap[0].$a1 : '');
		echo "<span style=\"font-size: smaller;\">\n  <br>".(isset($da_cap[1]) ? $da_cap[1] : '')."</span></p>";
		$f = false; $str = '';
		foreach ($da_tag as $i) {
			if (!$f) $f = true;
			else $str .= ", ";
			$str .= $i;
			}
		if ($f) echo "\n  <p style=\"font-size: smaller; margin-bottom: 0;\">".$str."</p>";
		echo "</div>";
		}
	    }  /*  nd  */
	//  without overflow: hidden, spacing is weird, marry this + title above = 72px;
	echo "\n<div class=list_tail>".($tag == 'ondeck' ? '[create]' : '... ')."</div>\n";
	echo "\n</div>\n";
	}

function article($art = NULL) {
	//  return  array of article attibutes, success
	//          NULL if not found
	global $dflags;
	global $edit_url;
	global $catalog_data;

	if (!is_null($art) && $da = get_map($catalog_data, NULL, $art)) {
		$da_cap = explode('|', $da[0][2]);
		$da_tag = explode('|', $da[0][3]);
		return array(
		  "article" => $art,
		  "caption" => $da_cap,
		  "tags" =>    $da_tag);
		}
	return NULL;
	}

function article_out($artrec) {
	global $dflags;
	global $edit_url, $edit_urla, $data_dir;

	if (is_null($artrec))
		return;

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

	echo "<p style=\"margin-top: 0;\">".(isset($artrec['caption'][0]) ? '<b>'.$artrec['caption'][0].'</b>' : '');
	echo "<span style=\"color: grey;\"><br>".(isset($artrec['caption'][1]) ? $artrec['caption'][1] : '')."</span></p>";
	/*  using readfile() purposely insures PHP commands in file are ignored  */
	readfile($data_dir.'/'.$artrec['article']);
	}

function exchange_latest($nlist = 4, $tag = NULL, $art = NULL, $usr = NULL, $lab = NULL) {
	global $catalog_data, $dflags;

	$data = get_map($catalog_data, $tag, $art, $usr);
	//  $data = get_map($catalog_data, $tag, $art, session_username_active());
	if (is_null($data) || sizeof($data) < 1) {
		if ($tag != 'ondeck')  //  even if no list returned, need to show create button
			return;
		$nd = true;  //  no data returned
		}
	else
		$nd = false; //  data returned

	if (!$nd) {  /*  nd  */
	foreach($data as $da) {
		$da_tag = explode('|', $da[3]);
		$da_cap = explode('|', $da[2]);
		if (isset($da[1])) {
			/*  FUTURE: make this an ajax call  */
			if (strlen($da[5]) > 0) {
				$a0 = "<a class=exref\n  href=\"".$da[5]."\">";
				$a1 = "</a>";  }
			else {
				$a0 = "<b><a\n   href=\"?art=".$da[1]."\">";
				$a1 = "</a></b>";  }
			}
		else {
			$a0 ='';  $a1 = '';  }
		//  without overflow: hidden, spacing is weird
		echo "\n<div class=\"cat_rows\" style=\"background-color: #ffefef; clear: both; overflow: hidden;\">".$a0.
		  "<img src=\"gfx/".$da[4]."\" align=left style=\"width: 72px; margin: 0;\">".$a1;
		echo "<p style=\"margin: 0;\">".(isset($da_cap[0]) ? $a0.$da_cap[0].$a1 : '');
		echo "<span style=\"font-size: smaller;\">\n  <br>".(isset($da_cap[1]) ? $da_cap[1] : '')."</span></p>";
		$f = false; $str = '';
		foreach ($da_tag as $i) {
			if (!$f) $f = true;
			else $str .= ", ";
			$str .= $i;
			}
		if ($f) echo "\n  <p style=\"font-size: smaller; margin-bottom: 0;\">".$str."</p>";
		echo "</div>";
		}
	    }  /*  nd  */
	//  without overflow: hidden, spacing is weird, marry this + title above = 72px;
	echo "\n<div class=list_tail>".(session_userid_active() ? '[<a href=?artex>create</a>]' : '... ')."</div>\n";
//	echo "\n<div class=list_tail>".($tag == 'ondeck' ? '[create]' : '... ')."</div>\n";
//	echo "\n<div class=list_tail>[create]</div>\n";
	}

?>
