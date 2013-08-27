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



/// OSP

if($action == "delete")
{
	if($krok == "task")
	{
		echo "
			<form action='osp.php?action=delete&id=$id' method='POST'>
			Opravdu chcete odstranit fakturu?			
			<br><button name='ok' value='1'>Ano</button>
			<button name='ok' value='0'>Ne</button>
			</form>
		";
	}
	if(isset($_POST["ok"]) and $_POST["ok"] == 1)
	{
		$sql = "DELETE FROM `$db`.`faktury` WHERE `faktury`.`id` = $id";
		mysql_query($sql, $spojeni);
	}
}

if(empty($krok))
{
	echo "
	<p align='left'><a href='faktura.php?typ=osp&nastrance=$nastrance'>osp faktura</a></p>
	<table>
		<tr>
			<td width='150'>
				vs
			</td>
			<td width='150'>
				uživatel
			</td>
			<td width='150'>
				datum
			</td>
			<td></td>
			<td></td>	
		</tr>	
	";
	$sql = "SELECT * FROM  `$db`.`faktury` WHERE `typ` = 'osp' ORDER BY `faktury`.`time` DESC";
	$q = mysql_query($sql, $spojeni);
	$i = 0;
	while($d = mysql_fetch_array($q))
	{
		if($i == 0) $delete = "<a href='osp.php?action=delete&krok=task&id=".$d["id"]."'><img src='var/img/main/delete.png' border='0'></a>";
		else $delete = "";
		$i++;

		echo "
			<tr class='radek'>
				<td>
					".$d["vs"]."
				</td>
				<td>
					".user_jmeno($d["user"])."
				</td>
				<td>
					".date("j.m.Y", $d["time"])."
				</td>
				<td>
					<a onClick=\"window.open('download_fakt.php?osp=".$d["vs"]."', 'faktura222', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/pdf.jpg' border='0'></a>
				</td>
				<td>
					$delete
				</td>
			</tr>	
		";
	}
	echo "</table>";
}
///


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
