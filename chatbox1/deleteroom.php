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
	die(header("Location: index.php"));
}

$roomid = $_SESSION['log_room'];

$roomquery = mysqli_query($mysql, "SELECT roomname, roompassword, roomowner FROM room WHERE roomid = ".$roomid);
if(mysqli_num_rows($roomquery) != 1){
	$_SESSION['log_room'] = "";
	unset($_SESSION['log_room']);
	die(header("Location: index.php"));
}

$roomfetch = mysqli_fetch_array($roomquery);
$roomown = $roomfetch["roomowner"];

if($roomown != $log_userid){
	die(header("Location: chatroom.php?id=".$chatid));
}

mysqli_query($mysql, "DELETE FROM chat WHERE roomid = ".$roomid);	
mysqli_query($mysql, "DELETE FROM room WHERE roomid = ".$roomid);	
	
$_SESSION['log_room'] = "";
unset($_SESSION['log_room']);

header("location: index.php");
?>