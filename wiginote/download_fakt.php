<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

include "core/header.php"; // header
include "core/inc.php"; // included files

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if(!rights()) echo "<h1>access denied</h1>";

elseif($access == 1)
{
/**********/
	if(isset($_GET["vs"]))
	{
		$file = $_GET["vs"].".pdf";
		$soubor = "var/faktury/wigishop/".$file;
		header("Content-Description: File Transfer");
		header('Content-type: application/pdf');
		header("Content-Disposition: attachment; filename=\"$file\"");

		readfile($soubor);
	}
	elseif(isset($_GET["osp"]))
	{
		$file = $_GET["osp"].".pdf";
		$soubor = "var/faktury/osp/".$file;
		header("Content-Description: File Transfer");
		header('Content-type: application/pdf');
		header("Content-Disposition: attachment; filename=\"$file\"");

		readfile($soubor);
	}
	elseif(isset($_GET["tmp"]))
	{
		$file = $_GET["tmp"].".pdf";
		$soubor = "var/faktury/multifakt/".$file;
		header("Content-Description: File Transfer");
		header('Content-type: application/pdf');
		header("Content-Disposition: attachment; filename=\"$file\"");

		readfile($soubor);
	}
	elseif(isset($_GET["aukro"]))
	{
		$file = $_GET["aukro"].".pdf";
		$soubor = "var/faktury/aukro/".$file;
		header("Content-Description: File Transfer");
		header('Content-type: application/pdf');
		header("Content-Disposition: attachment; filename=\"$file\"");

		readfile($soubor);
	}
	elseif(isset($_GET["topgadget"]))
	{
		$file = $_GET["topgadget"].".pdf";
		$soubor = "var/faktury/topgadget/".$file;
		header("Content-Description: File Transfer");
		header('Content-type: application/pdf');
		header("Content-Disposition: attachment; filename=\"$file\"");

		readfile($soubor);
	}
	elseif(isset($_GET["hrackovnik"]))
	{
		$file = $_GET["hrackovnik"].".pdf";
		$soubor = "var/faktury/hrackovnik/".$file;
		header("Content-Description: File Transfer");
		header('Content-type: application/pdf');
		header("Content-Disposition: attachment; filename=\"$file\"");

		readfile($soubor);
	}
	else
		echo "neplazne zadani";

	
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
