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

if(isset($_SESSION['log_room'])){
	die(header("location: chatroom.php?id=".$_SESSION['log_room']));
}

// update last active
mysqli_query($mysql, "UPDATE user SET lastactive = NOW() WHERE userid = " . $log_userid);

$post_email = "";
$post_screen = "";

// Search stuff
$search = "";
if(isset($_POST["searchtxt"])){
	$search = $_POST["searchtxt"];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>ChatBin.us - Room List</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="script.js"></script>
</head>

<body onResize="positionHideBoxOnCenter();" onLoad="timerRefresh();">
<div id="headlogo"></div>
<div id="floatdiv">
    <div id="roomlist">
        <div class="box_head">Room List</div>
        <div style="margin-left: 5px; margin-top: 5px; float: left;">
            <button class="button" onClick="showHiddenBox('createroom')">+ Create Room</button>
        	<button class="button" onClick="location.href = location.href"><font class="symboltext">&#10226;</font> Refresh List</button>
        </div>
        <div style="margin-right: 5px; margin-top: 5px; float: right;">
        	<form method="post" action="index.php" enctype="application/x-www-form-urlencoded" name="searchform">
        		<input type="text" class="textbox" name="searchtxt" value="<?php echo $search; ?>" placeholder="Search Room" onFocus="timerPause();" onBlur="timerPause(false);" />
            	<button type="submit" class="button" name="searchbtn"><font class="symboltext">&#128269;</font></button>
            </form>
            <form method="post" action="index.php" enctype="application/x-www-form-urlencoded" name="searchformhidden" style="display: none;">
                <input type="hidden" name="searchtxt" value="<?php echo $search; ?>" readonly />
            </form>
        </div>
        <div class="clear"></div>
        
        
        <table width="740" cellpadding="10" cellspacing="0" id="tablelist_head">
            <tr>
                <th align="left" width="30">Lock</th>
                <th align="left" width="520">Roomname</th>
                <th align="right" width="170">Active User</th>
            </tr>
        </table>
        <div id="tablelist" class="autohidescroll">
        	<table width="100%" cellpadding="10" cellspacing="0" style="">
                <?php
					$room_query		 = "SELECT roomid, roomowner, roomname, roompassword, hidden ";
					$room_query		.= "FROM room WHERE";
					if($search != ""){
						$room_query	.= " roomname LIKE '%".$search."%' AND";
					}
					$room_query		.= " hidden = 0";
					$room_sql		 = mysqli_query($mysql, $room_query);
					
					$roomnum = 0;
					
					while($room_fetch = mysqli_fetch_array($room_sql)){
						$delusercount = "SELECT chatid FROM chat WHERE roomid = ".$room_fetch["roomid"]." AND time > DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
						$delusercount = mysqli_num_rows(mysqli_query($mysql, $delusercount));
						if($delusercount == 0 && $room_fetch["roomid"] > 2){
							mysqli_query($mysql, "DELETE FROM chat WHERE roomid = ".$room_fetch["roomid"]);
							mysqli_query($mysql, "DELETE FROM room WHERE roomid = ".$room_fetch["roomid"]);
							continue;						
						}
							
						$usercount = "SELECT userid FROM chat WHERE roomid = ".$room_fetch["roomid"]." AND time > DATE_SUB(NOW(), INTERVAL 5 MINUTE) AND userid <> 0 GROUP BY userid";
						$usercount = mysqli_num_rows(mysqli_query($mysql, $usercount));
									
						$roomnum++;
						if($room_fetch["roompassword"] == "" || $room_fetch["roomowner"] == $log_userid){
							$tr_onclick = "location.href = 'chatroom.php?id=".$room_fetch["roomid"]."'";
						} else {
							$tr_onclick = "goRoomPassword('".$room_fetch["roomid"]."', '".$room_fetch["roomname"]."')";
						}
				?>
                
                <tr onClick="<?php echo $tr_onclick; ?>" <?php if($roomnum%2 == 0){?> bgcolor="#A5D3A6"<?php } ?>>
                	<td align="center" width="30"><font class="symboltext" style="color: #6C0000;"><?php if($room_fetch["roompassword"] != ""){ echo "&#128274;"; }?></font></th>
                	<td align="left" width="520"><?php echo $room_fetch["roomname"]; ?></td>
                    <td align="right" width="150"><?php echo $usercount; ?> User</td>
                </tr>
                
                <?php
					}
				?>
            </table>
        </div>
    </div>
    <div id="infobox">
        <div class="box_head" style="padding-left: 10px;">Welcome</div>
        <div style="margin: 10px;">
            Hello, <strong><?php echo $log_screen; ?></strong><br /><br />
            &raquo; <a href="logout.php">Logout</a><br />
            &raquo; <a href="javascript:void(0)" onClick="showHiddenBox('changepassword');">Change Password</a><br />
            &raquo; <a href="javascript:void(0)" onClick="showHiddenBox('changescreenname');">Change Screen Name</a><br />
        </div>
    </div>
    <div id="userlist">
    	<div class="box_head" style="padding-left: 10px;">Online List</div>
        <div style="margin: 10px;">
        	<?php
				$useronline_query = mysqli_query($mysql, "SELECT screenname, userid FROM user WHERE lastactive > DATE_SUB(NOW(), INTERVAL 5 MINUTE) AND userid <> 0 GROUP BY screenname ORDER BY screenname");
				$useronline_count = mysqli_num_rows($useronline_query);
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
						echo "<strong>".$log_screen."</strong>";
					} else {
                		echo $useronline_fetch["screenname"]; 
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
<div id="blackbox" onClick="hideAllHiddenBox();"></div>
<div id="hiddenbox">
    
    <!-- create room box -->
	<div class="popupbox" id="createroom">
    	<div class="closebutton" onClick="hideAllHiddenBox();">&#10008;</div>
    	<div class="box_head" style="margin: 0;">Create Room</div>
        <div class="box_error" id="cr_error"></div>
        <div style="width: 500px; margin: auto; margin-top: 20px;">
            <form action="javascript:void(0);" method="post" enctype="application/x-www-form-urlencoded" name="create_room" onSubmit="createChatRoom()">
            <table cellpadding="5" cellspacing="5" width="500" align="left">        
                <tr>
                    <th align="left" width="150" valign="top">Room Name:</th>
                    <td align="left" width="300"><input type="text" class="textbox" name="roomname" style="width: 290px" maxlength="30" /><br /><font style="font-size: 11px; color: #999999">6-30 Character.</font></td>
                </tr>
                <tr>
                    <th align="left" valign="top">Room Password:</th>
                    <td align="left"><input type="password" class="textbox" name="password" style="width: 290px" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">5-15 Character.<br />Leave it empty if you don't want to lock your room.</font></td>
                </tr>
                <tr>
                    <th align="left" valign="top">Confirm Password:</th>
                    <td align="left"><input type="password" class="textbox" name="password2" style="width: 290px" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">Must same with Password.<br />Leave it empty if you don't want to lock your room.</font></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="left"><label><input type="checkbox" name="hiddenroom" value="yes" /> Hide from room list (Hidden Room)</label></td>
                </tr>
                <tr>
                    <td align="center" colspan="2"><input type="submit" value="Create Room" class="button" name="createnow" /></td>
                </tr>
            </table>
            </form>
        </div>
    </div>
    
    <!-- change password box -->
    <div class="popupbox" id="changepassword">
    	<div class="closebutton" onClick="hideAllHiddenBox();">&#10008;</div>
    	<div class="box_head" style="margin: 0;">Change Password</div>
        <div class="box_error" id="cp_error"></div>
        <div style="width: 500px; margin: auto; margin-top: 20px;">
            <form action="javascript:void(0)" method="post" enctype="application/x-www-form-urlencoded" name="change_password" onSubmit="return changePassword();">
            <table cellpadding="5" cellspacing="5" width="500" align="left">        
                <tr>
                    <th align="left" width="150" valign="top">Current Password:</th>
                    <td align="left" width="300"><input type="password" class="textbox" name="oldpass" style="width: 290px" maxlength="15" /></td>
                </tr>
                <tr>
                    <th align="left" valign="top">New Password:</th>
                    <td align="left"><input type="password" class="textbox" name="password" style="width: 290px" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">5-15 Character.<br />Must be different with current password</font></td>
                </tr>
                <tr>
                    <th align="left" valign="top">Confirm New Password:</th>
                    <td align="left"><input type="password" class="textbox" name="password2" style="width: 290px" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">Must be same with new password.</font></td>
                </tr>
                <tr>
                    <td align="center" colspan="2"><input type="submit" value="Change Password" class="button" name="changenow" /></td>
                </tr>
            </table>
            </form>
        </div>
    </div>
    
    <!-- change screen name box -->
    <div class="popupbox" id="changescreenname">
    	<div class="closebutton" onClick="hideAllHiddenBox();">&#10008;</div>
    	<div class="box_head" style="margin: 0;">Change Screen Name</div>
        <div class="box_error" id="cs_error"></div>
        <div style="width: 500px; margin: auto; margin-top: 20px;">
            <form action="javascript:void(0)" method="post" enctype="application/x-www-form-urlencoded" name="change_screenname" onSubmit="return changeScreenName();">
            <input type="hidden" value="<?php echo $log_screen; ?>" name="screennameold" readonly />
            <table cellpadding="5" cellspacing="5" width="500" align="left">        
                <tr>
                    <th align="left" width="150" valign="top">Screen Name:</th>
                    <td align="left" width="300"><input type="text" class="textbox" name="screenname" style="width: 290px" maxlength="15" value="<?php echo $log_screen; ?>" /><br /><font style="font-size: 11px; color: #999999">4-15 character. AlphaNumeric only.</font></td>
                </tr>
                <tr>
                    <th align="left" valign="top">Your Password:</th>
                    <td align="left"><input type="password" class="textbox" name="password" style="width: 290px" maxlength="15" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="2"><br /><input type="submit" value="Change Screen Name" class="button" name="changenow" /></td>
                </tr>
            </table>
            </form>
        </div>
    </div>
    
    <!-- room password box -->
    <div class="popupbox" id="roompasswordbox">
    	<div class="closebutton" onClick="hideAllHiddenBox();">&#10008;</div>
    	<div class="box_head" style="margin: 0;">Locked Room</div>
        <div class="box_error" id="rp_error"></div>
        <div style="width: 500px; margin: auto; margin-top: 20px;">
            <form method="post" enctype="application/x-www-form-urlencoded" name="room_password">
            <table cellpadding="5" cellspacing="5" width="500" align="left">        
                <tr>
                    <th align="left" width="150" valign="top">Room Name:</th>
                    <td align="left" width="300"><input type="text" class="textbox" name="roomname" style="width: 290px" readonly /></td>
                </tr>
                <tr>
                    <th align="left" valign="top">Room Password:</th>
                    <td align="left"><input type="password" class="textbox" name="password" style="width: 290px" maxlength="15" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="2"><br /><input type="submit" value="Enter Room" class="button" name="enter" /></td>
                </tr>
            </table>
            </form>
        </div>
    </div> 
</div>
</body>
</html>