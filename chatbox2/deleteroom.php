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

if(!isset($_SESSION['log_room']) || !isset($_SESSION['log_roomname'])){
	die(header("Location: room.bc"));
}

$roomid = $_SESSION['log_room'];

$roomquery = mysqli_query($mysql, "SELECT roomname, roompassword, roomowner FROM room WHERE roomid = ".sql_req_esc($mysql, $roomid));
if(mysqli_num_rows($roomquery) != 1){
	$_SESSION['log_room'] = "";
	$_SESSION['log_roomname'] = "";
	unset($_SESSION['log_room'], $_SESSION['log_roomname']);
	die(header("Location: room.bc"));
}

$roomfetch = mysqli_fetch_array($roomquery);
$roomown = $roomfetch["roomowner"];

if($roomown != $log_userid){
	die(header("location: ".$_SESSION['log_room']."-".$_SESSION['log_roomname'].".bc"));
}

mysqli_query($mysql, "DELETE FROM chat WHERE roomid = ".sql_req_esc($mysql, $roomid));	
mysqli_query($mysql, "DELETE FROM room WHERE roomid = ".sql_req_esc($mysql, $roomid));	
	
$_SESSION['log_room'] = "";
$_SESSION['log_roomname'] = "";
unset($_SESSION['log_room'], $_SESSION['log_roomname']);;

header("location: room.bc");
?>