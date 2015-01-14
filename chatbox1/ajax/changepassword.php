<?php
session_start();
include("../include/sql_conn.php");
if(!isset($_SESSION['log_user'])){
	session_destroy();
	die(header("location: login.php"));
}

$log_user	= $_SESSION["log_user"];

$user_query = mysqli_query($mysql, "SELECT userid, screenname, password FROM user WHERE email = '".$log_user."'");
$user_fetch = mysqli_fetch_array($user_query);
if($user_fetch){
	$log_screen = $user_fetch["screenname"];
	$log_userid = $user_fetch["userid"];
	$log_password = $user_fetch["password"];
} else {
	session_destroy();
	die("Invalid Session!");
}

if(!isset($_POST["op"]) || !isset($_POST["np"])){
	die("Please fill all the input.");
}

$post_oldpass = $_POST["op"];
$post_newpass = $_POST["np"];

if($post_oldpass == $post_newpass){
	die("New Password must be different with Current Password!");
}

if(strlen($post_newpass) > 15 || strlen($post_newpass) < 5){
	die("Your password must be 5 to 15 character!");
}

$oldhash = md5($post_oldpass);
if($oldhash != $log_password){
	die("Current Password is invalid!");
}

$newhash = md5($post_newpass);
$changequery = mysqli_query($mysql, "UPDATE user SET password = '".$newhash."', lastactive = NOW() WHERE userid = " . $log_userid);
if($changequery){
	die("success");
} else {
	die("Failed to update. Try again later.");
}

?>