<?php

// post
$soubor = "etc/variables/post";
$file = fopen($soubor, "r");
if(filesize($soubor) > 0)
{
	$data = fread($file, filesize($soubor));
	$post = explode("\n", $data);

	foreach ($post as $value) {
		if (isset($_POST[$value])) {
			$$value = $_POST[$value];
		}
		else {
			$$value = "";
		}
	}
}
fclose($file);

// get
$soubor = "etc/variables/get";
$file = fopen($soubor, "r");
if(filesize($soubor) > 0)
{
	$data = fread($file, filesize($soubor));
	$get = explode("\n", $data);

	foreach ($get as $value) {
		if (isset($_GET[$value])) {
			$$value = $_GET[$value];
		}
		else {
			$$value = "";
		}
	}
}
fclose($file);

// session
$soubor = "etc/variables/session";
$file = fopen($soubor, "r");
if(filesize($soubor) > 0)
{
	$data = fread($file, filesize($soubor));
	$session = explode("\n", $data);

	foreach ($session as $value) {
		if (isset($_SESSION[$value])) {
			session_register($value);
			$$value = $_SESSION[$value];
		}
		else {
			$$value = "";
		}
	}
}
fclose($file);

// var
$soubor = "etc/variables/var";
$file = fopen($soubor, "r");
if(filesize($soubor) > 0)
{
	$data = fread($file, filesize($soubor));
	$var = explode("\n", $data);

	foreach ($var as $value) {
		$$value = "";
	}
}
fclose($file);

$get = array("id","action","sekce","sekce2","krok","news","alert","page","shop","typ","str","nastrance","shop");


$session = array();
foreach ($session as $value) {
	if (isset($_SESSION[$value])) {
		session_register($value);
		$$value = $_SESSION[$value];
	}
	else {
		$$value = "";
	}
}

?>
