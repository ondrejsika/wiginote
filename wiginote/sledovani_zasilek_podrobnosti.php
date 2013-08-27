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

	///
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
	$query = mysql_query($sql,$spojeni);
	$d = mysql_fetch_array($query);
	
	echo "<title>".$d["vs"]." | ".$d["p_jmeno"]."</title>";

	$dop = postovne($d["doprava"]);
	if($dop[0] == "") $doprava = "OSP";
	else $doprava = $dop[0];

	if($d["zpusob_platby"] == "cashondelivery") $postovne = $dop[1] + 50;
	else $postovne = $dop[1];

	$sdph = celkova_cena($id) + $postovne;
	$dph = $sdph * 0.2;
	$bdph = $sdph * 0.8;

	echo "
		<p>vs: <b>".$d["vs"]."</b> <a href='sledovani_zasilek_upravit.php?id=$id'>Upravit</a>
		<br>datum objednávky: ".$d["datum_obednavky"]."
		<br>zpusob dodání: ".$doprava."
		<br>zpusob platby: ".$d["zpusob_platby"]."
		<table><tr><td width='400'>
			Aktuální ceny
			<br>cena bez DPH: ".$bdph."
			<br>DPH: ".$dph."
			<br>poštovné: ".$postovne."
			<br>cena s DPH: ".$sdph."
		</td><td>
			Magento
			<br>cena bez DPH: ".$d["cena_bdph"]."
			<br>DPH: ".$d["dph"]."
			<br>poštovné: ".$d["postovne"]."
			<br>cena s DPH: ".$d["cena_celkem_sdph"]."
		</td></tr></table>

		<p>email: ".$d["email"]."<p>
		<table><tr><td width='400'>
			<p>dodací údaje
			<br>".$d["d_jmeno"]."   ".$d["d_spolecnost"]."
			<br>tel: ".$d["d_telefon"]."
			<br>".$d["d_ulice"]."
			<br>".$d["d_psc"]." ".$d["d_mesto"]."
			<br>".$d["d_stat2"]." ".$d["d_stat"]."
		</td><td>
			<p>platební údaje
			<br>".$d["p_jmeno"]."   ".$d["p_spolecnost"]."
			<br>tel: ".$d["p_telefon"]."
			<br>".$d["p_ulice"]."
			<br>".$d["p_psc"]." ".$d["p_mesto"]."
			<br>".$d["p_stat2"]." ".$d["p_stat"]."  
		</td></tr></table>
		<p>počet položek: ".$d["pocet_polozek"]."
		<br>položky:
	";
	echo "<p><a href='sledovani_zasilek_pridat_produkt.php?id=$id'>Přidat produkt</a>";
	
		$jmeno_polozky = explode(";", $d["jmeno_polozky"]);
		$sku = explode(";", $d["sku"]);
		$mnozstvi = explode(";", $d["mnozstvi_polozky"]);
		$bdph = explode(";", $d["cena_polozky_bdph"]);
		$dph = explode(";", $d["dph_polozky"]);
		$sdph = explode(";", $d["cena_polozky_sdph"]);
			$i = 0;
	
		foreach($sku as $a)
		{
			if(!contains($a, "~")){
			$zb = new sklad();
			$id_zb = $zb->selectFromSku($a);
			$zb->getZbozi($id_zb, $shop);
	
			
			$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = $a";
			$q = mysql_query($sql,$spojeni);
			$d = mysql_fetch_array($q);
			if(!isset($mnozstvi[$i])) $mnozstvi[$i] = $mnozstvi[$i-1];

			$nasklade = $zb->naSklade("naprodejne");
			if($nasklade > 0) $skladem = "<font color='green'>SKLADEM $nasklade</font>";
			if($nasklade <= 0) $skladem = "<font color='red'>NESKLADEM $nasklade</font>";
			$nazev = $zb->nazev;
			
			$x = "$skladem <a onclick=\"window.open('obrazek_produktu.php?sku=$a', 'img', 'width=300,height=300,left=0,top=0,location=no,scrollbars=no')\">Obrázek</a>";
			}
	
			else{
			$nazev = str_replace("~","", $a);
			$a = "";
			}

			echo "<p><b>".$a."</b> ".$nazev." $x";
			echo "<br>mnozstvi: ".$mnozstvi[$i];
			echo "<br>cena bez DPH: ".naCislo($sdph[$i])*0.8;
			echo "<br>DPH: ".naCislo($sdph[$i])*0.2;
			echo "<br>cena s DPH: ".naCislo($sdph[$i]);
			$i++;	
		}

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
