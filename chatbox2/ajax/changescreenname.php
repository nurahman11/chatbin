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

if(!isset($_POST["sn"]) || !isset($_POST["pw"])){
	die("Please fill all the input.");
}

$post_screenname = $_POST["sn"];
$post_password = $_POST["pw"];

if(strlen($post_screenname) > 15 || strlen($post_screenname) < 4){
	die("Screen Name must be 4 to 15 character!");
}

if(!ctype_alnum($post_screenname)){
	die("Screen Name must be alphanumeric only!");
}

if($post_screenname == $log_screen){
	die("New Screen Name must be different with Current Screen Name!");
}

$check_screen = mysqli_num_rows(mysqli_query($mysql, "SELECT userid FROM user WHERE screenname = '".sql_req_esc($mysql, $post_screenname)."'"));
if($check_screen > 0){
	die("Screen name is already used, please insert another name.");
}

$pwdhash = md5(strrev($post_password.$log_user));
if($pwdhash != $log_password){
	die("Current Password is invalid!");
}

$changequery = mysqli_query($mysql, "UPDATE user SET screenname = '".sql_req_esc($mysql, $post_screenname)."', lastactive = NOW() WHERE userid = " . sql_req_esc($mysql, $log_userid));
if($changequery){
	die("success");
} else {
	die("Failed to update. Try again later.");
}

?>