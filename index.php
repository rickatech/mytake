<?PHP
include 'mobile_detect/Mobile_Detect.php';
//  require_once '/Users/fredness/howto/public_html/php/test/Mobile-Detect-2.8.17/Mobile_Detect.php';
$detect = new Mobile_Detect;    //  Include and instantiate the class.
include 'session.php';
include "config_sample.php";
include "mytake.php";
include "detect_dev.php";
session_detect();
$login_form = 'login disabled';
login_state($login_form);

if (isset($_GET['tag'])) {
	$tag = $_GET['tag'];
	$h0 = '<a href='.$home_url.'>';  $h1 = '</a>';
	}
else if (isset($_GET['art'])) {
	$art = $_GET['art'];
	$h0 = '<a href='.$home_url.'>';  $h1 = '</a>';
	$art_rec = article($art);
	}
else {
	$art = NULL;
	$h0 = '';  $h1 = '';
	$art_rec = NULL;
	}  ?>

<HTML>
<HEAD>
<TITLE><?PHP  
	echo is_null($art_rec) ? "rickatech - My Take" : "My Take - ".$art_rec['caption'][0]  ?></TITLE>
<LINK rel="stylesheet" type="text/css" href="base.css">
<!--  typically only mobile devices recognize device-width, desktops ignore  -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
function window_adjust() {
	var dwin = document.getElementById('main_div');
	var wox = window.outerWidth;
	var wix = window.innerWidth;
	var dwin_x = wox - wix;
	var sx = window.screen.width;
	var sy = window.screen.height;
	notes = dflags + ', ' + wox + '|' + wix + ', ' + dwin_x + ', ' + sx + '/' + sy;
	if (!(dflags)) {  //  i.e. if desktop
		//  dwin.style.width requires parseFloat() to return pure numerical,
		//  and likely isn't set in DOM initially
		if (wix > desk_width)
			dwin.style.width = (wix - xm) + 'px';
		else
			dwin.style.width = (desk_width - xm) + 'px';
		}
	document.getElementById('mt_msg').innerHTML = notes;
	}

function detectKeyLogin(event) {
	if (event.keyCode == 13)
                head_login();
	}

function head_login() {
	un = document.getElementById("username_dg").value;
	pw = document.getElementById("password").value;
	//  alert('login attempt: ' + un + ', ' + pw);
	//  window.open('?ajax=1&username_dg='+un+'&password='+pw', '_self');
	window.open('?ajax=0&username_dg=' + un + '&password=' + pw, '_self');
	//  window.open('?username_dg=' + un + '&password=' + pw + '&ajax=0', '_self');
	}

function head_logout() {
	window.open('?ajax=0&logout', '_self');
        }

var FLAG_MOBILE = 1;
var desk_width = <?PHP  echo DESK_WIDTH_MIN;  ?>;
var desk_width_pad = <?PHP  echo DESK_WIDTH_PAD;  ?>;
var xm = desk_width_pad << 1;
var dflags = <?PHP  echo $dflags;  ?>;

window.onresize = window_adjust;
window.onload = window_adjust;
</script>
</HEAD>

<BODY BGCOLOR=#BFBFBF><?PHP
	echo "<div style=\"float: right; text-align: right;\"><span>".$login_form."</span>";
	echo "\n<span>".$notes."</span> <span id='mt_msg'></span></div>";
	echo "\n<b>".$h0."My Take".$h1."</b><br>&nbsp;";
	if (!$dflags & DFLAGS_MOBILE) {  // i.e. not mobile  ?>
<div id='main_div' style="position: relative; width: 1024px;; background: #FFFFFF; border: solid; margin: auto;"><?PHP
	} else {  ?>
<div id='main_div' style="position: relative; background: #FFFFFF; border: solid;"><?PHP
	}  ?>

<!--	this shows logged in user's thumbnail image and link to their profile
	FUTURE: customize this to be a seperate 'plug-in' PHP file
		to allow this placement/layout to be customized  -->
<div style="position: absolute; right: 0px; bottom: -1px;"><a href=/rickatech/><img
  SRC=https://images.zaptech.com/rickatech/dukeanomx.gif
  BORDER=0 ALT="rickatech"></a></div>
<div class=version style="position: absolute; right: 0px; bottom: 0;"><?PHP
	echo $version  ?></div>

<?PHP
	if (isset($tag)) {  ?>
<div style="float: left; width: 288px;"><?PHP
		catalog_latest(12, $tag);  ?>
	<br style="clear: both;"> &nbsp; </div>  <?PHP
		}
	else if (!is_null($art_rec)) {  ?>
<div style="margin-top: 0.5em; margin-bottom: 0.5em; margin-left: 0.5em; margin-right: 0.5em;">  <?PHP
		article_out($art_rec);  ?>
</div>  <?PHP
		}
	else {  /*  --- START MAIN SUMMARY DISPLAY ---  */  ?>

<div style="float: left; width: 288px;"><?PHP
		catalog_latest(6);  ?>
	</div>

<div style="float: left; width: 288px;"><B>Exchange</B>
[<A HREF=http://blog.zaptech.com/?file=mytake/exchange&area=1>edit</A>]<?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        readfile("/Users/fredness/zaptech/blog/content/mytake/exchange");  ?>
	</div>

<div style="float: left; width: 288px;"><?PHP
		catalog_latest(6, 'music');  ?>
	</div>

<div style="float: left; width: 288px;"><?PHP
		catalog_latest(6, 'book');  ?>
	</div>

<div style="float: left; width: 288px;"><?PHP
		catalog_latest(6, 'film');  ?>
	</div>

<!--div style="float: left; width: 288px;"><B>Tunes</B>
[<A HREF=http://blog.zaptech.com/?file=mytake/tunes&area=1>edit</A>]<?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        //  readfile("/Users/fredness/zaptech/blog/content/mytake/tunes");  ?>
	</div  -->

<div style="float: left; width: 288px;"><B>Flicks</B>
[<A HREF=http://blog.zaptech.com/?file=mytake/flicks&area=1>edit</A>]<ul style="margin-top: 0px;"><?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        readfile("/Users/fredness/zaptech/blog/content/mytake/flicks");  ?></ul>
	</div>

<div style="float: left; width: 288px; "><B>Read</B>
[<A HREF=http://blog.zaptech.com/?file=mytake/read&area=1>edit</A>]<ul style="margin-top: 0px;"><?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        readfile("/Users/fredness/zaptech/blog/content/mytake/read");  ?></ul>
	</div>

<div style="float: left; width0: 288px;"><B>WTF</B>
[<A HREF=http://blog.zaptech.com/?file=mytake/wtf&area=1>edit</A>]<?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        readfile("/Users/fredness/zaptech/blog/content/mytake/wtf");  ?>
	</div><?PHP
	}  /*  --- START MAIN SUMMARY DISPLAY ---  */  ?>

<div style="clear: both; text-align: center; font-family: sans-serif;"><A HREF=toread.php>on&nbsp;deck</A>,
    <A HREF=archive.html>archive</A>,
    <A HREF=mailto:mytake@zaptech.com>mytake@zaptech.com</A><?PHP
	if (!$dflags & DFLAGS_MOBILE) {  // i.e. not mobile  ?>,
    <A HREF=https://images.zaptech.com/mytake/prettydb.php>gfx</A>/<a
      href=https://blog.zaptech.com/?file=mytake/data_mt&area=1>catalog</a>, <a
      href=https://blog.zaptech.com/?file=mytake/data_cp&area=1>copy</a><?PHP
	}  ?>
    &nbsp; </div>
</div>

</BODY>
</HTML>

