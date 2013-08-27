<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if($access == 1)
{
/**********/
if(isset($_GET["shop"])) $shop = $_GET["shop"];
else $shop = "wigishop";

header('Content-Type','text/html; charset=WINDOWS-1250');
$echo = "";
	if(isset($_GET["ids_str"]))
	{
		foreach(explode(";",$_GET["ids_str"]) as $id)
		{
			if(!empty($id))
			{
				if($shop == "wigishop") $sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = $id AND `doprava` = 1";
				if($shop == "aukro") $sql = "SELECT * FROM `$db`.`zasilky_aukro` WHERE `id` = $id AND `zpusob_dodani` = 1";

				$q = mysql_query($sql, $spojeni);
				if(mysql_num_rows($q) != 0)
				{
					$d = mysql_fetch_array($q);
					$n = explode(" ", $d["d_jmeno"]);
					$jmeno = $n[0];
					$prijmeni = $n[1];

					$sku = explode(";", $d["sku"]);

					/*$hmotnost = 0;
					foreach($sku as $sku2)
					{
						$sql2 = "SELECT * FROM `$db`.`sklad` WHERE `sku` = $sku2";
						$q2 = mysql_query($sql2, $spojeni);
						$d2 = mysql_fetch_array($q2);
						$hmotnost = $hmotnost + floatval($d2["hmotnost"]);
					}*/

					$cena = floatval(celkova_cena($id)) + 70;
					if($d["zpusob_platby"] == "cashondelivery") $dobirka = $cena + 50;
					else $dobirka = "";
	
					$psc = str_replace(" ", "", $d["d_psc"]);	

					$radek = iconv("UTF-8","windows-1250", "\"\";\"".$d["d_jmeno"]."\";\"".$d["d_ulice"]."\";\"".$d["d_mesto"]."\";\"".$psc."\";\"".$d["d_stat"]."\";\"$jmeno\";\"$prijmeni\";\"".$d["email"]."\";\"".$d["d_telefon"]."\";\"".$d["d_telefon"]."\";\"\";\"\";\"".$d["vs"]."\";1.00;1.00;1.00;$cena;$dobirka;N;N;N;N;\"\"");

					$radek = str_replace("\n","", $radek);
					$echo .= $radek."\n";
				}
			}
		}
		echo $echo;
		
		
		$file = "intime_export_".time().".csv";
		$soubor = "var/export/intime/".$file;
		newFile($soubor,$echo);
		//*
		header("Content-Description: File Transfer");
		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$file\"");
		//*/
	}
	else
		echo "neplazne zadani";

	
/**********/
}


?>
