<?php

function csv_to_array($filename='', $delimiter=';')
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;

	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}
	return $data;
}

class csv
{
	var $csv;
	var $path;
	
	public function csv($path = "")
	{
		$this->path = $path;
	}
	
	public function load($path = "")
	{
		if($path == "")	$path = $this->path;

		if(file_exists($path))
		{
			$read = "";
			$handle = @fopen($path, "r");
			if (1==1) {
			    while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				$read = $read . $buffer;
			    }
			    fclose($handle);
			}
			$read = str_replace("\"","",$read);
			$lines = explode("\n",$read);
			$i = 0;
			foreach($lines as $line)
			{
				if(!empty($lines[$i]))
				{
					$csv_lines = "";
					$ds = explode(";",$line);
					foreach($ds as $d)
					{
						$csv_lines[] = $d;
					}
					$csv[] = $csv_lines;
				}
				$i++;
			}
			$this->csv = $csv;
			return $csv;
		}
		else 
		{
			$this->csv = false;
			return false;
		}
	}
	public function simply($b = 0)
	{
		$r = "<table width='100%' border=$b>\n";
		foreach($this->csv as $line)
		{
			$r = $r . "\t<tr>\n";
			foreach($line as $d) $r = $r . "\t\t<td>$d</td>\n";
			$r = $r . "\t</tr>\n";
		}
		$r = $r . "</table>\n";
		return $r;
	}
	public function delete($d)
	{
		$i = 0;
		foreach($this->csv as $line)
		{
			if($i != $d) $csv[] = $line;
			$i++;
		}
		$this->csv = $csv;
		return $csv;
	}
	public function save($path = "")
	{
		if($path == "") $path = $this->path;
		$wr = "";
		foreach($this->csv as $line)
		{
			$l = 0;
			$i = 0;
			foreach($line as $a) $l++;
			foreach($line as $val)
			{
				$i++;
				$wr = $wr . $val;
				if($i < $l) $wr = $wr . ";";
			}
			$wr = $wr . "\n";
		}
		$file = fopen($path, "w");
		fwrite($file, $wr);
		fclose($file);
	}

	public function write($line_array)
	{
		$this->csv[] = $line_array;
	}	
}
?>
