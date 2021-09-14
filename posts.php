<?php

require "validate_login.php";
require "utils.php";
require "print_posts.php";

?>

<html>
<head>
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>

<html>

<body>

<div id="hover_post" style="display: none"></div>

<div id="reply_container" style="display: none">
	
	<div id="reply_container_header">
		<div id="reply_container_header_text">
			<div style="display:inline; float:left;"> Reply</div><div id="reply_container_close">X</div>
			</div>
	</div>

		<?php
			echo "<form method='post' action='reply.php?thread_id=" . $_GET["thread_id"] . "' enctype='multipart/form-data'>";
		?>

		<input type = "text" placeholder="Anonymous" name="name">
		<br><input type = "text" placeholder="Subject" name = "subject">
		<br><textarea id="reply_container_body" name="comment" cols="50" rows = "10"></textarea>
		<br><input type = "file" name="file">
		<input type="checkbox" name="spoiler" value="1">Spoiler?

		<?php

			$displayStandard = true;

			if(isset($_SESSION["last_post_time"]) && !empty($_SESSION["last_post_time"])){
				$timeLeft = ($_SESSION["last_post_time"] + 60) - time();
				if($timeLeft > 0){
					echo "<input id='reply_container_post_button' type='submit' name='submit' value='" . $timeLeft . "'>";
					echo "<script>window.setTimeout(post_button_timer, 1000);</script>";
					$displayStandard = false;
				}
			}

			if($displayStandard){
				echo "<input id='reply_container_post_button' type='submit' name='submit' value='Post'>";
			}
		?>
	</form>
</div>

<?php

$sql = "SELECT * FROM `posts` WHERE user_id='" . $user_id . "'";

$result = mysqli_query($sql_conn, $sql);

for($i = 0; $i < mysqli_num_rows($result); $i++){

	$rows = mysqli_fetch_assoc($result);

	$url = "thread.php?thread_id=" . $rows["reply_to_id"] ."#p" . $rows["id"];

	echo "<a style='color: #34345c; font-size:10pt;' href ='" . $url . "'>" . $url . "</a>";
	
	print_post_no_links($sql_conn, $rows["subject"], $rows["name"], parse_post_no_links($rows["comment"]),
		$rows["time"], $rows["id"], $rows["upload_id"], false);
}

?>
</body>

</html>