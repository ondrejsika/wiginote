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
include "core/pages/head.php";
include "core/pages/menu.php";



//include "apps/export_produkty.php";

//**********//
if(empty($shop)) $shop = "wigishop";

$allZbozi = new sklad;
foreach($allZbozi-> allZbozi() as $id)
{
	$form_zb = new sklad;
	$form_zb-> getZbozi($id);
	if(isset($_POST["enable"]) and $_POST["enable"] == 1)
	{
		if(isset($_POST["tool_free_$id"]) and $_POST["tool_free_$id"] == 1)
			$form_zb->editZbozi("ostatni", array(array("tool_free",1)));
		else
			$form_zb->editZbozi("ostatni", array(array("tool_free",0)));
	}
	$form_zb-> saveZbozi();
}


echo "<a onClick=\"window.open('sklad2_pridat.php', 's2p', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\">Přidat zboží</a><br>";

if ($shop == "wigishop") echo "<a href='sklad2.php?shop=wigishop&nastrance=$nastrance'><b>Wigishop</b></a> | ";
else echo "<a href='sklad2.php?shop=wigishop&nastrance=$nastrance'>Wigishop</a> | ";
if ($shop == "hrackovnik") echo "<a href='sklad2.php?shop=hrackovnik&nastrance=$nastrance'><b>Hračkovník</b></a> | ";
else echo "<a href='sklad2.php?shop=hrackovnik&nastrance=$nastrance'>Hračkovník</a> | ";
if ($shop == "topgadget") echo "<a href='sklad2.php?shop=topgadget'><b>Topgadget</b></a> | ";
else echo "<a href='sklad2.php?shop=topgadget&nastrance=$nastrance'>Topgadget</a> | ";
if ($shop == "aukro") echo "<a href='sklad2.php?shop=aukro&nastrance=$nastrance'><b>Aukro</b></a>";
else echo "<a href='sklad2.php?shop=aukro'>Aukro</a>";

echo "
	<form action='sklad2.php?shop=$shop' method='post'>
	<input type='hidden' name='enalble' value='1'>
	<input type='submit'>
	<table>
		<tr>
			<td></td>
			<td width='50'>sku</td>
			<td width='100'>sku shop</td>
			<td width='100'>název</td>
			<td width='100'>na prodejně</td>
			<td width='100'>celkem</td>
			<td width='100'>dostupnost</td>
			<td width='100'>cena</td>
			<td width='100'>toll free</td>
		</tr>
";

foreach($allZbozi-> allZbozi("sku") as $id)
{
	$zbozi = new sklad;
	$zbozi-> getZbozi($id, $shop);
	
	if($zbozi->ostatni("tool_free") == 1) $checked = "CHECKED";
	else $checked = "";

	echo "
		<tr class='radek'>
			<td><a onClick=\"window.open('sklad2_podrobnosti.php?id=$id', 's2podr', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\">
			<img src='var/img/main/plus.png' border='0'></a></td>
			<td>".$zbozi-> sku."</td>
			<td>".$zbozi-> sku_shop."</td>
			<td>".$zbozi-> nazev."</td>
			<td>".$zbozi-> naSklade("naprodejne")."ks</td>
			<td>".$zbozi-> naSkladech()."ks</td>
			<td>".$zbozi-> dostupnost("naprodejne","str")."</td>
			<td>".$zbozi-> cena_shop."Kč</td>
			<td><input type='checkbox' name='tool_free_$id' value='1' $checked></td>
		</tr>
	";
}

echo "</table></form>";

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
