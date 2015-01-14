<?php
session_start();
include("include/sql_conn.php");
if(isset($_SESSION['log_user'])){
	die(header("location: index.php"));
}

$post_email = "";
$post_screen = "";
$error_register = "";

// check register post
if(isset($_POST["register"])){
	$post_email		= $_POST["email"];
	$post_password	= $_POST["password"];
	$post_password2	= $_POST["password2"];
	$post_screen	= $_POST["screenname"];
	
	if($post_email == "" || $post_password == "" || $post_password2 == "" || $post_screen == ""){
		$error_register = "Please fill all the input.";
	} else {
		// checking for register
		$email_regex = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/";
		//if(!filter_var($post_email, FILTER_VALIDATE_EMAIL))	{
		if(!preg_match($email_regex, $post_email)){
			$error_register = "Your email is Invalid";
		} else if(strlen($post_password) > 15 || strlen($post_password) < 5){
			$error_register = "Your password must be 5 to 15 character!";
		} else if(strlen($post_screen) > 15 || strlen($post_screen) < 4){
			$error_register = "Your screen name must be 4 to 15 character!";
		} else if(!ctype_alnum($post_screen)){
			$error_register = "Your screen name must be alphanumeric only!";
		} else {
			$check_email 	= mysqli_num_rows(mysqli_query($mysql, "SELECT userid FROM user WHERE email = '".$post_email."'"));
			$check_screen 	= mysqli_num_rows(mysqli_query($mysql, "SELECT userid FROM user WHERE screenname = '".$post_screen."'"));
			if($check_email > 0){
				$error_register = "E-mail is already used, please insert another email.";
			} else if($check_screen > 0){
				$error_register = "Screen name is already used, please insert another name.";
			} else {
				$hash			= md5($post_password);
				$do_register	= mysqli_query($mysql, "INSERT INTO user (email, password, screenname) VALUES('".$post_email."','".$hash."','".$post_screen."')");
				if($do_register){
					$error_register		= "<font color=\"#28934E\">Register for &quot;".$post_email."&quot; success.</font>";
				} else {
					$error_register		= "Failed to register. Please try again later.";
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>ChatBin.us - Register</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="style_login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="headlogo_login" style="margin-top: 30px;"></div>
<div id="login">
    <div id="login_head">Register</div>
    <div id="login_error"><?php echo $error_register ;?></div>
    <div id="login_table" style="height: 400px;">
    <form action="register.php" method="post" enctype="application/x-www-form-urlencoded">
    <table cellpadding="5" cellspacing="5" width="400" align="left">        
        <tr>
            <th align="left" width="150" valign="top">E-Mail:</th>
            <td align="left" width="200"><input type="email" class="textbox" name="email" value="<?php echo $post_email; ?>" maxlength="50" /><br /><font style="font-size: 11px; color: #999999">Must valid E-Mail</font></td>
        </tr>
        <tr>
            <th align="left" valign="top">Password:</th>
            <td align="left"><input type="password" class="textbox" name="password" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">5-15 Character</font></td>
        </tr>
        <tr>
            <th align="left" valign="top">Confirm Password:</th>
            <td align="left"><input type="password" class="textbox" name="password2" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">Must same with Password</font></td>
        </tr>
        <tr>
            <th align="left" valign="top">Screen Name:</th>
            <td align="left"><input type="text" class="textbox" name="screenname" value="<?php echo $post_screen; ?>" maxlength="15" /><br /><font style="font-size: 11px; color: #999999">Used for chat name. 4-15 character. AlphaNumeric only.</font></td>
        </tr>
        <tr>
            <td align="center" colspan="2"><input type="submit" value="Register" class="button" name="register" /><br /><br /><a href="login.php">Already have an account? Login here!</a></td>
        </tr>
    </table>
    </form>
    </div>
</div>
</body>
</html>