<?php

$hostname = getenv('MYSQL_HOST'); 
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');
$database = getenv('MYSQL_DATABASE');

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    $logger->error("Connection failed: " . $conn->connect_error);
    die('A fatal error occurred and has been logged.');    
}

?>