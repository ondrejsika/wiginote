<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

include "core/header.php"; // header
include "core/inc.php"; // included files

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if(!rights()) echo "<h1>access denied</h1>";

elseif($access == 1)
{
/**********/
include "core/pages/head.php";
include "core/pages/menu.php";

if($overovat == 1) $cho = "CHECKED";
else $cho = "";

if(isset($_POST["nastrance"])) $nastrance = $_POST["nastrance"];
else $nastrance = 1;
$nastrance2 = new select("nastrance",array(1,2,3,4,5),array(30,50,100,200,"all"),$nastrance-1);

if($nastrance == 1) $limit = 30;
if($nastrance == 2) $limit = 50;
if($nastrance == 3) $limit = 100;
if($nastrance == 4) $limit = 200;
if($nastrance == 5) $limit = 9999;

if(isset($_POST["dledopravy"])) $dledopravy = $_POST["dledopravy"];
else $dledopravy = 0;
$dle_dopravy = new select("dledopravy",array(0,1,2),array("all","intime","cpost"),$dledopravy);
if($dledopravy == 0) 

if(isset($_POST["action"]))
{
	$action = $_POST["action"];
	
	$ids = array();
	for($i = $_POST["min"]; $i <= $_POST["max"]; $i++)
	{
		if(isset($_POST["select_$i"])) $ids[] = $i;
	}

	$ids_str = "";
	foreach($ids as $id)
	{
		if(empty($ids_str)) $ids_str = $id;
		else $ids_str .= ";" . $id;
	}
}

// BEGIN action
if($action == "pridat")	window_open("sz_hrackovnik_pridat.php", "", "width=1024,height=400");
if($action == "cpost_export")	window_open("apps.php?app=export_cpost&z=$ids_str");
if($action == "intime_export")	window_open("apps.php?app=export_intime&ids_str=$ids_str&shop=hrackovnik");
if($action == "tisk_faktur")	window_open("faktura_pdf_hrackovnik.php?id=$ids_str&shop=hrackovnik");
if($action == "load_file") include "apps/load_file.php";
// END action

echo "
	<form action='sz_hrackovnik.php' method='POST' ENCTYPE='multipart/form-data'>

	<table width='100%'><tr><td>
	<input type='submit' value='aktualizovat'>
	<button name='action' value='cpost_export'>cpost export</button>
	<button name='action' value='intime_export'>intime export</button>
	<button name='action' value='tisk_faktur'>tisk faktur</button>

	<button name='action' value='load_file'>load file</button>
	<input type='file' name='file' accept='*'>
	</td><td align='right'>
	ověřovat
	<input type='checkbox' name='overovat' value='1' $cho>
	<button name='action' value='pridat'>přidat</button>
	<input type='text' name='search' value='$search'>
	".$dle_dopravy->write()."
	<input type='submit' value='ok'>
	".$nastrance2->write()."
	</td></tr></table>
	<table>
	<tr>
		<td width='20'></td>
		<td width='20'></td>
		<td width='1'>S</td>
		<td width='1'>E</td>
		<td width='100'>vs</td>
		<td width='100'>cz</td>
		<td width='100'>zaplaceno</td>
		<td width='100'></td>
		<td width='100'>odesláno</td>
		<td width='100'></td>
		<td width='200'>stav</td>
		<td width='100'>poznámka</td>
		<td width='100'>doprava</td>
		<td width='100'>platba</td>
		<td width='10'></td>
		<td width='10'>A</td>
		<td width='100'>jméno</td>
		<td width='100'>cena</td>
		<td width='100'>telefon</td>
	</tr>
";

$mm = 0;
//if(empty($search)) $sql = "SELECT * FROM `$db`.`zasilky_hrackovnik`";else 
if($dledopravy != 0) $dd = "`zpusob_dodani` = '$dledopravy' and";
else $dd = "";
$sql = "SELECT * FROM `$db`.`zasilky_hrackovnik` WHERE $dd (
`vs` LIKE '%$search%' or
`nick` LIKE '%$search%' or
`cz` like '%$search%'
)
ORDER BY  `zasilky_hrackovnik`.`datum_obednavky` DESC
limit 0, $limit";

$q = mysql_query($sql, $spojeni);
while($d = mysql_fetch_array($q))
{
	$id = $d["id"];
	if($mm == 0) echo "<input type='hidden' name='max' value='$id'>";//$min = $id;
	$mm++;

	// BEGIN edit
	if(isset($_POST["edit_$id"]))
	{	
		$sql = "UPDATE  `$db`.`zasilky_hrackovnik` SET  `cz` =  '".$_POST["cz_$id"]."',
	`paid` =  '".$_POST["paid_$id"]."',
	`send` =  '".$_POST["send_$id"]."',
	`note` =  '".$_POST["note_$id"]."',
	`zpusob_platby` =  '".$_POST["platba_$id"]."',
	`zpusob_dodani` =  '".$_POST["doprava_$id"]."' WHERE  `zasilky_hrackovnik`.`id` = $id";

 	mysql_query($sql, $spojeni);
	}
	// END edit
	
	$z = new zasilky_hrackovnik($id);

	if(isset($_POST["select_$id"])) $selected = "CHECKED";
	else $selected = "";

	$paid = new select("paid_$id",array(0,1,2,3),array("auto","unpaid","paid","zrušeno"),$z->view("paid"));
	$send = new select("send_$id",array(0,1,2,3,4,5,6),array("auto","neod","odes","vratka","část.","do 14.","znovu"),$z->view("send"));
	$doprava = new select("doprava_$id",array(0,1,2,3,4,5),array("","intime","cpost","ems", "ems s", "osp"),$z->view("zpusob_dodani"));
	$platba = new select("platba_$id",array(0,1,2),array("","dobirka","naucet"),$z->view("zpusob_platby"));

	if($overovat == 1) $stav = $z->stav("cpost","text");
	else $stav = "";

	if($d["avizovano"] == 1) 
	{
		$avizovano = "CHECKED";
		if(!isset($_POST["avizace_$id"]))
		{
			$sql = "UPDATE  `$db`.`zasilky_hrackovnik` SET  `avizovano` =  '0' WHERE  `zasilky_hrackovnik`.`id` = $id;";
			mysql_query($sql, $spojeni);
			$avizovano = "";
		}
	}
	else 
	{
		$avizovano = "";
		if(isset($_POST["avizace_$id"]))
		{
			$sql = "UPDATE  `$db`.`zasilky_hrackovnik` SET  `avizovano` =  '1' WHERE  `zasilky_hrackovnik`.`id` = $id;";
			mysql_query($sql, $spojeni);
			$avizovano = "CHECKED";
		}
	}

	$adr = new adresy;
	$adr->load($d["adresa_dorucovaci"]);

	echo "
		<tr class='radek' bgcolor='".$z->barva()."'>
		<td width='20'>".date("d.m.", $z->view("datum_obednavky"))."</td>
		<td width='20'><a onClick=\"window.open('sz_hrackovnik_podrobnosti.php?id=$id&shop=$shop', 'id', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/plus.png' border='0'></a></td>
		<td width='1'><input type='checkbox' name='select_$id' value='1' $selected></td>
		<td width='1'><input type='checkbox' name='edit_$id' value='1'></td>
		<td width='100'>".$z->view("vs")."</td>
		<td width='100'>".$z->input("cz_$id",$z->view("cz"))."</td>
		<td width='100'>".$paid->write()."</td>
		<td width='100'>".$z->stav("paid","text")."</td>
		<td width='100'>".$send->write()."</td>
		<td width='100'>".$z->stav("send","text")."</td>
		<td width='200'>".$stav."</td>
		<td width='100'>".$z->input("note_$id", $z->view("note"))."</td>
		<td width='100'>".$doprava->write()."</td>
		<td width='100'>".$platba->write()."</td>
		<td width='100'><a onClick=\"window.open('faktura_pdf_hrackovnik.php?id=$id&shop=$shop', 'faktura', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/pdf.jpg' border='0'></a></td>
		<td width='10'><input type='checkbox' name='avizace_$id' value='1' $avizovano></td></td>
		<td width='100'>".$adr->jmeno."</td>
		<td width='100'>".$z->cena_celkem()."Kč</td>
		<td width='100'>".$adr->telefon."</td>
	</tr>
	";
}

echo "
	</table>
	
	<input type='hidden' name='min' value='$id'>
	</form>
";
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
