<?php
// &nbsp;

function ed($var)
{
	global $db, $spojeni;
	
	$sql = "SELECT * FROM `$db`.`email_data` WHERE `var` = '$var';";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);
	return $d["val"];
}

function send_email($id,$typ)
{
	global $spojeni, $db;
	
	$sql = "SELECT * FROM `$db`.`email` WHERE `typ` = '$typ'";
	$q = mysql_query($sql, $spojeni);
	if(mysql_num_rows($q) != 0)
	{	
		$d = mysql_fetch_array($q);
		$text = $d["zprava"];
		$predmet = $d["predmet"];
		
		$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			$v = array("vs","cz");
			foreach($v as $v2) $$v2 = $d[$v2];

			if($d["shop"] == "wigishop") $shop_predmet = ed("wigishop_predmet");
			else $shop_predmet = "";
			$ls = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=".$cz."&locale=CZ&send.x=0&send.y=0&send=submit&go=ok";

			$predmet = str_replace("~shop_predmet~", $shop_predmet, $predmet);
			$predmet = str_replace("~vs~", $vs, $predmet);
			$predmet = str_replace("á", "a", $predmet);
			//$predmet = iconv("UTF-8", "ISO-8859-1", $predmet);
	
			$text = "<center><img src='http://wigitron.cz/wiginote/var/img/main/email_head.jpg'></center>\n".$text;
			$text = str_replace("~vs~", $vs, $text);
			$text = str_replace("~cz~", $cz, $text);
			$text = str_replace("~link~", "<a href='~ls~'>~ls~</a>", $text);
			$text = str_replace("~ls~", $ls, $text);
			$text = str_replace("~user~", user_jmeno(user()), $text);
			$text = str_replace("\n", "\n<br>", $text);
			//$text = iconv("UTF-8", "ISO-8859-1", $text);
			mail($d["email"], $predmet, windrow($text), "from: expedice@wigishop.cz\r\nContent-type: text/html; charset=UTF-8\r\n");//"obchod@wigishop.cz"
		}
	}
}
/*
send_email(1,"nedoruceno_cpost");
send_email(1,"odeslano_cpost");
send_email(1,"odeslano_intime");
send_email(1,"odeslano_ems");
//*/
?>
