<?php

function xml($xml, $name)
{
	$data = stristr($xml, "<$name>");
	$data = stristr($data, "</$name>", true);
	$data = str_replace("<$name>", "", $data);
	$data = str_replace("\n", "", $data);
	$data = str_replace("\t", "", $data);
	return $data;
}

?>
