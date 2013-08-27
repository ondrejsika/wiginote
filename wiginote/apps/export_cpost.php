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
	if(isset($_GET["z"]))
	{
		$echo = "";
		$krok = 1;
		$krok2 = 1;
		foreach(explode(";",$_GET["z"]) as $id)
		{
			if($krok == $krok2)
				{
				$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = $id AND `doprava` = 2 ";
				$q = mysql_query($sql, $spojeni);
				if(mysql_num_rows($q) != 0)
				{
					$d = mysql_fetch_array($q);
					$n = explode(" ", $d["d_jmeno"]);
					$jmeno = $n[0];
					$prijmeni = $n[1];

					$sku = explode(";", $d["sku"]);

					$hmotnost = 0;
/*
					foreach($sku as $sku2)
					{
						$sql2 = "SELECT * FROM `$db`.`sklad` WHERE `sku` = $sku2";
						$q2 = mysql_query($sql2, $spojeni);
						$d2 = mysql_fetch_array($q2);
						if(1==2)//($d2["hmotnost"] == 0 or $d2["hmotnost"] == "")
						{
							$k = $krok2+1;
							echo "
								<form action='cpost_export.php?krok=".$k."'>
									<input type='text' name='hm'>
								</form>			
							";
							break;
						}
						else $krok2++;
	
						$hmotnost = $hmotnost + floatval($d2["hmotnost"]);
					}
//*/
					if($d["zpusob_platby"] == "cashondelivery") $dobirka = 50;
					else $dobirka = 0;
					$celkova_cena = celkova_cena($id) + 80 + $dobirka; // 80 postovne

					if($celkova_cena < 500) $cena = 500;
					elseif($celkova_cena > 30000) $cena = 30000;
					else $cena = $celkova_cena;

					$echo .= $d["vs"].";;;\"$prijmeni\";;;\"$jmeno\";\"".$d["d_mesto"]."\";;\"".$d["d_ulice"]."\";;;".$d["d_psc"].",\"".$d["d_stat"]."\";;;;".$d["d_telefon"].";;\"".$d["email"]."\";".$d["vs"].";;$hmotnost;$cena;".$d["vs"].";7;$celkova_cena;\"CZK\";".$d["vs"]."\n";
				}
			}
		}
		echo $echo;
		//*
		$file = "cpost_export_".time().".csv";
		$soubor = "var/export/cpost/".$file;
		$f = fopen($soubor, "w");
		fwrite($f, $echo);
		fclose($f);
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
