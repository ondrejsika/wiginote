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
include "core/pages/head.php";
include "core/pages/menu.php";



		$pocet = 10; // pocet polozek na fakture
	///
	if(empty($krok))
	{
		$na = $sa = array("");
		$sql = "SELECT * FROM `$db`.`sklad` ORDER BY `sklad`.`nazev` ASC";
		$q = mysql_query($sql, $spojeni);
		while($d = mysql_fetch_array($q))
		{
			$na[] = $d["nazev"];
			$sa[] = $d["sku"];
		}

		echo "
			<form action='faktura.php?krok=2' method='post'>
			<table>
				<tr>
					<td>
						zbozi
					</td>
					<td>
						nazev
					</td>
					<td>
						cena(nemusi se vyplnovat)
					</td>
					<td>
						mnozstvi
					</td>
				</tr>
				";
		for($i=0;$i<$pocet;$i++)
		{
			$sp = new select("sku_$i",$sa,$na,0);
			echo "
				<tr>
					<td>
						".$sp->write()."
					</td>
					<td>
						<input type='text' name='nazev_$i'>
					</td>
					<td>
						<input type='text' name='cena_$i'>
					</td>
					<td>
						<input type='text' name='mnozstvi_$i'>
					</td>
				</tr>";



//"<br>".$sp->write()."";
		}

	echo "
			</table>
		";
		echo "
			<br><button>ok</button>
		";
	}
	if($krok == 2)
	{
		$s_sku = $s_mnozstvi = $s_cena = array();
		for($i=0;$i<$pocet;$i++)
		{
			$sku = $_POST["sku_$i"];
			$nazev = $_POST["nazev_$i"];
			$cena = $_POST["cena_$i"];
			$mnozstvi = $_POST["mnozstvi_$i"];
			if(!empty($mnozstvi))
			{
				if(empty($cena)) $cena = 0;
				if(!empty($nazev)) $s_sku[] = $nazev;
				else $s_sku[] = $sku;
				$s_mnozstvi[] = $mnozstvi;
				$s_cena[] = $cena;
			}
			
		}

		$s_sku; // sku zbozi
		$s_mnozstvi; // mnozstvi
		$s_cena; // nova cena na fakturu

		$i=0;
		$vystup = "";
		foreach($s_sku as $a) 
		{
			if(empty($vystup)) $vystup = $a.",".$s_mnozstvi[$i].",".$s_cena[$i];
			else $vystup .= ";".$a.",".$s_mnozstvi[$i].",".$s_cena[$i];
			$i++;	
		}
		echo "
			<script>
				window.open('faktura_pdf_osp.php?zbozi=".$vystup."', 'osp_faktura', 'width=0,height=0,left=0,top=0,location=no,scrollbars=no');
			</script>
			<p><a href='osp.php' target='blank'>Zpět na OSP faktury</a>
		";//
		//header("location: osp.php");
	}

	///


/**********/
}
else
{
	include "core/pages/refresher.php";
}

include "core/footer.php"; // footer
?>
