<?PHP

function ap($a) {
	echo "<pre style=\"font-size: smaller;\">";
	print_r($a);
	echo "</pre>";
	}

function get_map($filename, $tag = NULL, $art = NULL) {
        //  read in 'nosql' data file, return array
	//  tag     tag to match, NULL skip tag matching
	//  art     article ID to match, NULL skip article ID matching
	//  FUTURE: all multiple tags, article ID's to be passed in

        $row = 0;
        if ($fh = fopen($filename, 'r')) {
                while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
			$m = false;
			if (!is_null($tag)) {
				$ta = explode('|', $data[3]);
				foreach($ta as $t) {
					if ($t == $tag) {
						$m = true; break;  }
					}
				}
			else if (!is_null($art)) {
				if ($data[1] == $art)
					$m = true;
				}
			else
				$m = true;
			if ($m) {
                                $new_map[$row] = $data;
				$row++;
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

function catalog_latest($nlist = 4, $tag = NULL, $art = NULL) {
	global $catalog_data;

	$data = get_map($catalog_data, $tag, $art);
	if (is_null($data))
		return;
	if (sizeof($data) < 1)
		return;
	//  without overflow: hidden, spacing is weird
	echo "<div style=\"height: 24px; overflow: hidden;\">";
	if (is_null($tag)) {
		echo "&nbsp;<B>Latest</B>\n";
		}
	else {
		echo "&nbsp;<B><a href=?tag=".$tag.">";  //  FUTURE: omit link if superfluous
		if ($tag == 'music')     echo 'Tunes';
		else if ($tag == 'book') echo 'Read';
		else if ($tag == 'film') echo 'Filcks';
		else echo $tag;
		echo "</a></B>\n";
		}
	echo "</div>";
	foreach($data as $da) {
		if ($da[0] != 'ID') {  //  skip past column titles row
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
		}
	//  without overflow: hidden, spacing is weirdi, marry this + title above = 72px;
	echo "<div style=\"height: 48px; overflow: hidden; margin-left: 72px;\">... </div>";
	}

function article($art = NULL) {
	global $dflags;
	global $edit_url;
	global $catalog_data;

	if (is_null($art))
		return NULL;
	$da = get_map($catalog_data, NULL, $art);
	$da_cap = explode('|', $da[0][2]);
	$da_tag = explode('|', $da[0][3]);
	return array(
	  "article" => $art,
	  "caption" => $da_cap,
	  "tags" =>    $da_tag);
	}

function article_out($artrec) {
	global $dflags;
	global $edit_url;

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
		echo " [&nbsp;<A href=".$edit_url.$artrec['article']."&area=1>edit</a>&nbsp;]";
		}
	echo "</div>";

	echo "<p style=\"margin-top: 0;\">".(isset($artrec['caption'][0]) ? '<b>'.$artrec['caption'][0].'</b>' : '');
	echo "<span style=\"color: grey;\"><br>".(isset($artrec['caption'][1]) ? $artrec['caption'][1] : '')."</span></p>";
	/*  using readfile() purposely insures PHP commands in file are ignored  */
	readfile("/Users/fredness/zaptech/blog/content/mytake/".$artrec['article']);
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
