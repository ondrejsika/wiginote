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


	
	$sql = "SELECT * FROM `$db`.`note` WHERE `id` = $id";
	$query = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($query);

	if($d["only_for"] == 1) $pro = "soukromé";
	else $pro = "veřejné";
	if(jeSoubor($d["id"])) $jeSoubor = "<a href='download.php?id=".$d["id"]."'><img src='with_file.png' border='0'></a>";
	else $jeSoubor = "";
	if (($d["only_for"] == 1 and $d["pro"] == user()) or $d["only_for"] != 1 or user_prava(user()) == 0 or ($d["visible"] == 1 and user_prava(user()) == 0 )){
	echo "
		<p>".$d["id"]." - <b>".title($d["title"])."</b> $jeSoubor
		<br>autor: ".user_jmeno($d["autor"])." | pro: ".user_jmeno($d["pro"])."
		<br>$pro
		<br>vytvořeno: ".date("j/m/Y H:i:s", $d["vytvoreno"])." | ".splneno($d["splneno"])."
		<p>".br($d["text"])."
	";
	
	echo "
		<hr size='1' color='black'>
		
		<table width='300'>
		<tr valign='top'>
		<td width='100'>
	";
	if (user() == $d["pro"] or user() == $d["autor"])
	{
	if ($d["splneno"] == 0) echo tlacitko("action.php?id=".$d["id"]."&action=1","Splnit");
	else echo tlacitko("action.php?id=".$d["id"]."&action=5","Nesplnit");
	}
	echo "
		</td>
		<td width='100'>
	";
	if ((user() == $d["autor"]) and $d["visible"] == 1) echo tlacitko("action.php?id=$id&action=2","Vymazat");
	
	echo "
		</td>
		<td width='100'>
	";
	if ($d["autor"] == user())
	echo tlacitko("action.php?id=$id&action=3","Upravit");
	else echo "";
	echo "
		</td>
		</tr>
		</table>
	";
	//echo "<hr size='1' color='black'>";
	echo "<b>Diskuze</b>";
	if(!empty($text))
	{	
		$query = mysql_query("SELECT * FROM `$db`.`diskuze` WHERE `note` = '$id' ORDER BY `diskuze`.`time` DESC LIMIT 1", $spojeni);
		if(mysql_num_rows($query) != 0)
		{
			$da = mysql_fetch_array($query);
			$ok = $da["text"];
		}
		else $ok = "";
		
		if($text != $ok)
		$sql = "INSERT INTO `$db`.`diskuze` VALUES ('".id("diskuze")."','$id','".time()."','".user()."','$text')";
		mysql_query($sql, $spojeni);

		if(user() != autor($id) and user() != pro($id))
		{
			if(pro($id) == autor($id))
			{
				sendEmail(user(),autor($id),4,$id,$d["title"]);
			}
			else
			{
				sendEmail(user(),autor($id),4,$id,$d["title"]);
				sendEmail(user(),pro($id),4,$id,$d["title"]);
			}
		}
	
		
	}
	echo "
		<form action='poznamka.php?id=$id' method='POST'>
		<textarea rows='4' name='text' cols='60'>"."</textarea>
		<p><input type='submit' value='Přidat'>
		</form>
	";
	$query = mysql_query("SELECT * FROM `$db`.`diskuze` WHERE `note` = '$id' ORDER BY `diskuze`.`time` DESC", $spojeni);
	if(mysql_num_rows($query) != 0)
	{
		while($d = mysql_fetch_array($query))
		{
			echo "
				<p>".user_jmeno($d["user"]).", <font size='2.5'>".date("j/m/Y H:i", $d["time"])."
				<br>".$d["text"]."</font>
			";
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
