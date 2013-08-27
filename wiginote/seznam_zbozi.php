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

function k($vstup) {
	$vstup = str_replace("ě", "e", $vstup);
	$vstup = str_replace("š", "s", $vstup);
	$vstup = str_replace("ř", "r", $vstup);
	$vstup = str_replace("Ě", "E", $vstup);
	$vstup = str_replace("Č", "C", $vstup);
	$vstup = str_replace("č", "c", $vstup);
	$vstup = str_replace("Ř", "R", $vstup);
	$vstup = str_replace("Ň", "N", $vstup);
	$vstup = str_replace("ň", "n", $vstup);
  return iconv("UTF-8","windows-1250",$vstup);
}

$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',12);
$array = explode(";", $_GET["z"]);
$i = 10;
foreach($array as $obj)
{
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$obj'";
	$q = mysql_query($sql,$spojeni);
	$d = mysql_fetch_array($q);
	$ks = explode(";", $d["mnozstvi_polozky"]);
	$pr = explode(";", $d["sku"]);
	$x=0;
	foreach($pr as $sku)
	{
		$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = '$sku'";
		$q = mysql_query($sql,$spojeni);
		$d2 = mysql_fetch_array($q);
		$pdf->Text(10,$i,$sku." - ". $d2["nazev"]);
		echo "<br>".$sku." - ".$ks[$x] ."ks ". $d2["nazev"];
		$i = $i + 13;
		$x++;
	}

	$i = $i + 13;
}

	//$pdf->Text(10,250,k("ěščřžýáíéňťó"));
///	END stranka faktury
/*

	$pdf->Output("tmp/".time().".pdf","F");
	header("location: download_fakt.php?tmp=".time());

//*/
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
