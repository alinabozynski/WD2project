<?php  
 
    SESSION_START();

    if(isset($_POST['login'])){
        require('connect.php');

        // Retrieve the row in MySQL with the entered username.
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query = "SELECT * FROM logins WHERE username = :username LIMIT 1";     
        $statement = $db->prepare($query); 
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();

        // Retrieve the salted and hashed version of what the user enterec.
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if(!$row || !password_verify($password, $row['password'])){
            echo "Error: The entered data does not match the records. Try again.";
        } else {
            $_SESSION['username'] = $row['username'];
        }

    } elseif (isset($_POST['logout'])){
        $_SESSION = array();
        session_destroy();
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