<?php
$host = "localhost";
$username = "root";
$password = "";; // Update if required
$dbname = "quickhire"; // Replace with your DB name

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
