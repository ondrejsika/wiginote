<?php

//echo url();

//if(user() == 7) echo "<font size='5'>";
//else echo "<font>";

$nesplnene = pocetNesplnenych(user());
$query = mysql_query("SELECT * FROM `$db`.`chat_nastaveni` WHERE `user` LIKE ".user(), $spojeni);
$d = mysql_fetch_array($query);
$pa = $d["online"]+onlineTime(user());//60*2
if($d["online"] != 0) online(user());

if(jeOnline(user()))
{
$color = "green";
online(user());
}
else $color = "red";


$query = mysql_query("SELECT * FROM `$db`.`text` WHERE `name` = 'vize'", $spojeni);
if(mysql_num_rows($query) != 0)
{
	$d = mysql_fetch_array($query);
	if(!empty($d["text"]))$zobraz = true;
	else $zobraz = false;
}
else $zobraz = false;

$pos = chat()-posledni(user());
if(posledni(user()) != chat()) $chat = "($pos)";
else $chat = "";

	echo "
		<table width='100%'>
		<tr valign='top'>
		<td>";

	echo "
		<a href='home.php'>Home</a> | 	
		<a href='pridat.php'>Přidat</a>";
	if(rights("zbozi.php")) echo " | <a href='zbozi.php'>Zboží</a>";
	if(rights("aukce.php")) echo " | <a href='aukce.php'>Aukce</a>";
	echo " | <a href='news.php?action=2'>Novinky</a>
		 | <a href='chat_menu.php'>Chat</a> <a onClick=\"window.open('chat.php', 'chat', 'width=400,height=800,left=0,top=0,location=no,scrollbars=yes')\">$chat</a>";
	if(rights("strategie.php")) echo " | <a href='strategie.php'>Strategie</a>";
	if(rights("vizitky.php")) echo " | <a href='vizitky.php'>Vizitky</a>";
	if(rights("sz_menu.php")) echo " | <a href='sz_menu.php'>Sledovani</a>";
	if(rights("osp.php")) echo " | <a href='osp.php'>OSP</a>";
	if(rights("faktury.php")) echo " | <a href='faktury.php'>Faktury</a>";
	if(rights("sklad2.php")) echo " | <a href='sklad2.php'>Sklad</a>";
	if(rights("apps_menu.php")) echo " | <a href='apps_menu.php'>Apps</a>";
	if(rights("bugs.php")) echo " | <a href='bugs.php'>Bugs</a>";
	if(rights("admin.php")) echo " | <a href='admin.php'>Administrace</a>";

	$all_url = explode("/", $_SERVER['REQUEST_URI']);
	foreach($all_url as $url);
	if(contains($_SERVER['REQUEST_URI'],"note3")) echo " | switch: <a href='http://wigishop.cz/note/$url'>note</a>";
	else echo " | switch: <a href='http://wigishop.cz/note3/$url'>note3</a>";

	echo "
		</td>
		<td align='right'>
			<a onClick=\"window.open('legenda.php', 'legenda', 'width=300,height=200,left=0,top=0,location=no,scrollbars=no')\"><img src='var/img/main/legenda.png' border='0'></a>
		
			<a href='search.php'><img src='var/img/main/search.png' border='0'></a>
			<a onClick=\"window.open('work_time.php', 'work_time', 'width=200,height=50,left=0,top=0,location=no,scrollbars=yes')\"><img src='var/img/main/work_time.png' border='0'></a>
				
			<b><a href=user_admin.php><font color='$color'>".user_jmeno(user())."</font></a></b> ($nesplnene) | <a href='logout.php'>Odhlásit se</a>
		</td>
		</tr>
		</table>
		<hr size=1 color='black'>
	";


if($zobraz) 
echo "
<center><font size='2'>".$d["text"]."</font></center>
	<hr size=1 color='black'>
";

if (!empty($task))
	echo "<font color='red'>$task</font><hr size='1' color='black'>";//<script type='text/javascript' src='/inc/script.js'></script>
	$_SESSION["task"] = "";
?>
