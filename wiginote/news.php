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

	$disp = array(1024,768);
	

	$left = $disp[0]/2 - $okno[0]/2;
	$top = $disp[1]/2 - $okno[1]/2;
	
	if(empty($action)) // zobraz novinky
	{
		echo "<center><font size='4'>Novinky</font></center><br>";
		$query = mysql_query("SELECT * FROM `$db`.`news`", $spojeni);
		if (mysql_num_rows($query) != 0)
		{
			$d = mysql_fetch_array($query);
			if(!empty($d["text"])) echo windrow($d["text"]);
			else echo "Nejsou žádné novinky";
		}
		else
		echo "Nejsou žádné novinky";
	}
	if($action == 2) //menu novinky
	{
include "core/pages/menu.php";
		if (!empty($task))
		echo "<font color='red'>$task</font><hr size='1' color='black'>";
		$query = mysql_query("SELECT * FROM `$db`.`news`", $spojeni);
		$d = mysql_fetch_array($query);
		echo "
			<p><a href='news.php?action=2'>Zobrazit novinky</a> | <a href='news.php?action=3'>Upravit novinky</a>
		";
		echo "<hr size='1' color='black'>".br($d["text"]);
	}
	if($action == 3) //upravit novinky
	{
		include "menu.php";
		if (empty($krok))
		{
			$query = mysql_query("SELECT * FROM `$db`.`news`", $spojeni);
			$d = mysql_fetch_array($query);
			echo "
				<p><a href='news.php?action=2'>Zobrazit novinky</a> | <a href='news.php?action=3'>Upravit novinky</a>
		";// window.open('news.php', 'novinky', 'width=400,height=200,left=$left,top=$top,location=no')
			echo "
				<form action='news.php?action=3&krok=2' method='POST'>
				<textarea rows='10' name='text' cols='60'>".$d["text"]."</textarea>
				<p><input type='submit' value='Upravit'>
				</form>
			";
		}
		else
		{
			echo "
				<p><a ohref='news.php?action=2'>Zobrazit novinky</a> | <a href='news.php?action=3'>Upravit novinky</a>
		";
			$sql = "UPDATE `$db`.`news` SET `text` =  '$text' WHERE `news`.`id` = 1 ;";
			
			if(mysql_query($sql, $spojeni))
			{
				echo "<p>Upraveno";
				zobraz_novinky2();
				header("location: news.php?action=2&task=Upraveno");
			}
			else
			{
				echo "<p>Vyskytla se chyba!<p>" . $sql;;
			}
		}
	}

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
