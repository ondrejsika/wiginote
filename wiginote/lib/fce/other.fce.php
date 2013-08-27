<?php

function windrow($text)
{
	return str_replace("\n", "<br>\n", $text); 
}

function cut($str, $pocet)
{
	$oddeleno = chunk_split($str,$pocet,".^;^.");
	$rozdeleno = explode(".^;^.", $oddeleno);
	return $rozdeleno[0];
}

function splneno3($date,$id)
{
	if (empty($date)) return "<a href='action.php?id=".$id."&action=1'><font color='red'>nesplněno</font></a>";
	else /*return "
		<script>
		function dotaz(){
		okno=window.confirm('Smazat poznámku?');
		if(okno) window.location.href='action.php?action=2&id=$id';
		}
		</script>

		<a onClick=\"dotaz();\" ><font color='green'>splněno: ".date("j/m/Y H:i", $date)."</font></a>
	";*/
	return "<a href='action.php?id=".$id."&action=6'><font color='green'>splněno: ".date("j/m/Y H:i", $date)."</font></a>";
}
function splneno($date)
{
	if (empty($date)) return "<font color='red'>nesplněno</font>";
	else return "<font color='green'>splněno: ".date("j/m/Y H:i", $date)."</font>";
}

function splneno2($date)
{
	if (empty($date)) return "<font color='red'>nesplněno</font>";
	else return "<font color='green'>".date("j/m/Y H:i", $date)."</font>";
}

function ostatni($user)
{
global $spojeni, $db;
$query = mysql_query("SELECT * FROM `$db`.`user`", $spojeni);
if (mysql_num_rows($query) != 0){
	while($d = mysql_fetch_array($query)){
		if($d["id"] != $user)$ostatni[]=$d["id"];
	}
}
else echo "";

return $ostatni;
}

function id($table)
{
	global $spojeni, $db;
	$sql = "SELECT * FROM `$db`.`$table` ORDER BY `$table`.`id` ASC";
	$query = mysql_query($sql, $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
		$id = $d["id"];
		}
		return $id + 1;
	}
	else return 1;
}

function users()
{
global $spojeni, $db;
$query = mysql_query("SELECT * FROM `$db`.`user` ORDER BY `user`.`id` ASC", $spojeni);
if (mysql_num_rows($query) != 0){
	while($d = mysql_fetch_array($query)){
		$ostatni[]=$d["id"];
	}
}
else return "";

return $ostatni;
}

function tlacitko($page,$title)
{
	return "<form action='$page' method='POST'><input type='submit' value='$title'></form>";
}

function jen_pro($a)
{
	if ($a == 1) return "ano";
	else return "ne";
}
function br($text)
{
	return Str_Replace("\n", "<br>\n", $text); 
}

function jeSoubor($id)
{
	if($id)
	{
		foreach(filesInDir("var/files/poznamky") as $file )
		{
		$id2 = explode("_",$file);
		if($id == $id2[0]) return true;
		}
		return false;
	}
	else return false;
}
function jmenoSouboru($id)
{
	foreach(filesInDir("var/files/poznamky") as $file ) {
	$id2 = explode("_",$file);
	if($id == $id2[0]) return $file;
	}
	return false;
}
function jmenoObrazku($id)
{
	foreach(filesInDir("var/img/zbozi") as $file ) {
	$id2 = explode("_",$file);
	if($id == $id2[0]) return $file;
	}
	return false;
}
function pocetNesplnenych($user)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `pro`=".user()." and `visible`='1' AND `splneno` = 0", $spojeni);
	return mysql_num_rows($query);
}

function title($title)
{
	if(empty($title)) return "Bez titulku";
	else return $title;
}

function refresh($time)
{
	echo "<meta http-equiv=\"refresh\" content=\"$time;\">";
}

function userColor($u)
{
	if($u == 1) return "red";
	if($u == 2) return "blue";
	if($u == 4) return "green";
	if($u == 6) return "orange";
	if($u == 5) return "brown";
}

function online($user)
{
	global $spojeni, $db;
	$sql = "UPDATE `$db`.`chat_nastaveni` SET `online` =  ".time()." WHERE `chat_nastaveni`.`user` = $user";
	mysql_query($sql, $spojeni);
}
function offline($user)
{
	global $spojeni, $db;
	$sql = "UPDATE `$db`.`chat_nastaveni` SET `online` =  '0' WHERE `chat_nastaveni`.`user` LIKE $user";
	mysql_query($sql, $spojeni);
}
function jeOnline($user)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".$user, $spojeni);
	$d = mysql_fetch_array($query);
	$pa = $d["online"]+(60*2);
	if($d["online"] != 0)
	{
		if($pa > time()) return true;
		else return false;
	}
	else return false;
}
function refreshTime($user)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".$user, $spojeni);
	$d = mysql_fetch_array($query);
	return $d["refresh"];
}
function setRefresh($user,$time)
{
	global $spojeni, $db;
	$sql = "UPDATE `$db`.`chat_nastaveni` SET `refresh` =  '$time' WHERE `chat_nastaveni`.`user` LIKE $user";
	mysql_query($sql, $spojeni);
}



function pocetZprav($user)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".$user, $spojeni);
	$d = mysql_fetch_array($query);
	return $d["pocet_zprav"];

}
function onlineTime($user)

{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".$user, $spojeni);
	$d = mysql_fetch_array($query);
	return $d["online_time"];

}

function posledni($user)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".$user, $spojeni);
	$d = mysql_fetch_array($query);
	return $d["posledni"];

}

function posledniRegister($posledni, $user)
{
	global $spojeni, $db;
	$sql = "UPDATE `$db`.`chat_nastaveni` SET `posledni` =  '$posledni' WHERE `chat_nastaveni`.`user` LIKE $user";
	mysql_query($sql, $spojeni);
}

function chat()
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chat`", $spojeni);
	return mysql_num_rows($query);
}

function sendEmail($od,$pro,$co,$id,$ost)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`user`", $spojeni);
	while($d = mysql_fetch_array($query))
	{
		if($d["id"] == $od) {$odEmail = $d["email"]; $odJmeno = $d["jmeno"];}
		if($d["id"] == $pro) {$proEmail = $d["email"]; $proJmeno = $d["jmeno"];}
	}

	$o = explode(";",$ost);

	if ($co == 1)
	{
		$predmet = "Nová poznámka";
		$text = "Máte novou poznámu od $odJmeno ($odEmail).\n".$o[1];
	}
	if ($co == 2)
	{
		$predmet = "Poznámka splněna";
		$text = "Uživatel $odJmeno ($odEmail) spnil vaši poznámku ".$o[0].".";
	}
	if ($co == 3)
	{
		$predmet = "Poznámka obnovena";
		$text = "Uživatel $odJmeno ($odEmail) obnovil vaši poznámku ".$o[0].".";
	}
	if ($co == 4)
	{
		$predmet = "Nový příspěvek diskuze";
		$text = "Uživatel $odJmeno ($odEmail) přidal příspěvek do dizkuze ".$o[0].".";
	}
///////////////////////////
	$naServru = 1;
	if($naServru == 0)
	{
		$mail="from: $odJmeno ($odEmail)
to: $proJmeno ($proEmail)
predmet: $predmet
$text";
	newFile("mail.txt",$mail);
	}
	else
	{
		
		Mail($proEmail, $predmet, $text, "From: " . $odEmail);
	}
///////////////////////////
}

function pro($id)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `id` LIKE '$id'", $spojeni);
	if (mysql_num_rows($query) == 1)
	{
		$d = mysql_fetch_array($query);
		return $d["pro"];
	}
	else return false;
}

function autor($id)
{
	global $spojeni, $db;	
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `id` LIKE '$id'", $spojeni);
	if (mysql_num_rows($query) == 1)
	{
		$d = mysql_fetch_array($query);
		return $d["autor"];
	}
	else return false;
}

function title2($id)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`note` WHERE `id` LIKE '$id'", $spojeni);
	if (mysql_num_rows($query) == 1)
	{
		$d = mysql_fetch_array($query);
		return $d["title"];
	}
	else return false;
}
function jmenoPozadi($id)
{
	foreach(filesInDir("pozadi") as $file ) {
	$id2 = explode("_",$file);
	if($id == $id2[0]) return $file;
	}
	return false;
}
function background($user)
{
	global $spojeni, $db;

	$pozice = "top";
	$repeat = "no-repeat";

	$sql = "SELECT * FROM `pozadi` WHERE `user` like '$user'";
	$query = mysql_query($sql, $spojeni);
	if (mysql_num_rows($query) != 0){
		$d = mysql_fetch_array($query);
		if($d["pozice"] == 1) $pozice = "top";
		if($d["pozice"] == 2) $pozice = "";
		if($d["repeat"] == 1) $repeat = "no-repeat";
		if($d["repeat"] == 2) $repeat = "repeat-x";
		if($d["repeat"] == 3) $repeat = "repeat-y";
		if($d["repeat"] == 4) $repeat = "repeat";
	}
	else
	{
		
	}
	$img = "pozadi/".jmenoPozadi($user);

	if(file_exists($img)) echo "<body style=\"background-image: url('$img'); background-repeat: $repeat; background-position: $pozice\">";
	
}
function chats()
{
	global $spojeni, $db;
	$sql = "SELECT * FROM `$db`.`chats` WHERE `visible` = 1 ORDER BY `chats`.`id` ASC";
	$query = mysql_query($sql, $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
			$ostatni[]=$d["id"];
		}
	}
	else return "";

	return $ostatni;
}
function pro_me($id)
{
	global $spojeni, $db;
	$for_me = "";
	$query = mysql_query("SELECT * FROM `$db`.`chats` WHERE `chats`.`visible` = '1' ORDER BY `chats`.`id` ASC", $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
			$a = explode(";",$d["user"]);
			foreach($a as $b) if($b == user()) $for_me[] = $d["id"];
		}
	}
	else return "";
	return $for_me;
}

function chatName($id)
{
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chats` WHERE `id` LIKE '$id'", $spojeni);
	if (mysql_num_rows($query) == 1)
	{
		$d = mysql_fetch_array($query);
		return $d["nazev"];
	}
	else return false;
}

function mujChat($id)
{
	if(user_prava(user()) == 0) return true;
	
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chats` ORDER BY `chats`.`id` ASC", $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
			$a = explode(";",$d["user"]);
			foreach($a as $b) if($b == user()) return true;
		}
	}
	else return false;
}

function chatPro($chat)
{
	global $spojeni, $db;
	$pro = "";
	$query = mysql_query("SELECT * FROM `$db`.`chats` WHERE `chats`.`id` = '$chat'", $spojeni);
	if (mysql_num_rows($query) != 0){
		$d = mysql_fetch_array($query);
		$pro = explode(";",$d["user"]);
	}
	return $pro;
}
function autorChatu($id)
{
	global $spojeni, $db;	
	$query = mysql_query("SELECT * FROM `$db`.`chats` WHERE `autor`='$id'", $spojeni);
	if (mysql_num_rows($query) == 1)
	{
		$d = mysql_fetch_array($query);
		return $d["id"];
	}
	else return false;
}
function jeAutorChatu($id)
{
	global $spojeni, $db;	
	$query = mysql_query("SELECT * FROM `$db`.`chats` WHERE `id`='$id'", $spojeni);
	if (mysql_num_rows($query) == 1)
	{
		$d = mysql_fetch_array($query);
		return $d["autor"];
	}
	else return false;
}
function mujChat2($id)
{
	//if(user_prava(user()) == 0) return true;
	
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`chats` ORDER BY `chats`.`id` ASC", $spojeni);
	if (mysql_num_rows($query) != 0){
		while($d = mysql_fetch_array($query)){
			$a = explode(";",$d["user"]);
			foreach($a as $b) if($b == user()) return true;
		}
	}
	else return false;
}
function fontColor()
{
	//if(user_prava(user()) == 0) return true;
	
	global $spojeni, $db;
	$query = mysql_query("SELECT * FROM `$db`.`user` WHERE `user`.`id` = '".user()."'", $spojeni);
	if (mysql_num_rows($query) != 0){
		$d = mysql_fetch_array($query);
		if(empty($d["font_color"])) return "black";
		else return $d["font_color"];
	}
	else return "black";
}
//if(function_exists("fontColor")) echo "<font color='".fontColor()."'>";

// BEGIN class

class select
{
	var $sn;
	var $aval;
	var $valn;
	var $sel;
	
	public function select($sn,$aval,$valn,$sel)
	{
		$this->sn = $sn;
		$this->aval = $aval;
		$this->valn = $valn;
		$this->sel = $sel;
	}

	public function write()
	{
		$b = "<select name='".$this->sn."'>\n";
		$o = "";
		$i = 0;
		foreach($this->aval as $val)
		{
			if($this->sel == $i) $s = "SELECTED";
			else $s = "";
			$o = $o . "\t<option value='$val' $s>".$this->valn[$i]."</option>\n";
			$i++;
		}
		$e = "</select>\n";
		return $b.$o.$e;
	}
}

// END class

function load_file($file)
{
	$ret = "";
	$handle = @fopen($file, "r");
	if ($handle) {
	    while (!feof($handle)) {
		$buffer = fgets($handle, 4096);
		$ret = $ret . $buffer;
	    }
	    fclose($handle);
	}
	else return false;
	return $ret;
}

function contains($str, $search)
{
	$a = strstr($str,$search);
	if (empty($a)) return false;
	else return true;
}


function stav($bc)
{

	$url = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=$bc&locale=EN&send.x=73&send.y=10&send=submit&go=ok";

	$file = load_file($url);
	if($file == false)
		return "not_conection";
	else
	{
		if(contains($file,"Item delivered on"))
			return "Doručeno";
		else
			if(contains($file,"After unsuccessful attempt of delivery on")) 
				return "<font color='red'>Nedoručeno</font>";
			else 
				return "nenalezeny_informace";
	}
}

function zaplaceno($vs)
{
	global $spojeni, $db;
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `vs` = '$vs'";
	$query = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($query);
	if($d["bv_paid"] == 1) return "zaplaceno";
	else return "nezaplaceno";
}

function zaplaceno2($vs, $file = "var/import/bank/bv.csv")
{
	global $spojeni, $db;
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `vs` = '$vs'";
	$query = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($query);
	$cena = intval($d["cena_celkem_sdph"]);

	$bv = new csv($file);
	$bv->load();
	foreach($bv->csv as $var)
	{
		if($var[2] == $vs and ($cena > $var[3]-10 and $cena < $var[3]+100)) return true;
		//echo $var[2];
 	}
	return false;
}
function je_posta($vs)/////////////////////////////////////////////////////////////////////////////////////
{
	$vs = cut($vs, 2);
	if($vs == "EE" or $vs == "BO") return true;
	else return false;
}
function set_zaplaceno($vs)
{
	global $spojeni, $db;
	$sql = "UPDATE `$db`.`zasilky` SET `bv_paid` =  '1' WHERE `zasilky`.`vs` = '$vs';";
	mysql_query($sql, $spojeni);
}

function skladem($sku)
{
	global $db, $spojeni;
	$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = '$sku'";
	$q = mysql_query($sql,$spojeni);
	if(mysql_num_rows($q) != 0)
	{
		$d = mysql_fetch_array($q);
		return $d["mnozstvi"];
	}
	else return 0;
}



function byl_odeslan($id,$typ = "")
{
	global $db, $spojeni;
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);
	if(empty($typ)) return $d["sended_email"];
	$sended_email = explode(";", $d["sended_email"]);
	if($typ == "odeslano" and empty($sended_email[0])) return true;
	else return false;
	if($typ == "nedoruceno" and empty($sended_email[1])) return true;
	else return false;
}

function zobraz_novinky($user)
{
	global $db, $spojeni;
	$sql = "SELECT * FROM `$db`.`news_zobraz` WHERE `user` = '$user'";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);

	$sql = "UPDATE `$db`.`news_zobraz` SET `zobraz` = '0' WHERE `news_zobraz`.`user` = '$user'";
	mysql_query($sql, $spojeni);

	if($d["zobraz"] == 0) return false;
	if($d["zobraz"] == 1) return true;
}

function zobraz_novinky2()
{
	global $db, $spojeni;
	foreach(users() as $user)
		{
			$sql = "UPDATE `$db`.`news_zobraz` SET `zobraz` = '0' WHERE `news_zobraz`.`user` = '$user'";
			mysql_query($sql, $spojeni);
		}
}

function pole($var,$val = 0,$od = ";")
{
	$var = explode($od,$var);
	return $var[$val];
}

function celkova_cena($id)
{
	global $db, $spojeni;
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);

	$cena = 0;
	$i = 0;
	$mn = explode(";", $d["mnozstvi_polozky"]);
	foreach(explode(";", $d["cena_polozky_sdph"]) as $cena2)
	{
		while(!isset($mn[$i])) $i = $i - 1;		

		$cena = $cena + $cena2 * $mn[$i];
		$i++;
	}
	return $cena;
}

function array_delete($array,$num)
{
	unset($array[$num]);
	$ra = array();
	foreach($array as $a)
	{
		$ra[] = $a;
	}
	return $ra;
}

function inArray($array, $str)
{
	foreach($array as $val)
	{
		if($val == $str) return true;
	}
	return false;
}

 function sku2()
	{// BEGIN function sku
		global $db, $tb, $spojeni;

		$sql = "SELECT * FROM `$db`.`sklad2` ORDER BY `sklad2`.`sku` DESC LIMIT 1";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		return $d["sku"]+1;
	}// END function sku


function magento_datum($in)
{
	$datum_obednavky = explode(" ", $in);
	$datum2 = $datum_obednavky[0];
	$datum3 = explode(".", $datum2);
	return $datum3[0].".".$datum3[1].".";
}

function window_open($file, $name = "", $ost = "")
{
	echo "
		<script>
			window.open('$file', '".time()."', 'name' , 'ost');
		</script>
	";
}
$okno = array(600,400);
?>
