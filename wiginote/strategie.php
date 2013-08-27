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
include "core/pages/menu.php";



	$query = mysql_query("SELECT * FROM `$db`.`text`", $spojeni);
	while($d = mysql_fetch_array($query))
	{
		if($d["name"] == "strategie") $strategie = $d["text"];
		if($d["name"] == "projekty") $projekty = $d["text"];
	}
	
	
	echo "
		<font size='4'>Strategie</font>
		<p>".windrow($strategie)."
		<hr size='1' color='black'>
		<font size='4'>Projekty</font>
		<p>".windrow($projekty)."
	";
	

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
