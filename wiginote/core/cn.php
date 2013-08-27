<?php

$soubor = "etc/mysql/cn.xml";
$file = fopen($soubor, "r");
$xml = fread($file, filesize($soubor));
fclose($file);

$server = xml($xml, "server");
$user = xml($xml, "user");
$password = xml($xml, "password");
$db = xml($xml, "db_name");

$spojeni = mysql_connect($server, $user,$password);

function cn()
{
	global $spojeni;
	return $spojeni;
}

?>
