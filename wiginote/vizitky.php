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
		<a href='vizitky.php'>zobrazit</a> | 
		<a href='vizitky.php?action=2'>pridat</a>
	";	
	if(empty($action))
	{
		echo "<h2>Vizitky</h2>";

		$query = mysql_query("SELECT * FROM `$db`.`vizitky` ORDER BY `vizitky`.`jmeno` ASC", $spojeni);
	if (mysql_num_rows($query) != 0){
		echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='25%'>
			jméno
			</td>
			<td width='25%'>
			telefon
			</td>
			<td width='25%'>
			email
			</td>
			<td width=25%'>
			www
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
				<a href='vizitky.php?action=1&id=".$d["id"]."'>".$d["jmeno"]."</a>
				</td>
				<td>
				<a href='vizitky.php?action=1&id=".$d["id"]."'>".$d["tel"]."</a>
				</td>
				<td>
				<a href='vizitky.php?action=1&id=".$d["id"]."'>".$d["mail"]."</a>
				</td>
				<td>
				<a href='vizitky.php?action=1&id=".$d["id"]."'>".$d["www"]."</a>
				</td>
				</tr>
		
			";
		
		
		}
		echo "</table>";
	}
	else echo "V databázi nejsou žádné vizitky";
	}
	if($action == 1)
	{
		$query = mysql_query("SELECT * FROM `$db`.`vizitky` WHERE `id` LIKE $id", $spojeni);
		if(mysql_num_rows($query) != 0){
		$d = mysql_fetch_array($query);
		echo "
			<p><b>".$d["jmeno"]."</b>
			<br>funkce: ".$d["funkce"]."
			<br>telefon: ".$d["tel"]."
			<br>email: ".$d["mail"]."
			<br>www: ".$d["www"]."
			<p><a href='vizitky.php?action=3&id=".$d["id"]."'>upravit</a>
		";
		
		}
		else echo"V databázi není tato vizitka.";
	}
	if($action == 2)
	{
		if(empty($krok))
	{
		echo "
			<form action='vizitky.php?action=2&krok=2' method='POST'>
			<p>jméno<br><input type='text' name='jmeno' size='30'>
			<p>funkce<br><input type='text' name='funkce' size='30'>
			<p>tel<br><input type='text' name='tel' size='30'>
			<p>email<br><input type='text' name='mail' size='30'>
			<p>www<br><input type='text' name='www' size='30'>
			<p><input type='submit' value='Přidat'>
			</form>
		";		
	}
	else 
	{			
		$sql = "INSERT INTO `$db`.`vizitky` VALUES ('".id("vizitky")."','$jmeno','$funkce','$tel','$mail','$www')";
		
		if(mysql_query($sql, $spojeni))
		{
			$task = $task."Data  upesne pridana.";
			$_SESSION["task"] = $task;
			header("location: vizitky.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}
	}
	}
	if($action == 3) // upravit vizitku
	{
		if(empty($krok))
		{
			$query = mysql_query("SELECT * FROM `$db`.`vizitky` WHERE `id` LIKE '$id'", $spojeni);
			mysql_num_rows($query);
			$d = mysql_fetch_array($query);
			echo "
				<form action='vizitky.php?action=3&krok=2&id=$id' method='POST'>
				<p>jméno<br><input type='text' name='jmeno' size='30' value='".$d["jmeno"]."'>
				<p>funkce<br><input type='text' name='funkce' size='30' value='".$d["funkce"]."'>
				<p>telefon<br><input type='text' name='tel' size='30' value='".$d["tel"]."'>
				<p>email<br><input type='text' name='mail' size='30' value='".$d["mail"]."'>
				<p>www<br><input type='text' name='www' size='30' value='".$d["www"]."'>
				<p><input type='submit' value='Upravit'>
				</form>
			";
		}
		else
		{
			$sql = "UPDATE `$db`.`vizitky` SET `jmeno` =  '$jmeno',`funkce` = '$funkce',`tel` = '$tel',`mail` = '$mail', `www` = '$www' WHERE `vizitky`.`id` = $id ;";
			//echo $sql;
			if(mysql_query($sql, $spojeni))
			{
				$_SESSION["task"] = "Vizitka upraveno";
				header("location: vizitky.php");
			}
			else
			{
				echo "<p>Vyskitla se chyba!";
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
