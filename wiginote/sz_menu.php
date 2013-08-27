<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

include "core/header.php"; // header
include "core/inc.php"; // included files

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if(!rights()) echo "<h1>access denied</h1>";

elseif($access == 1)
{
/**********/
include "core/pages/head.php";
include "core/pages/menu.php";

//<a href='sz_wigishop.php'>Wigishop</a>
echo "
	<font size='4'>
		<a href='sledovani_zasilek.php'>Wigishop</a> <a href='sz_wigishop.php'>(new)</a>
		<p><a href='sz_topgadget.php'>Topgadget</a>
		<p><a href='sz_hrackovnik.php'>Hrackovnik</a>
		<p><a href='sz_aukro.php'>Aukro</a>
	</font>
";

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
