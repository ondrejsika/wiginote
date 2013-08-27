<?php

$file = "var/import/bank/bv.csv";
if(file_exists($file)) unlink($file);
move_uploaded_file($_FILES['file']['tmp_name'], $file);

$sql = "SELECT * FROM `$db`.`zasilky`";
$query = mysql_query($sql, $spojeni);
while ($d = mysql_fetch_array($query))
{
	if(zaplaceno2($d["vs"])) {set_zaplaceno($d["vs"]); echo "<br>".$d["vs"];}
}
unlink($file);
$_SESSION["task"] = "Import byl proveden.";
header("location: sledovani_zasilek.php?shop=$shop");

?>
