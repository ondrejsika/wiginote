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


	

	if($action == 3)
	{
		if($krok == 1) online(user());
		if($krok == 2) offline(user());
	}
	
	echo "<font size='4'>Stránka nastevení uživatele <b>".user_jmeno(user())."</b></font>";

	if (empty($action))
	{
		echo "
			<p><a href='work_time_vypis.php'>Pracovní doba</a>
			<p><a href='user_admin.php?action=1'>Uživatelské nastavení</a>
			<p><a href='historie.php'>Historie</a>
			<p><a href='user_admin.php?action=3'>Chat</a>
			<p><a href='user_admin.php?action=2'>Obrázek na pozadí</a>
		";
	}
	
	if($action == 1)
	{
		if(empty($krok))
		{
			$query = mysql_query("SELECT * FROM `$db`.`user` WHERE `id` LIKE '".user()."'", $spojeni);
			$d = mysql_fetch_array($query);
			echo "
				<form action='user_admin.php?action=1&krok=2' method='POST'>
				<p>name  <input type='text' name='name' value='".$d["name"]."'>
				<p>pass  <input type='password' name='pass1' value='".$d["pass"]."'>
				<p>pass  <input type='password' name='pass2' value='".$d["pass"]."'>
				<p>email  <input type='text' name='email' value='".$d["email"]."' size='30'> emaily oddelovat stradnikem. (a@a.a;b@b.b)
				<p>barva písma  <select name='font_color'>
				<option value='black'>černá</option>
				<option value='white'>bílá</option>
				<option value='blue'>modrá</option>
				</select>
				<p><input type='submit' value='ok'>
			";
		}
		if($krok == 2)
		{
			if($pass1 == $pass2)
			{
				$sql = "UPDATE `$db`.`user` SET  `name` =  '$name', `pass` = '$pass1', `email` = '$email' WHERE `user`.`id` = ".user();
				if(mysql_query($sql, $spojeni))
				{
					$_SESSION["task"] = "Přístupové údaje změněny";
					header("location: user_admin.php");
				}
				else
				{
					echo "<p>Vyskytla se chyba!<p>".$sql;
				}
			}
			else echo "zadaná hesla se neschodují";
		}
	}
	if($action == 2) // změna obrázku na pozadí
	{
		echo "<p>změna obrázku na pozadí";
		
		if(!empty($_FILES['file']['name']))
		{
			$new = "./pozadi/".user()."_".$_FILES['file']['name'];
			unlink("pozadi/".jmenoPozadi(user()));
			if (move_uploaded_file($_FILES['file']['tmp_name'], $new)) echo "ok";
			else echo "ko";
			
		}
		if(isset($_POST["repeat"]) or isset($_POST["pozice"]))
		{
			$sql = "UPDATE `$db`.`pozadi` SET `repeat` =  '".$_POST["repeat"]."', `pozice` =  '".$_POST["pozice"]."' WHERE `pozadi`.`user` LIKE ".user();
			mysql_query($sql, $spojeni);
		}

		$sql = "SELECT * FROM `pozadi` WHERE `user` like '".user()."'";
		$query = mysql_query($sql, $spojeni);
		if (mysql_num_rows($query) != 0){
			$d = mysql_fetch_array($query);
			if($d["repeat"] == 1) $s1 = "selected";
			if($d["repeat"] == 2) $s2 = "selected";
			if($d["repeat"] == 3) $s3 = "selected";
			if($d["repeat"] == 4) $s6 = "selected";
			if($d["pozice"] == 1) $s4 = "selected";
			if($d["pozice"] == 2) $s5 = "selected";
		}

		if(empty($krok))
		{
			echo "
				<form action='user_admin.php?action=2' ENCTYPE='multipart/form-data' method='POST'>
				<p>obrázek<br><input type='file' name='file' accept='*'>
				<p>repeat<br>
				<select name='repeat'>
				<option value='1' $s1>neopakovat</option>
				<option value='2' $s2>podle osy x</option>
				<option value='3' $s3>podle osy y</option>
				<option value='4' $s6>opakovat</option>
				</select>
				<p>pozice<br>
				<select name='pozice'>
				<option value='1' $s4>nahoře</option>
				<option value='2' $s5>levý horní roh</option>
				</select>
				<p><input type='submit' value='set'>
				</form>
			";
		}
	}
	if($action == 3)
	{
		echo "<p><font size='4'><i>Chat</i></font>
		<p><a onClick=\"window.open('chat.php', 'chat', 'width=400,height=800,left=0,top=0,location=no,scrollbars=yes')\">Zobrazit chat</a>
		<p>jste ";
		if(jeOnline(user())) echo "online";
		else echo "offline";
		if (jeOnline(user())) echo tlacitko("user_admin.php?action=3&krok=2","offline");
		else echo tlacitko("user_admin.php?action=3&krok=1","online");
		if(isset($_POST["refresh"])) setRefresh(user(),$_POST["refresh"]);
		$refresh = refreshTime(user());
		echo "
			<form action='user_admin.php?action=3&krok=3' method='POST'>
			<br>refresh time  <input type='text' name='refresh' value='$refresh'>
			<input type='submit' value='set'>
			</form>
		";
		
		if(isset($_POST["pocetZprav"]))
		{
			$sql = "UPDATE `$db`.`chat_nastaveni` SET `pocet_zprav` =  ".$_POST["pocetZprav"]." WHERE `chat_nastaveni`.`user` = ".user();
			mysql_query($sql, $spojeni);
		}
		$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".user(), $spojeni);
		$d = mysql_fetch_array($query);
		echo "
			<form action='user_admin.php?action=3&krok=3' method='POST'>
			<br>pocet zobrazenych zpráv  <input type='text' name='pocetZprav' value='".$d["pocet_zprav"]."'>
			<input type='submit' value='set'>
			</form>

		";
		
		if(isset($_POST["onlineTime"]))
		{
			$sql = "UPDATE `$db`.`chat_nastaveni` SET `online_time` =  ".$_POST["onlineTime"]." WHERE `chat_nastaveni`.`user` = ".user();
			mysql_query($sql, $spojeni);
		}
		$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".user(), $spojeni);
		$d = mysql_fetch_array($query);
		echo "
			<form action='user_admin.php?action=3&krok=3' method='POST'>
			<br>online time  <input type='text' name='onlineTime' value='".$d["online_time"]."'>
			<input type='submit' value='set'>
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
