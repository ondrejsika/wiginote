<?php

function isImage($file)
{
	if(file_exists($file) and is_readable($file))
	{
		if(getimagesize($file)) return true;
		else return false;
	}
	else return false;
}

?>
