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
} else {
	session_destroy();
	die("Invalid Session!");
}

if(!isset($_POST["id"]) || !isset($_POST["pw"])){
	die("Please fill all the input.");
}

$post_id = $_POST["id"];
$post_password = $_POST["pw"];

$hash = md5($post_password);

$check_room = mysqli_query($mysql, "SELECT roompassword FROM room WHERE roomid = ".$post_id);
if(mysqli_num_rows($check_room) != 1){
	die("false");
}

$fetch_room = mysqli_fetch_array($check_room);
if($hash == $fetch_room["roompassword"] || $fetch_room["roompassword"] == ""){
	die("true");
} else {
	die("false");
}
?>