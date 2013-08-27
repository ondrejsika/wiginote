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
include "core/pages/menu.php";


	echo "
		<a href='zbozi.php'>zobrazit</a> | 
		<a href='zbozi.php?action=2'>pridat</a>
	";	
	if(empty($action))
	{
		echo "<h2>Všechno zboží</h2>";

		$query = mysql_query("SELECT * FROM `$db`.`zbozi` WHERE `visible` = '1' ORDER BY `zbozi`.`vytvoreno` DESC", $spojeni);
	if (mysql_num_rows($query) != 0){
		echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='5%'>
			id
			</td>
			<td width='5%'>
			sku
			</td>
			<td width='20%'>
			nazev
			</td>
			<td width='5%'>
			počet
			</td>
			<td width='15%'>
			vytvořeno
			</td>
			<td width='15%'>
			splněno
			</td>
			<td width='35%'>
			poznamka
			</td>
			</tr>	
		";
		$i = 0;
		while($d = mysql_fetch_array($query)){
			if($i == 0) $color = "bgcolor='#BFE0DE'";
			else $color = "";
			if($i == 1) $i = 0;
			else $i++;
			echo "
				<tr valign='top' $color>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".$d["id"]."</a>
				</td>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".$d["sku"]."</a>
				</td>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".$d["nazev"]."</a>
				</td>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".$d["pocet"]."</a>
				</td>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".date("j/m/Y H:i", $d["vytvoreno"])."</a>
				</td>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".splneno2($d["splneno"])."</a>
				</td>
				<td>
				<a href='zbozi.php?action=1&id=".$d["id"]."'>".cut($d["poznamka"],30)."</a>
				</td>
				</tr>
		
			";
		
		
		}
		echo "</table>";
	}
	else echo "V databázi nejsou žádné poznámky";
	}
	if($action == 1)
	{
		echo "<h2>Detail zboží</h2>";
		$query = mysql_query("SELECT * FROM `$db`.`zbozi` WHERE `id` LIKE $id AND `visible` = '1'", $spojeni);
		if(mysql_num_rows($query) != 0){
		if(jmenoObrazku($id))
		{
			$file = jmenoObrazku($id);
			$soubor = "var/img/zbozi/$file";
			$obrazek = "<img src='$soubor'>";
		}
		else $obrazek = "";
		$d = mysql_fetch_array($query);
		echo "
			<p>".$d["id"]." - <b>".$d["nazev"]."</b>
			<br>autor: ".user_jmeno($d["autor"])."
			<br>vytvořeno: ".date("j/m/Y H:i:s", $d["vytvoreno"])." | ".splneno($d["splneno"])."
			<br>počet: ".$d["pocet"]."
			<p>".br($d["poznamka"])."
			<p>$obrazek
		";
		echo "
			<hr size='1' color='black'>
		
			<table width='300'>
			<tr valign='top'>
			<td width='100'>
		";
		if ($d["splneno"] == 0) echo tlacitko("zbozi.php?id=".$d["id"]."&action=5","Splnit");
		else echo tlacitko("zbozi.php?id=".$d["id"]."&action=6","Nesplnit");

		echo "
			</td>
			<td width='100'>
		";
		if (user() == $d["autor"])
		echo tlacitko("zbozi.php?id=$id&action=7","Vymazat");
		echo "
			</td>
			<td width='100'>
		";
		if ($d["autor"] == user())
		echo tlacitko("zbozi.php?id=$id&action=3","Upravit");
		else echo "";
		echo "
			</td>
			</tr>
			</table>
		";
		}
		else echo"V databázi není toto zboží.";
	}
	if($action == 2)
	{
		if(empty($krok))
	{
		echo "
			<form ENCTYPE='multipart/form-data' action='zbozi.php?action=2&krok=2' method='POST'>
			<p>sku<br><input type='text' name='sku' size='30'>
			<p>název<br><input type='text' name='nazev' size='30'>
			<p>pocet<br><input type='text' name='pocet' size='30'>
			<p>obrázek<br><input type='file' name='file' accept='*'>
			<p>Poznámka<br><textarea rows='10' name='poznamka' cols='60'></textarea>
			<p><input type='submit' value='Přidat'>
			</form>
		";		
	}
	else 
	{
		$new = "var/img/zbozi/".id("zbozi")."_".$_FILES['file']['name'];
		if(move_uploaded_file($_FILES['file']['tmp_name'], $new))
		{
			$task = "Obrázek byl nahrán<br>";
		}
		else
		{
			$task = "";
		}
			
		$sql = "INSERT INTO `$db`.`zbozi` VALUES ('".id("zbozi")."','$sku','".time()."','0','".user()."','$nazev','$pocet','$poznamka','1')";
		
		if(mysql_query($sql, $spojeni))
		{
			$task = $task."Data  upesne pridana.";
			$_SESSION["task"] = $task;
			header("location: zbozi.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}
	}
	}
	if($action == 3) // upravit zboží
	{
		if(empty($krok))
		{
			$query = mysql_query("SELECT * FROM `$db`.`zbozi` WHERE `id` LIKE '$id'", $spojeni);
			mysql_num_rows($query);
			$d = mysql_fetch_array($query);
			echo "
				<form action='zbozi.php?action=3&krok=2&id=$id' method='POST'>
				<p>sku<br><input type='text' name='sku' size='30' value='".$d["sku"]."'>
				<p>název<br><input type='text' name='nazev' size='30' value='".$d["nazev"]."'>
				<p>pocet<br><input type='text' name='pocet' size='30' value='".$d["pocet"]."'>
				<p>Poznámka<br><textarea rows='10' name='poznamka' cols='60'>".$d["poznamka"]."</textarea>
				<p><input type='submit' value='Upravit'>
				</form>
			";
		}
		else
		{
			$sql = "UPDATE `$db`.`zbozi` SET `sku` =  '$sku',`nazev` = '$nazev',`pocet` = '$pocet',`poznamka` = '$poznamka' WHERE `zbozi`.`id` = $id ;";
			echo $sql;
			if(mysql_query($sql, $spojeni))
			{
				$_SESSION["task"] = "Zboží upraveno";
				header("location: zbozi.php");
			}
			else
			{
				echo "Vyskitla se chyba!";
			}
		}
	}
	if($action == 5) // splnit
	{
		$sql = "UPDATE `$db`.`zbozi` SET `splneno` =  '".time()."' WHERE `zbozi`.`id` LIKE $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Poznamka splněna.";
			$_SESSION["task"] = "Splněno.";
			header("location: zbozi.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}	
	}
	if($action == 6) // obnovit
	{
		$sql = "UPDATE `$db`.`zbozi` SET `splneno` =  '0' WHERE `zbozi`.`id` LIKE $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Zbozi obnoveno.";
			$_SESSION["task"] = "Zboží obnoveno";
			header("location: zbozi.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}		
	}
	if($action == 7)
	{
		$sql = "UPDATE `$db`.`zbozi` SET `visible` =  '0' WHERE `zbozi`.`id` LIKE $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Poznamka odstraněna.";
			$_SESSION["task"] = "Zboží odstraněno";
			header("location: zbozi.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
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
