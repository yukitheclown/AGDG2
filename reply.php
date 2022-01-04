<?php

require "validate_login.php";
require "utils.php";
ini_set('display_errors', true);

function reply($sql_conn, $user_id, $thread_id){

	if(!isset($_POST["submit"]) || !check_can_post($sql_conn))
		return;

	$valid_file = validate_file($_FILES["file"]);

	if(empty($_POST["comment"]) && !$valid_file)
		return;

	$upload_id = NULL;

	$spoiler = intval(isset($_POST["spoiler"]));
	$lewd = intval(isset($_POST["lewd"]));

	if($valid_file){
		$upload_id = upload_file($sql_conn, $_FILES["file"], $user_id, $spoiler, $lewd);
	}

	$comment = $_POST["comment"];
	$name = "Anonymous";
	$subject = "";

	if(!empty($_POST["name"])){
		$name = $_POST["name"];
	}

	if(!empty($_POST["subject"])){
		$subject = $_POST["subject"];
	}

	$comment = mysqli_real_escape_string($sql_conn, $comment);
	$subject = mysqli_real_escape_string($sql_conn, $subject);
	$name = mysqli_real_escape_string($sql_conn, $name);
	$ip = mysqli_real_escape_string($sql_conn, get_ip());

	$sql = "";

	if($upload_id !== NULL){

		$sql = "INSERT INTO `posts` (upload_id, comment, name, subject, reply_to_id, ip, user_id, time) VALUES ('" . $upload_id . "', '"
			. $comment . "', '" . $name . "', '" . $subject . "', '" . $thread_id . "', '" . $ip . "', '" . $user_id . "', '" . time() ."')";

	} else {

		$sql = "INSERT INTO `posts` (comment, name, subject, reply_to_id, ip, user_id, time) VALUES ('" . $comment . "', '" .
			$name . "', '" . $subject . "', '" . $thread_id . "', '" . $ip . "', '" . $user_id . "', '" . time() ."')";
	}

	mysqli_query($sql_conn, $sql);

	$sql = "SELECT id FROM `posts` ORDER BY id DESC LIMIT 1";

	$result = mysqli_query($sql_conn, $sql);

	$post_id = mysqli_fetch_assoc($result)["id"];

	insert_quotes($sql_conn, $post_id, $_POST["comment"]);

	$_SESSION["last_post_time"] = time();
}

if(empty($_GET["thread_id"]) == false){

	$thread_id = mysqli_real_escape_string($sql_conn, $_GET["thread_id"]);

	reply($sql_conn, $user_id, $thread_id);

	header("Location: /thread.php?thread_id=" . $thread_id);

} else {

	header("Location: /index.php");
}


?>