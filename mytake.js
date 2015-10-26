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
	if (debug_mask & 1)
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
var xm = desk_width_pad << 1;

