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
if(isset($_POST["action"])) $action = $_POST["action"];
else $action = "";

if($action == "do_skladu" or $action == "do skladu")
{
	if(isset($_POST["do_skladu"])) $do_skladu = $_POST["do_skladu"];
	else $do_skladu = "";

	//if($do_skladu = "")
	{
		$zb = new sklad;
		$zb->getZbozi($id);
		if(!empty($_POST["ze"]))
			$zb->zeSkladu($_POST["ze"],$do_skladu);
		if(!empty($_POST["do"]))
			$zb->doSkladu($_POST["do"],$do_skladu);

		$task = "";
		if($zb-> naSklade("doma") >= 0 and $zb-> naSklade("nezvestice") >= 0)
			$zb->saveZbozi();
		else
			$task = "&task=nelze";
	}
	if($page == "podrobnosti") header("location: sklad2_podrobnosti.php?id=$id"."$task");
}
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
