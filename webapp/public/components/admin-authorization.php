<?php

if (!isset($_SESSION['isSiteAdministrator']) || $_SESSION['isSiteAdministrator'] != true) {
    header('Location: /index.php');
    exit;
}

?>