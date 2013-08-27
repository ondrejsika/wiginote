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

if(empty($action))
{
	echo "<a href='bugs.php?action=pridat'>Přidat</a>";
	$sql = "SELECT * FROM `$db`.`bugs` ORDER BY `bugs`.`priority` ASC";
	$q = mysql_query($sql, $spojeni);

	if(mysql_num_rows($q)) while($d = mysql_fetch_array($q))
	{
		if(user() == 1 and $d["resolved"] == 0) $vyresit = "<a href='bugs.php?action=vyresit&id=".$d["id"]."'>vyřešit</a>";
		else $vyresit = "";
		if(!$d["resolved"]) $color = "red";
		else $color = "green";
		echo "
			<p><font color='$color'><b>".$d["title"]."</b></font>, ".date("j/m/Y H:i",$d["time"]).", ".user_jmeno($d["user"])." $vyresit
			<br>".windrow($d["text"])."
			<hr size=1>
		";
	}
}
if($action == "pridat")
{
	if(empty($krok))
	{
		echo "
			<form action='bugs.php?action=pridat&krok=2' method='POST'>
			<input type='text' name='title'>
			<br><textarea name='text' rows='10' cols='47'></textarea>
			<br><input type='submit' value='ok'>
			</form>
		";
	}
	else
	{
		$sql = "INSERT INTO `$db`.`bugs` (`id`, `time`, `user`, `priority`, `title`, `text`, `resolved`) VALUES ('".id("bugs")."', '".time()."', '".user()."', '1', '".$_POST["title"]."', '".$_POST["text"]."', '0')";
		if(mysql_query($sql, $spojeni)) header("location: bugs.php");
		else echo $sql;
	}
}
if($action == "vyresit")
{
	$sql = "UPDATE `wiginote`.`bugs` SET `resolved` = '1' WHERE `bugs`.`id` = $id";
	if(mysql_query($sql, $spojeni)) header("location: bugs.php");
	else echo $sql;

}
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
