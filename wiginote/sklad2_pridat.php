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
//**********//
if(empty($shop)) $shop = "wigishop";

if(empty($krok))
{
	echo "
		<form action='sklad2_pridat.php?shop=$shop&krok=2' method='POST'>
			<table width='100%'>
				<tr valign='top'>
					<td>
						SKU: <input type='text' name='sku' size='5'>  <input type='submit' value='ok'>
						<br>SKU shop: <input type='text' name='sku_shop' size='30' value='wigishop:'>
						<br>cena shop: <input type='text' name='cena_shop' size='30' value='wigishop:'>
						<br>cena nákupní: <input type='text' name='cena_nakupni' size='4'>
						<br>cena zvyhodnena: <input type='text' name='cena_zvyhodnena' size='30' value='wigishop:3ks:'>
						<br>popis krátký
						<br><textarea name='popis_kratky' cols='40' rows='10'></textarea>
					</td>
					<td>
						dodavatel: <input type='text' name='dodavatel' size='30'>
						<br>SKU dodavatel: <input type='text' name='sku_dodavatel' size='5'>
						<br>název: <input type='text' name='nazev' size='30'>
						<br>množství: <input type='text' name='mnozstvi' size='4'>
							kritické množství: <input type='text' name='mnozstvi_kriticke' size='3'>
						<br>dostupnost: <input type='text' name='dostupnost' size='30' value='do_3_dnu'>
						<br>popis dlouhý
						<br><textarea name='popis_dlouhy' cols='40' rows='10'></textarea>
					</td>
				</tr>
			</table>
		</form>
	";
}
else
{
	$post = array("sku", "sku_shop", "cena_shop", "cena_nakupni", "cena_zvyhodnena", "popis_kratky", "dodavatel", "sku_dodavatel", "nazev", "mnozstvi", "mnozstvi_kriticke", "dostupnost", "popis_dlouhy", "img", "ostatni");
	foreach ($post as $value) {
		if (isset($_POST[$value])) {
			$$value = $_POST[$value];
		}
		else {
			$$value = "";
		}
	}

	$zbozi = new sklad();
	$zbozi-> setZbozi(
		$dodavatel,
		$sku_dodavatel,
		$sku,
		$mnozstvi,
		$mnozstvi_kriticke,
		0,
		$sku_shop,
		$cena_shop,
		$cena_zvyhodnena,
		$cena_nakupni,
		0,
		$nazev,
		$popis_kratky,
		$popis_dlouhy,
		$img,
		"tool_free:0"
	);
	$zbozi-> setSkladem($dostupnost);
	$zbozi-> saveZbozi();
	header("location: sklad2_podrobnosti.php?id=".id("sklad2"));
}

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
