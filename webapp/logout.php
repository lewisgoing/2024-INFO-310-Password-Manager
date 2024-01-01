<?php

// Expire the authentication cookie
setcookie('authenticated', '', time() - 3600, '/');

// Redirect to the login page
header('Location: login.php');
exit();

?>