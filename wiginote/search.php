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
		<h2>Vyhledávání</h2>
	";
	
	$search = "";
	if(isset($_POST["search"]))
	{
		$search = $_POST["search"];
	}

	echo "
		<form action='search.php' method='POST'>
		<input type='text' name='search' value='$search' size='40'>
		<input type='submit' value='Vyhledat'>
		</form>
	";
	
	if(isset($_POST["search"]))
	{
		$sql = "
			SELECT * 
			FROM `$db`.`note` 
			WHERE `title` LIKE '%$search%' 
			OR `text` LIKE '%$search%'
			and `visible` = '1' 
			ORDER BY `note`.`vytvoreno` DESC
		";
		echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='15%'>
			vytvořeno
			</td>
			<td width='10%'>
			typ
			</td>
			<td width='40%'>
			
			</td>
			<td width='15%'>
			splněno
			</td>
			<td width='20%'>
			
			</td>
			</tr>	
		";
		$query = mysql_query($sql, $spojeni);
		if (mysql_num_rows($query) != 0)
		{
			while($d = mysql_fetch_array($query)){
				if($d["only_for"] == 1) $prava = "soukromé";
				else $prava = "veřejné";
				if(jeSoubor($d["id"])) $jeSoubor = "<a href='download.php?id=".$d["id"]."'><img src='with_file.png' border='0'></a>";
				else $jeSoubor = "";
				echo "
					<tr>
					<td>
					".date("j/m/Y H:i", $d["vytvoreno"])."
					</td>
					<td>
					poznamka
					</td>
					<td>
					<b><a href='poznamka.php?id=".$d["id"]."'>".title($d["title"])."</a></b>
					</td>
					<td>
					".splneno2($d["splneno"])."
					</td>
					<td>
					<b>$jeSoubor
					</td>
					</tr>
		
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
