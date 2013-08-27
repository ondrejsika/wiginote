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


	///
/*
	function cas($time)
	{
		return strval(intval(date("H", $time))-1 . "h " . date("i", $time) . "m " . date("s", $time) . "s");
	}
//*/

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
		<a href='work_time_vypis.php'>kompletní zobrazeí</a> | 
		<a href='work_time_vypis.php?sekce=d'>denní zobrazeí</a> | 
		<a href='work_time_vypis.php?sekce=m'>měsíční zobrazeí</a>		
	";

	if(empty($sekce))
	{
		$d_cas = 0;
		$m_cas = 0;
		$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = ".user()."  ORDER BY `work_time`.`start` DESC";// and `stop` != 0
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
	if($sekce == "d") // denni zorazeni
	{
		$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = ".user()."  ORDER BY `work_time`.`start` DESC";
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
	if($sekce == "m")  // mesicni zobrazeni
	{
		$sql = "SELECT * FROM `$db`.`work_time` WHERE `user` = ".user()."  ORDER BY `work_time`.`start` DESC";
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
	///


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
