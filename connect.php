<?php
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "donkeysDB";
//$connect = mysqli_connect($host,$db_user,$db_password,$db_name);
$conn = new mysqli($host,$db_user,$db_password,$db_name);
$conn->set_charset("UTF8");
?>