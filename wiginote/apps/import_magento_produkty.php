<?php

include "core/pages/head.php";

$name = "export";
$file = "var/import/magento/produkty/$name.csv";

$r = fopen($file, "r");
$text = fread($r, filesize($file));
fclose($r);

$text = str_replace("'", "", $text);

$w = fopen($file, "w");
fwrite($w, $text);
fclose($w);

$csv = csv_to_array($file);


foreach($csv as $d)
{
	$z = new sklad;
	echo "<p>SKU: ".$sku = $d["sku"];
	echo "<br>Název: ".$nazev = $d["product_name"];
	echo "<br>Krátký: ".$popis_kratky = $d["short_description"];
	echo "<br>Dlouhý: ".$popis_dlouhy = $d["description"];
	echo "<br>Cena: ".$cena = intval($d["price"]);
	echo "<br>IMG: ".$img = $d["image"];
	
	$z-> setZbozi(
		"",
		"",
		$sku,
		"naprodejne:0;nezvestice:0;doma:0",
		3,
		0,
		"wigishop:$sku",
		"wigishop:$cena",
		"",
		"",
		0,
		"$nazev",
		"$popis_kratky",
		"$popis_dlouhy",
		$img,
		""
	);

	$sql = "SELECT * FROM `$db`.`sklad2` WHERE `sku` = $sku";
	$q = mysql_query($sql, $spojeni);

	if(mysql_num_rows($q) == 0) $z-> saveZbozi();

	

}

?>
