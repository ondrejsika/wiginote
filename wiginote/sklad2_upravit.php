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
if(empty($shop)) $shop = "wigishop";

if(empty($krok))
{
	$zb = new sklad($id);
	
	echo "
		<form action='sklad2_upravit.php?shop=$shop&krok=2' method='POST'>
		<input type='hidden' name='mnozstvi_prodano' value='".$zb->mnozstvi_prodano."'>
			<table width='100%'>
				<tr valign='top'>
					<td>
						SKU: <input type='text' name='sku' size='5' value='".$zb->sku."'>  <input type='submit' value='ok'>
						<br>SKU shop: <input type='text' name='sku_shop' size='30' value='".$zb->getFieldString("sku_shop")."'>
						<br>cena shop: <input type='text' name='cena_shop' size='30'value='".$zb->getFieldString("cena_shop")."'>
						<br>cena nákupní: <input type='text' name='cena_nakupni' size='4' value='".$zb->cena_nakupni."'>
						<br>cena zvyhodnena: <input type='text' name='cena_zvyhodnena' size='30' value='".$zb->getFieldString("cena_zvyhodnena")."'>
						<br>popis krátký
						<br><textarea name='popis_kratky' cols='40' rows='10'>".$zb->popis_kratky."</textarea>
					</td>
					<td>
						dodavatel: <input type='text' name='dodavatel' size='30' value='".$zb->dodavatel."'>
						<br>SKU dodavatel: <input type='text' name='sku_dodavatel' size='5' value='".$zb->sku_dodavatel."'>
						<br>název: <input type='text' name='nazev' size='30' value='".$zb->nazev."'>
						<br>množství: <input type='text' name='mnozstvi' size='30' value='".$zb->getFieldString("mnozstvi")."'>
						<br>kritické množství: <input type='text' name='mnozstvi_kriticke' size='3' value='".$zb->mnozstvi_kriticke."'>
						<br>dostupnost: <input type='text' name='dostupnost' size='30' value='".$zb->dostupnost."'>
						<br>popis dlouhý
						<br><textarea name='popis_dlouhy' cols='40' rows='10'>".$zb->popis_dlouhy."</textarea>
					</td>
				</tr>
			</table>
		</form>
	";
}
else
{
	$post = array("id","mnozstvi_prodano","sku", "sku_shop", "cena_shop", "cena_nakupni", "cena_zvyhodnena", "popis_kratky", "dodavatel", "sku_dodavatel", "nazev", "mnozstvi", "mnozstvi_kriticke", "dostupnost", "popis_dlouhy", "img", "ostatni");
	foreach ($post as $value) {
		if (isset($_POST[$value])) {
			$$value = $_POST[$value];
		}
		else {
			$$value = "";
		}
	}

	$zbozi = new sklad();
	$zbozi->id = $id;
	$zbozi-> setZbozi(
		$dodavatel,
		$sku_dodavatel,
		$sku,
		$mnozstvi,
		$mnozstvi_kriticke,
		$mnozstvi_prodano,
		$sku_shop,
		$cena_shop,
		$cena_zvyhodnena,
		$cena_nakupni,
		$dostupnost,
		$nazev,
		$popis_kratky,
		$popis_dlouhy,
		$img,
		$ostatni
	);
	$zbozi-> setSkladem($dostupnost);
	$zbozi-> saveZbozi();
}
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
