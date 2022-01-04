<?php


$sql_servername = "127.0.0.1";
$sql_username = "root";
$sql_password = "";
$sql_database = "agdg2";

$sql_conn = mysqli_connect($sql_servername, $sql_username, $sql_password, $sql_database);

if($sql_conn == false){
	die("Connection error: " . mysqli_connect_error());
}

?>