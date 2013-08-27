<?php

$start_id = 20000;

$az = new sklad;
$allZbozi = $az-> allZbozi();
$line='"store";"websites";"attribute_set";"type";"category_ids";"sku";"has_options";"name";"meta_title";"meta_description";"image";"small_image";"thumbnail";"gallery";"url_key";"url_path";"custom_design";"page_layout";"options_container";"image_label";"small_image_label";"thumbnail_label";"gift_message_available";"price";"special_price";"cost";"weight";"description";"short_description";"meta_keyword";"custom_layout_update";"aw_video";"special_from_date";"special_to_date";"news_from_date";"news_to_date";"custom_design_from";"custom_design_to";"color";"status";"tax_class_id";"visibility";"enable_googlecheckout";"dostupnost";"qty";"min_qty";"use_config_min_qty";"is_qty_decimal";"backorders";"use_config_backorders";"min_sale_qty";"use_config_min_sale_qty";"max_sale_qty";"use_config_max_sale_qty";"is_in_stock";"low_stock_date";"notify_stock_qty";"use_config_notify_stock_qty";"manage_stock";"use_config_manage_stock";"stock_status_changed_automatically";"product_name";"store_id";"product_type_id";"product_status_changed";"product_changed_websites"'."\n";

foreach($allZbozi as $id)
{
	$z = new sklad;
	$z-> getZbozi($id, "wigishop");
	$img = "smartstore/".$z->sku_dodavatel.".jpg";
	
	$sku = $z->sku_shop + $start_id;

	$line .= "\"admin\";\"base\";\"Default\";\"simple\";\"55\";\"".$sku."\";\"0\";\"".$z->nazev."\";\"\";\"\";\"".$img."\";\"".$img."\";\"".$img."\";\"".$z->sku.".jpg\";\"".url_key($z->nazev)."\";\"".url_key($z->nazev)."\";\"\";\"3 columns\";\"Block after Info Column\";\"\";\"\";\"\";\"Použít nastavení\";\"".$z->cena_shop."\";\"\";\"\";\"1.0000\";\"".$z->popis_dlouhy."\";\"".$z->popis_kratky."\";\"\";\"\";\"\";\"\";\"\";\"\";\"\";\"\";\"\";\"\";\"Aktivní\";\"DPH\";\"Katalogu, Vyhledávání\";\"Ano\";\"skladem\";\"-1.0000\";\"0.0000\";\"1\";\"0\";\"1\";\"0\";\"1.0000\";\"1\";\"0.0000\";\"1\";\"1\";\"2010-09-1 09:01:47\";\"10.0000\";\"1\";\"0\";\"1\";\"0\";\"".$z->nazev."\";\"0\";\"simple\";\"\";\"\"\n";
}

//echo $line;

$fp = fopen("var/export/magento/produkty/export_produkty_".time().".csv", 'w');
fwrite($fp, $line);
fclose($fp);

?>
