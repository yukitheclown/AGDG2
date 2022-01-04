<html>

<head>
<link rel="stylesheet" href="style1.css">
</head>

<body>

<div id="header">Banned</div>


<?php

if(!isset($_GET["p"]))
	echo "<div id='header_sub'>Banned until ". date("l, F d Y", $_GET["t"]) . " at " .  date("g:i:sA", $_GET["t"]) . "</div>";
else
	echo "<div id='header_sub'>Forever</div>";

?>

</body>

</html>