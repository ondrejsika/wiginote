<?php

$file = "tmp/import_file_".time().".csv";
if(file_exists($file)) unlink($file);
move_uploaded_file($_FILES['file']['tmp_name'], $file);

$f = fopen($file, "r");
$text = fread($f, 99999);
fclose($f);

$magento = "\"Order Number\";\"Order Date\";\"Order Status\";\"Order Purchased From\";\"Order Payment Method\";\"Order Shipping Method\";\"Order Subtotal\";\"Order Tax\";\"Order Shipping\";\"Order Discount\";\"Order Grand Total\";\"Order Paid\";\"Order Refunded\";\"Order Due\";\"Total Qty Items Ordered\";\"Customer Name\";\"Customer Email\";\"Shipping Name\";\"Shipping Company\";\"Shipping Street\";\"Shipping Zip\";\"Shipping City\";\"Shipping State\";\"Shipping State Name\";\"Shipping Country\";\"Shipping Country Name\";\"Shipping Phone Number\";\"Billing Name\";\"Billing Company\";\"Billing Street\";\"Billing Zip\";\"Billing City\";\"Billing State\";\"Billing State Name\";\"Billing Country\";\"Billing Country Name\";\"Billing Phone Number\";\"Order Item Increment\";\"Item Name\";\"Item Status\";\"Item SKU\";\"Item Options\";\"Item Original Price\";\"Item Price\";\"Item Qty Ordered\";\"Item Qty Invoiced\";\"Item Qty Shipped\";\"Item Qty Canceled\";\"Item Qty Refunded\";\"Item Tax\";\"Item Discount\";\"Item Total\"";

$bv = "\"Valuta zaúčt.\";\"Var.symb.2\";\"Částka CZK\";\"Bankovní spojení\";\"Dat.zprac.\";\"Var.symb.1\";\"Storno\";\"Název protiúčtu\";\"Konst.symb.\";\"Spec.symb.\";\"Zpráva pro příjemce\";\"Referenční číslo\";";

if(contains($text, $magento))
{
	$target = "var/import/magento/obednavky/order_export_".time().".csv";
	copy($file, $target);
	import_order($target);
}
if(contains($text, iconv("UTF-8","windows-1250",$bv)))
{
	$target = "var/import/bank/bv_".time().".csv";
	copy($file, $target);
	
	$sql = "SELECT * FROM `$db`.`zasilky`";
	$query = mysql_query($sql, $spojeni);
	while ($d = mysql_fetch_array($query))
	{
		if(zaplaceno2($d["vs"], $target)) {set_zaplaceno($d["vs"]);}
	}
	echo "Import byl proveden.";
}
?>
