<?php

require "validate_login.php";
require "utils.php";
require "print_posts.php";

?>

<html>
<head>
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>

<body>

<?php


$sql = "SELECT * FROM `reports`";

$result = mysqli_query($sql_conn, $sql);

while($row = mysqli_fetch_assoc($result)){

	$sql = "SELECT * FROM `posts` where id='". $row["post_id"] . "'";

	$res = mysqli_query($sql_conn, $sql);

	$rows = mysqli_fetch_assoc($res);

	print_post($sql_conn, $rows);
}

?>
</body>

</html>