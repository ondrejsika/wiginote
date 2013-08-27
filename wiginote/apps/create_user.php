<?php

$jmeno = "Josef Kozák";
$name = "pepa";
$pass = "pepa";
$email = "a@a";
$prava = 2;

$id = id("user");

$sql = "INSERT INTO `$db`.`user` (`id`, `name`, `pass`, `jmeno`, `prava`, `email`) VALUES ('$id', '$name', '$pass', '$jmeno', '$prava', '$email');";
if(mysql_query($sql, $spojeni)) echo "<br><font color='green'>tvorba uzivatele OK</font>";
else echo "<br><font color='red'>tvorba uzivatele selhalo</font>";

$sql = "INSERT INTO `$db`.`chat_nastaveni` (`user`, `refresh`, `online`, `pocet_zprav`, `online_time`, `posledni`) VALUES ('$id', '2', '0', '50', '120', '0');"; 
if(mysql_query($sql, $spojeni)) echo "<br><font color='green'>nastaveni chatu OK</font>";
else echo "<br><font color='red'>nastaveni chatu selhalo</font>";
?>
