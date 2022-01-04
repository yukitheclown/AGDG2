<?php

// ini_set('display_errors', true);

require "validate_login.php";
require "utils.php";

if(isset($_POST["submit"]) && !empty($_POST["subject"]) && !empty($_POST["comment"])){

	$upload_id = NULL;

	if(validate_file($_FILES["file"])){

		$upload_id = upload_file($sql_conn, $_FILES["file"], $user_id, 0, 0);
	}

	if($upload_id !== NULL){

		$comment = mysqli_real_escape_string($sql_conn, $_POST["comment"]);
		$subject = mysqli_real_escape_string($sql_conn, $_POST["subject"]);

		$name = "Anonymous";

		if(!empty($_POST["name"])){
			$name = mysqli_real_escape_string($sql_conn, $_POST["name"]);
		}

		$ip = mysqli_real_escape_string($sql_conn,get_ip());

		$sql = "INSERT INTO `posts` (upload_id, comment, name, subject, ip, user_id, time) VALUES ('" . $upload_id .
			"', '" .  $comment . "', '" . $name . "', '" . $subject . "', '" . $ip . "', '" . $user_id . "', '" . time() . "')";

		mysqli_query($sql_conn, $sql);

		$_SESSION["last_post_time"] = time();
	}
}

header("Location: /index.php");

?>