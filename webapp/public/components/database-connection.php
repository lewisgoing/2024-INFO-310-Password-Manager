<?php

$hostname = 'mysql-database';
$username = 'user';
$password = 'supersecretpw';
$database = 'password_manager';

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    $logger->error("Connection failed: " . $conn->connect_error);
    die('A fatal error occurred and has been logged.');    
}

?>