<?php
if(!isset($_GET["x"])){
	die(header("location: login.bc"));
} elseif($_GET["x"] != 111){
	die(header("location: login.bc"));
}

session_start();
session_destroy();
die(header("location: login.bc"));
?>