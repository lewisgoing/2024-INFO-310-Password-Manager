# Week 5 | Sessions

Recall from lecture that web sessions are sequences of HTTP requests and responses associated with the same user. These sessions allow us to establish variables like privileges and local settings that will apply to every interaction the user has with the website. How we manage these sessions are extremely important when discussing cybersecurity as we need to ensure that authentication and authorization remain secure and locked down for each entity interacting with our service. In this lab, we will be looking at what insecure session management looks like, and how we can apply secure session management to our password manager to prevent attacks such as session hijacking!

## Part 0: Using our web app!

Our developers just pushed some new changes to the password manager! New users can now create an account, and vault owners can add people as viewers, editors, and owners of vaults.

To manager all the new accounts that have been and will be added, our devs created a "Global Admin" account that has full privileges on all vaults.

As a member of our security team, you may need to access this account to ensure proper security protocols are being followed throughout the app. To login as admin, use the following credentials:

```
Username: Admin
Password: Sup3rS3cr3t@dm1n
```

We also expanded to use of our web app to more employees within the HR, Development, and Executive departments. Below is a table that shows our employees names, usernames, department, and permission level for their department's vault:

| Employee First Name | Employee Last Name | Username    | Department | Permissions Level |
|---------------------|--------------------|-------------|------------|-------------------|
| User                | Name               | username    | N/A        | Viewer            |
| Super               | Admin              | admin       | N/A        | Owner             |
| Alice               | Smith              | alice_smith | Developers | Viewer            |
| Bob                 | Johnson            | bob_johnson | Developers | Viewer            |
| John                | Doe                | johndoe     | Developers | Editor            |
| Kevin               | Clark              | kevin_clark | Developers | Viewer            |
| Laura               | Jones              | laura_jones | Developers | Viewer            |
| Chris               | Miller             | chris_miller| HR         | Viewer            |
| Jane                | Doe                | janedoe     | HR         | Owner             |
| Emily               | Davis              | emily_davis | HR         | Editor            |
| Mike                | Brown              | mike_brown  | Executives | Viewer            |
| Sarah               | Wilson             | sarah_wilson| Executives | Editor            |

1. Let's make our vault for the security team! Create a new account on our password manager with your own information
    - Be sure to use a fake password, as your lab mates may be able to access this information!

2. Once your account is created, sign in to the web application and click "Vaults" in the navigation bar.

3. Click "Add Vault" and create the Security vault

4. Add at least three passwords to this vault. Make sure to include a .txt file with some "secret" information to at least one of the passwords!

## Part 1: Viewing our Cookies

Currently our web application utilizes cookies to manage who is logged in, what privileges the user has, as well as maintain the session. The web app does this by setting the `user` cookie when an individual logs in. Let's see if we can view this cookie:

1. Log into the web app using out new admin account.

2. Once logged in, open developer tools in your browser, and see if you are able to view the cookie set by your web app.

    ![View Cookies](/lab-writeup-imgs/view_cookies.png)

    We can see our webapp set the `authenticated` cookie and gave it the value of the account's username, in this case it is 'admin'.

    We also see that our web app has now set the `isSiteAdministrator`, with a value of 1. This is how our website will manage (part) of our authorization! If the value is set to one, our website knows that this user is an administrator, and has access to the Admin page!

## Part 2: Breaking authentication

As we learned last week, cookies are managed in the Application layer. We also learned that this layer is out of our control and is controlled by our client. We can actually change these cookies in our browser.

1. Log out of our web application.
    - Notice that our previous cookies are no longer set.

3. Are you able to Log in as Jane Doe, who is the owner of the HR vault, without knowing her password?
    - Hint you may need to alter your cookies!

## Part 3: Securing authentication

Now that we have seen how insecure authentication allows for an adversary to easily by pass our authentication mechanisms, lets work on making this more secure!

You may have noticed that there is a weird cookie that is always there when you visit the password manager called `PHPSESSID`. One great thing about PHP is that it has built in session management. While you may think that this is still insecure as it is still controlled in the client's browser, the fact that it is a unique, randomly generated value allows for a much higher level of security as an adversary would need to guess the complex and long value.

We can use `PHPSESSID` to manage the authentication and authorization for our web application!

1. Open login.php and look at lines 28-57. You should see the following code:

    ```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if($result->num_rows > 0) {
        
            $userFromDB = $result->fetch_assoc();

            setcookie('authenticated', $username, time() + 3600, '/');     

            if ($userFromDB['default_role_id'] == 1)
            {        
                setcookie('isSiteAdministrator', true, time() + 3600, '/');                
            }else{
                unset($_COOKIE['isSiteAdministrator']); 
                setcookie('isSiteAdministrator', '', -1, '/'); 
            }
            header("Location: index.php");
            exit();
        } else {
            $error_message = 'Invalid username or password.';
            $logger->warning("Login failed for username: $username"); // Log login failure
        }

        $conn->close();
    }
    ```
    This is how our user logs, below is a break down of how this works:

    - First, we get the username and password that the user is logging in with and assign it to the appropriate variables:

        ```
        $username = $_POST['username'];
        $password = $_POST['password'];
        ```

    - Next, we query our SQL database and find the row in the `users` table where the `username` and `password` column matches our user's input:

        ```
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);
        ```
    
    - If that query returns more than zero rows, we know that this user exists! We then set their `authenticated` cookie as assign it the value of their username:

        ```
        if($result->num_rows > 0) {
        
            $userFromDB = $result->fetch_assoc();

            setcookie('authenticated', $username, time() + 3600, '/');   
        ```
    
    - If the their `default_role_id` is 1, this indicates that they are an Admin, and we set the `isSiteAdministrator` cookie to the appropriate:

        ```
        if ($userFromDB['default_role_id'] == 1)
            {        
                setcookie('isSiteAdministrator', true, time() + 3600, '/');                
            }else{
                unset($_COOKIE['isSiteAdministrator']); 
                setcookie('isSiteAdministrator', '', -1, '/'); 
            }
        ```
    
    - Finally, we can then redirect them to `index.php`, which is our password manager's homepage:

        ```
        header("Location: index.php");
        exit();
        ```

2. Now, we can replace this with the more secure version using the `PHPSESSID`. Replace the line of code setting the `authenticated` cookie with the following line of code:

    ```
    $_SESSION['authenticated'] = $username;
    ```

    This will now create a new `authenticated` session utilizing the php session id!

3. Navigate to components > authenticate.php. You should see the following code:

    ```
    <?php

    session_start();

    if (!isset($_COOKIE['authenticated'])) {
        header('Location: /login.php');
        exit;
    }
    ?>
    ```

    We can just replace the `$_COOKIE` with `$_SESSION` to use our session id instead. Your updated code should look like the following:

    ```
    <?php

    session_start();

    if (!isset($_SESSION['authenticated'])) {
        header('Location: /login.php');
        exit;
    }
    ?>
    ```

## Part 4: Breaking Authorization

Now that we have locked down authentication, can users still access resources that they are not authorized to use? As we saw earlier, our web application partially handles authorization through the use of cookies.

1. Log in as your newly created account.

2. Is there a way you can access the Admin panel at `admin.php`?
    - Hint: You may need to set a new cookie :)

3. Once you are in the Admin panel, add your newly created account as an Owner of the Developer and Executive vaults!

## Part 5: Securing Authorization

Clearly these cookies aren't working. We were able to implement secure authentication by using `PHPSESSID`, let's take a similar approach to Authorization!

1. We need to also update `isSiteAdministrator` to use our session ID instead of a cookie. To do this, let's replace the code that sets this cookie with the following code:

    ```
    if ($userFromDB['default_role_id'] == 1)
        {        
            $_SESSION['isSiteAdministrator'] = 1;               
        }else{
            unset($_SESSION['isSiteAdministrator']); 
        }
    ```

    When users visit our webpage however, they land on `index.php`, which checks if their cookie is set correctly. We need to update this so that it checks the session instead. 

2. When users try to access the Admin panel, we run the `admin-authorization.php` component to verify that they are an administrator. It looks like the following:

    ```
    <?php

    if (!isset($_COOKIE['isSiteAdministrator']) || $_COOKIE['isSiteAdministrator'] != true) {
        header('Location: /index.php');
        exit;
    }

    ?>
    ```

    - We can update the if check so that it just checks if the session has the `isSiteAdministrator` key:

    ```
    <?php

    if (!isset($_SESSION['isSiteAdministrator']) || $_SESSION['isSiteAdministrator'] != true) {
        header('Location: /index.php');
        exit;
    }

    ?>
    ```

3. We also need to make sure we remove the `authenticated` key from the session when a user logs out. To do this we can update out `logout.php` script.

    - In the `logout.php` script, replace every instance of `_COOKIE` with `_SESSION`. Be sure to also delete the `setCookie` lines!
    
    - Yor logout.php script should look like the following:

    ```
    <?php
    unset($_SESSION['authenticated']); 

    unset($_SESSION['isSiteAdministrator']); 

    header('Location: /login.php');
    exit();

    ?>
    ```
## For Credit

Congratulations on implementing secure session management! Take a screenshot of you accessing the admin panel as your own personal user account, as well as your updated code from the `login.php` and `logout.php`.
