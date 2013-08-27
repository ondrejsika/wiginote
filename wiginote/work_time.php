<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

include "core/header.php"; // header
include "core/inc.php"; // included files

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if(!rights()) echo "<h1>acces denied</h1>";

elseif($access == 1)
{
/**********/
include "core/pages/head.php";


 	$ip = $_SERVER["REMOTE_ADDR"];


	$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = ".user()." AND `stop` = 0 ORDER BY `work_time`.`start` DESC LIMIT 1";
	$q = mysql_query($sql,$spojeni);
	if(mysql_num_rows($q) != 0)
	{
		// stop
		$d = mysql_fetch_array($q);
		$sql = "UPDATE `$db`.`work_time` SET `stop` = '".time()."' WHERE `work_time`.`id` = '".$d["id"]."' LIMIT 1 ;";
		if(mysql_query($sql, $spojeni)) echo "STOP";
	}
	else 
	{
		// start
			$sql = "INSERT INTO `$db`.`work_time` VALUES ('".id("work_time")."', '".user()."',  '$ip','".time()."', '0');";
			if(mysql_query($sql, $spojeni)) echo "START";
	}

	///
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
