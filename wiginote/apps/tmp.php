<?php

$dir = opendir("tmp");
while ($soubor = readdir($dir))
{
	if($soubor != "." and $soubor != "..")
	{
		$fileTime = filectime("tmp/$soubor") + 3600*24;
		if($fileTime < time()) unlink("tmp/$soubor");
	}
}

?>
