<?php
//session_start();
session_register("logon");
/*

login()
logon()
logout()
je_admin(name,pass)
user()
user_jmeno()
user_prava()

*/

function login()
{ // BEGIN function f_login
$_SESSION["logon"] = 1;
} // END function f_login

function logon()
{ // BEGIN function f_logon
$log = $_SESSION["logon"];
if ($log == 1)
  return true;
else
  return false;
} // END function f_logon

function logout()
{ // BEGIN function f_logout
$_SESSION["logon"] = 0;
} // END function f_logout

function je_admin($name,$pass)
{ // BEGIN function je_admin
global $spojeni;
global $db;
$sql = "SELECT * FROM `$db`.`user` WHERE `name` LIKE '$name'";
$query = mysql_query($sql, $spojeni);
if (mysql_num_rows($query) == 1)
{
	$d = mysql_fetch_array($query);
	if($pass == $d["pass"]) {
	session_register("user");
	$_SESSION["user"] = $d["id"];
	return true;
	}
	else return false;
}
else return false;
} // END function je_admin

function user()
{ // BEGIN function f_logout
return $_SESSION["user"];
} // END function f_logout

function user_jmeno($id)
{ // BEGIN function f_logout
global $spojeni;
global $db;
$sql = "SELECT * FROM `$db`.`user` WHERE `id` LIKE '$id'";
$query = mysql_query($sql, $spojeni);
if (mysql_num_rows($query) == 1)
{
	$d = mysql_fetch_array($query);
	return $d["jmeno"];
	
}
} // END function f_logout

function user_prava($id)
{ // BEGIN function f_logout
global $spojeni;
global $db;
$sql = "SELECT * FROM `$db`.`user` WHERE `id` LIKE '$id'";
$query = mysql_query($sql, $spojeni);
if (mysql_num_rows($query) == 1)
{
	$d = mysql_fetch_array($query);
	return $d["prava"];
	
}
} // END function f_logout

function rights($op = "")
{ // BEGIN function f_logout
	global $spojeni, $db;

	if(user_prava(user()) == 0) return true;

	if(empty($op))
	{
		$path = explode("/", $_SERVER["PHP_SELF"]); //$page = $_SERVER["SCRIPT_FILENAME"];
		foreach($path as $a) $page = $a; 
	}
	else $page = $op;

	$sql = "SELECT * FROM `$db`.`rights` WHERE `page` = '$page'";
	$query = mysql_query($sql, $spojeni);
	if (mysql_num_rows($query) != 0)
	{
		$d = mysql_fetch_array($query);
		$group = explode(";", $d["group"]);
		foreach($group as $a) if($a == user_prava(user())) return true;
		return false;
	}
	else return true;
} // END function f_logout
?>
