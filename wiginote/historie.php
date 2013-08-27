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



	$id = user();
	echo "
		<p><i><font size='4'>".user_jmeno($id)."</font></i> - historie uživatele 
	";
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `pro` LIKE $id ORDER BY `note`.`vytvoreno` DESC", $spojeni);
	if (mysql_num_rows($query) != 0){
		echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='5%'>
			id
			</td>
			<td width='45%'>
			titulek
			</td>
			<td width='25%'>
			vytvoreno
			</td>
			<td width='25%'>
			splneno
			</td>
			</tr>	
		";
		$i = 0;
		while($d = mysql_fetch_array($query)){
			if (($d["only_for"] == 1 and $d["pro"] == user()) or $d["only_for"] != 1){
			if($i == 0) $color = "bgcolor='#BFE0DE'";
				else $color = "";
				if($i == 1) $i = 0;
				else $i++;
			echo "
				<tr valign='top' $color>
				<td>
				<a href='poznamka.php?id=".$d["id"]."'>".$d["id"]."</a>
				</td>
				<td>
				<a href='poznamka.php?id=".$d["id"]."'>".title($d["title"])."</a>
				</td>
				<td>
				".date("j/m/Y H:i", $d["vytvoreno"])."
				</td>
				<td>
				".splneno2($d["splneno"])."
				</td>
				</tr>
		
			";
			}	
		
		}
		echo "</table>";
	}
	else echo "<p>V databázi nejsou žádné poznámky";


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
