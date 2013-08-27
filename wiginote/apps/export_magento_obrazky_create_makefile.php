<?php

$start_id = 20000;

$text = "";
$sql = "SELECT * FROM `$db`.`sklad2`";
$q = mysql_query($sql, $spojeni);
while($d = mysql_fetch_array($q))
{
	$id = $start_id + $d["id"];
	$img_url = "smartstore/".$d["sku_dodavatel"].".jpg";
	$text .= "$id;$img_url\n";
}

$file = "var/export/magento/obrazky/makefile";
$f = fopen($file, "w");
fwrite($f, $text);
fclose($f);

?>
