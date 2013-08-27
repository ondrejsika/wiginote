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

if(!empty($text) or !empty($title))
	{
		$new = "./var/files/poznamky/".id("note")."_".$_FILES['file']['name'];
		//echo $_FILES['file']['type'];
		if(!file_exists($new))
		{
			if (move_uploaded_file($_FILES['file']['tmp_name'], $new))
			{
				$up = "
					Soubor <B>".$_FILES['file']['name']."</B> 
					o velikosti <B>".$_FILES['file']['size']."</B> bajtů 
					byl nahran na sever pod nazvem <b>".id("note")."_".$_FILES['file']['name']."</b>
				";
			}
			else
				//$up = "Žádný soubor jste neuploadovali !!!";
				$up = "";
		}
		hideFile($new);
		$a = accessRights($new);
		$id = id("note");
		if ($only_for == "") $only_for = 0;
		if ($pro == 0) $only_for = 0;
		else $sql = "INSERT INTO `$db`.`note` VALUES ('$id','".user()."','$pro','$only_for','$title','$text','".time()."','','1')";
		
		if(mysql_query($sql, $spojeni))
		{
			$_SESSION["task"] = "Data upesne pridana.<br>$up";
			
			if(empty($title)) $title = "Bez předmětu";
			if(empty($text)) $title = " ";

			if(user() != $pro) sendEmail(user(),$pro,1,$id,"$title;".cut($text,500));
			if(empty($page)) header("location: home.php");
			if($page == 1)header("location: admin.php");
		}
		else
		{
			echo "Vyskytla se chyba!";
		}	
	}
	else 
	{

			echo "
				<form ENCTYPE='multipart/form-data' action='pridat.php?krok=2' method='POST'>
				<p>Titulek<br><input type='text' name='title' size='30'>
				<p>Pro | jen pro <input type='checkbox' name='only_for' value='1'>
				<br><select name='pro'>
				<option value='0'>pro všechny</option>
			";			
			foreach (users() as $value) {
				if ($value == user()) echo "<option value='".$value."' selected>"."mě"."</option>";
				else echo "<option value='".$value."'>".user_jmeno($value)."</option>";
			}
			echo "
				</select>
				<p><input type='file' name='file' accept='*'>
				<p>Poznámka<br><textarea rows='10' name='text' cols='60'></textarea>
				<p><input type='submit' value='Přidat'>
				</form>
			";	
		}
	

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
