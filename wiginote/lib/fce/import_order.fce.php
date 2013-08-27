<?php
// &nbsp;

function naCislo($cislo)
{
	$cislo = str_replace("Kč","",$cislo);
	$cislo = str_replace(" ","",$cislo);
	return round(floatval(str_replace(",",".",$cislo)),2);
}

function sku($sku)
{
	return str_replace("-",";",$sku);
}


function import_order($file)
{
	global $spojeni, $db, $tb;
	$csv = new csv($file); // "var/import/magento/obednavky/order_export.csv"
	$save = new csv("var/export/order_export_convert.csv");
	$csv->load();

	$i = 0;
	$x = 0;
	foreach($csv->csv as $a)
	{
		if($i != 0)
		{
			$vs = $a[0];
			$datum_obednavky = $a[1];
			$stav = $a[2];
			$zpusob_platby = $a[4];
			$zpusob_dodani = $a[5];
			$cena_bdph = naCislo($a[6]);
			$dph = naCislo($a[7]);
			if($zpusob_platby == "cashondelivery") $dobirka = 50;
			else $dobirka = 0;
			$postovne = naCislo($a[8])*1.2+$dobirka; // s dani *1.2
			$cena_celkem_sdph = naCislo($a[10]);
			$email = $a[16];
			$d_jmeno = $a[17];
			$d_spolecnost = $a[18];
			$d_ulice = $a[19];
			$d_psc = $a[20];
			$d_mesto = $a[21];
			$d_stat = $a[24];
			$d_stat2 = $a[25];
			$d_telefon = $a[26];
			$p_jmeno = $a[27];
			$p_spolecnost = $a[28];
			$p_ulice = $a[29];
			$p_psc = $a[30];
			$p_mesto = $a[31];
			$p_stat = $a[34];
			$p_stat2 = $a[35];
			$p_telefon = $a[36];
			$pocet_polozek = $a[14];
			$jmeno_polozky = $a[38];
			$sku = sku($a[40]);
			$magento_note = $a[40];
			$cena_polozky_bdph = naCislo($a[43]);
			$mnozstvi_polozky = $a[44];
			$dph_polozky = naCislo($a[49]);
			$cena_polozky_sdph = naCislo($a[51]);
			
			if(strstr("barva:",$magento_note)) $mn = array($sku, $magento_note);

			$send = $paid = $doruceno = 0;
			if($stav == "canceled") $paid = 3;
			if($stav == "complete") { $paid = $send = 2; $doruceno = 1;}


			$prislusenstvi = "";

			$sku2 = $a[40];
						
						if(!is_numeric($sku2))
						{
							$sku3 = explode("-", $sku2);
							$qqq = 0;
							foreach($sku3 as $sku4)
							{
								if($qqq != 0) 
								{
									if(empty($prislusenstvi))$prislusenstvi .= $sku4;
									else $prislusenstvi .= ";".$sku4;
								}
								$qqq++;
							}
						}
		//	if(isset($csv->csv[$i+1][0]))
		//	{
			if($vs == $csv->csv[$i+1][0])
			{
				$wh = 0;
//isset($csv->csv[$i+1+$wh][0])
					while ($vs == $csv->csv[$i+1+$wh][0])
					{

						$sku2 = $csv->csv[$i+1+$wh][40];
						
						if(!is_numeric($sku2))
						{
							$sku3 = explode("-", $sku2);
							$qqq2 = 0;
							foreach($sku3 as $sku4)
							{
								if($qqq2 != 0) 
								{
									if(empty($prislusenstvi))$prislusenstvi .= $sku4;
									else $prislusenstvi .= ";".$sku4;
								}
								$qqq2++;
							}
						}

						$jmeno_polozky = $jmeno_polozky . ";" . $csv->csv[$i+1+$wh][38];
						$sku = $sku . ";" . sku($csv->csv[$i+1+$wh][40]);
						$mnozstvi_polozky = $mnozstvi_polozky . ";" . naCislo($csv->csv[$i+1+$wh][44]);
						$cena_polozky_bdph = $cena_polozky_bdph . ";" . naCislo($csv->csv[$i+1+$wh][43]);
						$dph_polozky = $dph_polozky . ";" . naCislo($csv->csv[$i+1+$wh][49]);
						$cena_polozky_sdph = $cena_polozky_sdph . ";" . naCislo($csv->csv[$i+1+$wh][51]);
						$wh++;
					}
					
			}
			//}
			else
			{
				$zapis_do_db = true;
			}
			
//*
			$zapis_do_db = true;
			$sql = "SELECT * FROM `$db`.`zasilky`";
			$q = mysql_query($sql, $spojeni);
			while($d = mysql_fetch_array($q))
			{
				if($vs == $d["vs"]) $zapis_do_db = false;	
			}//*/			

			if($zapis_do_db == true)
			{
				$mnozstvi2 = explode(";",$mnozstvi_polozky);
				$i2 = 0;
				foreach(explode(";",$sku) as $sku2)
				{
					/*$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = '$sku2'";
					$q = mysql_query($sql,$spojeni);
					$d = mysql_fetch_array($q);
					$nm = $d["mnozstvi"] - $mnozstvi2[$i2];*/

					

					//$sql = "UPDATE `$db`.`sklad` SET `mnozstvi` = '$nm' WHERE `sklad`.`sku` = '$sku2'";
					//mysql_query($sql,$spojeni);
					$i2++;
				}
			}

			$zpusob_dodani = str_replace("<b>","",$zpusob_dodani);
			$zpusob_dodani = str_replace("</b>","",$zpusob_dodani);

			if($zpusob_dodani == "msmultiflat_Kurýrní služba inTime - do 48h, obdoba PPL") $doprava = 1;
			if($zpusob_dodani == "msmultiflat_Česká pošta - do 48h") $doprava = 2;
			if($zpusob_dodani == "msmultiflat_Expresní doručení EMS - do 24h - Česká pošta") $doprava = 3;
			if($zpusob_dodani == "msmultiflat_Osobní převzetí na prodejně v Plzni") $doprava = 5;

	

		if($zapis_do_db == true)
		{
				//*
		echo "<b>$sku</b>";
		$skus = explode(";", $sku);
		$i2x = 0;
		foreach($skus as $a)
		{
			$zb = new sklad();
			$id_zb = $zb->selectFromSku($a);
			$zb->getZbozi($id_zb, "wigishop");

			$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = $a";
			$q = mysql_query($sql,$spojeni);
			$d = mysql_fetch_array($q);
			echo "<b>".$zb->cena_shop."</b>";
			if($i2x == 0) $cena_polozky_sdph = $zb->cena_shop;
			else $cena_polozky_sdph .= ";" . $zb->cena_shop;
			
			$i2x++;
		}
		
		//*/
// echo "<p>".$vs." ".$cena_polozky_sdph." ".$sku;
//  			echo "<p>".
	 $sql = "INSERT INTO `$db`.`zasilky` (`id`, `shop`, `vs`, `cz`, `paid`, `bv_paid`, `send`, `note`, `doruceno`, `doprava`, `datum_obednavky`, `stav`, `zpusob_platby`, `zpusob_dodani`, `cena_bdph`, `dph`, `postovne`, `cena_celkem_sdph`, `email`, `d_spolecnost`, `d_jmeno`, `d_ulice`, `d_psc`, `d_mesto`, `d_stat`, `d_stat2`, `d_telefon`,`p_spolecnost` , `p_jmeno`, `p_ulice`, `p_psc`, `p_mesto`, `p_stat`, `p_stat2`, `p_telefon`, `pocet_polozek`, `jmeno_polozky`, `sku`, `cena_polozky_bdph`, `mnozstvi_polozky`, `dph_polozky`, `cena_polozky_sdph`, `prislusenstvi`, `sended_email`) VALUES ('".id("zasilky")."', 'wigishop', '$vs', '', '$paid', '0', '$send', '', '$doruceno', '$doprava', '$datum_obednavky', '$stav', '$zpusob_platby', '$zpusob_dodani', '$cena_bdph', '$dph', '$postovne', '$cena_celkem_sdph', '$email', '$d_spolecnost','$d_jmeno', '$d_ulice', '$d_psc', '$d_mesto', '$d_stat', '$d_stat2', '$d_telefon', '$p_spolecnost','$p_jmeno', '$p_ulice', '$p_psc', '$p_mesto', '$p_stat', '$p_stat2', '$p_telefon', '$pocet_polozek', '$jmeno_polozky', '$sku', '$cena_polozky_bdph', '$mnozstvi_polozky', '$dph_polozky', '$cena_polozky_sdph', '$prislusenstvi', '0;0;0');";
				
	 $sql2 = "INSERT INTO `$db`.`zasilky2` (`id`, `shop`, `vs`, `cz`, `paid`, `bv_paid`, `send`, `note`, `doruceno`, `doprava`, `datum_obednavky`, `stav`, `zpusob_platby`, `zpusob_dodani`, `cena_bdph`, `dph`, `postovne`, `cena_celkem_sdph`, `email`, `d_spolecnost`, `d_jmeno`, `d_ulice`, `d_psc`, `d_mesto`, `d_stat`, `d_stat2`, `d_telefon`,`p_spolecnost` , `p_jmeno`, `p_ulice`, `p_psc`, `p_mesto`, `p_stat`, `p_stat2`, `p_telefon`, `pocet_polozek`, `jmeno_polozky`, `sku`, `cena_polozky_bdph`, `mnozstvi_polozky`, `dph_polozky`, `cena_polozky_sdph`, `prislusenstvi`, `sended_email`) VALUES ('".id("zasilky")."', 'wigishop', '$vs', '', '$paid', '0', '$send', '', '$doruceno', '$doprava', '$datum_obednavky', '$stav', '$zpusob_platby', '$zpusob_dodani', '$cena_bdph', '$dph', '$postovne', '$cena_celkem_sdph', '$email', '$d_spolecnost','$d_jmeno', '$d_ulice', '$d_psc', '$d_mesto', '$d_stat', '$d_stat2', '$d_telefon', '$p_spolecnost','$p_jmeno', '$p_ulice', '$p_psc', '$p_mesto', '$p_stat', '$p_stat2', '$p_telefon', '$pocet_polozek', '$jmeno_polozky', '$sku', '$cena_polozky_bdph', '$mnozstvi_polozky', '$dph_polozky', '$cena_polozky_sdph', '$prislusenstvi', '0;0;0');";

					mysql_query($sql,$spojeni);
					mysql_query($sql2, $spojeni);
					$sku_all = explode(";", $sku);
					$mn_all = explode(";", $mnozstvi_polozky);
					$i2x = 0;
					foreach($sku_all as $sku_x)
					{
						$zb = new sklad;
						$id_zb = $zb->selectFromSku($sku_x);
						$zb->getZbozi($id_zb);
						while(!isset($i2x)) $i2x = $i2x - 1;
						$zb->doSkladu("naprodejne", $mn_all[$i2x]*-1);
						$zb->saveZbozi();
						echo "<font color='red'>$sku_x ".$mn_all[$i2x]."</font>";
						$i2x++;
					}
				}
				$zapis_do_db = false;
		}
		$i++;
	}
include "apps/prevod_zasilek_wigishop.php";

		$sql = "TRUNCATE TABLE `$db`.`zasilky2`";
		mysql_query($sql, $spojeni);
}
?>
