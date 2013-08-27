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
		<p><i><font size='4'>".user_jmeno($id)."</font></i> - poznámky uživatele
	";
	
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `pro` LIKE $id and `visible` LIKE '1' ORDER BY `note`.`vytvoreno` DESC", $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
			if ((($d["only_for"] == 1 and $d["pro"] == user()) or $d["only_for"] != 1 or $d["autor"] == user()) or user_prava(user()) == 0){
			if(jeSoubor($d["id"])) $jeSoubor = "<a href='download.php?id=".$d["id"]."'><img src='with_file.png' border='0'></a>";
			else $jeSoubor = "";
			echo "
				<p><b><a href='poznamka.php?id=".$d["id"]."'>".title($d["title"])."</a></b> $jeSoubor
				<br><font size='2'>autor: <a href='uzivatel.php?id=".$d["autor"]."'>".user_jmeno($d["autor"])."</a>
				<br>vytvořeno: ".date("j/m/Y H:i", $d["vytvoreno"])." | ".splneno($d["splneno"])."</font>
				<br>".cut($d["text"],100)."
		
			";
			}	
		}
	}
	else echo "V databázi nejsou žádné poznámky";


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
