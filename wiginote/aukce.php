<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

include "core/header.php"; // header
include "core/inc.php"; // included files

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if(!rights()) echo "<h1>access denied</h1>";

elseif($access == 1)
{
/**********/
include "core/pages/head.php";
include "core/pages/menu.php";


	echo "
		<a href='aukce.php'>zobrazit</a> | 
		<a href='aukce.php?action=2'>pridat</a>
	";	
	if($action == 1) echo " | <a href='aukce.php?action=3&id=$id'>upravit</a>";

	if(empty($action))
	{
		echo "<h2>Všechny aukce</h2>";

		$query = mysql_query("SELECT * FROM `$db`.`aukce` ORDER BY `aukce`.`time` DESC", $spojeni);
		if (mysql_num_rows($query) != 0){
		echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='10'>
			</td>
			<td>
			název
			</td>
			<td>
			datum
			</td>
			<td>
			sku
			</td>
			<td>
			cena
			</td>
			<td>
			druh
			</td>
			</tr>	
		";
		$i = 0;
		while($d = mysql_fetch_array($query)){
			echo "
				<tr valign='top' class=radek>
				<td>
					<a href='aukce.php?action=1&id=".$d["id"]."'><img src='var/img/main/plus.png' border='0'></a>
				</td>
				<td>
					".$d["nazev"]."
				</td>
				<td>
					".date("j/m/Y H:i:s",$d["time"])."
				</td>
				<td>
					".$d["sku"]."
				</td>
				<td>
					".$d["cena"]."
				</td>
				<td>
					".$d["druh"]."
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
		echo "<h2>Detail aukce</h2>";
		$sql = "SELECT * FROM `$db`.`aukce` WHERE `id` = '$id'";
		$query = mysql_query($sql, $spojeni);
		if(mysql_num_rows($query) != 0)
		{
			$d = mysql_fetch_array($query);
			echo "
				<p>".$d["id"]." - <b>".$d["nazev"]."</b>
				<br>vytvořeno: ".date("j/m/Y H:i:s", $d["time"])."
				<br>číslo aukce: ".$d["cislo_aukce"]." | SKU: ".$d["sku"]."
				<br>cena: ".$d["cena"]." | ".$d["druh"]."
				<p>".br($d["poznamka"])."
				<p><textarea rows='10' cols='60'>".$d["html_aukce"]."</textarea>
				<p>".$d["html_aukce"];
		}
		else echo"V databázi není tato aukce.";
	}
	if($action == 2)
	{
		if(empty($krok))
	{
		echo "
			<form action='aukce.php?action=2&krok=2' method='POST'>
			<p>Název
			<br><input type='text' name='nazev' size='30'>

			<p>číslo aukce
			<br><input type='text' name='cislo_aukce' size='30'>

			<p>sku
			<br><input type='text' name='sku' size='30'>

			<p>cena
			<br><input type='text' name='cena' size='30'>

			<p>druh
			<br><input type='text' name='druh' size='30'>

			<p>Poznámka
			<br><textarea rows='10' name='note' cols='60'></textarea>

			<p>html aukce
			<br><textarea rows='10' name='html_aukce' cols='60'></textarea>
			<p><input type='submit' value='Přidat'>
			</form>
		";		
	}
	else 
	{
		$sql = "INSERT INTO `$db`.`aukce` VALUES (
		'".id("aukce")."',
		'".time()."',
		'".$_POST["cislo_aukce"]."',
		'".$_POST["nazev"]."',
		'".$_POST["sku"]."',
		'".$_POST["cena"]."',
		'".$_POST["druh"]."',
		'".$_POST["html_aukce"]."',
		'".$_POST["note"]."'
		)";
		
		if(mysql_query($sql, $spojeni))
		{
			$task = $task."Data  upesne pridana.";
			$_SESSION["task"] = $task;
			header("location: aukce.php");
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
			$query = mysql_query("SELECT * FROM `$db`.`aukce` WHERE `id` = '$id'", $spojeni);
			mysql_num_rows($query);
			$d = mysql_fetch_array($query);
				echo "
			<h2>Upravujete aukci č. ".$d["cislo_aukce"].", id: $id</h2>
			<form action='aukce.php?action=3&krok=2&id=$id' method='POST'>
			<p>Název
			<br><input type='text' name='nazev' size='30' value='".$d["nazev"]."'>

			<p>číslo aukce
			<br><input type='text' name='cislo_aukce' size='30'  value='".$d["cislo_aukce"]."'>

			<p>sku
			<br><input type='text' name='sku' size='30'  value='".$d["sku"]."'>

			<p>cena
			<br><input type='text' name='cena' size='30' value='".$d["cena"]."'>

			<p>druh
			<br><input type='text' name='druh' size='30' value='".$d["druh"]."'>

			<p>Poznámka
			<br><textarea rows='10' name='note' cols='60'>".$d["poznamka"]."</textarea>

			<p>html aukce
			<br><textarea rows='10' name='html_aukce' cols='60'>".$d["html_aukce"]."</textarea>
			<p><input type='submit' value='Upravit'>
			</form>
		";		
			
		}
		else
		{
			$sql = "
				UPDATE  `$db`.`aukce` SET 
				`nazev` =  '".$_POST["nazev"]."',
				`cislo_aukce` =  '".$_POST["cislo_aukce"]."',
				`sku` =  '".$_POST["sku"]."',
				`cena` =  '".$_POST["cena"]."',
				`druh` =  '".$_POST["druh"]."',
				`html_aukce` =  '".$_POST["html_aukce"]."',
				`poznamka` =  '".$_POST["note"]."' WHERE  `aukce`.`id` = '$id'
			";
			echo $sql;
			if(mysql_query($sql, $spojeni))
			{
				$_SESSION["task"] = "Zboží upraveno";
				header("location: aukce.php");
			}
			else
			{
				echo "Vyskitla se chyba!";
			}
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
