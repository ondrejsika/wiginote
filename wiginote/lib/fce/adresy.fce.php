<?php
// WigiNote v.3
// 2010 (c) developed by NIAL group, Ondrej Sika
// powered by NIRS

class adresy
{
	var $id;

	var $jmeno;
	var $firma;
	var $ic;
	var $dic;
	var $ulice;
	var $mesto;
	var $psc;
	var $stat;
	var $telefon;

	public function set($jmeno, $firma, $ic, $dic, $ulice, $mesto, $psc, $stat, $telefon = "")
	{
		global $db, $spojeni;
		$id = id("adresy");
		$sql = "INSERT INTO `$db`.`adresy` (`id`, `jmeno`, `firma`, `ic`, `dic`, `ulice`, `mesto`, `psc`, `stat`, `telefon`)VALUES ('$id', '$jmeno', '$firma', '$ic', '$dic', '$ulice', '$mesto', '$psc', '$stat', '$telefon');";
		mysql_query($sql, $spojeni);
		return $this->id = $id;
	}
	
	public function load($id, $var = "")
	{
		global $db, $spojeni;
		$sql = "SELECT * FROM `$db`.`adresy` WHERE `id` = $id";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		
		$this->id = $id;

		$this->jmeno = $d["jmeno"];
		$this->ulice = $d["ulice"];
		$this->mesto = $d["mesto"];
		$this->psc = $d["psc"];
		$this->stat = $d["stat"];
		$this->telefon = $d["telefon"];
		$this->firma = $d["firma"];
		$this->ic = $d["ic"];
		$this->dic = $d["dic"];

		if(!empty($var) and isset($this->$var)) return $this->$var;
	}

	public function edit($id, $jmeno, $firma, $ic, $dic, $ulice, $mesto, $psc, $stat, $telefon = "")
	{
		global $db, $spojeni;
		$sql = "
			UPDATE  `$db`.`adresy` SET  `jmeno` =  '$jmeno',
			`firma` =  '$firma',
			`ic` =  '$ic',
			`dic` =  '$dic',
			`ulice` =  '$ulice',
			`mesto` =  '$mesto',
			`psc` =  '$psc',
			`stat` =  '$stat',
			`telefon` =  '$telefon' WHERE  `adresy`.`id` = $id;";
		mysql_query($sql, $spojeni);
	}
}
?>
