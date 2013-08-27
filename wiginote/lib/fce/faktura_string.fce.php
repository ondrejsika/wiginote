<?php

function fs($str)
{
	$s = $str;
//echo strlen($str);
	$x = 0;
	do
	{
		if($x != 0)
		{
			$s2 = explode(";",chunk_split($str,$len,";"));
			$i = 0;
			$s = "";
			foreach($s2 as $a)
			{
				if($i != 0) $s .= $a;
				$i++;
			}
		}
//echo "<p>$x - ".$s;
		if(strlen($s) > 40)
		{
			$s = explode(";",chunk_split($s,40,";"));
			$s = explode(" ",$s[0]);

			$i = 0;
			$s2 = "";
			foreach($s as $a)
			{
				$i++;
				if(isset($s[$i])) $s2[] = $a; 
			}
	//foreach($s2 as $a)echo $a;
			$i = 0;
			$s = "";
			foreach($s2 as $a)
			{
				if($i == 0)
					$s .= $a;
				else
					$s .= " ".$a;
				$i++;
			}
		}
		$return[] = $s;
	//	echo "<br>".$s;
		$len = 0;
		foreach($return as $a)
			$len = $len + strlen($a);
		//echo "<br>$x - ".	$len;
		$x++;
	}
	while($len < strlen($str));
	
	return $return;
}

?>
