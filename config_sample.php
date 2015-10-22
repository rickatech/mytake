<?PHP
$version = 'v0.0.2';
$home_url = '/rickatech/mytake/';
$edit_url = 'https://blog.zaptech.com/?file=mytake/';
$data_dir = '/Users/fredness/zaptech/blog/content/mytake';
$login_data = '/profiles_dev.txt';
$catalog_data = $data_dir.'/data_cp';

const DESK_WIDTH_MIN = 1024;
const DESK_WIDTH_PAD =  144;

//  toggle targeted features
const FEATURE_COOK =1;  //  enabled cookie to remember username
$feature_mask = 0;
$feature_mask = FEATURE_COOK;

//  cookie customization/enable
$cookie_expire = 120;   //  1 minutes (rather short for testing), in production make this 1 week?
$cookie_url = '.zaptech.com';  ?>
