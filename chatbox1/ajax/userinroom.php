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
	die("not found");
}

if(!isset($_SESSION['log_room'])){
	die("not found");
}

$c_id = $_SESSION['log_room'];

$room = mysqli_query($mysql, "SELECT roomowner FROM room WHERE roomid = ".$c_id);
if(mysqli_num_rows($room) != 1){
	die("not found");
}

$room_arr = mysqli_fetch_array($room);
$room_owner = $room_arr["roomowner"];

$useronline_query  = "SELECT screenname FROM user u, chat c ";
$useronline_query .= "WHERE u.userid = c.userid ";
$useronline_query .= "AND time > DATE_SUB(NOW(), INTERVAL 5 MINUTE) ";
$useronline_query .= "AND roomid = ".$c_id;
$useronline_query .= " AND u.userid <> 0 ";
$useronline_query .= "GROUP BY screenname ORDER BY screenname";
$useronline_query  = mysqli_query($mysql, $useronline_query);
$useronline_count  = mysqli_num_rows($useronline_query);
echo "        	";
echo $useronline_count." User";
if($useronline_count > 1){
	echo "s";
}
echo " Online<br />";

$useronline_count = 0;
?>
            <div style="overflow: auto; height: 300px; margin-top: 5px;">
<?php

	while($useronline_fetch = mysqli_fetch_array($useronline_query)){
		$useronline_count++;
		
		$modcolor = "";
		if($useronline_count%2 != 0){
			$modcolor = " background-color: #A5D3A6;";
		}
?>
				<div style="padding: 5px 7px;  overflow: hidden;<?php echo $modcolor; ?>"><?php
					if($useronline_fetch["screenname"] == $room_owner){
						echo "<font color=\"#6D8AEF\">".$log_screen."</font>";
					} elseif($useronline_fetch["screenname"] == $log_screen){
						echo "<strong>".$log_screen."</strong>";
					} else {
						echo $useronline_fetch["screenname"]; 
					}
				?></div>
<?php
	}
?>
			</div>