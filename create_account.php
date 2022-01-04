<?php

ini_set('display_errors', true);

require "sql.php";

$inviter = "";
$inviter_id = "";

if(empty($_GET["invite_key"]) == false){

	$inv_key = mysqli_real_escape_string($sql_conn, $_GET["invite_key"]);

	$sql = "SELECT user_id FROM `invite_keys` where inv_key='". $inv_key . "'";

	$result = mysqli_query($sql_conn, $sql);

	if(mysqli_num_rows($result) == 0){
	
		header("Location: /login.php");
		die();
	
	} else {

		$inviter_id = mysqli_fetch_assoc($result)["user_id"];
	
		$sql = "SELECT username FROM `users` where id='". $inviter_id . "'";

		$result = mysqli_query($sql_conn, $sql);

		if($result && mysqli_num_rows($result) != 0){
		
			$inviter = mysqli_fetch_assoc($result)["username"];
		}
	}

} else {

	header("Location: /login.php");
	die();
}


$output = "<html>
		<head>
		<link rel='stylesheet' href='style1.css'>
		<script src='script.js'></script>
		<title>Create Account /AGDG2/</title>
		<link rel='shortcut icon' type='image/x-icon' href='/favicon.ico'>
		</head>

		<body>
		<div id='header'>AGDG2</div>
		<div id='header_sub'>Amateur Game Development General</div>
		<div id='create_account_inviter'>
			<div id='prefix'>Invited by</div><div id='name'>" . $inviter . "</div></div>";

if(isset($_POST["submit"])){

	$username = $password = $password_repeat = "";
		
	if(!empty($_POST["username"])){
		$username = $_POST["username"];
	}
	if(!empty($_POST["password"])){
		$password = $_POST["password"];
	}

	if(!empty($_POST["repeat_password"])){
		$password_repeat = $_POST["repeat_password"];
	}

	$error = false;

	if(strlen($username) < 5 || strlen($username) > 16 || !preg_match("/^[a-z0-9A-Z ]*$/", $username)){
		$output .= "<div id='error'>Username must be between 5 and 16 characters long, and only use characters a-z, 0-9, and A-Z</div>";
		$error = true;
	}

	if(strlen($password) < 5){
		$output .= "<div id='error'>Password must be at least 5 characters</div>";
		$error = true;
	}

	if($password != $password_repeat){
		$output .= "<div id='error'>Passwords don't match.</div>";
		$error = true;
	}

	if($error == false){

		$username = mysqli_real_escape_string($sql_conn,$username);
		$password = mysqli_real_escape_string($sql_conn,sha1($password));

		$sql = "SELECT * FROM `users` where username='". $username . "'";

		$result = mysqli_query($sql_conn, $sql);

		if($result && mysqli_num_rows($result) != 0){
			$output .= "<div id='error'>Username taken.</div>";
			$error = true;
		}

		if($error == false){

			$_SESSION["username"] = $username;
			$_SESSION["password"] = $password;

			$sql = "INSERT INTO `users` (username, password, inviter_id, status) VALUES ('" . $username . "', '" .  $password ."', '" . $inviter_id. "', 'nodev')";

			if(!mysqli_query($sql_conn, $sql)){
				echo mysqli_error($sql_conn);
			}

			$sql = "SELECT id FROM `users` WHERE username='" . $username . "'";

			$result = mysqli_query($sql_conn, $sql);

			if($result && mysqli_num_rows($result) > 0){

				$id = mysqli_fetch_assoc($result)["id"];

				for($i = 0; $i < 3; $i++){

				    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				    $len = strlen($chars);
				    $key = '';

				    for ($k = 0; $k < 40; $k++) {
				        $key .= $chars[rand(0, $len - 1)];
				    }

					$sql = "INSERT INTO `invite_keys` (user_id, inv_key) VALUES ('" . $id . "', '" .  $key . "')";
						
					mysqli_query($sql_conn, $sql);
				}
			
				$inv_key = mysqli_real_escape_string($sql_conn, $_GET["invite_key"]);

				$sql = "DELETE FROM `invite_keys` WHERE inv_key='" . $inv_key . "'";

				mysqli_query($sql_conn, $sql);

				header("Location: /login.php");
				die();
			}
		}
	}
}

	echo $output;

?>

<div id="create_account_form">

<?php

echo "<form action = 'create_account.php?invite_key=" . $_GET["invite_key"] . "' method='post'>"

 ?>

<input type = "text" name = "username" placeholder="Username"><br>
<input type = "password" name = "password" placeholder="Password"><br>
<input type = "password" name = "repeat_password" placeholder="Repeat Password"><br>
<input type = "submit" name="submit" value="Create Account">
</form>

</div>

</body>
</html>