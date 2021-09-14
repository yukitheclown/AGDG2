<?php

ini_set('display_errors', true);

$error = false;

if(isset($_POST["submit"])){

	$username = $password = $password_repeat = "";
		
	if(!empty($_POST["username"])){
		$username = $_POST["username"];
	}
	if(!empty($_POST["password"])){
		$password = $_POST["password"];
	}


	if(strlen($username) < 5 || strlen($username) > 16 || !preg_match("/^[a-z0-9A-Z ]*$/", $username))
		$error = true;

	if(strlen($password) < 5)
		$error = true;

	if($error == false){

		require "sql.php";

		$username = mysqli_real_escape_string($sql_conn,$username);
		$password = mysqli_real_escape_string($sql_conn,sha1($password));

		$sql = "SELECT * FROM `users` WHERE username='". $username . "' AND password='" . $password . "'";

		$result = mysqli_query($sql_conn, $sql);

		if($result && mysqli_num_rows($result) != 0){
			
			session_start();

			$_SESSION["username"] = $username;
			$_SESSION["password"] = $password;

			header("Location: /index.php");

		} else {

			$error = true;
		}
	}
}

?>

<html>

<head>
<title>Login /AGDG2/</title>
<link rel="stylesheet" href="style1.css">
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<script src="script.js"></script>
</head>

<body>

<div id="header">AGDG2</div>
<div id="header_sub">Amateur Game Development General</div>

<div id = "login_form">
	<form method="post" action="login.php">
	<input type = "text" placeholder="Username" name="username">
	<br><input type = "password" placeholder="Password" name = "password">
	<br><input type="submit" name="submit" value="Login">
	</form>
</div>

<?php

if($error == true){
	echo "<div id='error'>Invalid username/password.</div>";
}


?>