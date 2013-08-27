<?php
function postovne($doprava, $shop = "wigishop")
{
	if($shop == "wigishop")
	{
		if($doprava == 1) return array("Intime",70); // intime
		if($doprava == 2) return array("Česká pošta",80); // cpost
		if($doprava == 3) return array("EMS",150); // ems
		if($doprava == 4) return array("EMS sobota",220); // ems sobota
		if($doprava == 5) return array("",0); // osp
	}
}
?>
