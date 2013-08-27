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

$pocet = 3;// ***************************************** počet

	$sql = "SELECT * FROM `$db`.`zasilky_hrackovnik` WHERE `id` = $id";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);
	
	$ap = new adresy;
	$ap->load($d["adresa_platebni"]);
	$ad = new adresy;
	$ad->load($d["adresa_dorucovaci"]);

	$items = explode(";", $d["items"]);
	$au = new zasilky_hrackovnik;
	$au->load($id);

	echo "
			<p>vs: ".$d["vs"]."
			 | <a href='sz_hrackovnik_upravit.php?id=$id'>Upravit</a>
			 | <a href='sz_hrackovnik_pridat_produkt.php?id=$id'>Přidat produkt</a>
			<br>nick: ".$d["nick"]."
			<br>email: ".$d["email"]."
			<p>doprava ".$d["zpusob_dodani"]."
			<br>platba ".$d["zpusob_platby"]."
			<br>poštovné ".$d["postovne"]." Kč
			<br>cena celkem: <b>".$au->cena_celkem()." Kč</b>

			<p><table><tr><td width='500'>
				<p>platební údaje 
				<br>".$ap->jmeno." ".$ap->firma."
				<br>tel: ".$ap->telefon."
				<br>".$ap->ulice."
				<br>".$ap->psc." ".$ap->mesto."
				<br>".$ap->stat."
			</td><td>
				<p>dodací údaje 
				<br>".$ad->jmeno." ".$ad->firma."
				<br>tel: ".$ad->telefon."
				<br>".$ad->ulice."
				<br>".$ad->psc." ".$ad->mesto."
				<br>".$ad->stat."
			</td></tr></table>
	";
	
	
		foreach($items as $item)
		{
			$it = new items;
			$it->load($item);
			$bez_dph = intval($it->cena*0.8);
			$dph = intval($it->cena*0.2);

			echo "
				<p><b>".$it->nazev."</b> SKU: ".$it->sku."
				<table width='400'>
					<tr>
						<td width='200'>
							množství: <b>".$it->mnozstvi."</b>
						</td>
						<td width='100'>
							cena/mj
						</td>
						<td width='100'>
							cena celkem
						</td>
					</tr>
					<tr>
						<td width='200'>
							cena s DPH:
						</td>
						<td width='100'>
							".intval($it->cena)." Kč
						</td>
						<td width='100'>
							<b>".intval($it->cena*$it->mnozstvi)." Kč</b>
						</td>
					</tr>
					<tr>
						<td width='200'>
							cena bez DPH: 
						</td>
						<td width='100'>
							".$bez_dph." Kč
						</td>
						<td width='100'>
							".$bez_dph*$it->mnozstvi." Kč
						</td>
					</tr>
					<tr>
						<td width='200'>
							DPH: 
						</td>
						<td width='100'>
							".$dph." Kč
						</td>
						<td width='100'>
							".$dph*$it->mnozstvi." Kč
						</td>
					</tr>
				</table>
			";
		}


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
