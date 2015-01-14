<?php
if(!isset($_GET["x"])){
	die(header("location: login.bc"));
} elseif($_GET["x"] != "111"){
	//die(header("location: login.bc"));
}

session_start();
include("include/sql_conn.php");
include("include/function.php");
if(isset($_SESSION['log_user'])){
	die(header("location: room.bc"));
}

$post_email = "";
$error_login = "";

// check login post
if(isset($_POST["login"])){

	$post_email = $_POST["email"];
	$post_password =  $_POST["password"];
	
	if($post_email == "" || $post_password == ""){
		$error_login = "Please insert your username and password.";
	} else {
		$hash			= md5(strrev($post_password.$post_email));
		$login_query	= mysqli_query($mysql, "SELECT userid FROM user WHERE email = '".sql_req_esc($mysql, $post_email)."' AND password = '".sql_req_esc($mysql, $hash)."'");
		$login_count	= mysqli_num_rows($login_query);
		
		if($login_count == 1){
			$_SESSION["log_user"] = $post_email;
			die(header("Location: room.bc"));
		} else {
			$error_login = "E-Mail or Password is invalid.";
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>ChatBin.us - Login</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="style_login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="headlogo_login"></div>
<div id="login">
    <div id="login_head">Login</div>
    <div id="login_error"><?php echo $error_login; ?></div>
    <div id="login_table">
    <form action="login.bc" method="post" enctype="application/x-www-form-urlencoded">
    <table cellpadding="10" cellspacing="10" width="400" align="left">
        <tr>
            <th align="left" width="150">E-Mail:</th>
            <td align="left" width="200"><input type="text" class="textbox" name="email" value="<?php echo html_esc($post_email); ?>" /></td>
        </tr>
        <tr>
            <th align="left">Password:</th>
            <td align="left"><input type="password" class="textbox" name="password" /></td>
        </tr>
        <tr>
          <td align="center" colspan="2"><input type="submit" value="Login" class="button" name="login" /><br /><br /><a href="register.bc">Or sign up here for free!</a></td>            
        </tr>
    </table>
    </form>
    </div>
</div>
</body>
</html>