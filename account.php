<!-- UPDATES a specified account, replacing the current username and password
    with the new values that are entered by the user when submitting the form. -->
<?php
    // Require the connection to the database for this page
    require('connect.php');

    // UPDATE login account if username and password fields are present in POST.
    // Used when user submits the form 
    if($_POST && isset($_GET['username']) && isset($_POST['username']) && isset($_POST['password1']) && isset($_POST['password2'])){

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

        // If both password fields match
        } else {

            // Try to update the account with the user's entered values.
            try {
                // Retrieve account id
                $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
                $query1 = "SELECT * FROM logins WHERE username = :username LIMIT 1";
                $statement1 = $db->prepare($query1);
                $statement1->bindValue(':username', $username, PDO::PARAM_STR);
                $statement1->execute();
                $login = $statement1->fetch();
                $id = $login['id'];

                // Try inserting user data to MySQL
                $new_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                $og_password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_STRING);

                // Salt and hash the entered password
                $options = [
                    'salt' => "78302huirlys8t9420hjgif",
                ];

                $password = password_hash($og_password, PASSWORD_BCRYPT, $options);

                // Perform the update
                $query = "UPDATE logins SET username = :new_username, password = :password WHERE id = :id LIMIT 1";
                $statement = $db->prepare($query); 
                $statement->bindValue(':new_username', $new_username, PDO::PARAM_STR);
                $statement->bindValue(':password', $password, PDO::PARAM_STR);
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();

                // Redirect after update
                header("Location: login_data.php");

            // If the query does not execute, the username already exists. So, display the error.
            } catch (PDOException $e) {
                print "Error: '" . $_POST['username'] . "' already exists. Please choose a different username.";
            }
        }

    // Grab account to be updated username is in the URL
    } elseif(isset($_GET['username'])){ 

        // Sanitize $_GET['username'] to use in the query.
        $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

        // Build the parametrized SQL query using the filtered value.
        $query = "SELECT * FROM logins WHERE username = :username LIMIT 1";

        // Prepare the parameterized SQL query
        $statement = $db->prepare($query);

        // Bind the :username parameter in the query to the sanitized username value.
        $statement->bindValue(':username', $username, PDO::PARAM_INT);

        // Execute the SELECT and fetch the single row returned.
        $statement->execute();

        $login = $statement->fetch();

        if($_POST){
            echo "Update failed.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $_GET['username'] ?></title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="account.css" />
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1>
        <h1 id="middle">Edit <?= $_GET['username'] ?>'s Account</h1>
        <h1><a href="login.php">📝</a></h1>
    </header>

    <?php if(!isset($_GET['username'])): ?>
        <p>No account selected.</p>
    <?php else: ?>
        <div>
            <form method="post" action="account.php?username=<?= $_GET['username'] ?>">
                <label for="username">Username: </label>
                <input id="username" name="username" value="<?= $_GET['username'] ?>">
                <label for="password1">New Password: </label>
                <input type="password" id="password1" name="password1" autofocus>
                <label for="password2">Re-enter Password: </label>
                <input type="password" id="password2" name="password2">
                <input type="submit" class="submit" value="Update Account">
            </form>

            <form method="post" action="delete.php?username=<?= $_GET['username'] ?>">
                <input type="submit" class="submit" value="DELETE Record">
            </form>
        </div>
    <?php endif ?>
</body>
</html> 