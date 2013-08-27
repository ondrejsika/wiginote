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

	print "
		<table width='100%'>
		<tr valign='top'>
		<td width='50%'>
		<h2>Moje poznámky</h2>
	";
	// 01
	$razeni = array("`splneno` = 0" , "`splneno` NOT LIKE 0");
	foreach ($razeni as $sql)
	{
	if(user() == 7) $pro_vsechny = "";
	else $pro_vsechny = "OR `pro` = 0";

	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE (`pro` = ".user()." $pro_vsechny) and `visible`='1' AND $sql ORDER BY `note`.`vytvoreno` DESC", $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
			if($d["only_for"] == 1) $prava = "soukromé";
			else $prava = "veřejné";
			if(jeSoubor($d["id"])) $jeSoubor = "<a href='download.php?id=".$d["id"]."'><img src='var/img/main/with_file.png' border='0'></a>";
			else $jeSoubor = "";
			echo "
				<p><b><a href='poznamka.php?id=".$d["id"]."'>".title($d["title"])."</a></b> $jeSoubor
				<br><font size='2'>autor: <a href='uzivatel.php?id=".$d["autor"]."'>".user_jmeno($d["autor"])."</a> | $prava 
				<br>vytvořeno: ".date("j/m/Y H:i", $d["vytvoreno"])." | ".splneno3($d["splneno"],$d["id"])."</font>
				<br>".cut($d["text"],50)."
		
			"; 
			}
		}
		
	}
	// 01
	print "
		</td>
		<td width='50%'> 
		<h2>Poznámky ostatních uživatelů</h2>
	";
	if(user() == 7) $ostatni = array(2);
	else $ostatni = ostatni(user());
	foreach($ostatni as $user)
	{
		
		// 01
		$u = 0;
		$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `pro` = ".$user." and `visible` = '1' and `splneno` = 0 and `only_for` = 0 ORDER BY `note`.`vytvoreno` DESC limit 0, 4", $spojeni);
		if (mysql_num_rows($query) != 0){
			
			$l = 1;
			while($d = mysql_fetch_array($query)){
				if(($d["only_for"] == 1 and ($d["autor"]) == user()) or $d["only_for"] == 0){
				if($d["only_for"] == 1) $prava = "soukromé";
				else $prava = "veřejné";
				if(jeSoubor($d["id"])) $jeSoubor = "<a href='download.php?id=".$d["id"]."'><img src='var/img/main/with_file.png' border='0'></a>";
				else $jeSoubor = "";
				if($u == 0)
				{
					echo "<p><i><font size='4'><a href='uzivatel.php?id=".$user."'>".user_jmeno($user)."</a></font></i><br>";
					$l = 0;
				}
				$u++;
				echo "
					<b><a href='poznamka.php?id=".$d["id"]."'>".title($d["title"])."</a></b> $jeSoubor
					<br><font size='2'>autor: <a href='uzivatel.php?id=".$d["autor"]."'>".user_jmeno($d["autor"])."</a> | $prava
					<br>vytvořeno: ".date("j/m/Y H:i", $d["vytvoreno"])." | ".splneno($d["splneno"])."</font>
					<br>".cut($d["text"],50)."<br>
		
				";
				}
			}
			//if($l == 0) echo "<hr size='1' color='black'>";
			$l++;
			}
		
		// 01
	}
	print "	
		<tr>
		</table>
	";

	$disp = array(1024,768);

	$left = $disp[0]/2 - $okno[0]/2;
	$top = $disp[1]/2 - $okno[1]/2;
	
	$n = pocetNesplnenych(user());
	if($n == 0) $alert = "";
	if($n == 1) $t = "Máte 1 nesplněnou poznámku.";
	if($n > 1 and $n < 5) $t = "Máte $n nespněné poznámky.";
	if($n > 4) $t = "Máte $n nespněných poznámek.";
	
	if($alert == 1) echo "<script>window.alert('$t')</script>";
	if($news == 1 and zobraz_novinky(user())) echo "<script>window.open(\"news.php\",\"_blank\",\"top=0,left=0,scrollbars=yes,width=\"+ screen.availWidth +\", height=\" + screen.availHeight)</script>";

	$_SESSION["news"] = 0;
	$_SESSION["alert"] = 0;

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>

