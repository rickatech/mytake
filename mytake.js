function window_adjust() {
	var dwin = document.getElementById('main_div');
	var wox = window.outerWidth;
	var wix = window.innerWidth;
	var dwin_x = wox - wix;
	var sx = window.screen.width;
	var sy = window.screen.height;
	notes = dflags + ', ' + wox + '|' + wix + ', ' + dwin_x + ', ' + window.outerHeight + '|' + window.innerHeight + ', ' + sx + '/' + sy;
	if (!(dflags) && desk_width_max > 0) {  //  i.e. if desktop
		//  dwin.style.width requires parseFloat() to return pure numerical,
		//  and likely isn't set in DOM initially
		if (wix > (desk_width_max + desk_width_pad))
			dwin.style.width = desk_width_max + 'px';
		else if (wix > (desk_width + desk_width_pad))
			dwin.style.width = wix - desk_width_pad + 'px';
		else
			dwin.style.width = desk_width + 'px';
		}
//	if (document.getElementById('slider1_container')) {
//		//  FUTURE - at some point, enable slider responsive code here
//		//  jssor_slider1.$ScaleWidth(window_adjust_o());
//		//  CITATION: http://www.howtolabs.net/web/jssor/responsive/
//		}
	if (debug_mask & 1)
		document.getElementById('mt_msg').innerHTML = notes;

	//  this kungfu will perform some repsonsive layout
	//  any element with class=thing1 (narrow) will have
	//  class=thing2 (wide) when larger display is detect
	//  and visa versa
//	if (typeof mytake_winadj_bpw1 !== 'undefined' && wix < mytake_winadj_bpw1) {
	if (mytake_winadj_bpw1 !== undefined && wix < mytake_winadj_bpw1) {
		//  FUTURE - store respnsive width state in persistent variable,
		//           no need to class swap if repsonsive state is same as last time
		//           ... so far attempts to do this are commented out :-/
//		if ((typeof resplast === 'undefined') || (resplast != 2)) {
			resplast = 2;  //  global, persistent!
			var elements = document.getElementsByClassName('thing1');
			for (var i in elements) {
			  if (elements.hasOwnProperty(i))
			    elements[i].className = 'thing2';
			  }
//			}
		}
	else {
//		if ((typeof resplast === 'undefined') || (resplast != 4)) {
			resplast = 4;  //  global, persistent!
			var elements = document.getElementsByClassName('thing2');
			for (var i in elements) {
			  if (elements.hasOwnProperty(i))
			    elements[i].className = 'thing1';
			  }
//			}
		}
//	alert(resplast);
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
