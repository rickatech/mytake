<?PHP
//  typically this is a staticly include into index.php
//  used to populate 'dashboard' portion of home page

/*  using readfile() purposely insures PHP commands in file are ignored  */
readfile($data_dir.'/is/home_car');  //  carousel or other additional special top of page content

/*  using file_get_contents() purposely insures PHP commands in file are ignored???  */
$m = file_get_contents($data_dir.'/is/home');

echo "\n<div style=\"padding: 0 4px 4px 4px;\">";
section_head(NULL, NULL, $m);
echo '</div>';  ?>

<!--  floating 50/50 split for non-narrow screens
      some javascript kungfu will revert to non-float stacking for narrower displays  -->
<div style="margin-left: 4px;"><!--  50/50 split non-narrow screen  -->
<div id="thing1" class=thing1><!--  left side  -->
<div style="margin: 0 4px 4px 0;"><?PHP
	//  SYMPOSIUM
	echo "\n<div><!--  symposium  -->";
	exchange::latest(6, NULL, 'exch');  //  show all
	echo "\n</div><!--  symposium [end]  -->";  ?>
</div>
</div><!--  left side [end]  -->
<div id="thing2" class=thing1><!--  right side  --><?PHP
	echo "\n<div style=\"margin-right: 4px;\">";
	section_head(NULL, 'Popular Practitioners');
	echo "\n<div style=\"height: 4px;\"></div>";

	//  FUTURE - new utility function friends_get_user() return array or null?
	if ($username) {
		$fr = array();
		lists::get($data_dir.'/friends', $fr);  //  FUTURE: what if this call fails?
		$ac = array();
		lists::get($data_dir.'/invites', $ac);  //  FUTURE: what if this call fails?
		//  FUTURE - following to become a new function call?
		$nv = array();
		foreach ($ac as $k => $v) {
			//  FUTURE: this will not scale, may need better schema for this
			if ($k != $username) {
				if (in_array($username, $v)) {
					array_push($nv, $k);
					}
				}
			}
		//  FUTURE - instead of passing in fr and nv arrays,
		//  can they be stored in static class that can be accessed like a global?
		//  ... further benefit of avoiding mulitple file reads if only reading?

		//  FUTURE - huh, given pivot is not a thing yet?
		user_list(USERLIST_PIVOT,
		  isset($fr[$username]) ? $fr[$username] : NULL,  //  array of friends
		  NULL,
		  isset($ac[$username]) ? $ac[$username] : NULL,  //  array of pending accepts
		  count($nv) > 0 ? $nv : NULL);
		}
	else
		//  FUTURE - must be logged in to search for other users?
		user_list(USERLIST_PIVOT);  //  FUTURE, this should be USERLIST_HOME or pass in user list of topical users
	echo "</div>";  ?>
</div><!--  right side [end]  -->
</div><!--  50/50 split non-narrow screen  -->

<div style="clear: both;"></div><!--  user list above has images that tend to overflow off bottom  --><?PHP

	echo "\n<div style=\"margin: 4px; clear: both; background-color: orange;\">";
	//section_head(NULL, 'Pivot Articles');
	//  Latest
	artex::latest(6);
	echo '</div>';

