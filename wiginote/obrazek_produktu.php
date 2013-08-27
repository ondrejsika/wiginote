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

$sku = $_GET["sku"];

$zb = new sklad();
$id_zb = $zb->selectFromSku($sku);
$zb->getZbozi($id_zb, "wigishop");

$img = $zb->img;
$expl_img = explode("_", $img);
$img = $expl_img[0];
$a = explode(".", $expl_img[1]);
$img .= ".".$a[1]; 

$path = "http://wigishop.cz/media/catalog/product$img";
echo "<img src='$path' width='100' heigh='100'>";

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
