<?php
// nidb
// (c) nial group, Ondrej Sika
include "inc/header.php";
include "inc/log_fce.php";
if (logon())
{//
	include "inc/cn.php";
	include "inc/fce.php";
	include "inc/var.php";

	refresh(5);
	if(jeOnline(user()))
	{
		$_SESSION["ses_jeOnline"] = true;
		online(user());
	}
	else 
	{
		$_SESSION["ses_jeOnline"] = false;
		offline(user())
	}
}//
else
{
	header("location: index.php");
}
include "inc/footer.php";
?>
