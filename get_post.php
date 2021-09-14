<?php

require "validate_login.php";
require "utils.php";
require "print_posts.php";

if(isset($_REQUEST["post_id"])){
	
	$post_id = mysqli_real_escape_string($sql_conn, $_REQUEST["post_id"]);

	$sql = "SELECT * FROM `posts` WHERE id='" . $post_id . "'";

	$result = mysqli_query($sql_conn, $sql);

	$rows = mysqli_fetch_assoc($result);

	if(!$rows["reply_to_id"])
		// print_op_post($sql_conn, $rows["subject"], $rows["name"], parse_post($rows["comment"]), $rows["time"], $rows["id"], $rows["upload_id"]);
		print_op_post($sql_conn, $rows);
	else
		print_post($sql_conn, $rows);
		// print_post($sql_conn, $rows["status"], $rows["subject"], $rows["name"],
		// 	parse_post($rows["comment"]), $rows["time"], $rows["id"], $rows["upload_id"]);

}

?>