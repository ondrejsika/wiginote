<?php
include "core/header.php";
include "core/pages/head.php";
include "core/inc.php";

if(empty($_POST["name"]) or empty($_POST["pass"]))
{
echo "	<form action='index.php' method='POST'>
		<center>
			<table width='400'>
				<tr>
					<td width='400'>
						WigiNote ver. 3.0
					</td>
				</tr>
			</table>
			<table width='400'>
				<tr>
					<td width='200'>
						<input type='text' size='20' name='name'>
					</td>
					<td width='200'>
						jméno
					</td>
				</tr>
				<tr>
					<td width='200'>
						<input type='password' size='20' name='pass'>
					</td>
					<td width='200'>
						heslo
					</td>
				</tr>
			</table>
			<table width='400'>
				<tr>
					<td width='400'>
						<input type='submit' value='Přihlaš'>
					</td>
				</tr>
			</table>
		</center>
	";
}
else
{
	// prihlaseni
	function cekat()
	{
		global $spojeni, $db;
		
		$query = mysql_query("SELECT * FROM `$db`.`pristupi` ORDER BY `pristupi`.`time` DESC LIMIT 0 , 3", $spojeni);
		if (mysql_num_rows($query) != 0){
			while($d = mysql_fetch_array($query)){
				$t[] = $d["time"];
				$p[] = $d["prihlasen"];
			}
			if($p[0] == 1 or $p[1] == 1 or $p[2] == 1) return true;
			if($t[0]+20 < time()) return true;
		}
		else return true;
		echo "čekejte 20 vterin";
		return false;
	}


	if(cekat())
	{
		global $spojeni, $db;
		if (je_admin($_POST["name"], $_POST["pass"]))
		{	
			login();
			$sql = "INSERT INTO `$db`.`pristupi` VALUES (".time().",'".$_POST["name"]."','".$_POST["pass"]."',1)";
			session_register("news");
			session_register("alert");
			$_SESSION["news"] = 1;
			$_SESSION["alert"] = 1;
			mysql_query($sql, $spojeni);
			include "inc/fce.php";
			online(user());
			header("location: home.php");
		}
		else
		{
			$sql = "INSERT INTO `$db`.`pristupi` VALUES (".time().",'".$_POST["name"]."','".$_POST["pass"]."',0)";
			mysql_query($sql, $spojeni);
			header("location: index.php");
		}
	}
	else
	{
		header("location: index.php");
	}
}
include "core/footer.php";
?>
