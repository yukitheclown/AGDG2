<?php

require "sql.php";

session_start();

$session_username = "";
$session_password = "";

if(empty($_SESSION["username"]) || empty($_SESSION["password"])){

	header("Location: /login.php");
	die();
}

$session_username = mysqli_real_escape_string($sql_conn, $_SESSION["username"]);
$session_password = mysqli_real_escape_string($sql_conn, $_SESSION["password"]);

$sql = "SELECT * FROM `users` WHERE username='". $session_username . "' AND password='" . $session_password . "'";

$result = mysqli_query($sql_conn, $sql);

$user_id = 0;
$user_status = "nodev";

if($result && mysqli_num_rows($result) != 0){

	$rows = mysqli_fetch_assoc($result);

	$user_id = mysqli_real_escape_string($sql_conn, $rows["id"]);
	$user_status = mysqli_real_escape_string($sql_conn, $rows["status"]);

	$sql = "SELECT * FROM `bans` WHERE ip='". $_SERVER['REMOTE_ADDR'] . "'";
	$result = mysqli_query($sql_conn, $sql);

	$error = false;

	if(mysqli_num_rows($result) == 0){

		$sql = "SELECT * FROM `bans` WHERE user_id='". $user_id . "'";

		$result = mysqli_query($sql_conn, $sql);

		if(mysqli_num_rows($result) != 0){

			$rows = mysqli_fetch_assoc($result);		

			if(!$rows["perm"] && time() > $rows["unban_time"]){

				$sql = "DELETE FROM `bans` WHERE user_id='". $user_id . "'";
		
				mysqli_query($sql_conn, $sql);

			} else {

				$error = true;
			}
		}
		
	} else {
		$sql = "SELECT * FROM `bans` WHERE user_id='". $user_id . "'";
		$result = mysqli_query($sql_conn, $sql);
		if(mysqli_num_rows($result) != 0){
			$rows = mysqli_fetch_assoc($result);		
		}		
		$error = true;
	}


	if($error){


		unset($_SESSION["username"]);
		unset($_SESSION["password"]);

		if($rows["perm"])
			header("Location: /banned.php?p=1");
		else
			header("Location: /banned.php?t=" . $rows["unban_time"]);

		die();
	}

} else {

	header("Location: /login.php");
	die();
}


?>