<?PHP

function ap($a) {
	echo "<pre style=\"font-size: smaller;\">";
	print_r($a);
	echo "</pre>";
	}

function file_log($file) {
	//  append log file with date stamp, followed by current active
	//  file contents (in case a revert is needed for some reason)
	$cmd = 'echo '.date('Y-m-d H:i:s').' >> '.$file.'_log';
	$out = shell_exec($cmd);
	$cmd = 'echo ------------------- >> '.$file.'_log';
	$out = shell_exec($cmd);
	$out = shell_exec("cat ".$file." >> ".$file.'_log');
	//  FUTURE - how could this fail?  ... add parameter to pass back $out
	}

function seq_next_free($cat, $un, $offset) {
	//  Scan generic catalog, find next free sequence number for username_seq id
	//  cat     file path to catalog to search
	//  un	    username to search for
	//  offset  field offset to hunt for username_seq
	//  return  n, next used sequence number
	//          1 (if no preexisting record found)
	//          false, unrecoverable error
	$result = false;
	if ($fh = fopen($cat, 'r')) {
		//  fgetcsv more actively preserves contents within double quotes :-)
		$r_max = 0;
		$r_min = 0;
		while (($data = fgetcsv($fh, 1000)) !== FALSE) {
			$un_s = sprintf('%s_%04d', $un, $r);
			//  for each match un_nnnn
			//    - determine the nnnn offset
			//      if min not set, make min v
			//      if max not set, make max v
			//      if min set, if v < min, min = v
			//      if max set, if v > max, man = v
			//      after scan if lowest > 1 then seq = low - 1, otherwise seq = high + 1
			//      FUTURE - there still could be gaps, but at least no collision
			if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
				$list = array_map('trim', $data);
				$e = explode("_", $data[$offset]);
				if ($un == $e[0]) {
					//  only search uid records that include current active username
					if ($e[1] > 0) {
						if ($r_max == 0) $r_max = $e[1];
						else if ($r_max < $e[1]) $r_max = $e[1];
						if ($r_min == 0) $r_min = $e[1];
						else if ($r_min > $e[1]) $r_min = $e[1];
						}
					}
				}
			}
		if ($r_min > 1)	
			$result = $r_min - 1;
		else $result = $r_max + 1;  //  if no records this becomes 1
		}
	return ($result);
	}

class mt_lock {
	//  lofi file based locking, suitable for use with shared nfs style storage
	//  assumes write lock order can be arbitrary
	//  FUTURE - consider making get and drop non-static, requiring object creation

	static public function get($file, &$fh) {
		//  get/open write lock - creates lock file
		//  file    base filename to lock
		//  fh      pointer to lock file handle (unset initially)
		//  return  true, fh has been set to lock file handle
		//          false, could not obtain lock,
		//          spit out error to console, FUTURE syslog?
		$tries = 0;
		while ($tries < FOPEN_X_RETRIES) {
			if ($fh = fopen($file.'_lock', 'x'))  //  fails if file already exists
				break;
			sleep(rand(0,3));  //  sleep up to three seconds
			$tries++;
			}
		if ($fh)
			$result = true;
		else {
			$result = false;
			echo '<br>AFTER '.FOPEN_X_RETRIES;
			echo ' retries, still could not get '.$file.' write lock, sorry';
			}
		return ($result);
		}

	static public function release(&$fh) {
		//  close/write lock - removes lock file,
		//  can also be used to purge ancient/corrupt write lock file?
		if ($fh) fclose($fh);
		}

	static public function check($file) {
		//  detect write lock - typically called preceding read action
		//  FUTURE - there is a tiny chance this check will return
		//  okay (no write lock), pause another process activtes,
		//  performs write lock, starts to wrap up with drop call,
		//  then that process stalls, original read process resumes,
		//  but the previous drop's 2nd rename hasn't occured yet
		//  so there is no file to open.  But there should be a file_0
		//  if this is not a new file
		//  if so sleep breifly, then try again until max retries 
		$success = false;
		return $success;
		}

	}
	
class lists {
	//  utility methods to write and retrieve array of multiple lists
	//
	//    # comment line
	//    list1: value1, value2, value3
	//    list2: value1, value2

	static public function put($file, $fr, $hc = NULL) {
		//  prepare array of all friends lists
		//  $file    file to open - can be friends or invites
		//  $fr      array, upon return contains list of friends
		//  $hc      (optional) # comment header, if supplied MUST begin with #!
		//  if error, ...
		$result = false;
		file_log($file);  //  why can't this be done after fopen?
		if ($fh = fopen($file, 'w')) {
			$str = (is_null($hc) ? '#' : $hc)."\n";
			fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
			foreach ($fr as $k => $v) {
				$str = $k.':';
				$d = ' ';
				foreach ($v as $k2 => $v2) {
					$str .= $d.$v2;
					$d = ', ';
					}
				$str .= "\n";
				fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
				}
			$result = true;
			if ($fh) fclose($fh);
			}
		return $result;
		}

	static public function get($file, &$fr, $m = NULL) {  //  FUTURE - aren't arrays already passed by reference?
		//  prepare array of all friends lists
		//  $file    file to open - can be friends or invites
		//  $fr      array, upon return contains list of friends
		//  $m       parse mode:
		//             NULL, default for friend lists processing
		//             'product', for active product processing
		//  if error, ...
		$result = false;
		if ($fh = fopen($file, 'r')) {
			while (($data = fgets($fh, 1000)) !== FALSE) {
				if (strpos($data[0], '#') === FALSE) {  //  skip if comment, #
					if (is_null($m)) {
						//  each line has username prefix demarked by :
						$pos  = strpos($data, ':');
						$u    = substr($data, 0 , $pos);
						//  after :, comma seperated list of other users
						$list = explode(',', substr($data, $pos + 1));
						//  trim off leading/trailing whitespace
						foreach ($list as $k => $v)
							$list[$k] = trim($list[$k]);
						$fr[$u] = $list;
						}
					else {
						//  active product catalog processing
						$list = explode(',', $data);
						unset($list[$i]);
						$fr[trim($list[1])][trim($list[2])] = array(trim($list[3]), $list[0]);
						}
					}
				}
			$result = true;
			fclose($fh);
			}
		return $result;
		}

	}  /*  lists class [end]  */

const CONTENT_ORD = 0;  //  ...
const CONTENT_UID = 1;
const CONTENT_BYL = 2;  //  title | date, author
const CONTENT_BYL_T = 0;      //  title
const CONTENT_BYL_DA = 1;     //  date, author
const CONTENT_BYL_DA_DT = 0;  //  date
const CONTENT_BYL_DA_AU = 1;  //  author
const CONTENT_TAG = 3;
const CONTENT_IMG = 4;
const CONTENT_URL = 5;
const CONTENT_HTG = 5;  //  hash tags

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
			echo ' retries, still could not get acat write lock, sorry';
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

const ECAT_ORD =    0;
const ECAT_UID =    1;
const ECAT_TITLE =  2;
const ECAT_DATE=    3;
const ECAT_AUTHOR = 4;
const ECAT_IMG=     5;
const ECAT_ARTID =  6;  //  related article
const ECAT_HTG =    7;  //  hash tags
const ECAT_RES1 =   8;  //  pivot tags?
const ECAT_RES2 =   9;  //  external URL?

const ECAT_NEW = 1;
const ECAT_UPDATE = 2;

class ecat {
	//  collection of tools for updating exchange catalog

	static public $ecat = NULL;

	static public function seq_next($dir, $uid) {
		//  FUTURE - make this generic, for any class of content file
		//  dir     directory path for sequence file and content file
		//          FUTURE - fopen(x) to open content file, if it exists then do 
		//                   catalog search to find first open sequence number that is free
		//  uid     author or prefix, content filename example: [uid]_0001
		//  return  sequence number to use > 0
		//          false, could not confirm sequence number to use
		//  CITATION http://stackoverflow.com/questions/22409780/flock-vs-lockf-on-linux
		$result = false;
		$file_seq = $dir.'/'.$uid.'_seq';
		if ($fs = fopen($file_seq, 'r')) {
			$seq_d = fgetcsv($fs, 1000, ",");
//			ap($seq_d);
			fclose($fs);
			}
		$seq = isset($seq_d[0]) ? $seq_d[0] : 1;
//		echo '<br>file_seq: '.$file_seq.' / '.$seq.' - ';
		//  FUTURE - call fopen(x) file content test
		if ($fs = fopen($file_seq, 'w')) {
			if ((fwrite($fs, ($seq + 1)."\n") !== false))
				$result = $seq;
			fclose($fs);
			}
//		echo '<br>file_seq: '.$file_seq.' / '.$result.' - ';
		return ($result);  //  could not write exchange seq file: $file_seq
		}

	static public function get2($file, $tag = NULL, $eid = NULL) {
		//  sets public $ecat property to result of catalog fetch
		self::$ecat = self::get($file, $tag, $eid);
		//  FUTURE - refactor to pull in all records?  ...
		//  then perform filter on in memory array (could be a memory pig)
		}

	function get($file, $autr = NULL, $eid = NULL) {
		//  autr    array of authors (primary)
		//          NULL, all authors
		//  eid     single eid to match (secondary)
		//          FUTURE - array of exchange ID (secondary)
		//          NULL, don't limit exchange ID's
		//  return  array containing exchange catalog
		//          NULL if nothing found
		//  related: get_map()
			//  tag     tag to match, NULL match all tags
			//          ignore elements matching 'ondeck' unless specifcally passed
			//  art     article ID to match, NULL match all article ID's
			//        if both art and tag are passed, tag will be ignored
			//  usr     only` show content authored by specific user
			//  flg     FUTURE: omit ondeck, filter by content type
			//  FUTURE: all multiple tags, article ID's to be passed in

	        $row = 0;
		$cat = array();
	        if ($fh = fopen($file, 'r')) {
	                while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
				if ($data[ECAT_ORD][0] != '#') {  //  skip past column titles row
					if ($eid)
						$m = ($eid == $data[ECAT_UID]) ? true : false;
					else if ($autr) {  //  limit to only authors contained in list
						$m = in_array($data[ECAT_AUTHOR], $autr) ? true : false;
						}
					else
						$m = true;
					if ($m) {
		                                $cat[$row] = $data;
						$row++;
						}
		                        }
				}
			fclose($fh);
                	return (isset($cat) ? $cat : NULL);
			}
	        else {
			echo "<p>fopen read error".$file." </p> \n\n";  //  FUTURE, make a log file entry for this?
			return (NULL);
			}
		}

	static public function update($file, $cmd, $a) {
		//  complete rewrite catalog with updated, added, dropped records
		//  file    catalog filename, including full path  
		//  cmd     NEW|UPDATE 
		//  a       new/updated article record array
		//  return  true, successful
		//          false, failed/incomplete
		//  Initial code lifted from acat::update().
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
			echo ' retries, still could not get ecat write lock, sorry';
			return ($result);
			}

		/*  perform write processing here  */
		$o = 1;  //  FUTURE is ordinal needed?  ... should form preserve it?
		$str =  "# ord, id_readable, title, date, author, image, artid, htags\n";
		fwrite($fh, $str);

//		echo '<br>ecat::update, result: '.($result ? 'true' : 'false');
//		ap($a);
if (1) {	//  test test
		$row = 0;
		if ($cmd == ECAT_NEW) {
				//  FUTURE - it is possible that this uid file already exists
				//           could do a fopen(x) and if it fails then bail with error
				//           saying new file would overwrite existing content
				$act_done = true;
				if (isset($a['ord']))
					$o = $a['ord'];
				//  FUTURE - following code in a small utility function?
				$str  = $o;
				$str .= ', "'.$a['eid'].'"';
				$str .= ', "'.$a['title'].'"';
				$str .= ', "'.$a['date'].'"';
				$str .= ', "'.$a['author'].'"';
				$str .= ', "stock"';
				$str .= ', "'.$a['artid'].'"';
				//  $str .= ', ".$a['ECAT_HTG'].'"';
				$str .= "\n";
				fwrite($fh, $str);  //  FUTURE, check if returns false, try/catch?
				$o++;
				$row++;
			}

		if ($fr = fopen($file, 'r')) {
			while (($data = fgetcsv($fr, 1000, ",")) !== FALSE) {
				//  skip past column titles row
				if ($data[ECAT_ORD][0] != '#') {
					if ($cmd == ECAT_UPDATE && $data[ECAT_UID] == $a['eid']) {
						$act_done = true;
						if (isset($a['ord']))
							$o = $a['ord'];
						//  FUTURE - following code in a small utility function?
						$str  = $o;
						$str .= ', "'.$a['eid'].'"';
						$str .= ', "'.$a['title'].'"';
						$str .= ', "'.$a['date'].'"';
						$str .= ', "'.$a['author'].'"';
						$str .= ', "'.$a['image'].'"';
					//	$str .= ', "stock"';
						$str .= ', "'.$a['artid'].'"';
						//  $str .= ', ".$a['ECAT_HTG'].'"';
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
			echo '<br>could not access ecat';

	}  //  testtest
		if ($fh) fclose($fh);
		if ($act_done)
			rename($file, $file.'_0');
//		if ($act_done && rename($file.'_lock', $file.'_done'))  /*  testing  */
		if ($act_done && rename($file.'_lock', $file))
			$result = true;
		else {
			unlink($file.'_lock');
			//  FUTURE / SYSTEM - make system log
			echo '<br>ecat::update, could not complete action ';
			//  DANGER, if this file remains all updates are  blocked!!!
			//  FUTURE - add routine check if lock file date is
			//           older than a few minutes, then force delete
			}
		return $result;
		}

	}  //  ecat [end]

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
                return (isset($new_map) ? $new_map : NULL);
                }
        else {
                echo "<p>fopen read error </p> \n\n";  //  FUTURE, make a log file entry for this?
		system_log('get_map, fopen error - '.$filename);
                return (NULL);
                }
        }

?>
