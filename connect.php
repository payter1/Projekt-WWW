<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "donkeysDB";
//$connect = mysqli_connect($host,$db_user,$db_password,$db_name);
$conn = new mysqli($host,$db_user,$db_password,$db_name);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}
$conn->set_charset("UTF8");

?>