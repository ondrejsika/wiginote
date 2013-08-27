<?php

function newFile($file,$text)
{
	if(is_writable($file) or !file_exists($file))
	{
		$soubor = fopen($file, "w");
		fwrite($soubor, $text);
		fclose($soubor);
		if(filesize($file) == 0) return false;
		$soubor2 = fopen($file, "r");
		$loadText = fread($soubor2, filesize($file));
		fclose($soubor2);
		if($loadText == $text) return true;
		else return false;
	}
	else
		return false;
}
function loadFile($file)
{
	if(file_exists($file) and is_readable($file))
	{
		$soubor = fopen($file, "r");
		$text = fread($soubor, filesize($file));
		fclose($soubor);
		return $text;
	}
	else
		return false;
}
function printFile($file)
{
	if(file_exists($file) and is_readable($file))
	{
		$soubor = fopen($file, "r");
		$text = fread($soubor, filesize($file));
		fclose($soubor);
		return str_replace("\n", "<br>\n", $text);
	}
	else
		return false;
}
function hideFile($file)
{
	if(file_exists($file))
	{
		chmod($file, 0004);
		return true;
	}
	else
		return false;
}
function displayFile($file)
{
	if(file_exists($file))
	{
		chmod($file, 0777);
		return true;
	}
	else
		return false;
}
function accessRights($file)
{
	if(file_exists($file))
	{
		 return substr(sprintf("%o", fileperms("$file")), -4);
	}
	else
		return false;
}
function filesInDir($sl)
{
	if(is_dir($sl))
	{
	$dir = opendir($sl);
	while ($soubor = readdir($dir))
	{
		if($soubor != "." and $soubor != "..")	$files[] = $soubor;
	}
	}
	return $files;
}
?>
