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



	
	if ($action == 1) // splneno
	{
		$sql = "UPDATE `$db`.`note` SET `splneno` =  '".time()."' WHERE `note`.`id` LIKE $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Poznamka splněna.";
			if(autor($id) != user()) sendEmail(user(),autor($id),2,$id,title2($id));
			$_SESSION["task"] = "Poznamka splněna.";
			if(empty($page)) header("location: home.php");
			if($page == 1)header("location: admin.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}
	}
	if ($action == 2) // vymazat
	{
		$sql = "UPDATE `$db`.`note` SET `visible` =  '0' WHERE `note`.`id` = $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Poznamka odstraněna.";
			$_SESSION["task"] = "Poznamka odstraněna.";
			if(empty($page)) header("location: home.php");
			if($page == 1)header("location: admin.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}
	}
	if ($action == 3) // upravit
	{
		if (empty($krok))
		{
			// formular
			$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `id` LIKE '$id'", $spojeni);
			mysql_num_rows($query);
			$d = mysql_fetch_array($query);
			
			if ($d["only_for"] == 1) $checked = "checked";
			else $checked = ""; 
			echo "
				<form action='action.php?action=3&krok=2&page=$page&id=$id' method='POST'>
				<p>id: ".$d["id"]."
				<p>Titulek<br><input type='text' name='title' size='30' value='".$d["title"]."'>
				<p>Pro | jen pro <input type='checkbox' name='only_for' value='1' $checked>
				<br><select name='pro'>
			";			
			foreach (users() as $value) {
				if ($d["pro"] == $value) $sel = "selected";
				else $sel = "";
				if ($value == user()) echo "<option value='".$value."' $sel>"."mě"."</option>";
				if ($value == 0) echo "<option value='0' $sel>"."všechny"."</option>";
				else echo "<option value='".$value."' $sel>".user_jmeno($value)."</option>";
			}
			echo "
				</select>
				<p>Poznámka<br><textarea rows='10' name='text' cols='60'>".$d["text"]."</textarea>
				<p><input type='submit' value='Upravit'>
				</form>
			";		
		}
		else
		{
			$sql = "UPDATE `$db`.`note` SET `pro` = '$pro',`vytvoreno` = '".time()."',`only_for` = '$only_for',`title` = '$title',`text` = '$text' WHERE `note`.`id` = $id ;";
			echo $sql;
			if(mysql_query($sql, $spojeni))
			{
				echo "Poznamka upravena.";
				$_SESSION["task"] = "Poznamka upravena.";
				if(empty($page)) header("location: home.php");
				if($page == 1)header("location: admin.php");
			}
			else
			{
				echo "Vyskitla se chyba!";
			}
		}
	}
	if ($action == 4) // zobrazit
	{
		$sql = "UPDATE `$db`.`note` SET `visible` =  '1' WHERE `note`.`id` LIKE $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Poznamka zobrazena.";
			$_SESSION["task"] = "Poznamka zobrazena.";
			if(empty($page)) header("location: home.php");
			if($page == 1)header("location: admin.php");
		}
		else
		{
			echo "Vyskitla se chyba!";
		}
	}
	if ($action == 5) // obnovit
	{
		$sql = "UPDATE `$db`.`note` SET `splneno` =  '0' WHERE `note`.`id` LIKE $id";
		if(mysql_query($sql, $spojeni))
		{
			echo "Poznámka byla obnovena.";
			$_SESSION["task"] = "Poznamka obnovena.";
			if(autor($id) != user()) sendEmail(user(),autor($id),3,$id,title2($id));
			if(empty($page)) header("location: home.php");
			if($page == 1)header("location: admin.php");
		}
		else
		{
			echo "Vyskitla se chyba!";
		}
	}
	if ($action == 6) // vymazat zeptat se
	{
		if(empty($krok))
		echo "
			<font size='4'>Opravdu chcete odstranit poznámku</font>
			<table width='100' border='0'>
			<tr>
			<td width='50%'>".tlacitko("action.php?action=6&krok=1&id=$id","Ano")."</td>
			<td width='50%'>".tlacitko("home.php","Ne")."</td>
			</tr>
			</table>
		";
		else
		{
			$sql = "UPDATE `$db`.`note` SET `visible` =  '0' WHERE `note`.`id` = $id";
			if(mysql_query($sql, $spojeni))
			{
				echo "Poznamka odstraněna.";
				$_SESSION["task"] = "Poznamka odstraněna.";
				if(empty($page)) header("location: home.php");
				if($page == 1)header("location: admin.php");
			}
			else
			{
				echo "Vyskytla se chyba!";
			}		
		}
	}
	else echo "";

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
