<?PHP

function ap($a) {
	echo "<pre style=\"font-size: smaller;\">";
	print_r($a);
	echo "</pre>";
	}

//  FUTURE - following can be rolled into a static class

const CONTENT_ORD = 0;  //  ...
const CONTENT_UID = 1;
const CONTENT_BYL = 2;  //  title | date, author
const CONTENT_TAG = 3;
const CONTENT_IMG = 4;
const CONTENT_URL = 5;

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
		echo "&nbsp;<B>".$lab."</B>\n";
	else if (is_null($tag)) {
		echo "&nbsp;<B>Latest</B>\n";
		}
	else {
		echo "&nbsp;<B><a href=?tag=".$tag.">";  //  FUTURE: omit link if superfluous
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
		  "<img src=\"gfx/".$da[4]."\" align=left style=\"width: 68px; margin: 2px;\">".$a1;
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

function tunes($mode) {
    /*  mode = 0  comma, recent only
    /*         1  LI list, all  */

    if ($mode == 1)
      echo "\n<LI>";
    echo "<A HREF=http://myspace.com/autolux>Autolux</A>";
    echo " - Turnstile Blues";
    if ($mode == 1) {
        echo "\n<BR>Discovered 2007-11";
        echo "\n<BR><IMG SRC=http://b6.ac-images.myspacecdn.com/00202/62/80/202560826_m.jpg>";
        }
    
    if ($mode == 1)
      echo "\n<LI>";
    else
      echo ", ";
    echo "<A HREF=http://www.peterbjornandjohn.com/>PBJ</A>";
    echo "&nbsp;-&nbsp;<A HREF=http://www.peterbjornandjohn.com/Pages/MusicVideo.html>Young&nbsp;folks</A>";
    if ($mode == 1) {
        echo "\n<BR>Discovered 2007-10";
        echo "\n<BR><IMG SRC=http://www.peterbjornandjohn.com/Images/record_youngfolks.jpg>";
        }

    if ($mode == 1) {
        echo "\n<LI>";
        echo "Gorecki";
        echo "&nbsp;-&nbsp;3rd Symphony";
        echo "\n<BR>Discovered 200?";
        }

    }

function read($mode) {
    if ($mode == 1)
      echo "\n<LI>";
    echo "<A HREF=http://www.readinggroupguides.com/guides3/friend_of_the_earth1.asp>A Friend of the Earth</A>";
    echo " - <a href=http://www.tcboyle.com/>T&nbsp;C&nbsp;Boyle</a>";
    if ($mode == 1) {
        echo "\n<BR>reading";
        }

    if ($mode == 1)
      echo "\n<LI>";
    else
      echo ", ";
    echo "Counting Heads";
    echo " - <a href=http://en.wikipedia.org/wiki/David_Marusek>David&nbsp;Marusek</a>";
    if ($mode == 1) {
        echo "\n<BR>read 2008-01";
        }

    if ($mode == 1)
      echo "\n<LI>";
    else
      echo ", ";
    echo "<a href=http://en.wikipedia.org/wiki/Flush_%28novel%29>Flush</a>";
    echo " - Carl&nbsp;Hiaasen";
    if ($mode == 1) {
        echo "\n<BR>read 2007-12";
        }

    if ($mode == 1)
      echo "\n<LI>";
    else
      echo ", ";
    echo "<a href=http://en.wikipedia.org/wiki/The_Possibility_of_an_Island>The Possibility of an Island</a>";
    echo " - Michel&nbsp;Houellebecq";
    if ($mode == 1) {
        echo "\n<BR>read 2007";
        }
    }

function flicks($mode) {
    echo "<a href=http://www.imdb.com/title/tt0414993/>The Fountain</a>";
    if ($mode == 1) {
        echo "\n<BR>DVD viewed 2007";
        }
    }

function wtf($mode) {
    if ($mode == 0) {
        echo "<a href=mytake/#darkness>A 16</A>";
        echo ", <a href=mytake/#darkness>Darkness</A>: ";
        }
    if ($mode == 1) {
        echo "<LI><a href=http://www.yelp.com/biz/emmys-spaghetti-shack-san-francisco>Emmy</A><A
            HREF=http://www.sfgate.com/cgi-bin/article.cgi?f=/c/a/2004/12/31/DDG6TAIQN11.DTL&type=food>'s</a>";
        echo "<LI><a href=http://www.a16sf.com/>A&nbsp;16</a>";
        echo "\n<BR>found 2008-05";
        echo "<LI><a name=darkness>Darkness</A>: ";
        echo "<a href=http://radio.darkness.com/>radiodarkness</a>";
        echo ", <a href=http://darkness.com/>Darkness.com</a>";
        echo ", <a href=http://darksites.com/links/Music/Music.html>Gothic&nbsp;Music</a>";
        echo "\n<BR>found 2007";
        }
    }

?>
