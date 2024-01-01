<?php

// Check if the user is authenticated
if (!isset($_COOKIE['authenticated']) || !$_COOKIE['authenticated']) {
    // Redirect to the login page
    header('Location: login.php');
    exit();
}

?>