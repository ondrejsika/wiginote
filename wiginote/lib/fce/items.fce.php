<?php
// WigiNote v.3
// 2010 (c) developed by NIAL group, Ondrej Sika
// powered by NIRS

class items
{
	var $id;

	var $sku;
	var $mnozstvi;
	var $cena;
	var $sleva;
	var $nazev;

	public function set($sku, $nazev, $cena, $mnozstvi, $sleva = "")
	{
		global $db, $spojeni;
		$id = id("items");
		$sql = "INSERT INTO `$db`.`items` (`id`, `sku`, `nazev`, `cena`, `mnozstvi` , `sleva`)VALUES ('".$id."', '$sku', '$nazev', '$cena', '$mnozstvi', '$sleva');";
		mysql_query($sql, $spojeni);
		return $id;
	}
	
	public function load($id, $var = "")
	{
		global $db, $spojeni;
		$sql = "SELECT * FROM `$db`.`items` WHERE `id` = $id";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		
		$this->sku = $d["sku"];
		$this->nazev = $d["nazev"];
		$this->cena = $d["cena"];
		$this->sleva = $d["sleva"];
		$this->mnozstvi = $d["mnozstvi"];

		if(empty($var) and isset($this->$var)) return $this->$var;
	}
}
?>
