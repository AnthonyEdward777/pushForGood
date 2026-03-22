<?php
$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "your_database_name"; // <--- CHANGE THIS

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>