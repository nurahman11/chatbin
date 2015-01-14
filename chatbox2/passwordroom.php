<?php
if(!isset($_GET["x"])){
	die(header("location: login.bc"));
} elseif($_GET["x"] != 111){
	die(header("location: login.bc"));
}

session_start();
include("include/sql_conn.php");
include("include/function.php");
if(!isset($_SESSION['log_user'])){
	session_destroy();
	die(header("location: login.bc"));
}

$log_user	= $_SESSION["log_user"];
$log_screen	= "";
$log_userid	= "";

$user_query = mysqli_query($mysql, "SELECT userid, screenname FROM user WHERE email = '".sql_req_esc($mysql, $log_user)."'");
$user_fetch = mysqli_fetch_array($user_query);
if($user_fetch){
	$log_screen = $user_fetch["screenname"];
	$log_userid = $user_fetch["userid"];
} else {
	session_destroy();
	die(header("location: login.bc"));
}

if(!isset($_SESSION['log_room']) || !isset($_SESSION['log_roomname']){
	die(header("location: room.bc"));
}

$chatid = $_SESSION['log_room'];

$roomquery = mysqli_query($mysql, "SELECT roomname, roompassword, roomowner FROM room WHERE roomid = ".sql_req_esc($mysql, $chatid));
if(mysqli_num_rows($roomquery) != 1){
	$_SESSION['log_room'] = "";
	$_SESSION['log_roomname'] = "";
	unset($_SESSION['log_room'], $_SESSION['log_roomname']);
	die(header("Location: room.bc"));
}

$roomfetch = mysqli_fetch_array($roomquery);
$roompw  = $roomfetch["roompassword"];
$roomown = $roomfetch["roomowner"];
$roomname = $roomfetch["roomname"];

if($roomown == $log_userid){
	die(header("location: ".$_SESSION['log_room']."-".$_SESSION['log_roomname'].".bc"));
}

// update last active
mysqli_query($mysql, "UPDATE user SET lastactive = NOW() WHERE userid = " . sql_req_esc($mysql, $log_userid));
?>
<!DOCTYPE html>
<html>
<head>
<title>ChatBin.us - Room List</title>
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
        <form action="javascript:void(0);" method="post" enctype="application/x-www-form-urlencoded" name="room_password" onSubmit="return enterRoomPassword(<?php echo html_esc($chatid); ?>)">
        <table cellpadding="5" cellspacing="5" width="500" align="left">        
            <tr>
                <th align="left" width="150" valign="top">Room Name:</th>
                <td align="left" width="300"><input type="text" class="textbox" name="roomname" style="width: 290px" value="<?php echo html_esc($roomname); ?>" readonly /></td>
            </tr>
            <tr>
                <th align="left" valign="top">Room Password:</th>
                <td align="left"><input type="password" class="textbox" name="password" style="width: 290px" maxlength="15" /></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><br /><input type="submit" value="Enter Room" class="button" name="enter" /> <input type="button" value="Leave Room" class="button" name="leave" onClick="location.href = 'leave.bc'" /></td>
            </tr>
        </table>
        </form>
    </div>
</div> 
</div>
</body>
</html>