<?php
$dbServerName = "80.241.220.20";
$dbUsername = "kiart_pickoh";
$dbPassword = "pickoh@2017";
$dbName = "kiart_pickoh_com";

// create connection
$conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>