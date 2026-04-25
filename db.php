<?php
$host = "sql111.infinityfree.com";
$user = "if0_41698120";
$password = "paolo092205";
$database = "if0_41698120_fixhub";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>