<!-- Uses SESSIONS to log a user in and out of the website. --> 
<?php  
 
    // Start a session to keep track of when a user is logged in.
    SESSION_START();

    // If the user has requested to log in 
    if(isset($_POST['login'])){
        // Require the connection to the database 
        require('connect.php');

        // Retrieve the row in MySQL with the entered username.
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query = "SELECT * FROM logins WHERE username = :username LIMIT 1";     
        $statement = $db->prepare($query); 
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();

        // Compare the entered password to the stored password for that user. 
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // If the password does not match the record, display an error message. 
        if(!$row || !password_verify($password, $row['password'])){
            echo "Error: The entered data does not match the records. Try again.";

        // If the login was successful, store a session variable to use in IF statements 
        } else {
            $_SESSION['username'] = $row['username'];
        }

    // If the user has requested to log out
    } elseif (isset($_POST['logout'])){

        // Unset all session variables and end the session.
        $_SESSION = array();
        session_destroy();

        // Redirect the user back to the login page upon log out
        header("Location: login.php");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="home.css" />
</head>
<body>
    <?php if(empty($_SESSION['username'])): ?>
        <header>
            <h1><a href="index.php">VROAR Inc.</a></h1>
        </header>

        <form method="POST"> 
            <h3>Login</h3>
            <label for="username">Username: </label>
            <input type="text" id="username" name="username">
            <label for="password">Password: </label>
            <input type="password" id="password" name="password">

            <input type="submit" class="submit" name="login">
        </form>
    <?php else: ?>
        <?php require('locked.php'); ?>
    <?php endif ?>
</body>
</html> 