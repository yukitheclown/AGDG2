<?php

include_once("validate_login.php");
// include_once("utils.php");

// ini_set('display_errors', true);

?>

<html>

<head>
<title>/AGDG2/</title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>

<body>

<div id='profile_link'><a href='/profile.php'>Profile</a></div>

<div id = "create_thread_form">

	<div id="create_thread_container_header">
		<div id="create_thread_container_header_text">
			Create Thread
			</div>
	</div>

	<form method="post" action="create_thread.php" enctype="multipart/form-data">

	<input type = "text" placeholder="Anonymous" name="name">
	<br><input type = "text" placeholder="Subject (Required)" name = "subject">
	<br><textarea name="comment" placeholder="Comment" rows="10" cols="50"></textarea>
	<br><input type = "file" name="file">
	<input style="float:right" type="submit" name="submit" value="Create thread">

	</form>
</div>

<?php

require "print_posts.php";
require "utils.php";

// if(empty($_GET["page_id"]) == false){

	// $page_id = mysqli_real_escape_string($sql_conn, $_GET["page_id"]);

	$sql = "SELECT * FROM `posts` WHERE reply_to_id IS NULL ORDER BY id DESC LIMIT 20";

	$result = mysqli_query($sql_conn, $sql);

	for($i = 0; $i < mysqli_num_rows($result); $i++){
	
		$rows = mysqli_fetch_assoc($result);

		print_post_home($sql_conn, $rows);

	}

// } else {

// 	die("404.");
// }


?>

</body>

</html>
