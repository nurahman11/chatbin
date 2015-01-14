<?php
session_start();
include("include/sql_conn.php");
if(isset($_SESSION['log_user'])){
	die(header("location: index.php"));
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
		$hash			= md5($post_password);
		$login_query	= mysqli_query($mysql, "SELECT userid FROM user WHERE email = '".$post_email."' AND password = '".$hash."'");
		$login_count	= mysqli_num_rows($login_query);
		
		if($login_count != 0){
			$_SESSION["log_user"] = $post_email;
			die(header("Location: index.php"));
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
    <form action="login.php" method="post" enctype="application/x-www-form-urlencoded">
    <table cellpadding="10" cellspacing="10" width="400" align="left">
        <tr>
            <th align="left" width="150">E-Mail:</th>
            <td align="left" width="200"><input type="text" class="textbox" name="email" value="<?php echo $post_email; ?>" /></td>
        </tr>
        <tr>
            <th align="left">Password:</th>
            <td align="left"><input type="password" class="textbox" name="password" /></td>
        </tr>
        <tr>
          <td align="center" colspan="2"><input type="submit" value="Login" class="button" name="login" /><br /><br /><a href="register.php">Or sign up here for free!</a></td>            
        </tr>
    </table>
    </form>
    </div>
</div>
</body>
</html>