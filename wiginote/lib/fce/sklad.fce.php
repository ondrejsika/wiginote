<?php
// WigiNote v.3
// 2010 (c) developed by NIAL group, Ondrej Sika
// powered by NIRS

// BEGIN conf
include_once "core/inc.php";
$tb = "sklad2";
// END conf

class sklad
{// BEGIN class sklad
	var $allZbozi;

	var $id; // id polozky v databazi
	var $dodavatel; // dodavatel
	var $sku_dodavatele; // pokud je z ciny neviplnuje se
	var $sku; // sku skladu
	var $mnozstvi; // mnozstvi na skladu
	var $mnozstvi_kriticke; // kritické monožství
	var $mnozstvi_prodano; // prodáno
	var $sku_shop; // sku na jednotlivych shopech
	var $cena_shop; // cena v jednotlivych shopech
	var $cena_zvyhodnena; // zvyhodneni ceny (k produktu, mnozstevni slevy)
	var $cena_nakupni; // nakupni cena zbozi 1 cislo
	var $dostupnost; // skladova dostupnost
	var $nazev; // nazev zbozi
	var $popis_kratky; // kratky popisek
	var $popis_dlouhy; // podrobny popis
	var $obrazek; // url obrazku (note:/img/produkty/321.jpg;wigishop:http://wigishop.cz/img/321.jpg;)
	var $ostatni; // ostatni informace	

	// BEGIN supported function
	protected function id()
	{// BEGIN function id
		global $db, $tb, $spojeni;

		$sql = "SELECT * FROM `$db`.`$tb` ORDER BY `$tb`.`id` DESC LIMIT 1";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		return $d["id"]+1;
	}// END function id

	protected function sku()
	{// BEGIN function sku
		global $db, $tb, $spojeni;

		$sql = "SELECT * FROM `$db`.`$tb` ORDER BY `$tb`.`sku` DESC LIMIT 1";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		return $d["sku"]+1;
	}// END function sku

	protected function rozdel($var, $co)
	{// BEGIN function rozdel
		$expl = explode(";", $var);
		$array = array();
		foreach($expl as $a)
		{
			if(!empty($a))
			{
				$expl2 = explode(":", $a);
				if($co == "sku_shop") $array[] = array("shop" => $expl2[0], "sku" => $expl2[1]);
				if($co == "cena_shop") $array[] = array("shop" => $expl2[0], "cena" => $expl2[1]);
				if($co == "cena_zvyhodnena") $array[] = array("shop" => $expl2[0], "akce" => $expl2[1], "cena" => $expl2[2]);
				if($co == "dostupnost") $array[] = array("shop" => $expl2[0], "dostupnost" => $expl2[1]);
				if($co == "img") $array[] = array("shop" => $expl2[0], "img" => $expl2[1]);
				if($co == "ostatni") $array[] = array("var" => $expl2[0], "val" => $expl2[1]);
				if($co == "sklad") $array[] = array("sklad" => $expl2[0], "mnozstvi" => $expl2[1]);
			}
		}
		return $array;
	}// END function rozdel
	
	protected function spoj($array)
	{// BEGIN function rozdel
		$i = 0;
		if(is_array($array))
		{
			foreach($array as $val)
			{
				$j = 0;
				foreach($val as $a)
				{
					if($j == 0) $str2 = $a;
					else $str2 .= ":" . $a;
					$j++;
				}
				if($i == 0) $str = $str2;
				else $str .= ";" . $str2;
				$i++;
			}
			if(!isset($str)) $str = "";
			return $str;
		}
		else
			return "";
	}// END function rozdel

	protected function vyber($var, $co, $shop)
	{// BEGIN function vyber
		$var = $this-> rozdel($var, $co);
		foreach($var as $a)
		{
			if($a["shop"] == $shop)
			{
				if($co == "sku_shop") return $a["sku"];
				if($co == "cena_shop") return $a["cena"];
			}
		}
	}// END function vyber
	// END supported function

	// BEGIN main function
	public function getZbozi($id, $shop = "")
	{// BEGIN function getZbozi
		global $db, $spojeni, $tb;

		$sql = "SELECT * FROM `$db`.`$tb` WHERE `id` = '$id'";
	
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			if(empty($shop))
			{
				$this->id = $d["id"];
				$this->dodavatel = $d["dodavatel"];
				$this->sku_dodavatel = $d["sku_dodavatel"];
				$this->sku = $d["sku"];
				$this->mnozstvi = $this-> rozdel($d["mnozstvi"], "sklad");
				$this->mnozstvi_kriticke = $d["mnozstvi_kriticke"];
				$this->mnozstvi_prodano = $d["mnozstvi_prodano"];
				$this->sku_shop = $this-> rozdel($d["sku_shop"], "sku_shop");
				$this->cena_shop = $this-> rozdel($d["cena_shop"], "cena_shop");
				$this->cena_zvyhodnena = $this-> rozdel($d["cena_zvyhodnena"], "cena_zvyhodnena", $shop);
				$this->cena_nakupni = $d["cena_nakupni"];
				$this->dostupnost = $d["dostupnost"];
				$this->nazev = $d["nazev"];
				$this->popis_kratky = $d["popis_kratky"];
				$this->popis_dlouhy = $d["popis_dlouhy"];
				$this->img = $d["img"];
				$this->ostatni = $this-> rozdel($d["ostatni"], "ostatni");
			}
			else
			{
				$this->id = $d["id"];
				$this->dodavatel = $d["dodavatel"];
				$this->sku_dodavatel = $d["sku_dodavatel"];
				$this->sku = $d["sku"];
				$this->mnozstvi = $this-> rozdel($d["mnozstvi"], "sklad");
				$this->mnozstvi_kriticke = $d["mnozstvi_kriticke"];
				$this->mnozstvi_prodano = $d["mnozstvi_prodano"];
				$this->sku_shop = $this-> vyber($d["sku_shop"], "sku_shop", $shop);
				$this->cena_shop = $this-> vyber($d["cena_shop"], "cena_shop", $shop);
				$this->cena_zvyhodnena = $this-> rozdel($d["cena_zvyhodnena"], "cena_zvyhodnena", $shop);
				$this->cena_nakupni = $d["cena_nakupni"];
				$this->dostupnost = $d["dostupnost"];
				$this->nazev = $d["nazev"];
				$this->popis_kratky = $d["popis_kratky"];
				$this->popis_dlouhy = $d["popis_dlouhy"];
				$this->img = $d["img"];
				$this->ostatni = $this-> rozdel($d["ostatni"], "ostatni");
			}
			return true;
		}
		else
			return false;
	}// END function getZbozi

	public function setZbozi(
		$dodavatel,
		$sku_dodavatel,
		$sku,
		$mnozstvi,
		$mnozstvi_kriticke,
		$mnozstvi_prodano,
		$sku_shop,
		$cena_shop,
		$cena_zvyhodnena,
		$cena_nakupni,
		$dostupnost,
		$nazev,
		$popis_kratky,
		$popis_dlouhy,
		$img,
		$ostatni
	)
	{// BEGIN function setZbozi
		global $db, $spojeni, $tb;

		$this->id = $this-> id();
		$this->dodavatel = $dodavatel;
		$this->sku_dodavatel = $sku_dodavatel;
		if($sku == "sku") $sku = $this-> sku();
		$this->sku = $sku;
		$this->mnozstvi = $this-> rozdel($mnozstvi, "sklad");
		$this->mnozstvi_kriticke = $mnozstvi_kriticke;
		$this->mnozstvi_prodano = $mnozstvi_prodano;
		$this->sku_shop = $this-> rozdel($sku_shop, "sku_shop");
		$this->cena_shop = $this-> rozdel($cena_shop, "cena_shop");
		$this->cena_zvyhodnena = $this-> rozdel($cena_zvyhodnena, "cena_zvyhodnena");
		$this->cena_nakupni = $cena_nakupni;
		$this->dostupnost = $dostupnost;
		$this->nazev = $nazev;
		$this->popis_kratky = $popis_kratky;
		$this->popis_dlouhy = $popis_dlouhy;
		$this->img = $img;
		$this->ostatni = $this-> rozdel($ostatni, "ostatni");
		return true;
	}// END function setZbozi

	public function saveZbozi($id = "")
	{// BEGIN function saveZbozi
		global $db, $spojeni, $tb;

		$sql = "SELECT * FROM `$db`.`$tb` WHERE `id` = '".$this->id."'";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) == 0)
		{
			if(empty($id)) $id = $this->id();
			$sql = "
				INSERT INTO  `$db`.`$tb` (`id`,`dodavatel`,`sku_dodavatel`,`sku`,`mnozstvi`,`mnozstvi_kriticke`,`mnozstvi_prodano`,`sku_shop`,`cena_shop`,`cena_zvyhodnena`,`cena_nakupni`,`dostupnost`,			`nazev`,`popis_kratky`,`popis_dlouhy`,`img`,`ostatni`) VALUES (".$id." ,  '".$this->dodavatel."',  '".$this->sku_dodavatel."',  '".$this->sku."',  '".$this-> spoj($this->mnozstvi)."',  '".$this->mnozstvi_kriticke."',  '".$this->mnozstvi_prodano."',  '".$this-> spoj($this->sku_shop)."',  '".$this-> spoj($this->cena_shop)."',  '".$this-> spoj($this->cena_zvyhodnena)."',  '".$this->cena_nakupni."',  '".$this->dostupnost."', '".$this->nazev."',  '".$this->popis_kratky."',  '".$this->popis_dlouhy."',  '".$this->img."',  '".$this-> spoj($this->ostatni)."');
			";
		}
		else
		{
			$sql = "
				UPDATE  `$db`.`$tb` SET  `dodavatel` =  '".$this->dodavatel."',
				`sku_dodavatel` =  '".$this->sku_dodavatel."',
				`sku` =  '".$this->sku."',
				`mnozstvi` =  '".$this-> spoj($this->mnozstvi)."',
				`mnozstvi_kriticke` =  '".$this->mnozstvi_kriticke."',
				`mnozstvi_prodano` =  '".$this->mnozstvi_prodano."',
				`sku_shop` =  '".$this-> spoj($this->sku_shop)."',
				`cena_shop` =  '".$this-> spoj($this->cena_shop)."',
				`cena_zvyhodnena` =  '".$this-> spoj($this->cena_zvyhodnena)."',
				`cena_nakupni` =  '".$this->cena_nakupni."',
				`dostupnost` =  '".$this->dostupnost."',
				`nazev` =  '".$this->nazev."',
				`popis_kratky` =  '".$this->popis_kratky."',
				`popis_dlouhy` =  '".$this->popis_dlouhy."',
				`img` =  '".$this->img."',
				`ostatni` =  '".$this-> spoj($this->ostatni)."' WHERE  `$tb`.`id` = ".$this->id.";
			";
		}
		
		if(mysql_query($sql, $spojeni)) return true;
		else return false;
	}// END function saveZbozi

	public function allZbozi($razeni = "id", $smer = "DESC")
	{// BEGIN function allZbozi
		global $db, $spojeni, $tb;
		$sql = "SELECT * FROM `$db`.`$tb` ORDER BY `$tb`.`$razeni` $smer";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$array = array();
			while($d = mysql_fetch_array($q))
			{
				$array[] = $d["id"];
			}
			$this->allZbozi = $array;
			return $array;
		}
		else return false;
	}// END function allZbozi

	// BEGIN constructor
	public function sklad($id = "", $shop = "")
	{
		if(!empty($id))
		{
			if(!empty($shop)) $this->getZbozi($id, $shop);
			else $this->getZbozi($id);
		}
	}
	// END constructor
	// END main function

	protected function skladem($skladem = "")
	{// BEGIN function skladem
		if(empty($skladem)) $skladem = $this->dostupnost;

		if($skladem == 0) return "neskladem";
		if($skladem == 2) return "do 3 dnů";
		if($skladem == 3) return "do týdne";
		if($skladem == 4) return "do 2 týdnů";

		if($skladem == "neskladem") return 0;
		if($skladem == "do 3 dnů" or $skladem == "do_3_dnu") return 2;
		if($skladem == "do týdne" or $skladem == "do_tydne") return 3;
		if($skladem == "do 2 týdnů" or $skladem == "do_2_tydnu") return 4;
	}// END function skladem

	public function setSkladem($skladem)
	{// BEGIN function setSkladem
		if($skladem == "neskladem") $this->dostupnost = 0;
		if($skladem == "2" or $skladem == "do_3_dnu") $this->dostupnost = 2;
		if($skladem == "3" or $skladem == "do_tydne") $this->dostupnost = 3;
		if($skladem == "4" or $skladem == "do_2_tydnu") $this->dostupnost = 4;
	}// END function setSkladem

	public function naSklade($sklad)
	{// BEGIN function naSklade
		foreach($this->mnozstvi as $a) if($a["sklad"] == $sklad) return $a["mnozstvi"];
		return "false";
	}// END function naSklade

	public function naSkladech()
	{// BEGIN function naSkladech
		$celkem = 0;
		foreach($this->mnozstvi as $a) if($a["sklad"] != "reklamace") $celkem += $a["mnozstvi"];
		return $celkem;
	}// END function naSkladech

	public function doSkladu($sklad, $ks = 1)
	{// BEGIN function doSkladu
		$na_sklade = $this->naSklade($sklad);
		if($na_sklade != "false")
		{
			$i = 0;
			foreach($this->mnozstvi as $a)
			{
				if($a["sklad"] == $sklad)
				{
					$this->mnozstvi[$i]["mnozstvi"] += $ks;
				}
				$i++;
			}
		}
	}// END function doSkladu

	public function zeSkladu($sklad, $ks = 1)
	{// BEGIN function zeSkladu
		$na_sklade = $this->naSklade($sklad);
		if($na_sklade != false)
		{
			$i = 0;
			foreach($this->mnozstvi as $a)
			{
				if($a["sklad"] == $sklad)
				{
					$this->mnozstvi[$i]["mnozstvi"] -= $ks;
				}
				$i++;
			}
		}
	}// END function zeSkladu

	public function dostupnost($vystup = "int")
	{// BEGIN function dostupnost
		if($this-> naSkladech() > 0 and $vystup == "int") $return = 1;
		else $return = $this->dostupnost;

		if($this-> naSkladech() > 0 and $vystup == "str") $return = "skladem";
		else $return = $this-> skladem($this-> dostupnost);

		return $return;
	}// END function dostupnost

	public function editZbozi($var, $val)
	{// BEGIN function editZbozi
		if(isset($this->$var))
		{
			$this->$var = $val;
			return true;
		}
		else
			return false;
	}// END function editZbozi

	public function ostatni($var)
	{// BEGIN function ostatni
		if(is_array($this->ostatni))
			foreach($this->ostatni as $a) if($a["var"] == $var) return $a["val"];
		return false;
	}// END function ostatni
	
	public function getFieldString($field)
	{// BEGIN function getFieldString
		global $db, $spojeni, $tb;

		$sql = "SELECT * FROM `$db`.`$tb` WHERE `id` = '".$this->id."'";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			if(isset($d[$field])) return $d[$field];
		}
		else return "";
	}// BEGIN function getFieldString

	public function searchZbozi($var, $val)
	{// BEGIN function getFieldString
		global $db, $spojeni, $tb;
		$sql = "SELECT * FROM `$db`.`$tb` WHERE `$var` = '$val'";
		$q = mysql_query($sql, $spojeni);
		$return = array();
		if(mysql_num_rows($q) != 0)
		{
			while($d = mysql_fetch_array($q))
			{
			$return[] = $d["id"];
			}
		}
		return $return;
	}// BEGIN function getFieldString

	public function selectFromSku($sku)
	{// BEGIN function getFieldString
		global $db, $spojeni, $tb;
		$sql = "SELECT * FROM `$db`.`$tb` WHERE `sku` = '$sku'";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		return $d["id"];
	}// BEGIN function getFieldString
}// END class sklad

?>
