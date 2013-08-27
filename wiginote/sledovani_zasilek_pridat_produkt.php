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
	$pocet = 1;	

	if(empty($krok))
	{
		$na = $sa = array("");
		$sql = "SELECT * FROM `$db`.`sklad2` ORDER BY `sklad2`.`nazev` ASC";
		$q = mysql_query($sql, $spojeni);
		while($d = mysql_fetch_array($q))
		{
			$sa[] = $d["sku"];
			$na[] = $d["nazev"].", ".$d["sku"];
		}

		echo "<form action='sledovani_zasilek_pridat_produkt.php?krok=2&id=$id' method='post'>";
			
			$sp = new select("sku",$sa,$na,0);
			echo "
				Produkt
				<br>".$sp->write()."
				<p>název
				<br><input type='text' name='nazev'>
				<p>cena
				<br><input type='text' name='cena'>
				<p>množství
				<br><input type='text' name='mnozstvi'>
				<br><button>ok</button>
		";
		/*
		
		<p>Název
				<br><input type='text' name='nazev'>

		//*/
	}
	if($krok == 2)
	{
		
			$s_sku = $_POST["sku"];
			if(isset($_POST["nazev"]) and !empty($_POST["nazev"])) $s_sku = "~".$_POST["nazev"];
			$s_cena = $_POST["cena"];
			$s_mnozstvi = $_POST["mnozstvi"];
			
			if(empty($s_mnozstvi)) $s_mnozstvi = 1;
			if(empty($s_cena))
			{
				$sql = "SELECT * FROM `$db`.`sklad2` WHERE `sku` = '$sku'";
				$q = mysql_query($sql, $spojeni);
				$d = mysql_fetch_array($q);
				$s_cena = pole($d["cena_shop"],1,":");
			}
			
		
		echo "<br>".$s_sku; // sku zbozi
		echo "<br>".$s_mnozstvi; // mnozstvi
		echo "<br>".$s_cena; // nova cena na fakturu
	
		$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		
		echo $sql = "UPDATE  `$db`.`zasilky` SET `sku` =  '".$d["sku"].";$s_sku',`mnozstvi_polozky` =  '".$d["mnozstvi_polozky"].";$s_mnozstvi',`cena_polozky_sdph` =  '".$d["cena_polozky_sdph"].";$s_cena' WHERE  `zasilky`.`id` = $id;";
		
		mysql_query($sql, $spojeni);
		header("location: sledovani_zasilek_podrobnosti.php?id=$id");
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
