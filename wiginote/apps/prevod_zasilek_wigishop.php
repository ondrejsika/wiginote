<?php

echo $sql = "SELECT * FROM `$db`.`zasilky2` ORDER BY `zasilky2`.`id` DESC";
$q = mysql_query($sql, $spojeni);
while($d = mysql_fetch_array($q))
{
	$sql2 = "SELECT * FROM `$db`.`zasilky_wigishop` WHERE `vs` = ".$d["vs"];
	$q2 = mysql_query($sql2, $spojeni);

	if(mysql_num_rows($q2) == 0)
	{
		$id2 = id("zasilky_wigishop");
		$a = new adresy;
		$adresa_dorucovaci = $a->set($d["d_jmeno"], $d["d_spolecnost"], "", "", $d["d_ulice"], $d["d_mesto"], $d["d_psc"], $d["d_stat"], $d["d_telefon"]);
	
		$adresa_platebni = $a->set($d["p_jmeno"], $d["p_spolecnost"], "", "", $d["p_ulice"], $d["p_mesto"], $d["p_psc"], $d["p_stat"], $d["p_telefon"]);

		$items = array();
		$it = new items;
		$i=0;
		$nazev = explode(";", $d["jmeno_polozky"]);
		$cena = explode(";", $d["cena_polozky_sdph"]);
		$mnozstvi = explode(";", $d["mnozstvi_polozky"]);
		foreach(explode(";", $d["sku"]) as $sku)
		{
	if(isset($mnozstvi[$i])) $mn = $mnozstvi[$i];
	else $mn = 1;
			$items[] = $it->set($sku, 
	$nazev[$i], 
	$cena[$i], 
	$mn);
			$i++;
		}
		$items = implode(";", $items);

		$dop = postovne($d["doprava"]);
		if($dop[0] == "") $doprava = "OSP";
		else $doprava = $dop[0];

		if($d["zpusob_platby"] == "cashondelivery") $postovne = $dop[1] + 50;
		else $postovne = $dop[1];

		if($d["zpusob_platby"] == "cashondelivery") $zp = 1;
		elseif($d["zpusob_platby"] == "bankpayment") $zp = 4;
		elseif($d["zpusob_platby"] == "paypal_standard") $zp = 3;
		elseif($d["zpusob_platby"] == "checkmo") $zp = 4;
		else $zp = 0;

		echo "<hr>";
		echo $sql = "INSERT INTO `$db`.`zasilky_wigishop` (`id`, `shop`, `vs`, `cz`, `paid`, `bv_paid`, `send`, `note`, `doruceno`, `postovne`, `datum_obednavky`, `zpusob_platby`, `zpusob_dodani`, `email`, `nick`, `adresa_platebni`, `adresa_dorucovaci`, `items`, `avizovano`) VALUES ('".$id2."', 'aukro', '".$d["vs"]."', '".$d["cz"]."', '".$d["paid"]."', '".$d["bv_paid"]."', '".$d["send"]."', '".$d["note"]."', '".$d["doruceno"]."', '".$postovne."', '".$d["datum_obednavky"]."', '$zp', '".$d["doprava"]."', '".$d["email"]."', '', '$adresa_platebni', '$adresa_dorucovaci', '$items', '0');";
	
		if(mysql_query($sql, $spojeni)) echo "<br>ok";
		else echo "<br>!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
	}
	else echo "<br>tato obednavka je jiz v systemu";
}
?>
