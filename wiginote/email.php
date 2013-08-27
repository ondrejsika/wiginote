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



//	include "menu.php";

///////////////////////////////
if(empty($krok))
{
	echo "
		<form action='email.php?krok=2&id=$id' method='post'>
			<font size='4'>Email</font>
			<br>Předmět
			<br><input type='text' name='predmet' size='87'>
			<p>Text
			<br><textarea name='text' cols='100' rows='30'>\n\n\n~user~\nwww.WigiShop.cz - internetový obchod se zábavnou elektronikou </textarea>
			<br>( ~vs~ variabilní symbol, ~cz~ cislo zasilky, ~link~ link sledování, ~user~ uživatel )
			<br><input type='submit' value='Odeslat'>
		</form>
	";
}
else
{
	$ids = explode(";", $id);
	foreach($ids as $id)
	{
		$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			$predmet = $_POST["predmet"];

			$text = $_POST["text"];
			$text = str_replace("~vs~", $d["vs"], $text);
			$cz = $d["cz"];
			$text = str_replace("~cz~",  $cz, $text);
			$ls = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=".$cz."&locale=CZ&send.x=0&send.y=0&send=submit&go=ok";
			$text = str_replace("~link~", "<a href='$ls'>$ls</a>", $text);
			$text = str_replace("~user~", user_jmeno(user()), $text);
			echo $text = str_replace("\n", "\n<br>", $text);
			mail($d["email"], $predmet, windrow($text), "from: expedice@wigishop.cz\r\nContent-type: text/html; charset=UTF-8\r\n");//"obchod@wigishop.cz"
		}
	}
}
//////////////////////////////


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
