function emblem_ch_sel(c, n) {
	//  c    specific emblem choice to toggle 
	//  n    quantity of emblems to choose from
	var i;
	for (i = 0; i < n; i++) {
		if (i != c) { 
			document.getElementById('emblem_' + i).checked = false;
			}
		}
	}

function head_profile() {
	//  this won't be called unless FEATURE_PROFILE is setup in config.php
	window.open('/profile/?profile', '_self');
	}

function formpop(test) {
	window.open('/?signup', '_self');
	}

function is_email(email) { 
	//  return: true (if is an email address)
	//  CITATION: http://stackoverflow.com/a/46181/11236
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
	}

function signup_form_okay() {
	//  return: true (if form fields are accatable)
	var email = document.forms["signup"]["email"].value;
	var fname = document.forms["signup"]["fname"].value;

	if (!is_email(email)) {
		document.getElementById('signup_form_msg').innerHTML = 'please provide a valid email address';
		return false;
		}
	if (fname == null || fname == "") {
		document.getElementById('signup_form_msg').innerHTML = 'First/Nickname can not be empty';
		return false;
		}
	//  validation okay, returning true will submit the form,
	//  disable submit button to visually signal request is in progress
	document.forms["signup"]["submit"].disabled = true;
	return true;
	}

function display_toggle(i) {
	var e;
	if (e = document.getElementById(i)) {
		if (e.style.display == 'none')
			e.style.display = 'block';
		else
			e.style.display = 'none';
		}
	}

function exchange_toggle() {
	if (document.getElementById('exch_toggle').innerHTML == 'mine')
		document.getElementById('exch_toggle').innerHTML = 'recent';
	else
		document.getElementById('exch_toggle').innerHTML = 'mine';
	display_toggle('exch_all');
	display_toggle('exch_mine');
	}

function artex_form_toggle(f) {
	if (f == 'artc') {
		document.getElementById('dlartc').style.display = 'block';
		document.getElementById('isartc').disabled = true;
		document.getElementById('issmry').disabled = true;
		document.getElementById('isartc_x').value = 'true';
		document.getElementById('issmry').checked = true;
		}
	else {
		if (f != 'smry') {
			document.getElementById('isartc').checked = false;
			document.getElementById('dlartc').style.display = 'none';
			document.getElementById('issmry').disabled = false; 
			document.getElementById('isartc').disabled = false; 
			document.getElementById('isartc_x').value = 'false';
			}
		}
	if (f == 'exch') {
		document.getElementById('isexch').disabled = true; 
		document.getElementById('isexch_x').value = 'true';
		}
	else {
		document.getElementById('isexch').disabled = false; 
		document.getElementById('isexch').checked = false;
		document.getElementById('isexch_x').value = 'false';
		}
	if (f == 'wall') {
		document.getElementById('iswall').disabled = true;
		document.getElementById('iswall_x').value = 'true';
		}
	else {
		document.getElementById('iswall').disabled = false; 
		document.getElementById('iswall').checked = false;
		document.getElementById('iswall_x').value = 'false';
		}
	if (f == 'pivt') {
		document.getElementById('ispivt').disabled = true;
		document.getElementById('ispivt_x').value = 'true';
		}
	else {
		document.getElementById('ispivt').disabled = false; 
		document.getElementById('ispivt').checked = false;
		document.getElementById('ispivt_x').value = 'false';
		}
	if (f == 'pmsg') {
		document.getElementById('ispmsg').disabled = true;
		document.getElementById('ispmsg_x').value = 'true';
		}
	else {
		document.getElementById('ispmsg').disabled = false;
		document.getElementById('ispmsg').checked = false;
		document.getElementById('ispmsg_x').value = 'false';
		}
	if (f == 'smry') {
		document.getElementById('issmry').disabled = true;
		document.getElementById('dlartc').style.display = 'block';
		document.getElementById('isartc').disabled = true;
		document.getElementById('isartc_x').value = 'true';
		document.getElementById('isartc').checked = true;
		}
	else {
		if (f != 'artc') {
			document.getElementById('issmry').disabled = false;
			document.getElementById('isartc').disabled = false;
			document.getElementById('isartc_x').value = 'false';
			document.getElementById('dlartc').style.display = 'none';
			document.getElementById('issmry').checked = false;
			}
		}
	}


function gallery_form_prefill(formc, imgid) {
	//  formx   container of form (to reveal/hide)
	//  imgid = unique image id, username_nnnn
	//          'new', new image form
	var e;

	if (e = document.getElementById(formc)) {
		if (e.style.display == 'none')
			e.style.display = 'block';
		}
//	document.getElementById("gallery_iprev").src="/gfx/gallery/" + imgid + "_144x.png";
//	alert(document.forms['form_gallery_image'].elements['inputA'].value);
	if (imgid == 'new') {
		document.forms['form_gallery_image'].elements['mode'].value =
		document.forms['form_gallery_image'].elements['mode_d'].value = 'new';
		document.getElementById("gallery_iprev").src="/gfx-stock/drama_144x.png";
	//	alert(document.forms['form_gallery_image'].elements['imgfn1'].value);
		document.forms['form_gallery_image'].elements['imgfn1'].value = 'default';
		document.forms['form_gallery_image'].elements['title'].value =
		document.forms['form_gallery_image'].elements['date_d'].value =
		document.forms['form_gallery_image'].elements['itags'].value = '';
		}
	else {
		document.forms['form_gallery_image'].elements['mode'].value = 'edit';
		document.forms['form_gallery_image'].elements['mode_d'].value = 'edit';
		imgsrc = document.getElementById(imgid + "_src").innerHTML;
		document.getElementById("gallery_iprev").src = "/gfx-upload/gallery/" + imgsrc;
		document.forms['form_gallery_image'].elements['imgfn1'].value =
		document.forms['form_gallery_image'].elements['imgfno'].value = imgsrc;
		document.forms['form_gallery_image'].elements['title'].value =
		  document.getElementById(imgid + "_title").innerHTML;
		document.forms['form_gallery_image'].elements['date'].value =
		  document.forms['form_gallery_image'].elements['date_d'].value =
		  document.getElementById(imgid + "_date").innerHTML;
		document.forms['form_gallery_image'].elements['itags'].value =
		  document.getElementById(imgid + "_htags").innerHTML;
		document.forms['form_gallery_image'].elements['gcatid'].value = imgid;
		}
//	alert(imgsrc);
	}

function artex_form_image(f) {
	//  artex form, image selector - prevent more than one image being selected
	//  FUTURE - a loop could shrink this code
	if (f == '3') {
		document.getElementById('image_stock').checked = false;
		if (c = document.getElementById('image_0'))
			c.checked = false;
		if (c = document.getElementById('image_1'))
			c.checked = false;
		if (c = document.getElementById('image_2'))
			c.checked = false;
		}
	if (f == '2') {
		document.getElementById('image_stock').checked = false;
		if (c = document.getElementById('image_0'))
			c.checked = false;
		if (c = document.getElementById('image_1'))
			c.checked = false;
		if (c = document.getElementById('image_3'))
			c.checked = false;
		}
	if (f == '1') {
		document.getElementById('image_stock').checked = false;
		if (c = document.getElementById('image_0'))
			c.checked = false;
		if (c = document.getElementById('image_2'))
			c.checked = false;
		if (c = document.getElementById('image_3'))
			c.checked = false;
		}
	if (f == '0') {
		document.getElementById('image_stock').checked = false;
		if (c = document.getElementById('image_1'))
			c.checked = false;
		if (c = document.getElementById('image_2'))
			c.checked = false;
		if (c = document.getElementById('image_3'))
			c.checked = false;
		}
	if (f == 's') {
		if (c = document.getElementById('image_0'))
			c.checked = false;
		if (c = document.getElementById('image_1'))
			c.checked = false;
		if (c = document.getElementById('image_2'))
			c.checked = false;
		if (c = document.getElementById('image_3'))
			c.checked = false;
		}
	}

function artex_form_postload() {
	//  user by artex::form
//	if (c = document.getElementById('copy')) {
	if (c = document.getElementById('artc_cp'))
		document.getElementById('copyartc').value = c.innerHTML;
	if (c = document.getElementById('artc_sm'))
		document.getElementById('copyexch').value = c.innerHTML;
	}

function artexch_form_postload() {
//	commented out this if 2016-10-07
//	//  user by artex::form
	if (c = document.getElementById('excp')) {
//		document.getElementById('copyexch').value = c.innerHTML;
		alert('exch - ask Rick why this happened');
		}
	//  This is better, it doesn't require span to be stored with content
	if (c = document.getElementById('excp_pl')) {
		document.getElementById('copyexch').value = c.innerHTML;
//		alert('pl_2');
		}
	}

function urep_form_rankmv2(attr, row, uq) {
	//  Only update alternate urep value attr not in current pick set
	//  attr    representation attribute to affect
	//  row     index number of current row to affect
	//  uq      DOM element prefix string (may be '')
	var i;
	var s;
	var v;

	s = document.getElementById('urep_' + attr + '_' + row).innerHTML;
	for (i = 0; i < 4; i++) {
		v = document.getElementsByName('ua_' + attr + '_' + i)[0].value;
		if (v == row)
			return;
		}
	document.getElementsByName('ua_' + uq + attr + '_4')[0].value = row;
	document.getElementById('ua_' + uq + attr + '_4').innerHTML = s;
	}

function urep_form_rankmove(attr, row, action, uq) {
	//  attr    representation attribute to affect
	//  row     index number of current row to affect
	//  action  up/down
	pre = 'ua_' + uq + attr + '_';
	if (action == '+') {
		t = document.getElementById(pre + row).innerHTML;
		document.getElementById(pre + row).innerHTML = document.getElementById(pre + (row - 1)).innerHTML;
		document.getElementById(pre + (row - 1)).innerHTML = t;
		n = document.getElementsByName(pre + row)[0].value;
		document.getElementsByName(pre + row)[0].value = document.getElementsByName(pre + (row - 1))[0].value;
		document.getElementsByName(pre + (row - 1))[0].value = n;
	//	alert(pre + (row - 1));
		}
	else if (action == '-') {
		t = document.getElementById(pre + row).innerHTML;
		document.getElementById(pre + row).innerHTML = document.getElementById(pre + (+row + 1)).innerHTML;
		document.getElementById(pre + (+row + 1)).innerHTML = t;
		n = document.getElementsByName(pre + row)[0].value;
		document.getElementsByName(pre + row)[0].value = document.getElementsByName(pre + (+row + 1))[0].value;
		document.getElementsByName(pre + (+row + 1))[0].value = n;
		}
	else
		alert('no action');
	}

function login_fields_toggle() {
	if ((document.getElementById('login_lab').style.display) == 'none') {
		if (document.getElementById('msg_err'))
			document.getElementById('msg_err').style.display = 'none';
		document.getElementById('password').style.display = 'none';
		document.getElementById('username_dg').style.display = 'none';
		document.getElementById('login_lab').style.display = 'block';
		document.getElementById('menu_lab').style.display = 'block';
		//  document.getElementById('login_key').style.display = 'inline-block';
		document.getElementById('signin_lab').style.display = 'block';
		}
	else {
		//document.getElementById('login_key').style.display = 'none';
		document.getElementById('login_lab').style.display = 'none';
		document.getElementById('signin_lab').style.display = 'none';
		document.getElementById('menu_lab').style.display = 'none';
		document.getElementById('password').style.display = 'block';
		document.getElementById('username_dg').style.display = 'block';
		document.getElementById('msg_err').style.display = 'block';
		}
	}

function vibe_rest(f, w) {  //  OBSOLETE ???
	//    FUTURE - add req parameter
	//    f   dynamic callback function
	//    w   timeout message string (optional)
	if (http_req_vb.readyState == 4 && http_req_vb.status == 200) {
		f();
//		alert(http_request.responseText);
		}
	else if (arguments.length > 1)
		alert(w); //  alert('There was a problem with the request');
	}

function vvv_rest(f, req, w) {
	//    f   dynamic callback function
	//    req http request object reference
	//    w   timeout message string (optional)
	if (req.readyState == 4 && req.status == 200)
		f();  //  alert(req.responseText);
	else if (arguments.length > 2)
		alert(w);  //  alert('There was a problem with the request');
	}

function more(s) {
	//  typically this is used to reveal/hide vocals block
	var ex = document.getElementById(s + '_more');
	var cm = document.getElementById(s + '_cmnt');
	var sm = document.getElementById(s + '_mor-');
	var sp = document.getElementById(s + '_mor+');
	var c, h;
	var min = '72px';
	var cmnt = 'inline';

	//  FUTURE - very very likely this expand/collapse behavior needs to evolve
//	alert(s + '_cmnt');
	c = cm.style.display;
//	alert('test 1');
	h = ex.style.height;
//	alert('test 2');
	if (h == min) {
		sm.style.display = 'none';
		sp.style.display = 'block';
		ex.style.height = 'auto';
		if (c == cmnt) cm.style.display = 'none';
		else cm.style.display = 'inline';
		}
	else {
		ex.style.height = min;
		cm.style.display = 'none';
		sm.style.display = 'block';
		sp.style.display = 'none';
		}
	}

function vocal(s, t) {
	//  Pass in artex id of asset being vocaled,
	//  trigger ajax request to adjust backend vocals/counts, adjust button state
	//    s   base DOM element id names
	//    t   asset type [ see VIBE_MODE_EXCH in PHP ]
	//  CITATION: http://www.javascriptkit.com/dhtmltutors/ajaxgetpost2.shtml
	var sh = ex_pre(t);
	var msg_e = document.getElementById(s + sh + '_msg');    //  new message text field
	msg_e.setAttribute("disabled", "true");
	var voc_e = document.getElementById(s + sh + '_cmnt');   //  new message text field
	var msg = encodeURIComponent(msg_e.value);
	if (t == 2) {
		var para = "fl_msg=" + msg + "&fl_param=" + s + "&fl_aud=artccmnt";
		url = "/rest/vocal.php?act=add&t=artc&aeid=" + s;
		}
	else if (t == 3) {
		var para = "fl_msg=" + msg + "&fl_param=" + s + "&fl_aud=wallcmnt";
		url = "/rest/vocal.php?act=add&t=wall&aeid=" + s;
		}
	else if (t == 4) {
		var para = "fl_msg=" + msg + "&fl_param=" + s + "&fl_aud=pivtcmnt";
		url = "/rest/vocal.php?act=add&t=pivt&aeid=" + s;
		}
	else {
		var para = "fl_msg=" + msg + "&fl_param=" + s + "&fl_aud=exchcmnt";
		url = "/rest/vocal.php?act=add&t=exch&aeid=" + s;
		}
	//  DANGER - do not NOT prepend with var, this will persist as global?
     	http_req_vo = new XMLHttpRequest();
	http_req_vo.onreadystatechange = function() { vvv_rest(function() {
		voc_e.innerHTML = http_req_vo.responseText;
		debug_refresh(s + '_debug');
		document.getElementById(s + sh + '_vbcm').innerHTML = document.getElementById(s + sh + '_nwvoct').innerHTML;
		}, http_req_vo); };
//	http_req_vo.open('GET', url, true);
	http_req_vo.open('POST', url, true);
	http_req_vo.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
//	http_req_vo.send(null);
	http_req_vo.send(para);
	}

function ex_pre(t) {
	//  different exchange data stores may present identical id's to javascript
	//  return exchange type prefix to help avoid this
	//  FUTURE - this needs to be harmaonized with PHP
	if (t == 3)
		return '_e';
	if (t == 4)
		return '_p';
	return '';
	}

function vibe(s, t) {
	//  Pass in artex id of asset being vibed,
	//  trigger ajax request to adjust backend vibe counts, adjust button state
	//    s   base DOM element id names
	//    t   asset type [ see VIBE_MODE_EXCH in PHP ]
//	alert('s: ' + s);
	var sh = ex_pre(t);
	var bt_e = document.getElementById(s + sh + '_vbbt');  //  vibe button logical state
	var ct_e = document.getElementById(s + sh + '_vbct');  //  aggregate vibe count
	var vb_e = document.getElementById(s + sh + '_vb');    //  vibe button label state
	var ct =   ct_e.innerHTML;
	var vl =   bt_e.innerHTML;
	var url;
	var vlb;

	//  do nothing if detect hold code,  previous button press AJAX is still pending completion
	if (vl < 2) {
		vb_e.innerHTML = ' ... ';  //  show 'busy'
		bt_e.innerHTML = 2;
		if (vl > 0) {  //  0 okay to vibe, 1 okay to unvibe
			vl--;
			ct_e.innerHTML--;
			url = "/rest/vibe.php?act=unvb&aeid=" + s + '&t=' + t;
			vlb = 'vibe';
			}
		else {
			vl++;
			ct_e.innerHTML++;
			url = "/rest/vibe.php?act=vibe&aeid=" + s + '&t=' + t;
			vlb = 'unvibe';
			}
		//  DANGER - do not NOT prepend with var, this will persist as global?
	       	http_req_vb = new XMLHttpRequest();
		http_req_vb.onreadystatechange = function() { vvv_rest(function() {
			document.getElementById(s + sh + '_vb').innerHTML = vlb;
			bt_e.innerHTML = vl;
			}, http_req_vb); };
//		alert(url);
		http_req_vb.open('GET', url, true);
		http_req_vb.send(null);
		}
	}

function wiz2(s) {
	//  Wizard div magic - to be called upon successful AJAX call
	//  Wizard leverages a structure of nested divs with particular ids.
	//  This is predicated on wiz_01_result being completely replaced upon success AJAX call prior to calling this fucntion.
	//  Upon success, delete wiz_01_body, then move wiz_next under wiz_01, rename wiz_01 id to wiz_01_body.
	//  wiz_01
	//    - wiz_01_result
	//        - generic status message
	//        - wiz_ok, only present if successful operation
	//        - wiz_next, contains complete form to display next
	//    - wiz_01_body
	if (document.getElementById("wiz_ok")) {
		var element;
		var fragment;

		fragment = document.createDocumentFragment();
		//  CITATION - http://stackoverflow.com/questions/3387427/remove-element-by-id
		element = document.getElementById("wiz_01_body");
		element.outerHTML = "";  //  this deletes dependent nodes?
		delete element;

		//  CITATION - http://stackoverflow.com/questions/1279957/how-to-move-an-element-into-another-element
		fragment.appendChild(document.getElementById('wiz_next'));
		document.getElementById('wiz_01').appendChild(fragment);
		document.getElementById('wiz_next').id = 'wiz_01_body'
		}
	}

function debug_refresh(id) {
	var dbg_e = document.getElementById('submit_image_results');
	var s = " <button style=\"margin: 0; position: absolute; top: 8px; right: 8px; display: inline-block;\" ";
	    s += "onclick=\"display_toggle('submit_image_results');\">d1smiss</button>";
	dbg_e.innerHTML = document.getElementById(id).innerHTML + s;
	dbg_e.style.display = 'block';  //  uncomment this to have dismisable yellow status box show after execution
	}

window.onresize = window_adjust;
window.onload = window_adjust;

//  responsive hint
mytake_winadj_bpw1 = 400;  //  window size above which to use multicolumn

/*Share by Email popup controlers
 * Validating Empty Field
 * Added: Josh N 8/3/2016
 */
function share_email_submit(eventObj) {
    eventObj.preventDefault();
    var shareForm = document.getElementById("shareEmailForm");
    var shareFormElements = shareForm.elements;
    
    for (var i = 0, input; input = shareFormElements[i++];) {
        if (input.value === "") {
            console.log(input.name + " is empty");
            var dontSend = true;
        }
    }
    if (dontSend === true) {
        document.getElementById("shareEmailError").innerHTML = "<span style=\"color:red;class:error\">Please fill all fields.</span>";
    } else {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(){share_email_response(xhr);};
        xhr.open (shareForm.method, shareForm.action, true);
        xhr.send (new FormData (shareForm));
    }
    return false;
}

function share_email_response(xhr) {
    var errorField = document.getElementById("shareEmailError")

    if(xhr.readyState == 4) {
        if(xhr.status == 200) {
            var result = JSON.parse(xhr.responseText);
            console.log(result);
            if (result["status"]=="success") {
                document.getElementById("shareEmailFields").innerHTML = result["msg"]; 
            } else {
                errorField.innerHTML = "<span style=\"color:red;class:error\">"+result["msg"]+"</span>";    
            }
            //document.getElementById("shareEmailPopup").innerHTML = xhr.responseText;
        } else {
            errorField.innerHTML = "<span style=\"color:red;class:error\">An error has occured, please try again later.</span>";
        }
     }
}


//Function To Display Popup
function show_share_popup() {
    document.getElementById('shareEmailPopup').style.display = "block";
}
//Function to Hide Popup
function hide_share_popup(){
document.getElementById('shareEmailPopup').style.display = "none";
}
