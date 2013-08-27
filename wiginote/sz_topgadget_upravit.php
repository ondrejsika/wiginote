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

if(empty($action))
{
	$sql = "SELECT * FROM `$db`.`zasilky_topgadget` WHERE `id` = $id";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);
	
	$ap = new adresy;
	$ap->load($d["adresa_platebni"]);
	$ad = new adresy;
	$ad->load($d["adresa_dorucovaci"]);

	$items = explode(";", $d["items"]);

	echo "
			<form action='sz_topgadget_upravit.php?action=2&id=$id' method='post'>
			<p>vs: ".$d["vs"]."
			<br>nick: <input type='text' name='nick' value='".$d["nick"]."'>
			<br>email: <input type='text' name='email' value='".$d["email"]."'>
			<p>doprava ".$d["zpusob_dodani"]."
			<br>platba ".$d["zpusob_platby"]."
			<br>poštovné: <input type='text' name='postovne' value='".$d["postovne"]."'>

			<p><table><tr><td width='500'>
				<p>platební údaje 
				<br><input type='text' name='p_jmeno' value='".$ap->jmeno."'>
				<br><input type='text' name='p_firma' value='".$ap->firma."'>
				<br>tel: <input type='text' name='p_telefon' value='".$ap->telefon."'>
				<br><input type='text' name='p_ulice' value='".$ap->ulice."'>
				<br><input type='text' name='p_psc' value='".$ap->psc."' size='6'> <input type='text' name='p_mesto' value='".$ap->mesto."'>
				<br><input type='text' name='p_stat' value='".$ap->stat."'>
			</td><td>
				<p>dodací údaje 
				<br><input type='text' name='d_jmeno' value='".$ad->jmeno."'>
				<br><input type='text' name='d_firma' value='".$ad->firma."'>
				<br>tel: <input type='text' name='d_telefon' value='".$ad->telefon."'>
				<br><input type='text' name='d_ulice' value='".$ad->ulice."'>
				<br><input type='text' name='d_psc' value='".$ad->psc."' size='6'> <input type='text' name='d_mesto' value='".$ad->mesto."'>
				<br><input type='text' name='d_stat' value='".$ad->stat."'>
			</td></tr></table>
	";
	
		$i = 0;
		foreach($items as $item)
		{
			$it = new items;
			$it->load($item);
			$bez_dph = intval($it->cena*0.8);
			$dph = intval($it->cena*0.2);

			echo "
				<input type='hidden' name='id_$i' value='$item'>
				<p><input type='text' name='nazev_$i' value='".$it->nazev."' size='40'> <input type='text' name='sku_$i' value='".$it->sku."' size='10'>
				<table width='400'>
					<tr>
						<td width='200'>
							množství: <input type='text' name='mnozstvi_$i' value='".$it->mnozstvi."'>
						</td>
						<td width='100'>
							cena/mj
						</td>
						<td width='100'>
							cena celkem
						</td>
					</tr>
					<tr>
						<td width='200'>
							cena s DPH:
						</td>
						<td width='100'>
							<input type='text' name='cena_$i' value='".intval($it->cena)."' size='8'>
						</td>
						<td width='100'>
							<b>".intval($it->cena*$it->mnozstvi)." Kč</b>
						</td>
					</tr>
					<tr>
						<td width='200'>
							cena bez DPH: 
						</td>
						<td width='100'>
							".$bez_dph." Kč
						</td>
						<td width='100'>
							".$bez_dph*$it->mnozstvi." Kč
						</td>
					</tr>
					<tr>
						<td width='200'>
							DPH: 
						</td>
						<td width='100'>
							".$dph." Kč
						</td>
						<td width='100'>
							".$dph*$it->mnozstvi." Kč
						</td>
					</tr>
				</table>
			";
			$i++;
		}
		echo "<input type='submit' value='Upravit'></form>";
}
else
{
	$sql = "SELECT * FROM `$db`.`zasilky_topgadget` WHERE `id` = $id";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);

	$a = new adresy;
	$a->edit($d["adresa_dorucovaci"], $_POST["d_jmeno"], $_POST["d_firma"], "", "", $_POST["d_ulice"], $_POST["d_mesto"], $_POST["d_psc"], $_POST["d_stat"], $_POST["d_telefon"]);
		
	$a->edit($d["adresa_platebni"], $_POST["p_jmeno"], $_POST["p_firma"], "", "", $_POST["p_ulice"], $_POST["p_mesto"], $_POST["p_psc"], $_POST["p_stat"], $_POST["p_telefon"]);

	$i = 0;
	while(isset($_POST["sku_$i"]))
	{
		$sql = "UPDATE  `$db`.`items` SET  `sku` =  '".$_POST["sku_$i"]."',
			`nazev` =  '".$_POST["nazev_$i"]."',
			`cena` =  '".$_POST["cena_$i"]."',
			`mnozstvi` =  '".$_POST["mnozstvi_$i"]."' WHERE  `items`.`id` = '".$_POST["id_$i"]."';
		";
		mysql_query($sql, $spojeni);
		$i++;
	}

	$sql = "UPDATE  `$db`.`zasilky_topgadget` SET  `postovne` =  '".$_POST["postovne"]."',
`email` =  '".$_POST["email"]."',
`nick` =  '".$_POST["nick"]."' WHERE  `zasilky_topgadget`.`id` = $id;";
	mysql_query($sql, $spojeni);

	header("location: sz_topgadget_podrobnosti.php?id=$id");
	
}
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
