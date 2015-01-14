<?php
session_start();
include("include/sql_conn.php");
if(!isset($_SESSION['log_user'])){
	session_destroy();
	die(header("location: login.php"));
}

$log_user	= $_SESSION["log_user"];
$log_screen	= "";
$log_userid	= "";

$user_query = mysqli_query($mysql, "SELECT userid, screenname FROM user WHERE email = '".$log_user."'");
$user_fetch = mysqli_fetch_array($user_query);
if($user_fetch){
	$log_screen = $user_fetch["screenname"];
	$log_userid = $user_fetch["userid"];
} else {
	session_destroy();
	die(header("location: login.php"));
}




if(!isset($_SESSION['log_room'])){
	die(header("location: index.php"));
}

$chatid = $_SESSION['log_room'];

$roomquery = mysqli_query($mysql, "SELECT roomname, roompassword, roomowner FROM room WHERE roomid = ".$chatid);
if(mysqli_num_rows($roomquery) <= 0){
	$_SESSION['log_room'] = "";
	unset($_SESSION['log_room']);
	die(header("Location: index.php"));
}

$roomfetch = mysqli_fetch_array($roomquery);
$roompw  = $roomfetch["roompassword"];
$roomown = $roomfetch["roomowner"];
$roomname = $roomfetch["roomname"];

if($roomown == $log_userid){
	die(header("Location: chatroom.php?id=".$chatid));
}

// update last active
mysqli_query($mysql, "UPDATE user SET lastactive = NOW() WHERE userid = " . $log_userid);
?>
<!DOCTYPE html>
<html>
<head>
<title>ChatBin.us - Locked Room</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="style_login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="script.js"></script>
</head>

<body>

<!-- room password box -->
<div id="login">
<div class="popupbox" id="roompasswordbox" style="margin: 1px;">
    <div class="box_head" style="margin: 0;">Locked Room</div>
    <div class="box_error" id="rp_error"></div>
    <div style="width: 500px; margin: auto; margin-top: 20px; height: 200px;">
        <form action="javascript:void(0);" method="post" enctype="application/x-www-form-urlencoded" name="room_password" onSubmit="return enterRoomPassword(<?php echo $chatid; ?>)">
        <table cellpadding="5" cellspacing="5" width="500" align="left">        
            <tr>
                <th align="left" width="150" valign="top">Room Name:</th>
                <td align="left" width="300"><input type="text" class="textbox" name="roomname" style="width: 290px" value="<?php echo $roomname; ?>" readonly /></td>
            </tr>
            <tr>
                <th align="left" valign="top">Room Password:</th>
                <td align="left"><input type="password" class="textbox" name="password" style="width: 290px" maxlength="15" /></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><br /><input type="submit" value="Enter Room" class="button" name="enter" /> <input type="button" value="Leave Room" class="button" name="leave" onClick="location.href = 'leaveroom.php'" /></td>
            </tr>
        </table>
        </form>
    </div>
</div> 
</div>
</body>
</html>