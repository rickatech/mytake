<?PHP
include 'mobile_detect/Mobile_Detect.php';
//  require_once '/Users/fredness/howto/public_html/php/test/Mobile-Detect-2.8.17/Mobile_Detect.php';
$detect = new Mobile_Detect;    //  Include and instantiate the class.
include 'session.php';
include "config.php";
include "mytake.php";
include "detect_dev.php";
include "headfoot.php";
session_detect();
$login_form = 'login disabled';
login_state($login_form);

if (isset($_GET['tag'])) {
	$tag = $_GET['tag'];
	$h0 = '<a class=brand_l href='.$home_url.'>';  $h1 = '</a>';
	}
else if (isset($_GET['art'])) {
	$art = $_GET['art'];
	$h0 = '<a class=brand_l href='.$home_url.'>';  $h1 = '</a>';
	$art_rec = article($art);
	}
else {
	$art = NULL;
	$h0 = '';  $h1 = '';
	$art_rec = NULL;
	}  ?>

<HTML>
<HEAD><?PHP
	head_meta();  ?>
<LINK rel="stylesheet" type="text/css" href="base.css">
<!--  typically only mobile devices recognize device-width, desktops ignore  -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/JavaScript" src="mytake.js"></script> 
<script>
	var desk_width = <?PHP  echo DESK_WIDTH_MIN;  ?>;
	var desk_width_pad = <?PHP  echo DESK_WIDTH_PAD;  ?>;
	var dflags = <?PHP  echo $dflags;  ?>;
	var debug_mask = <?PHP  echo $debug_mask;  ?>;
	var FLAG_MOBILE = 1;
	var xm = desk_width_pad << 1;

	window.onresize = window_adjust;
	window.onload = window_adjust;
</script>
</HEAD>

<BODY><?PHP
	head_body();
	body_lowright();

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

<div class0=list_body>&nbsp;<B>Exchange</B>
[<A HREF=https://rs02.zaptech.org/bog/?file=exchange&area=1>edit</A>]<?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        readfile($data_dir.'/exchange');  ?>
	</div>  <?PHP

		catalog_latest(6, 'health');
		catalog_latest(6, 'exercise');
		catalog_latest(6);
		catalog_latest(6, 'ondeck');
		catalog_latest(6, NULL, NULL, session_userid_active() ? session_username_active() : 'N/A', 'My Content');  ?>

<div class=list_body>&nbsp;<B>Lounge</B>
[<A HREF=https://rs02.zaptech.org/bog/?file=lounge&area=1>edit</A>]<?PHP
        /*  using readfile() purposely insures PHP commands in file are ignored  */
        readfile($data_dir.'/lounge');  ?>
	</div>  <?PHP
		}  ?>

<div style="clear: both; text-align: center; font-family: sans-serif;"><?PHP
	if (session_userid_active()) {
		if (!$dflags & DFLAGS_MOBILE) {  // i.e. not mobile
			echo "<a href=".$gfx_catalog.">gfx</A>/<a";
			echo "\n  href=".$gfx_gallery.">gallery</a>, <a";
			echo "\n  href=".$edit_url.">copy</a>";
			}
		}  ?>
    &nbsp; </div>
</div>

<?PHP
	foot();  ?>
</BODY>
</HTML>

