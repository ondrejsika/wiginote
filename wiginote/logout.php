<?php
// nidb
// (c) nial group, Ondrej Sika
include "core/header.php";
include "core/inc.php";

logout();
offline(user());
header("location: index.php");

include "core/footer.php";
?>
