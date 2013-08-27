<?php

$name = "smartstore_preklad";
$csv = csv_to_array("var/import/smartstore/produkty/$name.csv");
$i = 20000;
foreach($csv as $b)
{
	$zbozi = new sklad;
	//is_mumeric($cena[$i])
	$cena2 = intval($b["price_vat"]) - 10;
	$zbozi-> setZbozi("smartstore",$b["id"],$i,"naprodejne:0;nezvestice:0;doma:0;",0,0,"wigishop:".$i,"wigishop:".$cena2,"",$b["price_vat"],2,$b["name"],$b["caption"],$b["description"], $b["img"],"");
	if(!empty($b["id"]))$zbozi-> saveZbozi();
	$i++;
}

?>
