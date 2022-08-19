<!-- Creates a new login account and displayed the current account records. --> 
<?php    
    // Require the connection to the database for this page
    require('connect.php');

    // Build SQL String and prepare PDO::Statement from the query.
    $query = "SELECT * FROM logins ORDER BY username";

    $statement = $db->prepare($query);

    // Execute() on the DB server.
    $statement->execute(); 

    // If the user has created a new user from the admin page 
    if(isset($_POST['new_user'])){

        // Check if passwords match
        function filterinput(){
            $errors = false;

            $password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_STRING);
            $pass2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);

            if($password != $pass2){
                $errors = true;
            }

            return $errors;
        }

        // If the passwords do not match, display the error.
        if(filterinput() == true){
            echo "Password fields must match.";
        } else {
            ini_set('display_errors', false);

            // Try to create a login account with the user's entered values.
            try {
                $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                $og_password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_STRING);

                // Salt and hash the entered password to insert into the database.
                $options = [
                    'salt' => "78302huirlys8t9420hjgif",
                ];

                $password = password_hash($og_password, PASSWORD_BCRYPT, $options);

                // Build and prepare the parameterized SQL query and bind to the above sanitized values.
                $query = "INSERT INTO logins (username, password) VALUES (:username, :password)";        
                $statement = $db->prepare($query); 
                $statement->bindValue(':username', $username, PDO::PARAM_STR);
                $statement->bindValue('password', $password, PDO::PARAM_STR);

                // Execute the INSERT
                $statement->execute();

                // Redirect after INSERT
                header("Location: login_data.php");

            // If the username already exists, display an error message.
            } catch (PDOException $e) {
                print "Error: '" . $_POST['username'] . "' already exists. Please choose a different username.";
                
                // Ensure the page still displays everything it displayed before the error.
                $query = "SELECT * FROM logins ORDER BY username";
                $statement = $db->prepare($query);
                $statement->execute(); 
            }
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="login_data.css" />
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1>
        <h1 id="middle">Login Data</h1> 
        <h1><a href="login.php" title="Admin Access">📝</a></h1>
    </header>

    <form method="POST" action="login_data.php"> 
        <h3>Create a new user</h3>
        <label for="username">Username: </label>
        <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
        <label for="password1">Password: </label>
        <input type="password" id="password1" name="password1">
        <label for="password2">Re-enter Password: </label>
        <input type="password" id="password2" name="password2">

        <input type="submit" class="submit" value="Create User" name="new_user">
    </form>

    <h3>Login Data</h3>
    <h4>Click a username or password to edit or delete that account.</h4>
    <table>
        <tr>
            <td>Username:</td>
            <td>Password</td>
        </tr>
        <?php while($row = $statement->fetch()): ?>
            <tr>
                <td><a href="account.php?username=<?= $row['username'] ?>"><?= $row['username'] ?></a></td>
                <td><a href="account.php?username=<?= $row['username'] ?>"><?= $row['password'] ?></a></td>
            </tr>
        <?php endwhile ?>
    </table>
</body>
</html> 