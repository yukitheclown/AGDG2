<?php

function perma_ban_user($sql_conn, $user_id, $ip){

	$sql = "DELETE FROM `users` WHERE id='" . $user_id . "'";

	mysqli_query($sql_conn, $sql);

	$sql = "DELETE FROM `invite_keys` WHERE user_id='" . $user_id . "'";

	mysqli_query($sql_conn, $sql);

	$sql = "SELECT * FROM `bans` WHERE user_id='" . $user_id . "'";

	$result = mysqli_query($sql_conn, $sql);

	$sql = "";

	if(mysqli_num_rows($result) > 0)
		$sql = "UPDATE `bans` SET ip='" . $ip . "', unban_time='" . $time . "',perm='1' WHERE user_id='" . $user_id . "'";
	else
		$sql = "INSERT INTO `bans` (user_id, unban_time, perm, ip) VALUES ('" . $user_id . "','" . $time . "','1', '" . $ip . "')";

	mysqli_query($sql_conn, $sql);
}

function ban_user($sql_conn, $length, $user_id, $ip){
 
	$time = time() + $length;

	$sql = "SELECT * FROM `bans` WHERE user_id='" . $user_id . "'";

	$result = mysqli_query($sql_conn, $sql);

	$sql = "";

	if(mysqli_num_rows($result) > 0)
		$sql = "UPDATE `bans` SET ip='" . $ip . "', unban_time='" . $time . "',perm='0' WHERE user_id='" . $user_id . "'";
	else
		$sql = "INSERT INTO `bans` (user_id, unban_time, perm, ip) VALUES ('" . $user_id . "','" . $time . "','0', '" . $ip . "')";

	mysqli_query($sql_conn, $sql);
}

function delete_image($upload_id, $ext){

	$target_dir = "uploads/";
	$target_thumbnail_dir = "thumbnails/";

	$target_file = $target_dir . $upload_id . "." . $ext;
	$target_small_thumbnail_file = $target_thumbnail_dir . $upload_id . "s." . $ext;
	$target_large_thumbnail_file = $target_thumbnail_dir . $upload_id . "l." . $ext;

	if(file_exists($target_file))
		unlink($target_file);

	if(file_exists($target_small_thumbnail_file))
		unlink($target_small_thumbnail_file);

	if(file_exists($target_large_thumbnail_file))
		unlink($target_large_thumbnail_file);
}

function delete_post($sql_conn, $post_id){

	$sql = "SELECT * FROM `posts` WHERE id='" . $post_id . "'";

	$result = mysqli_query($sql_conn, $sql);

	$rows = mysqli_fetch_assoc($result);

	$upload_id = $rows["upload_id"];

	if($upload_id){

		$sql = "SELECT * FROM `uploads` WHERE id='" . $upload_id . "'";

		$result = mysqli_query($sql_conn, $sql);

		$upload_rows = mysqli_fetch_assoc($result);

		delete_image($upload_id, $upload_rows["ext"]);

		$sql = "DELETE FROM `uploads` WHERE id='" . $upload_id . "'";

		mysqli_query($sql_conn, $sql);

		$sql = "SELECT * FROM `posts` WHERE reply_to_id='" . $post_id . "'";

		$result = mysqli_query($sql_conn, $sql);

		while($row = mysqli_fetch_assoc($result)){
	
			$upload_id = $row["upload_id"];

			$sql = "SELECT * FROM `uploads` WHERE id='" . $upload_id . "'";

			$result = mysqli_query($sql_conn, $sql);

			$upload_rows = mysqli_fetch_assoc($result);

			delete_image($upload_id, $upload_rows["ext"]);

			$sql = "DELETE FROM `uploads` WHERE id='" . $upload_id . "'";
		}
	}

	$sql = "DELETE FROM `uploads` WHERE id='" . $upload_id . "'";

	mysqli_query($sql_conn, $sql);

	$sql = "DELETE FROM `quotes` WHERE post_id='" . $post_id . "'";

	mysqli_query($sql_conn, $sql);

	$sql = "DELETE FROM `reports` WHERE post_id='" . $post_id . "'";

	mysqli_query($sql_conn, $sql);

	$sql = "DELETE FROM `posts` WHERE reply_to_id='" . $post_id . "'";

	mysqli_query($sql_conn, $sql);

	$sql = "DELETE FROM `posts` WHERE id='" . $post_id . "'";

	mysqli_query($sql_conn, $sql);

}

function report_post($sql_conn, $post_id, $user_id){

	$sql = "SELECT `id` FROM `reports` WHERE post_id='" . $post_id . "'";

	$result = mysqli_query($sql_conn, $sql);

	if($result && mysqli_num_rows($result) > 0) return;

	$sql = "INSERT INTO `reports` (post_id, reporter_id) VALUES ('" . $post_id . "','". $user_id . "')";

	mysqli_query($sql_conn, $sql);
}


?>