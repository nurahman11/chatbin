<?php
session_start();
include("../include/sql_conn.php");
include("../include/function.php");

if(!isset($_SESSION['log_user'])){
	session_destroy();
	die(header("location: login.php"));
}

$log_user	= $_SESSION["log_user"];

$user_query = mysqli_query($mysql, "SELECT userid, screenname, password FROM user WHERE email = '".sql_req_esc($mysql, $log_user)."'");
$user_fetch = mysqli_fetch_array($user_query);
if($user_fetch){
	$log_screen = $user_fetch["screenname"];
	$log_userid = $user_fetch["userid"];
	$log_password = $user_fetch["password"];
} else {
	session_destroy();
	die("Invalid Session!");
}

if(!isset($_POST["rn"]) || !isset($_POST["pw"]) ){
	die("Please insert room name.");
}

$post_roomname	= $_POST["rn"];
$post_password	= $_POST["pw"];
$post_hidden	= (isset($_POST["hidden"])) ? $_POST["hidden"] : 0;

if(strlen($post_roomname) > 30 || strlen($post_roomname) < 6){
	die("Room Name must be 6 to 30 character!");
}

if($post_password != "" && (strlen($post_password) > 15 || strlen($post_password) < 5)){
	die("Room password must be 5 to 15 character!");
}

$hash = "";
if($post_password != ""){
	$hash = md5(strrev($post_password));
}

$createroom = mysqli_query($mysql, "INSERT INTO room (roomowner, roomname, roompassword, hidden) VALUES (".sql_req_esc($mysql, $log_userid).", '".sql_req_esc($mysql, $post_roomname)."', '".sql_req_esc($mysql, $hash)."', ".sql_req_esc($mysql, $post_hidden).")");
if($createroom){
	$lastid = mysqli_insert_id($mysql);
	mysqli_query($mysql, "INSERT INTO chat (roomid, userid, message) VALUES (".sql_req_esc($mysql, $lastid).",0,'".sql_req_esc($mysql, $log_screen)." created the room.')");
	echo $lastid;
	die();
} else {
	die("Failed to create room. Try again later.");
}

?>