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



if(empty($str)) $str = 1;

if(user_prava(user()) == 2) $up2 = "AND (`doprava` = 2 AND (`zpusob_platby` = 'bankpayment' OR `zpusob_platby` = 'paypal_standard'))";
else $up2 = "";


if(isset($_POST["action"]))
if($_POST["action"] == "cpost_export")
{
	$q = mysql_query("SELECT * FROM `$db`.`zasilky`", $spojeni);
	$max = id("zasilky");
	$select = array();
	for($i=1;$i<$max+1;$i++)
	{
		if(isset($_POST["select_".$i]))
		if($_POST["select_".$i] == 1) $select[] = $i;
	}
	$a = "";
	foreach($select as $val) 
	{
		if(empty($a)) $a = $val;
		else $a = $a . ";" .$val;
	}
	if(!empty($a))
	echo "
	<script>
			window.open('apps.php?app=export_cpost&z=$a', 'cpost_exp', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
		</script>
	";
	//$polozky = array(vs,e,e,prijmeni,e,e,jmeno,mesto,e,ulice,e,e,psc,stat,e,e,e,tel,e,email,vs,e,hmotnost,cena,vs,7,dobirka,"CZK",vs);
}
if(isset($_POST["action"]))
if($_POST["action"] == "intime_export")
{
	$q = mysql_query("SELECT * FROM `$db`.`zasilky`", $spojeni);
	$max = id("zasilky");
	$select = array();
	for($i=1;$i<$max+1;$i++)
	{
		if(isset($_POST["select_".$i]))
		if($_POST["select_".$i] == 1) $select[] = $i;
	}
	$a = "";
	foreach($select as $val) 
	{
		if(empty($a)) $a = $val;
		else $a = $a . ";" .$val;
	}
	if(!empty($a))
	echo "
		<script>
			window.open('apps.php?app=export_intime&ids_str=$a', 'intime_exp', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
		</script>
	";
	//$polozky = array(vs,e,e,prijmeni,e,e,jmeno,mesto,e,ulice,e,e,psc,stat,e,e,e,tel,e,email,vs,e,hmotnost,cena,vs,7,dobirka,"CZK",vs);
}
if(isset($_POST["action"]))
if($_POST["action"] == "seznam_zbozi")
{
	$q = mysql_query("SELECT * FROM `$db`.`zasilky`", $spojeni);
	$max = id("zasilky");
	$select = array();
	for($i=1;$i<$max+1;$i++)
	{
		if(isset($_POST["select_".$i]))
		if($_POST["select_".$i] == 1) $select[] = $i;
	}
	$a = "";
	foreach($select as $val) 
	{
		if(empty($a)) $a = $val;
		else $a = $a . ";" .$val;
	}
	if(!empty($a))
	echo "
		<script>
			window.open('seznam_zbozi.php?z=$a', 'seznam_zbozi', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
		</script>
	";
	//$polozky = array(vs,e,e,prijmeni,e,e,jmeno,mesto,e,ulice,e,e,psc,stat,e,e,e,tel,e,email,vs,e,hmotnost,cena,vs,7,dobirka,"CZK",vs);
}

if(isset($_POST["action"]))
if($_POST["action"] == "tisk_faktur")
{
	$q = mysql_query("SELECT * FROM `$db`.`zasilky`", $spojeni);
	$max = id("zasilky");//mysql_num_rows($q);
	$select = array();
	for($i=1;$i<$max+1;$i++)
	{
		if(isset($_POST["select_".$i]))
		if($_POST["select_".$i] == 1) $select[] = $i;
	}
	$a = "";
	foreach($select as $val) 
	{
		if(empty($a)) $a = $val;
		else $a = $a . ";" .$val;
	}
	if(!empty($a))
	if(!isset($shop) or empty($shop)) $shop = "wigishop";
	echo "
		<script>
			window.open('faktura_pdf.php?id=$a&shop=$shop', 'faktura', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
		</script>
	";//echo "window.open('faktura_pdf.php?id=$a', 'chat', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');";
	//$polozky = array(vs,e,e,prijmeni,e,e,jmeno,mesto,e,ulice,e,e,psc,stat,e,e,e,tel,e,email,vs,e,hmotnost,cena,vs,7,dobirka,"CZK",vs);
}
if(isset($_POST["action"]))
if($_POST["action"] == "magento" and !empty($_FILES['file']['tmp_name']))
{
	$file = "var/import/magento/obednavky/order_export_".time().".csv";
	if(file_exists($file)) unlink($file);
	move_uploaded_file($_FILES['file']['tmp_name'], $file);
	import_order($file);
}
if(isset($_POST["action"]))
if($_POST["action"] == "bv" and !empty($_FILES['file']['tmp_name']))
{
	$file = "var/import/bank/bv.csv";
	if(file_exists($file)) unlink($file);
	move_uploaded_file($_FILES['file']['tmp_name'], $file);
	
	$sql = "SELECT * FROM `$db`.`zasilky`";
	$query = mysql_query($sql, $spojeni);
	while ($d = mysql_fetch_array($query))
	{
		if(zaplaceno2($d["vs"])) {set_zaplaceno($d["vs"]); echo "<br>".$d["vs"];}
	}
	unlink($file);
	$_SESSION["task"] = "Import byl proveden.";
	header("location: sledovani_zasilek.php?shop=$shop");
}
if($action == "delete")
{
	$sql = "DELETE FROM `$db`.`zasilky` WHERE `zasilky`.`id` = $id LIMIT 1;";
	mysql_query($sql, $spojeni);  
	header("location: sledovani_zasilek.php");
}
if($action == "backup")
{
	$path = "backup/zasilky/".time().".csv";
	$zaloha = new csv($path);

	$query = mysql_query("SELECT * FROM `$db`.`zasilky` ORDER BY `zasilky`.`id` DESC", $spojeni);
	while ($d = mysql_fetch_array($query))
	{
		$zaloha->write(array($d["id"],$d["vs"],$d["cz"],$d["paid"],$d["send"],$d["note"],$d["doruceno"]));
	}
	$zaloha->save($path);
}

if($overovat == 1)
{
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `shop` = '$shop' AND `doruceno` = '0' AND `doprava` = '2' AND `cz` != '' ORDER BY `zasilky`.`id` DESC limit 0,10";
	$q = mysql_query($sql, $spojeni);
	while($d = mysql_fetch_array($q))
	{echo 1;
		//
		if($d["doruceno"] != 1)
		{
			if($d["send"] == 0 or $d["send"] == 2) // auto
			{
				if(!empty($d["cz"]))
				{
					if(stav($d["cz"]) == "Doručeno")
					{
						mysql_query("UPDATE `$db`.`zasilky` SET `doruceno` = '1' WHERE `zasilky`.`id` = '".$d["id"]."'",$spojeni);
					}
					if(stav($d["cz"]) == "<font color='red'>Nedoručeno</font>" and $d["doprava"] == 2 and byl_odeslan($d["id"], "nedoruceno"))
					{
						mysql_query("UPDATE `$db`.`zasilky` SET `sended_email` = '1;1;0' WHERE `zasilky`.`id` = '".$d["id"]."'",$spojeni);
						send_email($d["id"],"nedoruceno_cpost");
					}
				}
			}
		}
	}
}
if(isset($_POST["action"]))
if($_POST["action"] == "email")
{
	$q = mysql_query("SELECT * FROM `$db`.`zasilky`", $spojeni);
	$max = id("zasilky");
	$select = array();
	for($i=1;$i<$max+1;$i++)
	{
		if(isset($_POST["select_".$i]))
		if($_POST["select_".$i] == 1) $select[] = $i;
	}
	$a = "";
	foreach($select as $val) 
	{
		if(empty($a)) $a = $val;
		else $a = $a . ";" .$val;
	}
	if(!empty($a))
	echo "
		<script>
			window.open('email.php?id=$a', 'email', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
		</script>
	";
}


if (empty($shop)) $shop = "wigishop";
$npaid = new select("npaid",array(0,1,2),array("auto","unpaid","paid"),0);
$nsend = new select("nsend",array(0,1,2),array("auto","neod","odes"),0);
$ndoprava = new select("ndoprava",array(0,1,2,3,4,5),array("","intime","cpost","ems", "ems s", "osp"),0);

$vpaid = new select("vpaid",array(0,1,2),array("PLATBY","unpaid","paid"),0);
$vsend = new select("vsend",array(0,1,2),array("ODESLÁO","neod","odes"),0);
$vdoprava = new select("zdoprava",array("",1,2,3,4,5),array("DOPRAVA","intime","cpost","ems", "ems s", "osp"),0);
//$vplatba = new select("zplatba",array("","cashondelivery","bankpayment","paypal_standard","checkmo"),array("PLATBA","dobírka","na ucet","pypal", "osp"),0);

$pladby = new select("zplatby",array(0,1,2,3,4,5),array("","intime","cpost","ems", "ems s", "osp"),0);


	$zs; // pocet zobrazenych obednavek
if(!empty($nastrance)) $zs = $nastrance;// intval($nastrance);
else $zs = 30;

//$search = "hodinky";

// BEGIN limit
if(!isset($search) or empty($search))
{

	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `shop` = '$shop' $up2";
	$query = mysql_query($sql, $spojeni);
	$pocet = mysql_num_rows($query);
	$stranek = ceil($pocet / $zs);
	$stranky = "";
	for($st=1;$st<=$stranek;$st++)
	{
		if($st == $str) $cs = "<b>$st</b>";
		else $cs = $st;
		if(empty($stranky)) $stranky .= "<a href=sledovani_zasilek.php?shop=$shop&str=$st&nastrance=$nastrance>$cs</a>";
		else $stranky .= " | <a href=sledovani_zasilek.php?shop=$shop&str=$st&nastrance=$nastrance>$cs</a>";
	}
	$str2 = ($str-1)*$zs;
	$limit = "LIMIT $str2,$zs";
}
else $limit = $stranky = "";
// END limit

if($action == "reset")
$search = "";

if($overovat == 1 ) $och = "CHECKED";
else $och = "";
echo "
	<table width='100%'><tr valign='top'><td>
		<form action='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=$nastrance' method='POST' ENCTYPE='multipart/form-data'>
		<input type='file' name='file' accept='*'>
		<button name='action' value='magento'>Magento import</button>
		<button name='action' value='bv'>Bankovní výpis</button>
		</form>
	</td><td align='right'>
	
	<a onClick=\"window.open('legenda.php', 'legenda', 'width=300,height=200,left=0,top=0,location=no,scrollbars=no')\">Legenda</a> | 
	<a href='faktura.php?typ=osp&nastrance=$nastrance'>osp faktura</a>
	</td></tr><table>
	<table><tr valign='top'><td>"./*"	
		<form action='sledovani_zasilek.php?shop=$shop&action=backup&str=$str&nastrance=$nastrance' method='POST'><input type='submit' value='zaloha'></form>
	</td>
	<td>		
		<form action='sledovani_zasilek-export.php' method='POST'><input type='submit' value='export'></form>
	</td>*/"
	<td>		
		<form action='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=$nastrance' method='POST'><input type='submit'>
	</td>
	<td>
		<input type='checkbox' name='overovat' value='1' $och>Ověřovat
		<button name='action' value='cpost_export'>CPOST export</button>
		<button name='action' value='intime_export'>Intime export</button>
		<button name='action' value='tisk_faktur'>Tisk faktur</button>
		<button name='action' value='seznam_zbozi'>Seznam zboží</button>
		<button name='action' value='email'>email</button>
		search: <input type='text' name='search' size='15' value='$search'>
		<button>search</button>
	</td>
	</td></tr></table>
	<table width='100%'>
<tr><td>
	<a href='sledovani_zasilek.php?shop=wigishop&nastrance=$nastrance'><b>Wigishop</b></a> |
	<a href='sledovani_zasilek.php?shop=hrackovnik&nastrance=$nastrance'>Hračkovník</a> | 
	<a href='sledovani_zasilek.php?shop=topgadget'>Topgadget</a> |
	<a href='sz_aukro.php'>Aukro</a>

	</td>
	<td align='left'>
		<a href='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=30'>30</a> 
		<a href='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=50'>50</a> 
		<a href='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=100'>100</a> 
		<a href='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=200'>200</a> 
		<a href='sledovani_zasilek.php?shop=$shop&str=$str&nastrance=9999'>all</a> 
	</td>
	</tr></table>
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
		<td width='100'></td>
		<td width='100'>jméno</td>
		<td width='100'>cena</td>
		<td width='100'>cena magento</td>
		<td width='100'>telefon</td>
	</tr>
";

$query = mysql_query("SELECT * FROM `$db`.`zasilky` WHERE `shop` = '$shop' ORDER BY `zasilky`.`vs` DESC", $spojeni);
$max = mysql_num_rows($query);
for($i=1;$i<=$max+1;$i++)
{
	if (isset($_POST["cz_$i"])) $cz = $_POST["cz_$i"];
	if (isset($_POST["paid_$i"])) $paid = $_POST["paid_$i"];
	if (isset($_POST["send_$i"])) $send = $_POST["send_$i"];
	if (isset($_POST["note_$i"])) $note = $_POST["note_$i"];
	if (isset($_POST["auto_$i"])) $auto = $_POST["auto_$i"];
	if (isset($_POST["doprava_$i"])) $doprava = $_POST["doprava_$i"];
	if (isset($_POST["platba_$i"])) $platba = $_POST["platba_$i"];
	if (isset($_POST["edit_$i"])) $edit = $_POST["edit_$i"];else $edit = 0;

	if(isset($cz) and isset($paid) and isset($send) and isset($note) and isset($doprava) and isset($platba))
	{
		$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = $i";
		$q = mysql_query($sql, $spojeni);
		$dx = mysql_fetch_array($q);
		//echo "<br>$i ".$dx["send"]." $send";
		if($dx["send"] != 3 and $send == 3) vratkaDoSkladu($i);
		if($dx["send"] != 6 and $send == 6) znovuZeSkladu($i);	
		if($dx["send"] != 5 and $send == 5)
		{
			vratkaDoSkladu($i);
			echo "
				<script>
					window.open('dobropis_pdf.php?id=$i', 'doropis', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
				</script>
			";
		}

		$sql = "
			UPDATE `$db`.`zasilky` SET
			`cz` = '$cz', 
			`paid` = '$paid', 
			`send` = '$send',
			`doprava` = '$doprava',
			`zpusob_platby` = '$platba',
			`note` = '$note'
			WHERE `zasilky`.`id` = '$i'
		";
		if(isset($edit))
			if($edit == 1)
				mysql_query($sql,$spojeni);
	}
	$_POST["vs_$i"] = "";
	$_POST["cz_$i"] = ""; 
	$_POST["paid_$i"] = "";
	$_POST["send_$i"] = "";
	$_POST["note_$i"] = "";
	$_POST["auto_$i"] = "";
	$_POST["doprava_$i"] = "";
	$_POST["platba_$i"] = "";
}

/*
if(!empty($_POST["nvs"]) or !empty($_POST["ncz"]))
{
	$sql = "INSERT INTO `$db`.`zasilky` (`id`, `shop`, `vs`, `cz`, `paid`, `bv_paid`, `send`, `note`, `doruceno`, `doprava`) VALUES ('".id("zasilky")."', '$shop', '".$_POST["nvs"]."', '".$_POST["ncz"]."', '".$_POST["npaid"]."','0', '".$_POST["nsend"]."', '".$_POST["nnote"]."',0,'".$_POST["ndoprava"]."');";
	mysql_query($sql,$spojeni);
}
*/

$query = mysql_query("SELECT * FROM `$db`.`zasilky` WHERE `shop` = '$shop' ORDER BY `zasilky`.`vs` DESC $limit", $spojeni);
$pz = mysql_num_rows($query);
if(1 == 1) 
{
	$query = mysql_query("SELECT * FROM `$db`.`zasilky` WHERE `shop` = '$shop' $up2 ORDER BY `zasilky`.`vs` DESC $limit", $spojeni);
}

while ($d = mysql_fetch_array($query))
{
	$id = $d["id"];
	$od = $d["send"];
	$paid = new select("paid_$id",array(0,1,2,3),array("auto","unpaid","paid","zrušeno"),$d["paid"]);
	$send = new select("send_$id",array(0,1,2,3,4,5,6),array("auto","neod","odes","vratka","část.","do 14.","znovu"),$od);
	$doprava = new select("doprava_$id",array(0,1,2,3,4,5),array("","intime","cpost","ems", "ems s", "osp"),$d["doprava"]);
	
	if($d["zpusob_platby"] == "cashondelivery") $sd = 1;
	elseif($d["zpusob_platby"] == "bankpayment") $sd = 2;
	elseif($d["zpusob_platby"] == "paypal_standard") $sd = 3;
	elseif($d["zpusob_platby"] == "checkmo") $sd = 4;
	else $sd = 0;
	$platba2 = new select("platba_$id",array("","cashondelivery","bankpayment","paypal_standard","checkmo"),array("","dobírka","na ucet","paypal", "osp"),$sd);

	if($od == 0)
	{
		if(!empty($d["cz"])) $o = "odesláno";
		else $o = "neodesláno";
	}
	else
	{
		if($od == 1) $o = "neodesláno";
		else $o = "odesláno";
	}
	if($od == 3)
	$o = "vratka";
	if($od == 4) $o = "částečně";

	if($o == "odesláno" and byl_odeslan($id, "odeslano"))
	{
		if($d["doprava"] == 1)	send_email($id, "odeslano_intime");
		if($d["doprava"] == 2)	send_email($id, "odeslano_cpost");
		if($d["doprava"] == 3)	send_email($id, "odeslano_ems");
		mysql_query("UPDATE `$db`.`zasilky` SET `sended_email` = '1;0;0' WHERE `zasilky`.`id` = '".$d["id"]."'",$spojeni);
	}

	if($d["doruceno"] != 1 )
	{
		if($o == "odesláno" and $overovat == 1 and je_posta($d["cz"]))
		$stav = stav($d["cz"]);
		else $stav = "";
	}
	else $stav = "doručeno";

	$pl = $d["paid"];
	if($pl == 1) $zaplaceno = "nezaplaceno";
	if($pl == 2) $zaplaceno = "zaplaceno";
	if($pl == 3) $zaplaceno = "zrušeno";
	if($pl == 0) $zaplaceno = zaplaceno($d["vs"]);

	$datum_obednavky = explode(" ", $d["datum_obednavky"]);
	$datum2 = $datum_obednavky[0];
	$datum3 = explode(".", $datum2);
	$datum = $datum3[0].".".$datum3[1].".";

	$dop = postovne($d["doprava"]);
	if($d["zpusob_platby"] == "cashondelivery") $postovne = $dop[1] + 50;
	else $postovne = $dop[1];

	$sdph = celkova_cena($id) + $postovne;

if($stav == "Doručeno")
					{
						mysql_query("UPDATE `$db`.`zasilky` SET `doruceno` = '1' WHERE `zasilky`.`id` = '".$id."'",$spojeni);
					}


	// barva
	if($o == "neodesláno") $b = "red";
	if($o == "vratka") $b = "yellow";
	if($zaplaceno == "zaplaceno") $b = "green";
	if($zaplaceno == "zaplaceno" and $o == "odesláno") $b = "orange";
	if($zaplaceno == "zrušeno") $b = "black";
	if($o == "odesláno" and $zaplaceno == "nezaplaceno") $b = "blue";
	if($o == "odesláno" and $zaplaceno == "zaplaceno" and ( $stav == "doručeno" or $stav == "Doručeno")) $b = "";
	if($o == "odesláno" and ( $stav == "doručeno" or $stav == "Doručeno") and $zaplaceno == "nezaplaceno") $b = "Orchid";
	if($o == "částečně") $b = "CadetBlue";

	if(!empty($b)) $barva = "bgcolor='$b'";
	else $barva = "";
	$b = "";
	// barva
	
	if(isset($_POST["select_".$id]) and $_POST["select_".$id] == 1) $chs = "CHECKED";
	else $chs = "";

	$lcpost = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=".$d["cz"]."&locale=CZ&send.x=0&send.y=0&send=submit&go=ok";
	$polozky[$id] = "
		<div class='radek'>
		<tr class='radek' $barva>
			<td width='20'>
				".$datum."
			</td>
			<td width='20'>
				<a onClick=\"window.open('sledovani_zasilek_podrobnosti.php?id=$id&shop=$shop', 'obednavky_podrobnosti', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/plus.png' border='0'></a>
			</td>
			
			<td width='20'><input type='checkbox' name='select_$id' value='1' $chs></td>
			<td width='100'>
			<input type='checkbox' name='edit_$id' value='1'>
			</td>
			<td>
			".$d["vs"]."
			</td>
			<td width='100'>
			<input type='text' name='cz_$id' size='10' value='".$d["cz"]."'>
			</td>
			<td width='100'>
			".$paid->write()."
			</td>
			<td width='100'>
			".$zaplaceno."
			</td>
			<td width='100'>
			".$send->write()."
			</td>
			<td width='100'>$o</td>
			<td width='200'>
			<a href='$lcpost' target='blank'>".$stav."</a>
			</td>
			<td width='100'>
			<input type='text' name='note_$id' size='50' value='".$d["note"]."'>
			</td>
			<td width='100'>
			".$doprava->write()."
			</td>
			<td width='100'>
			".$platba2->write()."
			</td>
			<td width='100'>
			<a onClick=\"window.open('faktura_pdf_aukro.php?id=$id', 'faktura', 'width=800,height=400,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/pdf.jpg' border='0'></a>
			</td>
			<td>".str_replace(" ", "&nbsp;", $d["p_jmeno"])."</td>
			<td>".$sdph."</td>
			<td>".$d["cena_celkem_sdph"]."</td>
			<td>".str_replace(" ", "", $d["d_telefon"])."</td>
		</tr>
		</div>

	";//<a href='sledovani_zasilek.php?action=delete&id=$id'><img src='img/delete.png' border='0'></a>
}


if(isset($polozky))
{

	//$sql = "SELECT * FROM `$db`.`zasilky` WHERE `jmeno_polozky` like '%$search%'";
//	$q = mysql_query($sql, $spojeni);
//	while($d = mysql_fetch_array($q)) $search_array[] = $d["id"];

	$zobraz = array();

	$sql = "SELECT * FROM `$db`.`zasilky`";
	$q = mysql_query($sql, $spojeni);
	while($d = mysql_fetch_array($q)) $zobraz[] = $d["id"];

	$where = "";
	$i = 0;
	foreach($zobraz as $a) 
	{
		if($i == 0) $where .= "`id` = '$a'";
		else $where .= " OR `id` = '$a'";
		$i++;
	}

	if(empty($search)) $sql = "SELECT * FROM `$db`.`zasilky` WHERE ( $where ) $up2 ORDER BY `zasilky`.`vs` DESC";
	else $sql = "SELECT * FROM `$db`.`zasilky` WHERE (`jmeno_polozky` like '%$search%' OR `p_jmeno` like '%$search%' OR `p_telefon` like '%$search%' OR `email` like '%$search%' OR `vs` like '%$search%') $up2 ORDER BY `zasilky`.`vs` DESC";

	$q = mysql_query($sql, $spojeni);
	while($d = mysql_fetch_array($q))
	{
		if(isset($polozky[$d["id"]])) echo $polozky[$d["id"]];
	}
}

echo "
	</table>
	</form>
";
echo "<center>".$stranky."</center>";

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
