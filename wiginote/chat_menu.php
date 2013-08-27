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


	
	if (empty($action))
	{
		echo "
			<a onClick=\"window.open('chat.php', 'chat', 'width=400,height=800,left=0,top=0,location=no,scrollbars=yes')\">Zobrazit chat</a>
		";
		refresh(10);
		echo "<p>";
		foreach(users() as $user)
		{
			if(jeOnline($user)) echo "<font color='green'>".user_jmeno($user)."</font> - online<br>";
			else echo "<font color='red'>".user_jmeno($user)."</font> - offline<br>";
		}
		echo "
			<p><font size='4'>User chats</font>
			<br><a href='chat_menu.php?action=1'>Přidat chat</a>
			
		";
		if(user_prava(user()) == 0) $chats = chats();
		else $chats = pro_me(user());
		if(!empty($chats))
		{
			echo "<p>Pro vás přístupné chaty";
			foreach ($chats as $chat)
			{
				{
					echo "<br><a onClick=\"window.open('chat2.php?id=$chat', 'chat', 'width=400,height=800,left=0,top=0,location=no,scrollbars=yes')\"><b>".chatName($chat)."</b></a> - pro: ";
					foreach(chatPro($chat) as $user) echo user_jmeno($user).", ";
					if(user() == jeAutorChatu($chat) or user_prava(user()) == 0) echo " <a href='chat_menu.php?action=2&id=$chat'>Odstranit</a>";
				}
			}
		}
	}
	
	if($action == 1)
	{
		if(empty($krok) or empty($nazev))
		{
			echo "
				<form action='chat_menu.php?action=1&id=$id&krok=2' method='POST'>
				<p>název chatu<br><input type='text' size='30' name='nazev'>
				<br>pro:
			";
			foreach(users() as $user) 
				{
					if($user == user()) $sel = "checked"; else $sel = "";
					echo "<br><input type='checkbox' name='$user' value='1' $sel>".user_jmeno($user);
				}
			echo "
				<p><input type='submit' value='přidat'>
				</form>
			";
		}
		else 
		{
			$users = "";
			foreach(users() as $user) if(isset($_POST[$user])) $users = $users.$user.";";

			$sql = "INSERT INTO `$db`.`chats` VALUES ('".id("chats")."','".user()."','$nazev','$users','1')";
		
			if(mysql_query($sql, $spojeni)) $task =  "Nový chat vytvořen";
			else $task = "Nový chat nebyl vytvořen";
			$_SESSION["task"] = $task;
			header("location: chat_menu.php");
		}
	}
	if($action == 2)
	{
		if(user() == jeAutorChatu($id) or user_prava(user()) == 0)
		{
			$sql = "UPDATE `$db`.`chats` SET `visible` =  '0' WHERE `chats`.`id` LIKE $id";
			if(mysql_query($sql, $spojeni)) $task = "Chat odstraněn.";
			else $task = "Chat nebyl odstraněn.";
			$_SESSION["task"] = $task;
			header("location: chat_menu.php");
		}
		else 
		echo "access denied!";
	}

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
