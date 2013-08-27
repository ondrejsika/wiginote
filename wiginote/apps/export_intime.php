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
				if($shop == "wigishop") {$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = $id AND `doprava` = 1"; $z = new zasilky_wigishop;}
				if($shop == "aukro") {$sql = "SELECT * FROM `$db`.`zasilky_aukro` WHERE `id` = $id AND `zpusob_dodani` = 1"; $z = new aukro;}

				$q = mysql_query($sql, $spojeni);
				if(mysql_num_rows($q) != 0)
				{
					$d = mysql_fetch_array($q);
					$z->load($d["id"]);
					$ad = new adresy;
					$ad->load($d["adresa_dorucovaci"]);

					$n = explode(" ", $ad->jmeno);
					$jmeno = $n[0];
					$prijmeni = $n[1];

					$cena = $z->cena_celkem();
					if($d["zpusob_platby"] == "cashondelivery") $dobirka = $cena;
					else $dobirka = "";
	
					$psc = str_replace(" ", "", $ad->psc);	

					$radek = iconv("UTF-8","windows-1250", "\"\";\"".$ad->jmeno."\";\"".$ad->ulice."\";\"".$ad->mesto."\";\"".$psc."\";\"".$ad->stat."\";\"$jmeno\";\"$prijmeni\";\"".$d["email"]."\";\"".$ad->telefon."\";\"".$ad->telefon."\";\"\";\"\";\"".$d["vs"]."\";1.00;1.00;1.00;$cena;$dobirka;N;N;N;N;\"\"");

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
