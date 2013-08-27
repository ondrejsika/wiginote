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

if(empty($krok))
{
	$z = new zasilky_topgadget;
	$doprava = new select("doprava",array(0,1,2,3,4,5),array("","intime","cpost","ems", "ems s", "osp"),0);
	$platba = new select("platba",array(0,1,2),array("","dobirka","naucet"),0);

	echo "
			<form action='sz_topgadget_pridat.php?krok=2' method='POST'>
			<p>vs: <input type='text' name='vs' value='".$z->nove("vs")."'>
			<br>nick: <input type='text' name='nick'>
			<br>email: <input type='text' name='email' value=''>
			<p>doprava ".$doprava->write()."
			<br>platba ".$platba->write()."
			<br>poštovné: <input type='text' name='postovne' value=''>
			<p><textarea name='adresa' rows='6' cols='47'></textarea>
			<p><input type='submit' value='další >>'>
			<form>
	";
}
elseif($krok == 2)
{
	// session
	$_SESSION["vs"] = $_POST["vs"];
	$_SESSION["nick"] = $_POST["nick"];
	$_SESSION["email"] = $_POST["email"];
	$_SESSION["zpusob_dodani"] = $_POST["doprava"];
	$_SESSION["zpusob_platby"] = $_POST["platba"];
	$_SESSION["postovne"] = $_POST["postovne"];
	// session

	if(!empty($_POST["adresa"]))
	{
		$adresa = $_POST["adresa"];
		//echo str_replace("\n","~", $adresa);
		$addr = str_replace("\t","",$adresa);
		$addr2 = explode("\n", $addr);
		$a = array();
		$i = 0;
		foreach($addr2 as $x)	
		{
			if($i == 1 or $x == 0) $a[] = $x;
			else $a[] = explode(" ", $x);
			$i++;
		}
	
		$jmeno = $a[0];
		$ulice = $a[1];
		if(strlen($a[2][1]) != 5)
		{
			$psc = $a[2][0].$a[2][1];
			$mesto = $a[2][2];
		}
		else
		{
			$psc = $a[2][0];
			$mesto = $a[2][1];
		}
		$telefon = $a[3][0];
	}
	else $jmeno = $telefon = $ulice = $psc = $mesto = "";

	echo "
			<form action='sz_topgadget_pridat.php?krok=3' method='POST'>
			<p>vs: ".$_POST["vs"]."
			<br>nick: ".$_POST["nick"]."
			<br>email: ".$_POST["email"]."

			<p><table><tr><td width='500'>
				<p>platební údaje <input type='checkbox' name='shodne' value='1' checked>
				<br>jméno:<br><input type='text' name='p_jmeno' value='$jmeno'>
				<br>firma:<br><input type='text' name='p_firma' value=''>
				<br>tel:<br><input type='text' name='p_telefon' value='$telefon'>
				<br>ulice:<br><input type='text' name='p_ulice' value='$ulice'>
				<br>psč:<br><input type='text' name='p_psc' value='$psc'>
				<br>město:<br><input type='text' name='p_mesto' value='$mesto'>
				<br><input type='text' name='p_stat' value='CZ'>
			</td><td>
				<p>dodací údaje 
				<br>jméno:<br><input type='text' name='d_jmeno' value='$jmeno'>
				<br>firma:<br><input type='text' name='d_firma' value=''>
				<br>tel:<br><input type='text' name='d_telefon' value='$telefon'>
				<br>ulice:<br><input type='text' name='d_ulice' value='$ulice'>
				<br>psč:<br><input type='text' name='d_psc' value='$psc'>
				<br>město:<br><input type='text' name='d_mesto' value='$mesto'>
				<br><input type='text' name='d_stat' value='CZ'>
			</td></tr></table>
			
			<input type='submit' value='další>>'>
		";
		$_SESSION["sku"] = $_SESSION["mnozstvi"] = $_SESSION["cena"] = $_SESSION["nazev"] = "";
}
if($krok == 3)
{
	// session
	$vars = array("p_jmeno", "p_firma", "p_telefon", "p_ulice", "p_psc", "p_mesto", "p_stat", "d_jmeno", "d_firma", "d_telefon", "d_ulice", "d_psc", "d_mesto", "d_stat");
	if(!isset($_POST["act"]))foreach($vars as $var) $_SESSION[$var] = $_POST[$var];

	if(isset($_POST["act"]))
	{
		$z = new sklad;
		$id = $z->selectFromSku($_POST["sku"]); 
		$z-> getZbozi($id, "wigishop");

		if(!empty($_POST["cena"])) $cena = $_POST["cena"];
		else $cena = $z->cena_shop;

		if(!empty($_POST["nazev"])) $nazev = $_POST["nazev"];
		else $nazev = $z->nazev;
	
		if(!empty($_SESSION["mnozstvi"]))//or empty($_SESSION["mnozstvi"]))
		{
			$_SESSION["sku"] .= ";" . $_POST["sku"];
			$_SESSION["nazev"] .= ";" . $nazev;
			$_SESSION["cena"] .= ";" . $cena;
			$_SESSION["mnozstvi"] .= ";" . $_POST["mnozstvi"];
		}
		else
		{
			$_SESSION["sku"] = $_POST["sku"];
			$_SESSION["nazev"] = $nazev;
			$_SESSION["cena"] = $cena;
			$_SESSION["mnozstvi"] = $_POST["mnozstvi"];
		}
	}
	// session

	if(!isset($_POST["act"]) or $_POST["act"] == "dalsi")
	{
		$na = $sa = array("");
		$sql = "SELECT * FROM `$db`.`sklad2` ORDER BY `sklad2`.`nazev` ASC";
		$q = mysql_query($sql, $spojeni);
		while($d = mysql_fetch_array($q))
		{
			if($d["sku"] != 0 and !empty($d["nazev"]))
			{
				$sa[] = $d["sku"];
				$na[] = $d["nazev"].", ".$d["sku"];
			}
		}

		$sp = new select("sku",$sa,$na,0);

		echo "
			<form action='sz_topgadget_pridat.php?krok=3&action=2' method='POST'>
			<p>vs: ".$_SESSION["vs"]."
			<br>nick: ".$_SESSION["nick"]."
			<br>email: ".$_SESSION["email"]."

			Produkt
			<br>".$sp->write()."
			<p>název
			<br><input type='text' name='nazev'>
			<p>cena
			<br><input type='text' name='cena'>
			<p>množství
			<br><input type='text' name='mnozstvi' value='1'>
			<br><button name='act' value='dalsi'>další produkt</button>
			<button name='act' value='dokoncit'>dokončit</button>
		";
	}
	if(isset($_POST["act"]) and $_POST["act"] == "dokoncit")
	{
		echo "<p>sku: ".$_SESSION["sku"];
		echo "<p>mnozstvi: ".$_SESSION["mnozstvi"];
		echo "<p>cena: ".$_SESSION["cena"];
		echo "<p>nazev: ".$_SESSION["nazev"];

		$i = new items;
		$sku = explode(";", $_SESSION["sku"]);
		$nazev = explode(";", $_SESSION["nazev"]);
		$cena = explode(";", $_SESSION["cena"]);
		$mnozstvi = explode(";", $_SESSION["mnozstvi"]);

		$items = "";
		$i2q = 0;
		foreach($sku as $a)
		{
			$item = $i->set($a, $nazev[$i2q], intval($cena[$i2q]), intval($mnozstvi[$i2q]));
			if(empty($items)) $items = $item;
			else $items .= ";" . $item;
			$i2q++;
		}
			
		$id2 = id("zasilky_topgadget");
		$a = new adresy;
		$adresa_dorucovaci = $a->set($_SESSION["d_jmeno"], $_SESSION["d_firma"], "", "", $_SESSION["d_ulice"], $_SESSION["d_mesto"], $_SESSION["d_psc"], $_SESSION["d_stat"], $_SESSION["d_telefon"]);
		
		$adresa_platebni = $a->set($_SESSION["p_jmeno"], $_SESSION["p_firma"], "", "", $_SESSION["p_ulice"], $_SESSION["p_mesto"], $_SESSION["p_psc"], $_SESSION["p_stat"], $_SESSION["p_telefon"]);

		$sql = "INSERT INTO `$db`.`zasilky_topgadget` (`id`, `shop`, `vs`, `cz`, `paid`, `bv_paid`, `send`, `note`, `doruceno`, `postovne`, `datum_obednavky`, `zpusob_platby`, `zpusob_dodani`, `email`, `nick`, `adresa_platebni`, `adresa_dorucovaci`, `items`, `avizovano`) VALUES ('".$id2."', 'topgadget', '".$_SESSION["vs"]."', '', '0', '0', '0', '', '0', '".$_SESSION["postovne"]."', '".time()."', '".$_SESSION["zpusob_platby"]."', '".$_SESSION["zpusob_dodani"]."', '".$_SESSION["email"]."', '".$_SESSION["nick"]."', '$adresa_platebni', '$adresa_dorucovaci', '$items', '0');";

		mysql_query($sql, $spojeni);

		$_SESSION["sku"] = $_SESSION["mnozstvi"] = $_SESSION["cena"] = $_SESSION["nazev"] = "";
		header("location: sz_topgadget_podrobnosti.php?id=$id2");
	}
}
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
