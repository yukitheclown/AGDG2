<?php

require "post_options.php";
require "validate_login.php";
require "utils.php";

$post_id = mysqli_real_escape_string($sql_conn, $_GET["post_id"]);

if(isset($_POST["submit_length"]) & $user_status == "admin"){

	if($_POST["length"] == "permanent"){

		$sql = "SELECT * FROM `posts` WHERE id='" . $post_id . "'";

		$result = mysqli_query($sql_conn, $sql);

		$rows = mysqli_fetch_assoc($result);

		perma_ban_user($sql_conn, $rows["user_id"], $rows["ip"]);
		delete_post($sql_conn, $post_id);

	} else {

		$length = (int)$_POST["length"] * 60 * 60 * 12;

		if($length > 0){
	
			$sql = "SELECT * FROM `posts` WHERE id='" . $post_id . "'";

			$result = mysqli_query($sql_conn, $sql);

			$rows = mysqli_fetch_assoc($result);

			ban_user($sql_conn, $length,$rows["user_id"], $rows["ip"]);

			delete_post($sql_conn, $post_id);
		}
	}

	header("Location: /index.php");
	die();
}

if(isset($_POST["ban"]) && $user_status == "admin"){

	echo "
	<form method='post' action='/post_action.php?post_id=" . $post_id . "''>
	<input type = 'text' placeholder='days' name='length'>
	<input type = 'submit' name='submit_length' value='Submit'>
	</form>
	";
}

if(isset($_POST["report"])){

	report_post($sql_conn, $post_id, $user_id);

	header("Location: /index.php");
	die();
}

if(isset($_POST["delete"])){
	
	if($user_status == "admin"){

		delete_post($sql_conn, $post_id);

	} else {

		$sql = "SELECT user_id FROM `posts` WHERE user_id='" . $user_id . "' AND id='" . $post_id . "'";

		$result = mysqli_query($sql_conn, $sql);

		if(mysqli_num_rows($result)){
			delete_post($sql_conn, $post_id);
		}
	}

	header("Location: /index.php");
	die();
}



?>
