<?php

session_start();

include './components/loggly-logger.php';
include './components/database-connection.php';

$logger->debug('Login page called');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("ss", $username, $password);    

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();



    if($result->num_rows > 0) {
        session_regenerate_id(true);
        $userFromDB = $result->fetch_assoc();        
        $_SESSION['authenticated'] = $username;

        if ($userFromDB['default_role_id'] == 1)
        {        
            $_SESSION['isSiteAdministrator'] = true;

        }else if(isset($_SESSION['isSiteAdministrator'])){            
           unset($_SESSION['isSiteAdministrator']);            
        }
        header("Location: index.php");
        exit();
    } else {
        $error_message = 'Invalid username or password.';
        $logger->warning("Login failed for username: $username"); // Log login failure
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Login Page</title>
</head>
<body>
    <div class="container mt-5">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center">Login</h2>
            <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <div class="mt-3 text-center">
                <a href="./users/create_account.php" class="btn btn-secondary btn-block">Create an Account</a>
            </div>
        </div>
    </div>
</body>
</html>
