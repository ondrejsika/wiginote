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

echo "
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
$sql = "SELECT * FROM  `$db`.`faktury` ORDER BY  `faktury`.`time` DESC";
$q = mysql_query($sql, $spojeni);

$i = 0;
while($d = mysql_fetch_array($q))
{
	if(contains($d["typ"], "_")) $kde = "tmp";
	elseif($d["typ"] == "osp") $kde = "osp";
	elseif($d["typ"] == "aukro") $kde = "aukro";
	else $kde = "vs";

	echo "
		<tr class='radek'>
			<td>
				".$d["vs"]."
			</td>
			<td>
				".user_jmeno($d["user"])."
			</td>
			<td>
				".date("j.m.Y h:i:s", $d["time"])."
			</td>
			<td>
				<a onClick=\"window.open('download_fakt.php?$kde=".$d["file"]."&shop=$kde', 'faktura222', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/pdf.jpg' border='0'></a>
			</td>
		</tr>	
	";
}
echo "</table>";
///


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
