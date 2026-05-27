<?php

// $dbUsers = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.16.4.20)(PORT = 1521)))(CONNECT_DATA=(SID=sssw2)))";
// $connUsers = oci_connect($_SESSION['login'], $_SESSION['pass'], $dbUsers, 'AL32UTF8');

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "oslydb";
//$connect = mysqli_connect($host,$db_user,$db_password,$db_name);
$conn = new mysqli($host,$db_user,$db_password,$db_name);
$conn->set_charset("UTF8");
?>