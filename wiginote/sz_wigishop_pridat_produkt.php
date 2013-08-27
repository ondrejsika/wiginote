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

if(empty($krok))
{
		$na = $sa = array("");
		$sql = "SELECT * FROM `$db`.`sklad2` ORDER BY `sklad2`.`nazev` ASC";
		$q = mysql_query($sql, $spojeni);
		while($d = mysql_fetch_array($q))
		{
			if($d["sku"] != 0 and !empty($d["nazev"]))
			{
				$sa[] = $d["sku"];
				$na[] = $d["nazev"].", ".$d["sku"];
			}
		}

		$sp = new select("sku",$sa,$na,0);

		echo "
			<form action='sz_wigishop_pridat_produkt.php?krok=2&id=$id' method='POST'>

			Produkt
			<br>".$sp->write()."
			<p>název
			<br><input type='text' name='nazev'>
			<p>cena
			<br><input type='text' name='cena'>
			<p>množství
			<br><input type='text' name='mnozstvi' value='1'>
			
			<button name='act' value='dokoncit'>Přidat</button>
		";
}
else
{
	$z = new sklad;
	$zb_id = $z->selectFromSku($_POST["sku"]); 
	$z-> getZbozi($zb_id, "wigishop");

	if(!empty($_POST["cena"])) $cena = $_POST["cena"];
	else $cena = $z->cena_shop;

	if(!empty($_POST["nazev"])) $nazev = $_POST["nazev"];
	else $nazev = $z->nazev;

	$sku = $_POST["sku"];
	$nazev = $nazev;
	$cena = $cena;
	$mnozstvi = $_POST["mnozstvi"];

	$it = new items;
	$item_id = $it->set($sku, $nazev, $cena, $mnozstvi);

	$sql = "SELECT * FROM `$db`.`zasilky_wigishop` WHERE `id` = $id";
	$q = mysql_query($sql, $query);
	$d = mysql_fetch_array($q);

	$sql = "UPDATE  `$db`.`zasilky_wigishop` SET  `items` =  '".$d["items"].";$item_id' WHERE  `zasilky_wigishop`.`id` = $id;";

	header("location: sz_wigishop_podrobnosti.php?id=$id");
}
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
