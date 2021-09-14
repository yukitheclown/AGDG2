<?php

require "validate_login.php";
require "utils.php";
require "print_posts.php";

if(empty($_GET["thread_id"]) == true){
	header('Location: ', "404.html");
}

?>

<html>
<head>

<?php

$thread_id = mysqli_real_escape_string($sql_conn, $_GET["thread_id"]);

$sql = "SELECT * FROM `posts` WHERE id='" . $thread_id . "'";

$result = mysqli_query($sql_conn, $sql);

$rows = mysqli_fetch_assoc($result);

echo "<title>/AGDG2/ - " . $rows["subject"] . "</title>";

?>

<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>

<body>

<?php
 	echo "<div id='expand_options'>
		<form method='post' action=''>";

	if($user_status != "admin" && $user_status != "mod"){
		echo "<input type='submit' name='delete' value='Delete'>
		<input type='submit' name='report' value='Report'>";
	} else {
		echo "<input type='submit' name='delete' value='Delete'>
		<input type='submit' name='ban' value='Ban'>";
	}
	echo "</form></div>";
?>

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
		<div style='float:right; display: table'>
			<input type="checkbox" name="spoiler" value="1">Spoiler?
			<input type="checkbox" name="lewd" value="1">Lewd?

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
		</div>
	</form>
</div>

<?php


print_op_post($sql_conn, $rows);

$sql = "SELECT * FROM `posts` where reply_to_id='". $thread_id . "'";

$result = mysqli_query($sql_conn, $sql);

for($i = 0; $i < mysqli_num_rows($result); $i++){

	$rows = mysqli_fetch_assoc($result);

	print_post($sql_conn, $rows);
}

?>
</body>

</html>