<?php

$file = "var/export/magento/obrazky/makefile";
$f = fopen($file, "r");
$str = fread($f, filesize($file));
fclose($f);

$magento_db = "wigishop_cz_magento";

$products = explode("\n", $str);
foreach($products as $a)
{
	$x = explode(";", $a);
	$sku = $x[0];
	$img_url = $x[1];

	echo $sql = "SELECT * FROM `$magento_db`.`catalog_product_entity` WHERE `sku` = '$sku'";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);
	echo $id = $d["entity_id"];

	echo $sql = "
		INSERT INTO  `$magento_db`.`catalog_product_entity_media_gallery`
		(`value_id` ,`attribute_id`,`entity_id`,`value`)
		VALUES (NULL, '77', '$id', '$img_url')
	";

	mysql_query($sql, $spojeni);
}

?>
