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
		<a href='admin.php?sekce=4'>pracovní doby</a> |
		<a href='admin.php'>vsechny poznamky</a> | 
		<a href='admin.php?sekce=2'>historie uzivatelu</a> |
		<a href='admin.php?sekce=3'>změna textů</a>
		<p>
	";

	if (user_prava(user()) == 0 or user_prava(user()) == 1)
	{
		if (empty($sekce))
		{
			echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='5%'>
			id
			</td>
			<td width='35%'>
			titulek
			</td>
			<td width='17%'>
			autor
			</td>
			<td width='17%'>
			pro
			</td>
			<td width='5%'>
			
			</td>
			<td width='7%'>
			
			</td>
			<td width='7%'>
			
			</td>
			<td width='7%'>
			
			</td>
			</tr>
	
		";
		$i = 0;
		$query = mysql_query("SELECT * FROM `$db`.`note` ORDER BY `note`.`vytvoreno` DESC", $spojeni);
		if (mysql_num_rows($query) != 0){
			while($d = mysql_fetch_array($query)){
				if($i == 0) $color = "bgcolor='#BFE0DE'";
				else $color = "";
				if($i == 1) $i = 0;
				else $i++;
				if ($d["splneno"] == 0) $splneno = tlacitko("action.php?id=".$d["id"]."&action=1&page=1","Splnit");
				else $splneno = tlacitko("action.php?id=".$d["id"]."&action=5&page=1","Nesplnit");
				if ($d["visible"] == 1) $visible = tlacitko("action.php?id=".$d["id"]."&action=2&page=1","Vymazat");
				else $visible = tlacitko("action.php?id=".$d["id"]."&action=4&page=1","Zobrazit");
				echo "
					<tr valign='top' $color>
					<td>
					<a href='poznamka.php?id=".$d["id"]."'>".$d["id"]."</a>
					</td>
					<td>
					<a href='poznamka.php?id=".$d["id"]."'>".title($d["title"])."</a>
					</td>
					<td>
					".user_jmeno($d["autor"])."
					</td>
					<td>
					".user_jmeno($d["pro"])."
					</td>
					<td>
					".jen_pro($d["only_for"])."
					</td>
					<td>
					".$splneno."
					</td>
					<td>
					".$visible."
					</td>
					<td>
					".tlacitko("action.php?id=".$d["id"]."&action=3&page=1","Upravit")."
					</td>
					</tr>
				";
				}
			}
			else echo "";		
		}
		if ($sekce == 2)
		{
			if(empty($id))
			{
				foreach(users() as $user)
				{
					echo "
						<br><a href=admin.php?sekce=2&id=$user>".user_jmeno($user)."</a>
					";
				}
			}
			else
			{
				echo "
		<p><i><font size='4'>".user_jmeno($id)."</font></i> - historie uživatele 
	";
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `pro` LIKE $id ORDER BY `note`.`vytvoreno` DESC", $spojeni);
	if (mysql_num_rows($query) != 0){
		$i = 0;
		echo "
			<table width='100%'>
			<tr valign='top'>
			<td width='5%'>
			id
			</td>
			<td width='45%'>
			titulek
			</td>
			<td width='10%'>
			visible
			</td>
			<td width='20%'>
			vytvoreno
			</td>
			<td width='20%'>
			splneno
			</td>
			</tr>	
		";
		while($d = mysql_fetch_array($query)){
			if($d["visible"] == 1) $visible = "ano";
			else $visible = "ne";
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
				<a href='poznamka.php?id=".$d["id"]."'>".$d["title"]."</a>
				</td>
				<td>
				$visible
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
		echo "</table>";
	}
	else echo "V databázi nejsou žádné poznámky";

			}
		}
		if($sekce == 3)
		{
			echo "
				<font size='4'>Změna textů</font>
				<br><a href='admin.php?sekce=3&krok=1'>změna vize podniku<a> | 
				<a href='admin.php?sekce=3&krok=2'>změna strategie<a> | 
				<a href='admin.php?sekce=3&krok=3'>změna projektů<a>
			";
		}

		if(!empty($krok))
		{
			if($krok == 1) $name = "vize";
			if($krok == 2) $name = "strategie";
			if($krok == 3) $name = "projekty";

			if(!empty($action))
			{
				$sql = "UPDATE `$db`.`text` SET `text` = '$text' WHERE `text`.`name` = '$name';";
				mysql_query($sql, $spojeni);
			}
			$query = mysql_query("SELECT * FROM `$db`.`text` WHERE `name` = '$name'", $spojeni);
			$d = mysql_fetch_array($query);
			echo "
				<form action='admin.php?sekce=3&krok=$krok&action=2' method='POST'>
				<textarea rows='10' name='text' cols='60'>".$d["text"]."</textarea>
				<p><input type='submit' value='Upravit'>
				</form>
			";
		}
		if($sekce == 4)
		{
			if(empty($id))
			{
				foreach(users() as $user)
				{
					echo "
						<br><a href=admin.php?sekce=4&id=$user>".user_jmeno($user)."</a>
					";
				}
			}
			else
			{
				echo "Pracovní doba uživatele: ".user_jmeno($id);
function cas($time)
{
	$h = intval($time / 3600);
	$m = intval(($time/3600 - $h)*60);
	$m2 = ($time/3600 - $h)*60;
	$s = intval(($m2 - $m)*60);
	return $h."h ".$m."m ".$s."s"; 
}

	function mesic($m)
	{
		if($m == 1) return "leden";
		if($m == 2) return "únor";
		if($m == 3) return "březen";
		if($m == 4) return "duben";
		if($m == 5) return "květen";
		if($m == 6) return "červen";
		if($m == 7) return "červenec";
		if($m == 8) return "srpen";
		if($m == 9) return "září";
		if($m == 10) return "říjen";
		if($m == 11) return "listopad";
		if($m == 12) return "prosinec";
	}

	echo "
		<br><a href='admin.php?id=$id&sekce=4'>kompletní zobrazeí</a> | 
		<a href='admin.php?id=$id&sekce=4&sekce2=d'>denní zobrazeí</a> | 
		<a href='admin.php?id=$id&sekce=4&sekce2=m'>měsíční zobrazeí</a>		
	";
	$user = $id;
	if(empty($sekce2))
	{
		$d_cas = 0;
		$m_cas = 0;
		$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = '".$user."'  ORDER BY `work_time`.`start` DESC";// and `stop` != 0
		$q = mysql_query($sql, $spojeni);	
		echo "
				<table>
					<tr>
						<td width='200'>
							datum
						</td>
						<td width='200'>
							čas
						</td>
						<td width='200'>
							příchod
						</td>
						<td width='200'>
							odchod
						</td>
					</tr>
			";

		while($md = mysql_fetch_array($q))
		{
			if($md["stop"] == 0) $md["stop"] = time();
			$cas = $md["stop"] - $md["start"];
			echo "
				<table>
					<tr class='radek'>
						<td width='200'>
							".date("d.m.y", $md["start"])."
						</td>
						<td width='200'>
							".cas($cas)."
						</td>
						<td width='200'>
							".date("H:i:s", $md["start"])."
						</td>
						<td width='200'>
							".date("H:i:s", $md["stop"])."
						</td>
					</tr>
				</table>
			";
			$zaznam[] = array(date("m", $md["start"]),date("d", $md["start"]),$cas); 		
		}
	}
	if($sekce2 == "d") // denni zorazeni
	{
		$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = ".$user."  ORDER BY `work_time`.`start` DESC";
		$q = mysql_query($sql, $spojeni);
		$mesic = array();
		while($d = mysql_fetch_array($q))
		{
			if($d["stop"] == 0) $d["stop"] = time();
			$m = date("n;j", $d["start"]);
			if(empty($mesic[$m][1])) $mesic[$m][1] = $d["stop"] - $d["start"];
			else $mesic[$m][1] += $d["stop"] - $d["start"];
			$mesic[$m][0] = $m;
		}
		foreach($mesic as $cas)
		{
			$datum = explode(";", $cas[0]);
			$datum = $datum[1].". ".mesic($datum[0]);
			echo "<p>$datum: ".cas($cas[1]);
		}
	}
	if($sekce2 == "m")  // mesicni zobrazeni
	{
		$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = ".$user."  ORDER BY `work_time`.`start` DESC";
		$q = mysql_query($sql, $spojeni);
		$mesic = array();
		while($d = mysql_fetch_array($q))
		{
			if($d["stop"] == 0) $d["stop"] = time();
			$m = date("n", $d["start"]);
			if(empty($mesic[$m][1])) $mesic[$m][1] = $d["stop"] - $d["start"];
			else $mesic[$m][1] += $d["stop"] - $d["start"];
			//echo "<p>".cas($d["stop"] - $d["start"])." ".strval($d["stop"] - $d["start"])."<br>". $mesic[$m][1] ." ".cas($mesic[$m][1]);
			$mesic[$m][0] = $m;
		}
		foreach($mesic as $cas)
		{
			echo "<p>".mesic($cas[0]).": ".cas($cas[1]);
		}
	}
			}
		}
	}
	else echo "Nemáte patřičné oprávění";

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
