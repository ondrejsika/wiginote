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
//echo "<meta http-equiv='content-type' content='text/html; charset=UTF-8'>";

	$file = jmenoSouboru($id);
	$soubor = "var/files/poznamky/$file";

	header("Content-Description: File Transfer");
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=\"$file\"");

	//echo $soubor;

	displayFile($soubor);
	readfile($soubor);
	hideFile($soubor);
}
else
{
	include "core/pages/refresher.php";
}

//include "core/footer.php"; // footer
?>
