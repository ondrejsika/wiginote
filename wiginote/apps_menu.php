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

function aplikace($page, $name)
{ 
	echo "<br><a onClick=\"window.open('$page', 'apps', 'width=400,height=400,left=0,top=0,location=no,scrollbars=yes')\">$name</a>";
}
echo "<a href='phpinfo.php' target='blank'>phpinfo</a>";
aplikace("apps.php?app=import_magento_produkty","Import produktů z Magenta");
aplikace("apps.php?app=create_user","Create user");
aplikace("apps.php?app=import_smartstore_produkty","Import produktů ze Smartstore");
aplikace("apps.php?app=export_magento_produkty","Export produktů do Magenta");
aplikace("apps.php?app=export_magento_obrazky_create_makefile","Tvorba makefile obrázků do magenta");
aplikace("apps.php?app=prevod_zasilek_wigishop","Převod zásilek Wigishop");

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
