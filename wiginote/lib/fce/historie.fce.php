<?php
// &nbsp;


function historie($zasilka,$action,$data)
{
	global $db, $spojeni;
	$sql = "NSERT INTO `$db`.`historie` VALUES ('".id("historie")."', $zasilka, '".user()."', '".time()."',$action,$data)";
	if(mysql_query($sql,$spojeni)) return true;
	else return false;
}

?>
