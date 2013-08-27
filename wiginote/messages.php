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
include "core/page/menu.php";

if(isset($_GET["from"]))$from = $_GET["from"];
else $from = "wigitron";

if($from == "wigitron")
{
	echo "Zprávy z Wigitron";

	$sql = "SELECT * FROM `$db`.`messages` WHERE `from` = '$from' ORDER BY `message`.`time` DESC";
	$q = mysql_query($sql, $spojeni);
	while($d = mysql_fetch_array($q))
	{
		echo "<br>".$text;
	}
}

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
