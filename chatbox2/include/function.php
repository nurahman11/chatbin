<?php
	function sql_esc($mysql, $str){
		return mysqli_real_escape_string($mysql, $str);
	}
	
	function sql_req_esc($mysql, $str){
		if(get_magic_quotes_gpc()){
			$str = stripslashes($str);
		}
		
		return sql_esc($mysql, $str);
	}
	
	function html_esc($str){
		return htmlentities($str, ENT_QUOTES);
	}
	
	function roomname_esc($str){
		$str = preg_replace("/[^a-zA-Z0-9 ]/"," ",$str);
		$str = trim($str);
		$str = preg_replace("/\s+/","-",$str);
		$str = strtolower($str);
		return $str;
	}
	
?>