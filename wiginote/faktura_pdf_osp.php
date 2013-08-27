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

function k($vstup) {			//prevede utf8 do cp1250
  $vstup = str_replace("ě", "e", $vstup);
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

$data = array();
$data[] = array("nazev","pocet","mj","DPH","cena/mj","s dph","celkem","celkem dph");
$sum_cena = 0;
$zb = explode(";", $_GET["zbozi"]);

foreach($zb as $zbozi)
{
	$zbozi = explode(",",$zbozi);
	$sku = $zbozi[0];
	$mnozstvi = $zbozi[1];
	if(!is_numeric($sku))
	{
		$nazev = $sku;
		$cena_dph = $zbozi[2];
	}
	else
	{
		$sql = "SELECT * FROM `$db`.`sklad` WHERE `sku` = $sku";
		$q = mysql_query($sql, $spojeni);
		$d = mysql_fetch_array($q);
		$nazev = $d["nazev"];
		if($zbozi[2] == 0)
			{ 
				$cena_dph = $d["cena"];
				$cena_dph = explode(";",$cena_dph);
				$cena_dph = round($cena_dph[0]);
			}
		else $cena_dph = $zbozi[2];
	}
	$cena = $cena_dph / 1.2;
	$cena_celkem = $mnozstvi * $cena;
	$castka_dph = $cena_dph * $mnozstvi;
	
	if(strlen($nazev) < 40)	$data[] = array(k($nazev),$mnozstvi,"ks","20%",round($cena, 2),$cena_dph,round($cena_celkem,2),$castka_dph);
	else
	{
		$i2a = 0;
		foreach(fs($nazev) as $n)
		{
			if($i2a == 0) $data[] = array(k($n),$mnozstvi,"ks","20%",round($cena, 2),$cena_dph,round($cena_celkem,2),$castka_dph);
			else $data[] = array(k($n),"","","","","","","");
			$i2a++;
		}
	}

	$sum_cena = $sum_cena + $castka_dph;
	
}

//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->AddFont('my_arial', '', 'my_arial.php'); 

$line = '________________________________________________________________________________';
$f = 10;

$pdf->SetFont('Arial','',12);

$sql = "SELECT * FROM `$db`.`faktury` WHERE `typ` = 'osp' ORDER BY  `faktury`.`vs` DESC";
$q = mysql_query($sql, $spojeni);
if(mysql_num_rows($q) != 0)
{
	$d = mysql_fetch_array($q);
	$vs = $d["vs"]+1;
}
else $vs = 200000001;


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

$pdf->Text(110,60+$f,'Konecny prodej');

$pdf->Text(10,98+$f,$line);

$a = 18;
$b = 18;
$pdf->SetFont('Arial','',10);
foreach($data as $d)
{
	$pdf->Text(10,90+$b,$d[0]);
	$pdf->Text(65+$a,90+$b,$d[1]);
	$pdf->Text(75+$a,90+$b,$d[2]);
	$pdf->Text(83+$a,90+$b,$d[3]);
	$pdf->Text(95+$a,90+$b,$d[4]);
	$pdf->Text(115+$a,90+$b,$d[5]);
	$pdf->Text(135+$a,90+$b,$d[6]);
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

$scbd = round($sum_cena/1.2,2);
$scsd = round($sum_cena,2);
$pdf->Text(90,$c+5,"Zakladni sazba");
$pdf->Text(130,$c+5,"20");
$pdf->Text(140,$c+5,$scbd);
$pdf->Text(160,$c+5,$scsd - $scbd);
$pdf->Text(180,$c+5,$scsd);

$pdf->SetFont('Arial','',14);
$pdf->Text(130,$c+15,"Fakturace celkem");
$pdf->Text(175,$c+15,round($sum_cena,2)." Kc");
$pdf->SetFont('Arial','',12);

//if(user() == "") 
$vystavil = user_jmeno(user());
//else $vystavil = "Lukas Cesal";
$pdf->Text(10,275,"Vystavil:");
$pdf->Text(40,275,"$vystavil");

$pdf->Text(140,270,"_____________________");
$pdf->Text(150,275,"razitko & podpis");

$pdf->SetFont('courier','',20);
//$pdf->Text(10,250,k("ěščřžýáíé"));

$pdf->Output("var/faktury/osp/".$vs.".pdf","F");

$sql = "INSERT INTO `$db`.`faktury` (`id` ,`vs` ,`user` ,`time` ,`typ`, `file`) VALUES ('".id("faktury")."',  '$vs',  '".user()."',  '".time()."',  'osp', '$vs')";
mysql_query($sql, $spojeni);

header("location: download_fakt.php?osp=".$vs);

/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
