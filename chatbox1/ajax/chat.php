<?php
session_start();
include("../include/sql_conn.php");

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
	die("-");
}

if(!isset($_SESSION['log_room'])){
	die("-");
}

$c_id = $_SESSION['log_room'];

$room = mysqli_query($mysql, "SELECT roomowner FROM room WHERE roomid = ".$c_id);
if(mysqli_num_rows($room) != 1){
	die("not found");
}

$room_arr = mysqli_fetch_array($room);
$room_owner = $room_arr["roomowner"];

if(isset($_POST['tx'])){
	$c_msg = $_POST['tx'];
	
	if($c_msg != ""){
		mysqli_query($mysql, "INSERT INTO chat (roomid, userid, message) VALUES (".$c_id.", ".$log_userid.", '".$c_msg."')");
		mysqli_query($mysql, "UPDATE user SET lastactive = NOW() WHERE userid = ".$log_userid);
	}
	
	if($log_userid == $room_owner && substr($c_msg,0,6) == "/kick "){
		$kicked_user = substr($c_msg,6);
		mysqli_query($mysql, "INSERT INTO chat (roomid, userid, message) VALUES (".$c_id.", 0, 'User \"".$kicked_user."\" kicked.')");
	}
}

$o_last = 0;
if(isset($_POST['lm'])){
	$o_last = $_POST['lm'];
}

mysqli_query($mysql, "DELETE FROM chat WHERE time < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

$o_sql = "";

if(isset($_POST['f'])){
	$o_sql = "SELECT * FROM (";
}

$o_sql .= "SELECT chatid, screenname, c.userid, message, HOUR(time) AS timehour, MINUTE(time) AS timeminute, SECOND(time) AS timesecond ";
$o_sql .= "FROM chat c, user u WHERE c.userid = u.userid AND roomid = ".$c_id." AND chatid > ".$o_last;

if(isset($_POST['f'])){
	$o_sql .= " ORDER BY chatid DESC LIMIT 10) ch ORDER BY ch.chatid";
}

$o_sql = mysqli_query($mysql, $o_sql);

$addText = "ok\n";
while($o_arr = mysqli_fetch_array($o_sql)){
	
	// check kick user
	if($o_arr["userid"] == $room_owner && $o_arr["message"] == "/kick ".$log_screen){
		die("kicked");
	} elseif($o_arr["userid"] == $room_owner && substr($o_arr["message"],0,6) == "/kick "){
		continue;
	}
	
	$addText .= $o_arr["chatid"];
	$addText .= "\n";
	
	$addText .= ($o_arr["timehour"] < 10) ? "0".$o_arr["timehour"] : $o_arr["timehour"];
	$addText .= ":";
	$addText .= ($o_arr["timeminute"] < 10) ? "0".$o_arr["timeminute"] : $o_arr["timeminute"];
	$addText .= ":";
	$addText .= ($o_arr["timesecond"] < 10) ? "0".$o_arr["timesecond"] : $o_arr["timesecond"];
	$addText .= "\n";
	

	if($o_arr["userid"] == $room_owner){
		$addText .= "<font color=\"#6D8AEF\">";
		$addText .= $o_arr["screenname"];
		$addText .= "</font>";
	} else if($o_arr["userid"] == $log_userid) {
		$addText .= "<font color=\"#367E21\">";
		$addText .= $o_arr["screenname"];
		$addText .= "</font>";
	} else {
		$addText .= $o_arr["screenname"];
	}
	$addText .= "\n";
	
	$chat_message = $o_arr["message"];	
	$chat_message = preg_replace("/([[:space:]])((f|ht)tps?:\/\/[a-z0-9~#%@\&:=?+\/.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)/i"," <a href=\"\\2\" target=\"_blank\">\\2</a>",$chat_message);
	$chat_message = preg_replace("/([[:space:]])(www\.[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)/i"," <a href=\"http://\\2\" target=\"_blank\">\\2</a>",$chat_message);
	$chat_message = preg_replace("/^((f|ht)tp:\/\/[a-z0-9~#%@\&:=?+\/.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)/i","<a href=\"\\1\" target=\"_blank\">\\1</a>",$chat_message);
	$chat_message = preg_replace("/^(www\.[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_.;-]+)/i","<a href=\"http://\\1\" target=\"_blank\">\\1</a>",$chat_message);
	
	if($o_arr["screenname"] == "[BOT]"){
		$addText .= "<font color=\"#8C8C8C\"><em>";
		$addText .= str_replace("\n","<br />", $chat_message);
		$addText .= "</em></font>";
	} else {
		$addText .= str_replace("\n","<br />", $chat_message);
	}
	$addText .= "\n";
}

echo $addText;
?>