<?PHP
$version = 'v0.0.4b';
//  tag filtering to get()
//  private message/flexible calling fields support to ecat::update
$home_url = '/';
$actv_url = 'http://dungeon.local.zaptech.org/';
$edit_url = 'https://blog.zaptech.com/?file=mytake/';
//  $data_dir = '/home/rickatech/dungeon/bog';
$file_dir = '##FILUPL##';   //  e.g. '/public/mytake/site/gfx-upload'
$data_dir = '##CWDBOG##';   //  e.g. '/public/mytake/mt/bog'
$login_data = 'profiles.txt';
$catalog_data = $data_dir.'/data_cp';

$menu_mark = 'MENU';
$menu_helo = 'Hello ';  //  okay for this to be commented out/unset to skip
$foot = 'about | join | promotions | careers';

$brand_t = "rickatech - MyTake";
$brand_l = "<b>My Take</b>";
$brand_l = "<span style=\"font-family: sans-serif; font-size: 300%;\"><b>holistik</b> </span>";
//$brand_l = "<span style=\"font-family: Helvetica Narrow,Arial Narrow,Tahoma,Arial,Helvetica,sans-serif; color: #ffffff; font-size: 250%;\"><b>Holisticers</b> </span><span style=\"font-family: sans-serif;\"><br>THE WAY YOU LIVE</span>";
//$brand_l = "<span style=\"font-family: Helvetica Narrow,Arial Narrow,Tahoma,Arial,Helvetica,sans-serif; color: black; font-size: 250%;\"><b>mytake</b> </span><span style=\"font-family: sans-serif;\"><br>simple framework</span>";
$brand_l = "<span style=\"font-family: sans-serif; font-size: 300%;\"><b>mytake</b> </span>";

const DESK_WIDTH_MIN =  864;
const DESK_WIDTH_MAX = 1152;
const DESK_WIDTH_PAD =   72; 

const MAX_LINE_LENGTH = 1000;   //  used for various plain text array files
const FOPEN_X_RETRIES = 3;  //  used for various file write locks

//  toggle targeted features
const FEATURE_COOK =   1;       //  enabled cookie to remember username
const FEATURE_PROFILE = 2;      //  login user name has javascript profile call
$feature_mask = FEATURE_COOK;

//  toggle debug output
const DEBUG_DISPSIZE =       1; //  show screen/window size diminsions
const DEBUG_MOBILE_ADMIN =  2;  //  override hiding admin links for mobile
const DEBUG_MENU_BG =      4;   //  provide menu solid background color
const DEBUG_UREP_EDIT =   8;    //  disable workaround to prevent stretchy right profile column clipping urep form
const DEBUG_UREP_V2 =    16;    //  switch to enhanced urep display
const DEBUG_UREP_V3 =   32;     //  urep alt display v3
const DEBUG_JENN =      32;     //  enable jenn new features
const DEBUG_MSG_DRCT = 64;      //  enable direct message features
const DEBUG_RICK =     64;      //  enable rick new features
if (isset($_SESSION['debug']) && $_SESSION['debug']) $debug_mask = $_SESSION['debug']; else  //  !!! NEVER DO THIS ON PRODUCTION !!!  */
$debug_mask = DEBUG_MOBILE_ADMIN | DEBUG_MENU_BG | DEBUG_DISPSIZE | DEBUG_UREP_V2 | DEBUG_JENN | DEBUG_RICK;

//  cookie customization/enable
$cookie_expire = 120;   //  1 minutes (rather short for testing), in production make this 1 week?
$cookie_url = '.zaptech.com';  ?>
