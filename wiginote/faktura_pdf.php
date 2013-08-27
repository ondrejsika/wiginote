<?php
// WigiNote
// (c) nial group, Ondrej Sika

$locked = 1;

include "core/header.php"; // header
include "core/inc.php"; // included files

if($locked == 0) $access = 1;
elseif(logon() == 1) $access = 1;
else $access = 0;

if(!rights()) echo "<h1>acces denied</h1>";

elseif($access == 1)
{
/**********/



//define('FPDF_FONTPATH','./font/');  
header('Content-Type','text/html; charset=WINDOWS-1250');

$shop = $_GET["shop"];

function k($vstup) {
	$vstup = str_replace("ě", "e", $vstup);
	$vstup = str_replace("á", "a", $vstup);
	$vstup = str_replace("š", "s", $vstup);
	$vstup = str_replace("ř", "r", $vstup);
	$vstup = str_replace("Ě", "E", $vstup);
	$vstup = str_replace("Č", "C", $vstup);
	$vstup = str_replace("č", "c", $vstup);
	$vstup = str_replace("Ř", "R", $vstup);
	$vstup = str_replace("Ň", "N", $vstup);
	$vstup = str_replace("ň", "n", $vstup);
	$vstup = str_replace("ť", "t", $vstup);
	$vstup = str_replace("Ť", "T", $vstup);
  return iconv("UTF-8","windows-1250",$vstup);
}

function jp($id,$sku) {
	global $spojeni, $db;

	$sql = "SELECT * FROM `$db`.`zasilky` WHERE `id` = '$id'";
	$q = mysql_query($sql,$spojeni);
	$d = mysql_fetch_array($q);

	$p = explode(";",$d["prislusenstvi"]);
	foreach($p as $a)
	{
		if($a == $sku) return true;	
	}
	return false;
}

class PDF extends FPDF
{
	//Page header
	function Header()
	{
		  //Logo
		  //Arial bold 15
		  $this->SetFont('Arial','B',20);
		  //Move to the right
		  $this->Cell(80);
		  //Title
		  $this->Cell(30,20,'Faktura - danovy doklad',0,0,'C');
		  //Line break
		  $this->Ln(20);
	}

	function BasicTable($header,$data)
	{
		  //Header
		  foreach($header as $col)
		      $this->Cell(40,7,$col,1);
		  $this->Ln();
		  //Data
		  foreach($data as $row)
		  {
		      foreach($row as $col)
		          $this->Cell(40,6,$col,1);
		      $this->Ln();
		  }
	}
}

//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$xxx = 0;
foreach(explode(";",$id) as $id)
{
	$xxx++;
//	$pdf->AddPage();
	/*
	$sum_cena = 0;
	foreach($_SESSION["zbozi"] as $zbozi)
	{
		$nazev = $zbozi[0];
		$mnozstvi = $zbozi[1];
		$cena_dph = $zbozi[2];
		$cena = $cena_dph / 1.2;
		$cena_celkem = $mnozstvi * $cena;
		$castka_dph = $cena_dph * $mnozstvi;
		$data[] = array($nazev,$mnozstvi,"ks","20%",round($cena, 2),$cena_dph,round($cena_celkem,2),$castka_dph);

		$sum_cena = $sum_cena + $castka_dph;
	
	}*/

$sku = "";

	$sql = "SELECT * FROM  `$db`.`zasilky` WHERE `id` = '$id';";
	$q = mysql_query($sql,$spojeni);
	$d = mysql_fetch_array($q);
	$vs = $d["vs"];
	$cena_mag = $d["cena_celkem_sdph"];

		$sku = explode(";", $d["sku"]);
		$mnozstvi = explode(";", $d["mnozstvi_polozky"]);
//		$cena_mj_sdph = explode(";", $d["cena_polozky_sdph"]);
		$nazev = $cena = $cena_mj = $cena_mj_sdph = $dph = "";
		$i2a = 0;
		foreach($sku as $sku2)
		{
			/*
			$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = $sku2";
			$q = mysql_query($sql,$spojeni);
			$d2 = mysql_fetch_array($q);
			//*/
			$zb = new sklad();
			$id_zb = $zb->selectFromSku($sku2);
			$zb->getZbozi($id_zb, $shop);

			$nazev[] = $zb->nazev;
			
			$cena = pole($d["cena_polozky_sdph"], $i2a);
			echo "<p>$sku2 - $cena";
			$cena_mj[] = $cena/1.2;
			$cena_mj_sdph[] = $cena;
			$dph[] = $cena * 0.2;
			$i2a++;
			
		}


$data = "";
		$i = 0;

		$cena_celkem = 0;
		foreach($sku as $a)
		{//echo $a." ";

			////////////
		

			///////////

			if(!isset($mnozstvi[$i])) $mnozstvi[$i] = $mnozstvi[$i-1];
		if(contains($a, "~")) $n = k(str_replace("~","",$a));
		else {			
		$n = k($nazev[$i]);
		$n = "SKU: $a ".$n; }
			if(strlen($n) > 40)
			{
				$qw = 0;
				$a2 = "";
				$x = fs($n);
				foreach($x as $a2) 
				{	
					if($qw == 0)$data[] = array($a2,$mnozstvi[$i],"ks",naCislo($dph[$i]),naCislo($cena_mj[$i]),naCislo($cena_mj_sdph[$i]),naCislo($cena_mj[$i])*naCislo($mnozstvi[$i]),naCislo($cena_mj_sdph[$i])*naCislo($mnozstvi[$i]));
					else $data[] = array($a2,"","","","","","","");
					$qw++;
				}
			}
			else
			$data[] = array($n,$mnozstvi[$i],"ks",naCislo($dph[$i]),naCislo($cena_mj[$i]),naCislo($cena_mj_sdph[$i]),naCislo($cena_mj[$i])*naCislo($mnozstvi[$i]),naCislo($cena_mj_sdph[$i])*naCislo($mnozstvi[$i]));
//echo $n+
			$cena_celkem = $cena_celkem + naCislo($cena_mj_sdph[$i])*naCislo($mnozstvi[$i]);

			if(!isset($sku[$i+1]))
			{
				$cp = 0;
				$text = "";
				if($d["zpusob_platby"] == "cashondelivery"){ $cp = 50; $text = " + dobírka";}
				if($d["zpusob_platby"] != "checkmo")
				{
					$postovne = postovne($d["doprava"]);
					$cp = $cp + $postovne[1];
					$pdph = $cp * 0.2;
					$data[] = array(k($postovne[0].$text),1,"ks",naCislo($pdph),naCislo($cp*0.8),naCislo($cp),naCislo($cp*0.8),naCislo($cp));
					$cena_celkem = $cena_celkem + naCislo($cp);
				}
			}
			$i++;
		}
	
/*/
		if($cena_celkem < naCislo($cena_mag) + 2 and $cena_celkem > naCislo($cena_mag) - 2)
		{
			$pdf->SetFont('Arial','',20);
			$pdf->Text(10,10,'NESEDI SOUCTY');
			$pdf->SetFont('Arial','',12);
		}
		//*/


	///	BRGIN stranka faktury


	//$pdf->AddFont('my_arial', '', 'my_arial.php'); 

	$line = '________________________________________________________________________________';
	$f = 10;

	$pdf->SetFont('Arial','',12);

	$pdf->AddPage();

	$pdf->Text(10,40,'Datum vystaveni:');
	$pdf->Text(70,40,date("j.m.Y",time()),0,0,'C');
	$pdf->Text(10,45,'Datum splatnosti:');
	$pdf->Text(70,45,date("j.m.Y",time()),0,0,'C');
	$pdf->Text(10,50,'Datum zdanitelneho plneni:');
	$pdf->Text(70,50,date("j.m.Y",time()),0,0,'C');

	$pdf->Text(110,40,'Cislo dokladu (VS):');
	$pdf->Text(150,40,$vs);

	$pdf->Text(10,55+$f,'Dodavatel:');
	$pdf->Text(110,55+$f,'Odberatel:');

	$pdf->Text(10,60+$f,'Firma Lukas Cesal');
	$pdf->Text(10,65+$f,'Sokolovska 118');
	$pdf->Text(10,70+$f,'323 00 Plzen');
	$pdf->Text(10,75+$f,'ICO: 87163535');
	$pdf->Text(10,80+$f,'DIC: CZ8804032391');
	$pdf->Text(10,85+$f,'Ucet: 1588206193/0800');
	$pdf->Text(10,90+$f,'Banka: Ceska Sporitelna a.s.');

	$pdf->Text(110,60+$f, k($d["p_spolecnost"]));
	$pdf->Text(110,65+$f, k($d["p_jmeno"]));
	$pdf->Text(110,70+$f, k($d["p_ulice"]));
	$pdf->Text(110,75+$f, k($d["p_psc"]." ".$d["p_mesto"]));
	$pdf->Text(110,80+$f, k($d["p_stat2"]));
	$pdf->Text(110,85+$f, k("tel: ".$d["p_telefon"]));
	
//	$pdf->Text(110,60+$f,'Konecny prodej');

	$pdf->Text(10,98+$f,$line);

	$a = 18;
	$b = 18;
	$pdf->SetFont('Arial','',10);
	
	$d = array("nazev","pocet","mj","DPH","cena/mj","s dph/mj","celkem bez dph","celkem s dph");
		$pdf->Text(10,90+$b,$d[0]);
		$pdf->Text(65+$a,90+$b,$d[1]);
		$pdf->Text(75+$a,90+$b,$d[2]);
		$pdf->Text(83+$a,90+$b,$d[3]);
		$pdf->Text(95+$a,90+$b,$d[4]);
		$pdf->Text(115+$a,90+$b,$d[5]);
		$pdf->Text(133+$a,90+$b,$d[6]);
		$pdf->Text(160+$a,90+$b,$d[7]);
		$b = $b + 5;

	foreach($data as $d)
	{
		$pdf->Text(10,90+$b,$d[0]);
		$pdf->Text(68+$a,90+$b,$d[1]);
		$pdf->Text(75+$a,90+$b,$d[2]);
		$pdf->Text(83+$a,90+$b,$d[3]);
		$pdf->Text(95+$a,90+$b,$d[4]);
		$pdf->Text(115+$a,90+$b,$d[5]);
		$pdf->Text(133+$a,90+$b,$d[6]);
		$pdf->Text(160+$a,90+$b,$d[7]);
		$b = $b + 5;
	}
	$pdf->SetFont('Arial','',12);
	$pdf->Text(10,90+$b,$line);
	$c = 100+$b;
	$pdf->Text(90,$c,"Sazba");
	$pdf->Text(130,$c,"%");
	$pdf->Text(140,$c,"Zaklad");
	$pdf->Text(160,$c,"DPH");
	$pdf->Text(180,$c,"Celkem");


	$pdf->Text(90,$c+5,"Zakladni sazba");
	$pdf->Text(130,$c+5,"20");
	$pdf->Text(140,$c+5,round($cena_celkem*0.8,2));
	$pdf->Text(160,$c+5,round($cena_celkem*0.2,2));
	$pdf->Text(180,$c+5,$cena_celkem);

	$pdf->SetFont('Arial','',14);
	$pdf->Text(130,$c+15,"Fakturace celkem");
	$pdf->Text(175,$c+15,round($cena_celkem,2)." Kc");
	$pdf->SetFont('Arial','',12);

	//if(user() == "") 
	$vystavil = user_jmeno(user());
	//else $vystavil = "Lukas Cesal";
	$pdf->Text(10,275,"Vystavil:");
	$pdf->Text(40,275,k("$vystavil"));

	$pdf->Text(140,270,"_____________________");
	$pdf->Text(150,275,"razitko & podpis");

	$pdf->SetFont('Arial','',20);
	//$pdf->Text(10,250,k("ěščřžýáíéňťó"));
	
}
///	END stranka faktury
//*
if($xxx == 1)
{
	
	$i2e = 1;
	$file2 = "var/faktury/$shop/".$vs."_$i2e.pdf";
	while(file_exists("var/faktury/$shop/".$vs."_$i2e.pdf")) $i2e ++;

	$pdf->Output("var/faktury/$shop/".$vs."_$i2e.pdf","F");
	header("location: download_fakt.php?shop=$shop&vs=$vs"."_$i2e");
	
	$sql = "INSERT INTO `$db`.`faktury` (`id` ,`vs` ,`user` ,`time` ,`typ`, `file`) VALUES ('".id("faktury")."',  '$vs',  '".user()."',  '".time()."',  '$shop' ,'".$vs."_$i2e')";
	mysql_query($sql, $spojeni);
}
else
{
	$pdf->Output("var/faktury/multifakt/".time().".pdf","F");
	header("location: download_fakt.php?tmp=".time());
	
	$sql = "INSERT INTO `$db`.`faktury` (`id` ,`vs` ,`user` ,`time` ,`typ`, `file`) VALUES ('".id("faktury")."',  '".time()."',  '".user()."',  '".time()."',  '".$shop."_$xxx', '".time()."')";
	mysql_query($sql, $spojeni);
}
//*/
/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
