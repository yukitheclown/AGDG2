<?php

require "validate_login.php";
require "utils.php";

if(isset($_POST["submit"]) && !empty($_POST["checked"])){

	foreach($_POST["checked"] as $key){

		$key = mysqli_real_escape_string($sql_conn, $key);

		$sql = "SELECT * FROM `invite_keys` WHERE inv_key='" . $key . "'";

		$result = mysqli_query($sql_conn, $sql);

		$value = mysqli_fetch_assoc($result)["sent"];

		if($value)
			$value = 0;
		else
			$value = 1;

		$sql = "UPDATE `invite_keys` SET sent='" . $value . "' WHERE inv_key='" . $key . "'";

		mysqli_query($sql_conn, $sql);
	}
}

if(isset($_POST["create_more"]) && $user_status == "admin"){

	$sql = "SELECT * FROM `invite_keys` WHERE user_id='" . $user_id . "' AND sent='0'";

	$result = mysqli_query($sql_conn, $sql);

	if(mysqli_num_rows($result) < 15){

		for($i = 0; $i < 5; $i++){

		    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $len = strlen($chars);
		    $key = '';

		    for ($k = 0; $k < 40; $k++) {
		        $key .= $chars[rand(0, $len - 1)];
		    }

			$sql = "INSERT INTO `invite_keys` (user_id, inv_key) VALUES ('" . $user_id . "', '" .  $key . "')";
				
			mysqli_query($sql_conn, $sql);
		}
	}

}

?>

<html>
<head>
<title>/AGDG2/ Profile</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="style1.css">
<script src="script.js"></script>
</head>

<html>

<body>

<div id = "header" style="margin-bottom: 20px;">Invitation Links.</div>

<form method="post" action="profile.php">

<?php

$sql = "SELECT * FROM `invite_keys` WHERE user_id='" . $user_id . "'";

$result = mysqli_query($sql_conn, $sql);

for($i = 0; $i < mysqli_num_rows($result); $i++){

	$rows = mysqli_fetch_assoc($result);

	$link = "http://yukizini.com/create_account.php?invite_key=" . $rows["inv_key"];

	$sent = $rows["sent"];

	if($sent)
		$sent = 1;
	else
		$sent = 0;

	if($sent)
		echo "<div id='header_sub' style='text-decoration: line-through; margin-bottom: 5px; font-size: 10pt'>" . $link . 
			"<input type='checkbox' name='checked[]' value='" . $rows["inv_key"] . "'></div>";
	else
		echo "<div id='header_sub' style='margin-bottom: 5px; font-size: 10pt'>
				<a href=" . $link . ">" . $link . "</a><input type='checkbox' name='checked[]' value='" . $rows["inv_key"] . "'></div>";
}

?>

<input style='display:table; margin: 0 auto; margin-top: 20px;' type='submit' name='submit' value='Toggle checked as sent'>

<?php

if($user_status == "admin"){
	$sql = "SELECT * FROM `invite_keys` WHERE user_id='" . $user_id . "' AND sent='0'";

	$result = mysqli_query($sql_conn, $sql);

	if(mysqli_num_rows($result) < 15){
		echo "<input style='display:table; margin: 0 auto; margin-top: 20px;' type='submit' name='create_more' value='Create More'>";
	}
}

?>

</form>

<div id = "header_sub" style="margin-top: 20px;">Do not hesitate to invite! You will get more links later!</div>

</body>

</html>