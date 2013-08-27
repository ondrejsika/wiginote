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

$zbozi = new sklad;
$zbozi->getZbozi($id);

$sku_shop = "";
foreach($zbozi->sku_shop as $shop)
{
	$sku_shop .= "<br>&nbsp;&nbsp;-SKU ".$shop["shop"].": ".$shop["sku"];
}
$cena_shop = "";
foreach($zbozi->cena_shop as $shop)
{
	$cena_shop .= "<br>&nbsp;&nbsp;-cena ".$shop["shop"].": ".$shop["cena"];
}
$cena_zvyhodnena = "";
foreach($zbozi->cena_zvyhodnena as $shop)
{
	$cena_zvyhodnena .= "<br>&nbsp;&nbsp;-".$shop["shop"].": ".$shop["akce"].": ".$shop["cena"];
}
$mnozstvi = "";
foreach($zbozi->mnozstvi as $sklad)
{
	$mnozstvi .= "<br>&nbsp;&nbsp;-".$sklad["sklad"].": ".$sklad["mnozstvi"];
}

$ze = new select("ze",array("","naprodejne","nezvestice","doma","reklamace"),array("","na prodejne","nezvěstice","doma","reklamace"),0);
$do = new select("do",array("","naprodejne","nezvestice","doma","reklamace"),array("","na prodejne","nezvěstice","doma","reklamace"),1);

if(!empty($_GET["task"])) echo "<font color='red'>".$_GET["task"]."</font>";
echo "
	<a href='sklad2_upravit.php?id=$id'>Upravit</a>
	<table width='100%'>
		<tr valign='top'>
			<td>
				SKU: ".$zbozi->sku."
				$sku_shop
				$cena_shop
				<br>cena nákupní: ".$zbozi->cena_nakupni."
				<br>Zvýhodněné ceny
				$cena_zvyhodnena
			</td>
			<td>
				dodavatel:  ".$zbozi->dodavatel."
				<br>SKU dodavatel: ".$zbozi->sku_dodavatel."
				<br>název: ".$zbozi->nazev."
				<br>množství:
				$mnozstvi
				<br>kritické množství: ".$zbozi->mnozstvi_kriticke."
				<br>dostupnost: ".$zbozi->dostupnost("str")."
				<form action='sklad2_action.php?id=$id&page=podrobnosti' method='post'>
				z: ".$ze->write()."
				<br>do: ".$do->write()."
				<br><input type='text' name='do_skladu' size='2'>
				<button name='action' value='do_skladu'>do skladu</button>
				</form>
			</td>
		</tr>
		<tr valign='top'>
			<td><textarea name='popis_dlouhy' cols='40' rows='10'>".$zbozi->popis_kratky."</textarea></td>
			<td><textarea name='popis_dlouhy' cols='40' rows='10'>".$zbozi->popis_dlouhy."</textarea></td>
		</tr>
	</table>
";
if($zbozi->dodavatel == "smartstore") echo "<img src='var/img/produkty/smartstore/".$zbozi->sku_dodavatel.".jpg'>";
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
