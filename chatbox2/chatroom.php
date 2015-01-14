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

if(!isset($_GET["id"]) && !isset($_GET["rn"])){
	die(header("location: room.bc"));
}

$chatid = $_GET["id"];
$roomnm = $_GET["rn"];

if(!is_numeric($chatid)){
	die(header("location: room.bc"));
} elseif(!preg_match("/^[a-zA-Z0-9-]+$/", $roomnm)){
	die(header("location: room.bc"));
}

if(isset($_SESSION['log_room']) || isset($_SESSION['log_roomname'])){
	if($_SESSION['log_room'] != $chatid){
		die(header("location: ".$_SESSION['log_room']."-".$_SESSION['log_roomname'].".bc"));
	}
} else {
	$_SESSION['log_room'] = $chatid;
	$_SESSION['log_roomname'] = $roomnm;
}

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

if($roomnm != roomname_esc($roomname)){
	$_SESSION['log_room'] = "";
	$_SESSION['log_roomname'] = "";
	unset($_SESSION['log_room'], $_SESSION['log_roomname']);
	die(header("Location: room.bc"));
}


if($roompw != "" && $roomown != $log_userid){
	if(!isset($_POST["password"])){
		die(header("location: locked.bc"));
	}
	
	$pwhash = md5(strrev($_POST["password"]));
	if($pwhash != $roompw){
		die(header("location: locked.bc"));
	}
}

// update last active
mysqli_query($mysql, "UPDATE user SET lastactive = NOW() WHERE userid = " . sql_req_esc($mysql, $log_userid));
?>
<!DOCTYPE html>
<html>
<head>
<title>ChatBin.us - <?php echo $roomname; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="script.js"></script>
<script>setTimeout("receiveUserChat()", 10*1000)</script>
</head>

<body onUnload="clearInterval(handleTimeoutChat);">
<div id="headlogo"></div>
<div id="floatdiv">
    <div id="roomlist">
        <div class="box_head"><?php echo html_esc($roomname); ?></div>
        <div id="c_list" class="autohidescroll">
			<script>receiveFirstChat()</script>
        </div>
       	<textarea name="c_text" id="c_text" cols="3" onKeyDown="textareaOnKeyDown(this,event)" onKeyUp="textareaOnKeyUp(this,event)"></textarea><br />
    </div>
    <div id="infobox">
        <div class="box_head" style="padding-left: 10px;">Room Manage</div>
        <div style="margin: 10px;">
            <strong><?php echo html_esc($log_screen); ?></strong><br /><br />
            &raquo; <a href="leave.bc">Leave Room</a><br />
            <?php if($log_userid == $roomown) { ?>
            &raquo; <a href="delete.bc">Delete Room</a><br />
            <?php } ?>
            &raquo; <a href="logout.bc">Logout</a><br />
        </div>
    </div>
    <div id="userlist">
    	<div class="box_head" style="padding-left: 10px;">Online List</div>
        <div style="margin: 10px;" id="userlistbox">
        	<?php
				$useronline_query  = "SELECT screenname, u.userid FROM user u, chat c ";
				$useronline_query .= "WHERE u.userid = c.userid ";
				$useronline_query .= "AND time > DATE_SUB(NOW(), INTERVAL 5 MINUTE) ";
				$useronline_query .= "AND roomid = ".sql_req_esc($mysql, $chatid);
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
            <div style="height: 300px; margin-top: 5px;" class="autohidescroll">
            <?php
				while($useronline_fetch = mysqli_fetch_array($useronline_query)){
					$useronline_count++;
					
					$modcolor = "";
					if($useronline_count%2 != 0){
						$modcolor = " background-color: #A5D3A6;";
					}
			?>
            	<div style="padding: 5px 7px;  overflow: hidden;<?php echo $modcolor; ?>">
				<?php
					if($useronline_fetch["screenname"] == $log_screen){
						echo "<strong>".html_esc($log_screen)."</strong>";
					} else {
                		echo html_esc($useronline_fetch["screenname"]); 
					}
				?>
            	</div>
            <?php
				}
			?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
</body>
</html>