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

	if(empty($krok))
	{
		echo "<form action='sledovani_zasilek_upravit.php?id=$id&krok=2' method='POST'>";

		$dop = postovne($d["doprava"]);
		if($dop[0] == "") $doprava = "OSP";
		else $doprava = $dop[0];

		echo "
			<p>vs: <b>".$d["vs"]."</b>
			<p>email: <input type='text' name='email' value='".$d["email"]."'><p>
			<table><tr><td width='400'>
				<p>dodací údaje
				<br><input type='text' name='d_jmeno' value='".$d["d_jmeno"]."'>   <input type='text' name='d_spolecnost' value='".$d["d_spolecnost"]."'>
				<br>tel: <input type='text' name='d_telefon' value='".$d["d_telefon"]."'>
				<br><input type='text' name='d_ulice' value='".$d["d_ulice"]."'>
				<br><input type='text' name='d_psc' value='".$d["d_psc"]."'> <input type='text' name='d_mesto' value='".$d["d_mesto"]."'>
				<br><input type='text' name='d_stat2' value='".$d["d_stat2"]."'> <input type='text' name='d_stat' value='".$d["d_stat"]."'>
			</td><td>
				<p>platební údaje
				<br><input type='text' name='p_jmeno' value='".$d["p_jmeno"]."'>   <input type='text' name='p_spolecnost' value='".$d["p_spolecnost"]."'>
				<br>tel: <input type='text' name='p_telefon' value='".$d["p_telefon"]."'>
				<br><input type='text' name='p_ulice' value='".$d["p_ulice"]."'>
				<br><input type='text' name='p_psc' value='".$d["p_psc"]."'> <input type='text' name='p_mesto' value='".$d["p_mesto"]."'>
				<br><input type='text' name='p_stat2' value='".$d["p_stat2"]."'> <input type='text' name='p_stat' value='".$d["p_stat"]."'>
			</td></tr></table>
			<p>počet položek: ".$d["pocet_polozek"]."
			<br>položky:
		
		";
		$jmeno_polozky = explode(";", $d["jmeno_polozky"]);
		$sku = explode(";", $d["sku"]);
		$mnozstvi = explode(";", $d["mnozstvi_polozky"]);
		$bdph = explode(";", $d["cena_polozky_bdph"]);
		$dph = explode(";", $d["dph_polozky"]);
		$sdph = explode(";", $d["cena_polozky_sdph"]);
			$i = 0;
		foreach($sku as $a)
		{
			$zb = new sklad();
			$id_zb = $zb->selectFromSku($a);
			$zb->getZbozi($id_zb, $shop);

			if(!isset($mnozstvi[$i])) $mnozstvi[$i] = $mnozstvi[$i-1];

			echo "<p><b>".$a."</b> ".$zb->nazev." <input type='checkbox' name='vymazat_$i' value='1'> Vymazat";
			echo "<br>mnozstvi: <input type='text' name='mnozstvi_$i' value='".$mnozstvi[$i]."'>";
			echo "<br>cena bez DPH: ".naCislo($sdph[$i])*0.8;
			echo "<br>DPH: ".naCislo($sdph[$i])*0.2;
			echo "<br>cena s DPH: <input type='text' name='cena_$i' value='".naCislo($sdph[$i])."'>";
			$i++;	
		}
		echo "<p><input type='submit' value='upravit'>";
	}
	else
	//                   stranka 2
	{
		$post = array("email","d_spolecnost","d_jmeno","d_ulice","d_psc","d_mesto","d_stat","d_stat2","d_telefon","p_spolecnost","p_jmeno","p_ulice","p_psc","p_mesto","p_stat","p_stat2","p_telefon");
		foreach ($post as $value) {
			if (isset($_POST[$value])) {
				$$value = $_POST[$value];
			}
			else {
				$$value = "";
			}
		}

		// BEGIN sql uprava
		$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
		$query = mysql_query($sql,$spojeni);
		$d = mysql_fetch_array($query);

		$sku = explode(";", $d["sku"]);
		$mnozstvi = explode(";", $d["mnozstvi_polozky"]);
		
		$cena = explode(";", $d["cena_polozky_sdph"]);
		$i2q = 0;
		foreach($sku as $a)
		{
			if(!isset($_POST["vymazat_$i2q"]))
			{
				
				$cena[$i2q] = $_POST["cena_$i2q"];
				$mnozstvi[$i2q] = $_POST["mnozstvi_$i2q"];
			}
			else
			{
//				unset($sku[$i2q]);
				$sku = array_delete($sku, $i2q);
//				unset($mnozstvi[$i2q]);
				$mnozstvi = array_delete($mnozstvi, $i2q);
//				unset($cena[$i2q]);
				$cena = array_delete($cena, $i2q);
			}
			$i2q++;
		}

		
		$i2w = 0;
		foreach($sku as $a)
		{
			if($i2w == 0) $sku2 = $a;
			else  $sku2 .= ";" . $a;
			if($i2w == 0) $mnozstvi2 = $mnozstvi[$i2w];
			else  $mnozstvi2 .= ";" . $mnozstvi[$i2w];
			if($i2w == 0) $cena2 = $cena[$i2w];
			else  $cena2 .= ";" . $cena[$i2w];
			$i2w++;
		}
		

		//`cena_celkem_sdph` =  '$cena_celkem_sdph',
		$sql = "
			UPDATE  `$db`.`zasilky` SET 
			`sku` =  '$sku2',
			`email` =  '$email',
			`d_spolecnost` =  '$d_spolecnost',
			`d_jmeno` =  '$d_jmeno',
			`d_ulice` =  '$d_ulice',
			`d_psc` =  '$d_psc',
			`d_mesto` =  '$d_mesto',
			`d_stat` =  '$d_stat',
			`d_stat2` =  '$d_stat2',
			`d_telefon` =  '$d_telefon',
			`p_spolecnost` =  '$p_spolecnost',
			`p_jmeno` =  '$p_jmeno',
			`p_ulice` =  '$p_ulice',
			`p_psc` =  '$p_psc',
			`p_mesto` =  '$p_mesto',
			`p_stat` =  '$p_stat',
			`p_stat2` =  '$p_stat2',
			`p_telefon` =  '$p_telefon',
			`mnozstvi_polozky` =  '$mnozstvi2',
			`cena_polozky_sdph` =  '$cena2' 
			WHERE  `zasilky`.`id` = $id;
		";

		echo "sku>".$sku2." mnozstvi>".$mnozstvi2." cena>".$cena2;

		mysql_query($sql, $spojeni);
		header("location: sledovani_zasilek_podrobnosti.php?id=$id");
/*

*/

		// END sql uprava
	}
	///
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
