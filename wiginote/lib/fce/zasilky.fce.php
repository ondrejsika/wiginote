<?php
// WigiNote v.3
// 2010 (c) developed by NIAL group, Ondrej Sika
// powered by NIRS

function zbozi($id)
{
	global $db, $spojeni;
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = $id";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);
	return explode(";", $d["sku"]);
}

function mnozstviZbozi($id)
{
	global $db, $spojeni;
	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = $id";
	$q = mysql_query($sql, $spojeni);
	$d = mysql_fetch_array($q);

	$sku = zbozi($id);
	$mn = explode(";", $d["mnozstvi_polozky"]);

	$i = 0;
	$array = array();
	foreach($sku as $x)
	{
		while(!isset($mn[$i])) $i--;
		$array[] = array($sku[$i], $mn[$i]);
		$i++;
	}
	return $array;
}

function vratkaDoSkladu($id)
{
	$a = mnozstviZbozi($id);
	//echo "<br>$id";print_r($a);
	$i = 0;
	foreach($a as $b)
	{
		$zb = new sklad;
		$id_zb = $zb->selectFromSku($b[0]);
		$zb->getZbozi($id_zb);
		$zb->doSkladu("naprodejne", $b[1]);
		$zb->saveZbozi();
		$i++;
	}
}

function znovuZeSkladu($id)
{
	$a = mnozstviZbozi($id);
	//echo "<br>$id";print_r($a);
	$i = 0;
	foreach($a as $b)
	{
		$zb = new sklad;
		$id_zb = $zb->selectFromSku($b[0]);
		$zb->getZbozi($id_zb);
		$zb->doSkladu("naprodejne", -1*$b[1]);
		$zb->saveZbozi();
		$i++;
	}
}


//////////////////////////////////////////////////////  BEGIN  zasilky wigishop ////////////////////////////////////////////////////
class zasilky_wigishop
{
	var $id;
	var $vs;
	var $cz;
	var $paid;
	var $bv_paid;
	var $send;
	var $send_mail;
	var $sku;
	var $mnozstvi;
	var $cena;

	var $p_jmeno;
	var $zpusob_dodani;

	public function load($id)
	{
		global $spojeni, $db;
		$sql = "SELECT * FROM `$db`.`zasilky_wigishop` WHERE `id` = $id";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			$this->vs = $d["vs"];
			$this->cz = $d["cz"];
			$this->paid = $d["paid"];
			$this->bv_paid = $d["bv_paid"];
			$this->send = $d["send"];
			$this->doruceno = $d["doruceno"];
			$this->note = $d["note"];
			$this->zpusob_dodani = $d["zpusob_dodani"];
			$this->zpusob_platby = $d["zpusob_platby"];
			$this->datum_obednavky = $d["datum_obednavky"];
			$this->postovne = $d["postovne"];
		
			$this->nick = $d["nick"];
			$this->items = $d["items"];


			return true;
		}
		else
			return false;
	}
	
	public function zasilky_wigishop($id = "")
	{
		if(!empty($id)) $this->load($id);
	}

	public function view($var)
	{
		if(isset($this->$var)) return $this->$var;
		else return "";
	}

	public function input($name, $value = "", $size = "")
	{
		return "<input type='text' name='$name' value='$value' size='$size'>";
	}

	public function stav($name, $ret = "bool")
	{
		if($name == "paid")
		{
			if(($this->paid == 0 and $this->bv_paid == 1) or $this->paid == 2)
			{
				if($ret == "text") return "placeno";
				else return true;			
			}
			else
			{
				if($ret == "text") return "neplaceno";
				else return false;
			}
		}
		if($name == "send")
		{
			if($this->send == 0 and !empty($this->cz))
			{
				if($ret == "text") return "odesláno";
				else return true;
			}
			elseif($this->send == 2 or $this->send ==4 or $this->send == 6)
			{
				if($ret == "text") return "odesláno";
				else return true;
			}
			else
			{
				if($ret == "text") return "neodesláno";
				else return false;
			}
		}
		if($name == "cpost")
		{
			if($this->stav("send") and $this->doruceno == 0)
			{
				$bc = $this->cz;
				$url = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=$bc&locale=EN&send.x=73&send.y=10&send=submit&go=ok";
				$file = load_file($url);
				if($file != false)
				{
					if(contains($file,"Item delivered on"))
					{
						if($ret == "text") return "doručeno";
						if($ret == "int") return 1;
					}
					elseif(contains($file,"After unsuccessful attempt of delivery on"))
					{
						if($ret == "text") return "<font color='red'>Nedoručeno</font>";
						if($ret == "int") return 2;
					}
					else 
					{
						if($ret == "text") return "nenalezeny_informace";
						if($ret == "int") return 0;
					}
				}
				else return "not_conection";
			}
		}
	}

	public function postovne()
	{
		switch($this->zpusob_dodani)
		{
			case 0: return 0;
			case 1: return 60; // intime
			case 2: return 70; // cpost
			case 3: return 100; // ems
			case 4: return 120; // ems s
			case 5: return 0; // osp
		}
	}

	public function cena($typ = "sp")
	{
		if($typ == "bp")
		{
			$cena = explode(";", $this->cena);
			$mnozstvi = explode(";", $this->mnozstvi);
			$cena_celkem = 0;
			$i = 0;
			foreach($cena as $x)
			{
				$cena_celkem += $x*$mnozstvi[$i];
				$i++;
			}
			return $cena_celkem;
		}
		if($typ == "sp")
		{
			return $this->cena("bp") + $this->postovne();
		}
	}

	public function nove($typ = "")
	{			
		global $db, $spojeni;
		if($typ == "vs")
		{
			$sql = "SELECT * FROM `$db`.`zasilky_wigishop` ORDER BY `zasilky_wigishop`.`id` DESC";
			$q = mysql_query($sql, $spojeni);
			$d = mysql_fetch_array($q);
			return $d["vs"] + 1;
		}
	}
	
	public function barva()
	{
		if(!$this->stav("paid") and !$this->stav("send")) $return = "red";
		if($this->stav("paid") and $this->stav("send")) $return = "orange";
		if($this->stav("paid") and !$this->stav("send")) $return = "green";
		if(!$this->stav("paid") and $this->stav("send")) $return = "blue";
		if($this->send == 3) $return =  "yellow";
		//if($this->zpusob_dodani == 1 and $this->stav("paid") and $this->stav("send")) $return = "yellow";
		return $return;
	}

	public function cena_celkem()
	{
		$celkem = $this->postovne;
		$items = explode(";", $this->items);
		foreach($items as $item)
		{
			$it = new items;
			$it->load($item);
			$celkem += $it->cena*$it->mnozstvi;
		}
		return $celkem;
	}

}
//////////////////////////////////////////////////////  END  zasilky wigishop ////////////////////////////////////////////////////
//////////////////////////////////////////////////////  BEGIN  zasilky hrackovnik ////////////////////////////////////////////////////
class zasilky_hrackovnik
{
	var $id;
	var $vs;
	var $cz;
	var $paid;
	var $bv_paid;
	var $send;
	var $send_mail;
	var $sku;
	var $mnozstvi;
	var $cena;

	var $p_jmeno;

	public function load($id)
	{
		global $spojeni, $db;
		$sql = "SELECT * FROM `$db`.`zasilky_hrackovnik` WHERE `id` = $id";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			$this->vs = $d["vs"];
			$this->cz = $d["cz"];
			$this->paid = $d["paid"];
			$this->bv_paid = $d["bv_paid"];
			$this->send = $d["send"];
			$this->doruceno = $d["doruceno"];
			$this->note = $d["note"];
			$this->zpusob_dodani = $d["zpusob_dodani"];
			$this->zpusob_platby = $d["zpusob_platby"];
			$this->datum_obednavky = $d["datum_obednavky"];
			$this->postovne = $d["postovne"];
		
			$this->nick = $d["nick"];
			$this->items = $d["items"];


			return true;
		}
		else
			return false;
	}
	
	public function zasilky_hrackovnik($id = "")
	{
		if(!empty($id)) $this->load($id);
	}

	public function view($var)
	{
		if(isset($this->$var)) return $this->$var;
		else return "";
	}

	public function input($name, $value = "", $size = "")
	{
		return "<input type='text' name='$name' value='$value' size='$size'>";
	}

	public function stav($name, $ret = "bool")
	{
		if($name == "paid")
		{
			if(($this->paid == 0 and $this->bv_paid == 1) or $this->paid == 2)
			{
				if($ret == "text") return "placeno";
				else return true;			
			}
			else
			{
				if($ret == "text") return "neplaceno";
				else return false;
			}
		}
		if($name == "send")
		{
			if($this->send == 0 and !empty($this->cz))
			{
				if($ret == "text") return "odesláno";
				else return true;
			}
			elseif($this->send == 2 or $this->send ==4 or $this->send == 6)
			{
				if($ret == "text") return "odesláno";
				else return true;
			}
			else
			{
				if($ret == "text") return "neodesláno";
				else return false;
			}
		}
		if($name == "cpost")
		{
			if($this->stav("send") and $this->doruceno == 0)
			{
				$bc = $this->cz;
				$url = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=$bc&locale=EN&send.x=73&send.y=10&send=submit&go=ok";
				$file = load_file($url);
				if($file != false)
				{
					if(contains($file,"Item delivered on"))
					{
						if($ret == "text") return "doručeno";
						if($ret == "int") return 1;
					}
					elseif(contains($file,"After unsuccessful attempt of delivery on"))
					{
						if($ret == "text") return "<font color='red'>Nedoručeno</font>";
						if($ret == "int") return 2;
					}
					else 
					{
						if($ret == "text") return "nenalezeny_informace";
						if($ret == "int") return 0;
					}
				}
				else return "not_conection";
			}
		}
	}

	public function postovne()
	{
		switch($this->zpusob_dodani)
		{
			case 0: return 0;
			case 1: return 60; // intime
			case 2: return 70; // cpost
			case 3: return 100; // ems
			case 4: return 120; // ems s
			case 5: return 0; // osp
		}
	}

	public function nove($typ = "")
	{			
		global $db, $spojeni;
		if($typ == "vs")
		{
			$sql = "SELECT * FROM `$db`.`zasilky_hrackovnik` ORDER BY `zasilky_hrackovnik`.`id` DESC";
			$q = mysql_query($sql, $spojeni);
			$d = mysql_fetch_array($q);
			return $d["vs"] + 1;
		}
	}
	
	public function barva()
	{
		if(!$this->stav("paid") and !$this->stav("send")) $return = "red";
		if($this->stav("paid") and $this->stav("send")) $return = "orange";
		if($this->stav("paid") and !$this->stav("send")) $return = "green";
		if(!$this->stav("paid") and $this->stav("send")) $return = "blue";
		if($this->send == 3) $return =  "yellow";
		//if($this->zpusob_dodani == 1 and $this->stav("paid") and $this->stav("send")) $return = "yellow";
		return $return;
	}

	public function cena_celkem()
	{
		$celkem = $this->postovne;
		$items = explode(";", $this->items);
		foreach($items as $item)
		{
			$it = new items;
			$it->load($item);
			$celkem += $it->cena*$it->mnozstvi;
		}
		return $celkem;
	}

}
//////////////////////////////////////////////////////  END  zasilky hrackovnik ////////////////////////////////////////////////////
//////////////////////////////////////////////////////  BEGIN  zasilky topgadget ////////////////////////////////////////////////////
class zasilky_topgadget
{
	var $id;
	var $vs;
	var $cz;
	var $paid;
	var $bv_paid;
	var $send;
	var $send_mail;
	var $sku;
	var $mnozstvi;
	var $cena;

	var $p_jmeno;

	public function load($id)
	{
		global $spojeni, $db;
		$sql = "SELECT * FROM `$db`.`zasilky_topgadget` WHERE `id` = $id";
		$q = mysql_query($sql, $spojeni);
		if(mysql_num_rows($q) != 0)
		{
			$d = mysql_fetch_array($q);
			$this->vs = $d["vs"];
			$this->cz = $d["cz"];
			$this->paid = $d["paid"];
			$this->bv_paid = $d["bv_paid"];
			$this->send = $d["send"];
			$this->doruceno = $d["doruceno"];
			$this->note = $d["note"];
			$this->zpusob_dodani = $d["zpusob_dodani"];
			$this->zpusob_platby = $d["zpusob_platby"];
			$this->datum_obednavky = $d["datum_obednavky"];
			$this->postovne = $d["postovne"];
		
			$this->nick = $d["nick"];
			$this->items = $d["items"];


			return true;
		}
		else
			return false;
	}
	
	public function zasilky_topgadget($id = "")
	{
		if(!empty($id)) $this->load($id);
	}

	public function view($var)
	{
		if(isset($this->$var)) return $this->$var;
		else return "";
	}

	public function input($name, $value = "", $size = "")
	{
		return "<input type='text' name='$name' value='$value' size='$size'>";
	}

	public function stav($name, $ret = "bool")
	{
		if($name == "paid")
		{
			if(($this->paid == 0 and $this->bv_paid == 1) or $this->paid == 2)
			{
				if($ret == "text") return "placeno";
				else return true;			
			}
			else
			{
				if($ret == "text") return "neplaceno";
				else return false;
			}
		}
		if($name == "send")
		{
			if($this->send == 0 and !empty($this->cz))
			{
				if($ret == "text") return "odesláno";
				else return true;
			}
			elseif($this->send == 2 or $this->send ==4 or $this->send == 6)
			{
				if($ret == "text") return "odesláno";
				else return true;
			}
			else
			{
				if($ret == "text") return "neodesláno";
				else return false;
			}
		}
		if($name == "cpost")
		{
			if($this->stav("send") and $this->doruceno == 0)
			{
				$bc = $this->cz;
				$url = "http://cpost.cz/cz/nastroje/sledovani-zasilky.php?barcode=$bc&locale=EN&send.x=73&send.y=10&send=submit&go=ok";
				$file = load_file($url);
				if($file != false)
				{
					if(contains($file,"Item delivered on"))
					{
						if($ret == "text") return "doručeno";
						if($ret == "int") return 1;
					}
					elseif(contains($file,"After unsuccessful attempt of delivery on"))
					{
						if($ret == "text") return "<font color='red'>Nedoručeno</font>";
						if($ret == "int") return 2;
					}
					else 
					{
						if($ret == "text") return "nenalezeny_informace";
						if($ret == "int") return 0;
					}
				}
				else return "not_conection";
			}
		}
	}

	public function postovne()
	{
		switch($this->zpusob_dodani)
		{
			case 0: return 0;
			case 1: return 60; // intime
			case 2: return 70; // cpost
			case 3: return 100; // ems
			case 4: return 120; // ems s
			case 5: return 0; // osp
		}
	}

	public function nove($typ = "")
	{			
		global $db, $spojeni;
		if($typ == "vs")
		{
			$sql = "SELECT * FROM `$db`.`zasilky_topgadget` ORDER BY `zasilky_topgadget`.`id` DESC";
			$q = mysql_query($sql, $spojeni);
			$d = mysql_fetch_array($q);
			return $d["vs"] + 1;
		}
	}
	
	public function barva()
	{
		if(!$this->stav("paid") and !$this->stav("send")) $return = "red";
		if($this->stav("paid") and $this->stav("send")) $return = "orange";
		if($this->stav("paid") and !$this->stav("send")) $return = "green";
		if(!$this->stav("paid") and $this->stav("send")) $return = "blue";
		if($this->send == 3) $return =  "yellow";
		//if($this->zpusob_dodani == 1 and $this->stav("paid") and $this->stav("send")) $return = "yellow";
		return $return;
	}

	public function cena_celkem()
	{
		$celkem = $this->postovne;
		$items = explode(";", $this->items);
		foreach($items as $item)
		{
			$it = new items;
			$it->load($item);
			$celkem += $it->cena*$it->mnozstvi;
		}
		return $celkem;
	}

}
//////////////////////////////////////////////////////  END  zasilky topgadget ////////////////////////////////////////////////////
?>
