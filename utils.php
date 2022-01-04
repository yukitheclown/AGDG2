<?php

// date_default_timezone_get('UTC');

function create_thumbnail($img, $dest_path, $target_wh, $file_type){

	$width = imagesx($img);
	$height = imagesy($img);

	$new_height = 0;
	$new_width = 0;

	if($width > $height){
		$new_width = $target_wh;
		$new_height = floor($height * ($new_width / $width));
	} else {
		$new_height = $target_wh;
		$new_width = floor($width * ($new_height / $height));
	}

	$tmp_image = imagecreatetruecolor($new_width, $new_height);

	imagecopyresized($tmp_image, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	if($file_type == "gif"){
		imagegif($tmp_image, $dest_path);
	} else if($file_type == "jpeg" || $file_type == "jpg"){
		imagejpeg($tmp_image, $dest_path);
	} else if($file_type == "png"){
		imagepng($tmp_image, $dest_path);
	}

	imagedestroy($tmp_image);

	return true;
}

function create_thumbnails($image_path, $file_type, $target_small_thumbnail_file, $target_large_thumbnail_file){
	$img = NULL;

	if($file_type == "gif"){
		$img = @imagecreatefromgif($image_path);
	} else if($file_type == "jpeg" || $file_type == "jpg"){
		$img = @imagecreatefromjpeg($image_path);
	} else if($file_type == "png"){
		$img = @imagecreatefrompng($image_path);
	}

	if(!$img) return false;

	create_thumbnail($img, $target_small_thumbnail_file, 125, $file_type);
	create_thumbnail($img, $target_large_thumbnail_file, 250, $file_type);

	imagedestroy($img);
}

function upload_file($sql_conn, $file, $user_id, $spoiler, $lewd){

	$target_dir = "uploads/";
	$target_thumbnail_dir = "thumbnails/";
	$target_file_id = 1;

	$filename = mysqli_real_escape_string($sql_conn,$file["name"]);
	$file_type = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);

	$sql = "INSERT INTO `uploads` (filename, user_id, ext, spoiler, lewd) VALUES ('" . substr($filename, 0, 128) . 
		"', '" .  $user_id . "', '" . mysqli_real_escape_string($sql_conn, $file_type) . "', '" . $spoiler . "', '" . $lewd ."')";

	if(!mysqli_query($sql_conn, $sql)){
		die(mysqli_error($sql_conn));

	}

	$sql = "SELECT id FROM `uploads` ORDER BY id DESC LIMIT 1";

	$result = mysqli_query($sql_conn, $sql);

	if($result && mysqli_num_rows($result) > 0){

		$target_file_id = mysqli_fetch_assoc($result)["id"];
	}

	$target_file = $target_dir . $target_file_id . "." . $file_type;

	if(move_uploaded_file($file["tmp_name"], $target_file)){

		if($file_type != "webm"){
			$target_small_thumbnail_file = $target_thumbnail_dir . $target_file_id . "s." . $file_type;
			$target_large_thumbnail_file = $target_thumbnail_dir . $target_file_id . "l." . $file_type;
			create_thumbnails($target_file, $file_type, $target_small_thumbnail_file, $target_large_thumbnail_file);
		}

		return $target_file_id;
	}

	$sql = "DELETE FROM `uploads` WHERE id='" . $target_file_id . "'";

	mysqli_query($sql_conn, $sql);

	return NULL;
}

function insert_quotes($sql_conn, $id, $comment){

	$comment = trim($comment);
	$comment = stripslashes($comment);
	$comment = htmlspecialchars($comment);

	$regex = "@(&gt;){2,}(.*?)($|\s)@im";

	preg_match_all($regex, $comment, $out,  PREG_SET_ORDER);

	foreach($out as $value){

		$quote = mysqli_real_escape_string($sql_conn, $value[2]);

		$sql = "INSERT INTO `quotes` (post_id, quoted_post_id) VALUES ('" . $id . "', '" . $quote ."')";

		mysqli_query($sql_conn, $sql);
	}
}

function validate_file($file){
	
	if($file["size"] > 10*pow(1024, 2)){
		return false;
	}

	$file_type = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);

	if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif" && $file_type != "webm"){
		return false;
	}

	return true;
}

function get_ip() {
    return $_SERVER['REMOTE_ADDR'];
}

function check_can_post($sql_conn){

	$sql = "SELECT time FROM `posts` WHERE ip='" . get_ip() . "' ORDER BY time DESC LIMIT 1";

	$result = mysqli_query($sql_conn, $sql);

	if($result && mysqli_num_rows($result) > 0){

		if(time() - mysqli_fetch_assoc($result)["time"] > 60){

			return true;
		}
	
		return false;
	}

	return true;
}

function parse_post_no_links($post){

	$post = trim($post);
	$post = stripslashes($post);
	$post = htmlspecialchars($post);

	$regex[] = "@(&gt;){2,}(.*?)($|\s)@im";
	$replacements[] = "<a href='#' style='display:inline' id='quote'>>>$2</a>";

	// $regex[] = "@(&gt;){1,}([^\r]*?)($|\r)@im";
	$regex[] = "/(&gt;){1,}(.*?).$/mi";
	$replacements[] = "<span id='greentext'>>$2</span>";

	$regex[] = "@\[spoiler\](.*)\[/spoiler\]@im";
	$replacements[] = "<span id='spoiler'>$1</span>";

	$output = nl2br(preg_replace($regex, $replacements, $post));

	return $output;
}

function parse_post($post){

	$post = trim($post);
	$post = stripslashes($post);
	$post = htmlspecialchars($post);

	$regex[] = "@(&gt;){2,}(.*?)($|\s)@im";
	$replacements[] = "<div style='display:inline'><div style='display:none'></div><div style='display:inline;cursor:pointer;' onclick=\"click_post_quote(event, '$2')\" id='quote'>>>$2</div></div>";

	// $regex[] = "@(&gt;){1,}([^\r]*?)($|\r)@im";
	$regex[] = "/(&gt;){1,}(.*?).$/mi";
	$replacements[] = "<span id='greentext'>>$2</span>";

	$regex[] = "@\[spoiler\](.*)\[/spoiler\]@im";
	$replacements[] = "<span id='spoiler'>$1</span>";

	$output = nl2br(preg_replace($regex, $replacements, $post));

	return $output;
}

?>