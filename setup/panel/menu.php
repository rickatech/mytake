<?PHP
//  menu logic, most project will customize this to taste
//  include '../panel/menu.php';
//
//  FUTURE - make this a class with a namespace, then consider
//           enabling autoloader to include this file automatically

function body_menu_2($m, $m2 = false) {
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
        //  FUTURE - pass $us in?
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

