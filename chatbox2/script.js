// Room List (Index) Stuff..
var timerTick;
var timerSecond = 0;

function timerRefresh(bypass){
	
	if(bypass === undefined) { bypass = false; }
	
	var timeout_second = 60;
	timerSecond++;
	
	if(timerSecond > timeout_second || bypass){
		if(document.searchformhidden.searchtxt.value == ""){
			location.href = location.href;
		} else {
			document.searchformhidden.submit();
		}
	} else {
		timerTick = setTimeout("timerRefresh()", 1000);
	}
}

function timerPause(stat){
	
	if(stat === undefined) { stat = true; }
	
	if(stat){
		clearInterval(timerTick);	
	} else {
		timerRefresh();
	}
}

function positionHideBoxOnCenter(){
	var hb = document.getElementById("hiddenbox");	
	
	var sizevertical = (window.innerHeight / 2) - ((hb.clientHeight / 2));
	if(sizevertical < 30) {
		sizevertical = 30;
	}
	hb.style.top = (sizevertical-20) + "px";
	
	var sizehorizontal = (window.innerWidth / 2) - ((hb.clientWidth / 2));
	if(sizehorizontal < 10) {
		sizehorizontal = 10;
	}
	hb.style.left = sizehorizontal + "px";
}

function hideAllHiddenBox(){
	// start refresh timer
	timerPause(false);
	
	// hide big black box
	document.getElementById("blackbox").style.display = "none";
	
	// hide hiddenbox
	document.getElementById("hiddenbox").style.display = "none";
	
	// hide element box
	document.getElementById("createroom").style.display = "none";
	document.getElementById("changepassword").style.display = "none";
	document.getElementById("changescreenname").style.display = "none";
	document.getElementById("roompasswordbox").style.display = "none";
	
	// reset all form
	document.create_room.reset();
	document.change_password.reset();
	document.change_screenname.reset();
	document.room_password.reset();
	
	// clearing error
	document.getElementById("cr_error").innerHTML = "";
	document.getElementById("cp_error").innerHTML = "";
	document.getElementById("cs_error").innerHTML = "";
	document.getElementById("rp_error").innerHTML = "";
	
	// returning height
	document.getElementById("createroom").style.height = "400px";
	document.getElementById("changepassword").style.height = "320px";
	document.getElementById("changescreenname").style.height = "250px";
	document.getElementById("roompasswordbox").style.height = "250px";
	
	// overflow body
	document.body.style.overflow = "";
}

function showHiddenBox(id){
	// clear refresh timer
	timerPause();
	
	// show big black box
	document.getElementById("blackbox").style.display = "block";
	
	// show hiddenbox
	document.getElementById("hiddenbox").style.display = "block";
	
	// show other box
	document.getElementById(id).style.display = "block";
	
	// Change position
	positionHideBoxOnCenter();
	
	// overflow body
	document.body.style.overflow = "hidden";
}

function goRoomPassword(id, rm){
	document.room_password.setAttribute("action","javascript:void(0)");	
	document.room_password.setAttribute("onSubmit","enterRoomPassword('"+id+"','"+rm+"')");
	document.room_password.roomname.value = rm;
	showHiddenBox("roompasswordbox");
	document.room_password.password.focus();
}

// Chat Stuff 
//		Text Area Style
keyShift = false;

function textareaOnKeyDown(field,event){
	var theCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if(theCode == 16){
		keyShift = true;
	} else if(theCode == 13 && !keyShift){
		field.blur();
		sendChat();
	}
}

function textareaOnKeyUp(field,event){
	var theCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if(theCode == 16){
		keyShift = false;
	}			
}

// AJAX!
var ajax = new createAjax();
var ajaxUser = new createAjax();
var ajaxLastMsg = 0;
var ajaxTimer = 0;
var timeoutCHAT = 800;
var handleTimeoutChat;

function createAjax(){
	if(window.XMLHttpRequest){
		ajaxHTTP = new XMLHttpRequest();
		if (ajaxHTTP.overrideMimeType) {
			ajaxHTTP.overrideMimeType('text/html');
		}
	}
	else if(window.ActiveXObject){ 
		try {
			ajaxHTTP = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				ajaxHTTP = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	} else {
		ajaxHTTP = false;
		alert("Kamu menggunakan browser lama!\nUpdate browser kamu untuk melihat chatbox!");
	}
	return ajaxHTTP;
}

function sendChat(){
	clearInterval(handleTimeoutChat);
	
	var text = document.getElementById('c_text').value.replace(/^\s+|\s+$/g, "");
	
	if(text === ""){
		document.getElementById('c_text').focus();
		return false;
	}
	
	text = encodeURIComponent(text);
	setTimeout("document.getElementById('c_text').value = '';",10);
	setTimeout("document.getElementById('c_text').focus();",11);
	
	var param = 'tx='+text;
	param += '&lm='+ajaxLastMsg;
	ajax.open("POST","ajax/chat.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = createChat;
}

function receiveUserChat(){	
	var param = "";
	ajaxUser.open("POST","ajax/userinroom.php", true);
	ajaxUser.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajaxUser.setRequestHeader("Content-Length",param.length);
	ajaxUser.setRequestHeader("Connection","close");
	ajaxUser.send(param);
	ajaxUser.onreadystatechange = function(){
		if(ajaxUser.readyState == 4 && ajaxUser.status == 200){
			var ajax_text = ajaxUser.responseText;
			document.getElementById("userlistbox").innerHTML = ajax_text;
			setTimeout("receiveUserChat()", 10*1000)
		}
	};
}

function receiveChat(){
	clearInterval(handleTimeoutChat);
	
	var param = 'lm='+ajaxLastMsg;
	ajax.open("POST","ajax/chat.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = createChat;
}

function receiveFirstChat(){
	clearInterval(handleTimeoutChat);
	
	var param	 = 'lm='+ajaxLastMsg;
	param		+= '&f=1';
	ajax.open("POST","ajax/chat.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = createChat;
}

function createChat(){
	if(ajax.readyState == 4 && ajax.status == 200){
		var ajax_text = ajax.responseText;
		var ajax_text_array = ajax_text.split("\n");
		
		if(ajax_text_array[0] != "ok"){
			if(ajax_text_array[0] === "not found"){
				alert("Room not found!");
				location.href = "leave.bc";
			} else if(ajax_text_array[0] === "kicked"){
				alert("Kicked by Room Owner!");
				location.href = "leave.bc";
			}
		} else {
			var objDiv = document.getElementById("c_list");
			
			var addText = "";
			var i = 1;
			
			for(i; i < (ajax_text_array.length-1); i=i+4){
				ajaxLastMsg = ajax_text_array[i];
				var chat_time = ajax_text_array[i+1];
				var chat_username = ajax_text_array[i+2];
				var chat_message = ajax_text_array[i+3];				
								
				addText += "\n		";
				addText += "<div class=\"list_username\" id=\"wtmp\">";
				addText += "<font color=\"#6A6A6A\">["+chat_time+"]</font>"
				if(chat_username != "[BOT]"){
					addText += " <strong>"+chat_username+"</strong> : ";
				}
				addText += "</div>";
				
				objDiv.innerHTML += addText;
				var margin = document.getElementById("wtmp").clientWidth + 5 + "px";
				document.getElementById("wtmp").removeAttribute("id");
				
				addText  = "\n		";
				addText += "<div class=\"list_message\" style=\"margin-left: "+margin+"\">";
				addText += chat_message;
				addText += "</div>";
				addText += "\n		";
				addText += "<div class=\"clear\"></div>";
				addText += "\n		";			
				
			}
			
			objDiv.innerHTML += addText;	
			if(addText != ""){
				objDiv.scrollTop = objDiv.scrollHeight;
			}
			
			handleTimeoutChat = setTimeout("receiveChat()",timeoutCHAT);
		}
	}
}

// AJAX for Index
function createChatRoom(){
	
	var roomname	= document.create_room.roomname.value;
	var password	= document.create_room.password.value;
	var password2	= document.create_room.password2.value;
	var hidden		= document.create_room.hiddenroom.checked;
	
	document.getElementById("createroom").style.height = "450px";
	
	if(roomname == ""){
		document.getElementById("cr_error").innerHTML = "Please enter room name!";
		return false;
    } else if(roomname.length < 6 || roomname.length > 30){
		document.getElementById("cr_error").innerHTML = "Room Name must be 6 to 30 character!";
		document.create_room.password.select();
		document.create_room.password.focus();
		return false;
	}  else if(password != "" && (password.length < 5 || password.length > 15)){
		document.getElementById("cr_error").innerHTML = "Password must be 5 to 15 character!";
		document.create_room.password.select();
		document.create_room.password.focus();
		return false;
	} else if(password != "" && password != password2){
		document.getElementById("cr_error").innerHTML = "Confirm Password must be same with Room Password!";
		document.create_room.password2.select();
		document.create_room.password2.focus();
		return false;
	} else if(hidden && password === ""){
		document.getElementById("cr_error").innerHTML = "Password needed if you want to create Hidden Room!";
		document.create_room.password.select();
		document.create_room.password.focus();
		return false;
	}
	
	document.getElementById("cr_error").innerHTML = "<font color=\"#28934E\">Sending data, please wait...</font>";
	document.create_room.createnow.focus();
	document.create_room.createnow.disabled = true;
	
	// Encode to URI
	roomname	= encodeURIComponent(roomname);
	password	= encodeURIComponent(password);
	
	var param;
	param = 'rn='+roomname;
	param += '&pw='+password;
	if(hidden){
		param += '&hidden=1';
	}
	
	ajax.open("POST","ajax/createroom.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4 && ajax.status == 200){
			ajax_text = ajax.responseText;
			if(!isNaN(ajax_text) && parseInt(ajax_text) > 0){
				roomname_url = decodeURIComponent(roomname).replace(/[^a-zA-Z0-9]/g, " ").replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,'-').toLowerCase();
				var locto = ajax_text+"-"+roomname_url+".bc";
				location.href = locto;
			} else {
				document.getElementById("cr_error").innerHTML = ajax_text;
				document.create_room.createnow.disabled = false;
			}
		}	
	};
}

function changePassword(){
	
	var oldpass = document.change_password.oldpass.value;
	var newpass = document.change_password.password.value;
	var newpass2 = document.change_password.password2.value;
	
	document.getElementById("changepassword").style.height = "370px";
	
	if(oldpass == "" || newpass == "" || newpass2 == ""){
		document.getElementById("cp_error").innerHTML = "Please fill all the input!";
		return false;
	} else if(oldpass.length < 5 || oldpass.length > 15){
		document.getElementById("cp_error").innerHTML = "Current Password must be 5 to 15 character!";
		document.change_password.oldpass.select();
		document.change_password.oldpass.focus();
		return false;
	} else if (oldpass === newpass){
		document.getElementById("cp_error").innerHTML = "New Password must be different with Current Password!";
		document.change_password.password.select();
		document.change_password.password.focus();
		return false;
	} else if(newpass.length < 5 || newpass.length > 15){
		document.getElementById("cp_error").innerHTML = "New Password must be 5 to 15 character!";
		document.change_password.password.select();
		document.change_password.password.focus();
		return false;
	} else if (newpass != newpass2){
		document.getElementById("cp_error").innerHTML = "Confirm New Password must be same with New Password!";
		document.change_password.password2.select();
		document.change_password.password2.focus();
		return false;
	}
	
	document.getElementById("cp_error").innerHTML = "<font color=\"#28934E\">Sending data, please wait...</font>";
	document.change_password.changenow.focus();
	document.change_password.changenow.disabled = true;
	
	// Encode to URI
	oldpass = encodeURIComponent(oldpass);
	newpass = encodeURIComponent(newpass);	
	
	var param;
	param = 'op='+oldpass;
	param += '&np='+newpass;
	ajax.open("POST","ajax/changepassword.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4 && ajax.status == 200){
			ajax_text = ajax.responseText;
			if(ajax_text === "success"){
				document.getElementById("cp_error").innerHTML = "<font color=\"#28934E\">Success changing password.</font>";
				document.change_password.oldpass.value = "";
				document.change_password.password.value = "";
				document.change_password.password2.value = "";
				setTimeout("timerRefresh(true);", 1000);
			} else {
				document.getElementById("cp_error").innerHTML = ajax_text;
				document.change_password.changenow.disabled = false;
			}
		}	
	};
}

function changeScreenName(){
	
	var screenname	= document.change_screenname.screenname.value;
	var screenold	= document.change_screenname.screennameold.value;
	var password	= document.change_screenname.password.value;
	
	document.getElementById("changescreenname").style.height = "300px";
	
	if(screenname == "" || password == ""){
		document.getElementById("cs_error").innerHTML = "Please fill all the input!";
		return false;
	} else if (screenname === screenold){
		document.getElementById("cs_error").innerHTML = "New Screen Name must be different with Current Screen Name!";
		document.change_screenname.screenname.select();
		document.change_screenname.screenname.focus();
		return false;
	} else if(screenname.length < 4 || screenname.length > 15){
		document.getElementById("cs_error").innerHTML = "Screen Name must be 4 to 15 character!";
		document.change_screenname.screenname.select();
		document.change_screenname.screenname.focus();
		return false;
	} else if(/[^a-zA-Z0-9]/.test(screenname)) {
		document.getElementById("cs_error").innerHTML = "Screen Name must be alphanumeric only!";
		document.change_screenname.screenname.select();
		document.change_screenname.screenname.focus();
       return false;
    } else if(password.length < 5 || password.length > 15){
		document.getElementById("cs_error").innerHTML = "Password must be 5 to 15 character!";
		document.change_screenname.password.select();
		document.change_screenname.password.focus();
		return false;
	}
	
	document.getElementById("cs_error").innerHTML = "<font color=\"#28934E\">Sending data, please wait...</font>";
	document.change_screenname.changenow.focus();
	document.change_screenname.changenow.disabled = true;
	
	// Encode to URI
	screenname	= encodeURIComponent(screenname);
	password	= encodeURIComponent(password);
	
	var param;
	param = 'sn='+screenname;
	param += '&pw='+password;
	ajax.open("POST","ajax/changescreenname.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4 && ajax.status == 200){
			ajax_text = ajax.responseText;
			if(ajax_text === "success"){
				document.getElementById("cs_error").innerHTML = "<font color=\"#28934E\">Success changing screen name.</font>";
				document.change_screenname.screenname.value = "";
				document.change_screenname.password.value = "";
				setTimeout("timerRefresh(true);", 1000);
			} else {
				document.getElementById("cs_error").innerHTML = ajax_text;
				document.change_screenname.changenow.disabled = false;
			}
		}	
	};
}

function enterRoomPassword(id, rm){
	var password	= document.room_password.password.value;
	
	document.getElementById("roompasswordbox").style.height = "300px";
	
	if(password == ""){
		document.getElementById("rp_error").innerHTML = "Please insert the room password!";
		return false;
    } else if(password.length < 5 || password.length > 15){
		document.getElementById("rp_error").innerHTML = "Password must be 5 to 15 character!";
		document.room_password.password.select();
		document.room_password.password.focus();
		return false;
	}
	
	document.getElementById("rp_error").innerHTML = "<font color=\"#28934E\">Sending data, please wait...</font>";
	document.room_password.enter.focus();
	document.room_password.enter.disabled = true;
	
	// Encode to URI
	id			= encodeURIComponent(id);
	password	= encodeURIComponent(password);
	
	var param;
	param = 'id='+id;
	param += '&pw='+password;
	ajax.open("POST","ajax/checkroompassword.php", true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-Length",param.length);
	ajax.setRequestHeader("Connection","close");
	ajax.send(param);
	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4 && ajax.status == 200){
			ajax_text = ajax.responseText;
			if(ajax_text === "true"){
				roomname = rm.replace(/[^a-zA-Z0-9]/g, " ").replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,'-').toLowerCase();
				document.room_password.setAttribute("action",id+"-"+roomname+".bc");	
				document.room_password.removeAttribute("onSubmit");
				document.room_password.submit();
			} else {
				document.getElementById("rp_error").innerHTML = "Invalid Password!";
				document.room_password.password.select();
				document.room_password.password.focus();
				document.room_password.enter.disabled = false;
			}
		}	
	};
}