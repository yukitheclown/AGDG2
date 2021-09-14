<?php

function print_post_upload($sql_conn, $upload_id, $large_thumbnail){

	$sql = "SELECT * FROM `uploads` WHERE id='" . $upload_id . "'";

	$upload_result = mysqli_query($sql_conn, $sql);

	$upload_rows = mysqli_fetch_assoc($upload_result);

	$filename = "uploads/" . $upload_rows["id"] . "." . $upload_rows["ext"];
	$thumbnail_file_name = "thumbnails/" . $upload_rows["id"] . "s." . $upload_rows["ext"];

	if($large_thumbnail)
		$thumbnail_file_name = "thumbnails/" . $upload_rows["id"] . "l." . $upload_rows["ext"];

	if($upload_rows["spoiler"])
		$thumbnail_file_name = "thumbnails/spoiler.png";

	if($upload_rows["lewd"])
		$thumbnail_file_name = "thumbnails/lewd_spoiler.png";

	// if($upload_rows["ext"] == "webm")
	// 	if($large_thumbnail)
	// 		echo "<video controls  width='200' loop='1' height='200' ><source src='" . $filename . "' type='video/webm'></video>";
	// 	else
	// 		echo "<video controls  width='125' loop='1' height='125' ><source src='" . $filename . "' type='video/webm'></video>";
	// else
	// 	echo "<a id='post_img' href='" . $filename . "'><img src ='" . $thumbnail_file_name . "'></img></a>";

	if($upload_rows["ext"] == "webm")
		if($large_thumbnail)
			echo "<video controls onclick='expand_video(event, 200)' loop='1' muted = '1'
			style=\"max-width: 125px;\"><source src='" . $filename . "' type='video/webm'></video>";
		else
			echo "<video controls  onclick='expand_video(event, 125)' loop='1' muted = '1'
		style=\"max-width: 125px;\"><source src='" . $filename . "' type='video/webm'></video>";
	else
		echo "<div id='post_img' href='" . $filename . "'><img onclick=\"expand_image(event, '" . $filename . "','" .  $thumbnail_file_name . "');\" src ='" . $thumbnail_file_name . "'></img></div>";

		// echo "<a id='post_img' href='" . $filename . "'><img src ='" . $thumbnail_file_name . "'></img></a>";
}

function print_post_info($sql_conn, $rows){

	$subject = $rows["subject"];
	$name = $rows["name"];
	$time = $rows["time"];
	$id = $rows["id"];

	$sql = "SELECT * FROM `users` where id='". $rows["user_id"] . "'";

	$result = mysqli_query($sql_conn, $sql);

	$row = mysqli_fetch_assoc($result);

	$status = $row["status"];

	$time = date("m/d/y(D)H:i:s", $time);

	echo "<div id=\"info\">
			<span id = \"subject\">" . $subject . "</span>";

	if($status == "admin" && $name != "Anonymous")
		echo "<span id = \"admin_name\"> " . $name . " </span>";
	else
		echo "<span id = \"name\"> " . $name . " </span>";

	echo "<span id = \"time\">" . $time . "</span>
			<span id = \"post_num\">No." . $id . "</span>
			<a onmouseup=\"reply('" . $id . "')\"". $id ."\">Reply</a>";

	$sql = "SELECT * FROM `quotes` where quoted_post_id='". $id . "'";

	$result = mysqli_query($sql_conn, $sql);

	while($row = mysqli_fetch_assoc($result)){

		echo "<a id='backlink' onmouseover=\"backlink_mouseover(event, '". $row["post_id"] . "');\"
			onmouseout=\"backlink_mouseout(event, '". $row["post_id"] . "');\"  href='#p" . 
			$row["post_id"] . "'>>>" . $row["post_id"] . "</a>";
	}

	echo "<div id='expand_arrow' onclick=\"expand_post_options(event, '" . $id . "')\">&#9654;</div>";

	echo "</div>";
}

function print_op_post($sql_conn, $rows){

	$id = $rows["id"];
	$upload_id = $rows["upload_id"];
	$comment = parse_post($rows["comment"]);

	echo "<div id='p" . $id . "'>
	<div class=\"thread_container\">
		<div class=\"thread\">";

	print_post_info($sql_conn, $rows);

	echo "<div id='contents'>";

	if($upload_id)
		print_post_upload($sql_conn, $upload_id, true);

	echo "<div id = \"comment\">" . $comment . "</div>";

	echo "</div></div></div></div>";
}

function print_post($sql_conn, $rows){

	$id = $rows["id"];
	$upload_id = $rows["upload_id"];
	$comment = parse_post($rows["comment"]);

	echo "<div id='p" . $id . "'>
	<div class=\"thread_container\">
		<div class=\"thread\">";

	print_post_info($sql_conn, $rows);

	echo "<div id='contents'>";

	if($upload_id)
		print_post_upload($sql_conn, $upload_id, false);

	echo "<div id = \"comment\">" . $comment . "</div>";

	echo "</div></div></div></div>";
}

function print_post_home($sql_conn, $rows){

	$id = $rows["id"];
	$upload_id = $rows["upload_id"];
	$comment = parse_post($rows["comment"]);
	$subject = $rows["subject"];
	$time = $rows["time"];

	$time = date("m/d/y(D)H:i:s", $time);

	echo "<div class='home_thread'>";

	echo "<div id = \"subject\"><a href='/thread.php?thread_id=". $id ."'>" . $subject . "</a></div>";

	echo "<div id='background'>";

	if($upload_id){

		$sql = "SELECT * FROM `uploads` WHERE id='" . $upload_id . "'";

		$upload_result = mysqli_query($sql_conn, $sql);

		$upload_rows = mysqli_fetch_assoc($upload_result);

		$filename = "uploads/" . $upload_rows["id"] . "." . $upload_rows["ext"];
		$thumbnail_file_name = "thumbnails/" . $upload_rows["id"] . "l." . $upload_rows["ext"];

		if($upload_rows["spoiler"])
			$thumbnail_file_name = "thumbnails/spoiler.png";

		if($upload_rows["lewd"])
			$thumbnail_file_name = "thumbnails/lewd_spoiler.png";


		if($upload_rows["ext"] == "webm"){

			echo "<video controls width='200' loop='1' height='200' muted = '1'><source src='" . $filename . "' type='video/webm'></video>";

		} else {

			// echo "<img onmouseup=\"expand_image(event, '" . $filename ."','" .
			// 	 $thumbnail_file_name . "')\" src ='" . $thumbnail_file_name . "'></img>";


			echo "<a href='/thread.php?thread_id=". $id ."'><img src ='" . $thumbnail_file_name . "'></img></a>";
		}
	}

	echo "</div>";
	echo "<div id = \"comment\">" . $comment . "</div>";
	echo "</div>";

}

?>