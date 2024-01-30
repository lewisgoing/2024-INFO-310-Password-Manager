<?php

if (!isset($_COOKIE['authenticated']) || !$_COOKIE['authenticated']) {
    header('Location: login.php');
    exit();
}

?>