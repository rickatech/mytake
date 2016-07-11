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

function mt_post(event, fs, url, out, fn, fn2) {
	//  Javascript AJAX POST wrapper with multifile upload support
	//    event   internal event triggering submit
	//    fs      form files input id
	//    url     URL to process POST request
	//    out     div element, replace content with AJAX output
	//    fn      custom function to call to indicate in progress
	//    fn2     custom function to call upon success
	//  CITATION  pure javascript multi-file upload :-D http://blog.teamtreehouse.com/uploading-files-ajax
	var files;
	var formData = new FormData();
	var file;
	var i;
	var js_type;
	var mf = false;

	fn();  //  custom 'in progress' call

	//  For standard inputs javascript has no easy way to disccover,
	//  so parse special input string to determine them
	//    js_nf   form includes NO file upload fields
	//    js_mf   form includes multi-file upload field
	// CITATION - http://stackoverflow.com/questions/3010840/loop-through-an-array-in-javascript
	if ((js_type = document.getElementsByName('js_mf')).length > 0) {
		js_type[0].value.split(',').forEach( function(s) { 
			formData.append(s, document.getElementsByName(s)[0].value);
			} );
		mf = true;
		}
	else if ((js_type = document.getElementsByName('js_nf')).length > 0) {
//	else if (js_type = document.getElementsByName('js_nf')) {
//		alert('z2');
		js_type[0].value.split(',').forEach( function(s) { 
			formData.append(s, document.getElementsByName(s)[0].value);
			} );
		}

	//   Disable standard processing (same as returning false?)
	event.preventDefault();

//	alert('zz');
	if (mf) {
		files = document.getElementById(fs).files;
		// Loop through each of the selected files.
		for (i = 0; i < files.length; i++) {
			file = files[i];

			// Check the file type.
			if (!file.type.match('image.*')) {
				continue;
				}

			// Add the file to the request.
			formData.append('photos[]', file, file.name);
			}
		}

	var xhr = new XMLHttpRequest();
	xhr.open('POST', url, true);
	xhr.onload = function () {
		if (xhr.status === 200) {
			//  File(s) uploaded
			document.getElementById(out).innerHTML = xhr.responseText;
			fn2();  //  custom 'completed' call
			//  XXXX
			//  wiz_01_msg
			//    div hidden
			//      wiz_01_status: 'success' | 'fail'
			//      wiz_01_next: inline HTML of next form
			//  wiz_01_body, need another custom function to check status of previous call
			//  CITATION: http://stackoverflow.com/questions/1279957/how-to-move-an-element-into-another-element
			}
		else {
			alert('An error occurred!');
			}
		};
	xhr.send(formData);
	}

