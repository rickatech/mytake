<?PHP
$version = 'v0.0.3';
$home_url = '/';
$actv_url = 'http://mytake.zaptech.com/';
$edit_url = 'https://blog.zaptech.com/?file=mytake/';
$data_dir = '/Users/fredness/zaptech/blog/content/mytake';
$login_data = '/profiles_dev.txt';
$catalog_data = $data_dir.'/data_cp';

$menu_mark = 'MENU';
$menu_full = 'pivot | go | dashboard | providers | search';

$brand_t = "rickatech - MyTake";
$brand_l = "<b>My Take</b>";
$brand_l = "<span style=\"font-family: sans-serif; font-size: 300%;\"><b>holistik</b> </span>";
$brand_l = "<span style=\"font-family: Helvetica Narrow,Arial Narrow,Tahoma,Arial,Helvetica,sans-serif; color: #ffffff; font-size: 250%;\"><b>Holisticers</b> </span><span style=\"font-family: sans-serif;\"><br>THE WAY YOU LIVE</span>";

const DESK_WIDTH_MIN = 1024;
const DESK_WIDTH_PAD =  144;

//  toggle targeted features
const FEATURE_COOK =   1;       //  enabled cookie to remember username
const FEATURE_PROFILE = 2;      //  login user name has javascript profile call
$feature_mask = FEATURE_COOK;

//  toggle debug output
const DEBUG_DISPSIZE =1;  //  show screen/window size diminsions
$debug_mask = DEBUG_DISPSIZE;

//  cookie customization/enable
$cookie_expire = 120;   //  1 minutes (rather short for testing), in production make this 1 week?
$cookie_url = '.zaptech.com';  ?>
