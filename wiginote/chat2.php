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
echo "
	<META NAME=\"ROBOTS\" CONTENT=\"NOINDEX, NOFOLLOW\">
	<meta http-equiv='content-type' content='text/html; charset=UTF-8'>
	<link rel='stylesheet' href='inc/style.css' type='text/css'>

	<title>chat | wiginote</title>
";


	
	if (empty($action))
	{
		if(mujChat2($id))
		echo "
			<frameset rows='40, *'>
			<frame name='hlavicka' src='chat2.php?action=2&id=$id' scrolling='no'>
			<frame name='obsah' src='chat2.php?action=1&id=$id'>
			</frameset>	
		";
		else
		echo "
			<frameset rows='*'>
			<frame name='obsah' src='chat2.php?action=1&id=$id'>
			</frameset>
		";
	}

	if($action == 1)
	{
		refresh(refreshTime(user()));
		$sql = "SELECT * FROM `$db`.`chat2` WHERE `chat2`.`chat` = $id ORDER BY `chat2`.`time` DESC limit ".pocetZprav(user());
		$query = mysql_query($sql, $spojeni);
		$pocet = mysql_num_rows($query);
		if ($pocet != 0 and mujChat($id)){
			while($d = mysql_fetch_array($query)){
				$userColor = userColor($d["user"]);
				echo "<br><font color='$userColor'>".user_jmeno($d["user"]).", ".date("H:i", $d["time"])."</font>>> ".$d["text"];
			}
		}
		
	}
	if($action == 2)
	{
		echo "
			<form action='chat2.php?action=2&id=$id' method='POST'>
			<input type='text' size='30' name='text'>
			<input type='submit' value='odeslat'>
			</form>
		";
		if(!empty($text))
		{
			$sql = "INSERT INTO `$db`.`chat2` VALUES ('$id', '".user()."','".time()."','$text')";
			mysql_query($sql, $spojeni);
		}
		//echo "<hr size='1' color='black'>";
	}

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
