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
		echo "
			<frameset rows='40, *'>
			<frame name='hlavicka' src='chat.php?action=2' scrolling='no'>
			<frame name='obsah' src='chat.php?action=1'>
			</frameset>	
		";
	}

	if($action == 1)
	{
		refresh(refreshTime(user()));
		$query = mysql_query("SELECT * FROM `$db`.`chat` ORDER BY `chat`.`time` DESC limit ".pocetZprav(user()), $spojeni);
		$pocet = mysql_num_rows($query);
		if ($pocet != 0){
			while($d = mysql_fetch_array($query)){
				$userColor = userColor($d["user"]);
				echo "
					<br><font color='$userColor'>".user_jmeno($d["user"]).", ".date("H:i", $d["time"])."</font>>> ".$d["text"]."
				";
			}
		}
		$query = mysql_query("SELECT * FROM `$db`.`chat`", $spojeni);
		$posl = mysql_num_rows($query);
		posledniRegister($posl, user());
	}
	if($action == 2)
	{
		echo "
			<form action='chat.php?action=2' method='POST'>
			<input type='text' size='30' name='text'>
			<input type='submit' value='odeslat'>
			</form>
		";
		if(!empty($text))
		{
			$sql = "INSERT INTO `$db`.`chat` VALUES ('".user()."','".time()."','$text')";
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
