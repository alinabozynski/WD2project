<!-- UPDATES a specified existing blog post, replacing the current title and content values
    with the new values that are altered by the user when submitting the form. -->
<?php
    require('connect.php');

    // UPDATE blog if title, content and id are present in POST.
    // Used when user submits the form 
    if($_POST && isset($_GET['username']) && isset($_POST['username']) && isset($_POST['password1']) && isset($_POST['password2'])){
        function filterinput(){
            $errors = false;

            $password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $pass2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if($password != $pass2){
                $errors = true;
            }

            return $errors;
        }

        if(filterinput() == true){
            echo "Password fields must match.";

            $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

            // Build the parametrized SQL query using the filtered value.
            $query = "SELECT * FROM logins WHERE username = :username LIMIT 1";

            $statement = $db->prepare($query);

            // Bind the :id parameter in the query to the sanitized id value.
            // $id specifies an Integer binding-type.
            $statement->bindValue(':username', $username);

            // Execute the SELECT and fetch the single row returned.
            $statement->execute();

            // Only grabbing one row, so the fetch is here, (otherwise it would be looped through in the html)
            $login = $statement->fetch();

        } else {
            try {
                // Retrieve account id
                $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
                $query1 = "SELECT * FROM logins WHERE username = :username LIMIT 1";
                $statement1 = $db->prepare($query1);
                $statement1->bindValue(':username', $username);
                $statement1->execute();
                $login = $statement1->fetch();
                $id = $login['id'];

                // Try inserting user data to MySQL.
                $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                $og_password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                $options = [
                    'salt' => "78302huirlys8t9420hjgif",
                ];

                $password = password_hash($og_password, PASSWORD_BCRYPT, $options);
// Warning: password_hash(): The "salt" option has been ignored, since providing a custom salt is no longer supported

                $query = "UPDATE logins SET username = :username, password = :password WHERE id = :id LIMIT 1";
                $statement = $db->prepare($query); 
                $statement->bindValue(':username', $username);
                $statement->bindValue(':password', $password);
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();

                header("Location: login_data.php");

            } catch (PDOException $e) {
                print "Error: '" . $_POST['username'] . "' already exists. Please choose a different username.";

                $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

                // Build the parametrized SQL query using the filtered value.
                $query = "SELECT * FROM logins WHERE username = :username LIMIT 1";

                $statement = $db->prepare($query);

                // Bind the :id parameter in the query to the sanitized id value.
                // $id specifies an Integer binding-type.
                $statement->bindValue(':username', $username);

                // Execute the SELECT and fetch the single row returned.
                $statement->execute();

                // Only grabbing one row, so the fetch is here, (otherwise it would be looped through in the html)
                $login = $statement->fetch();
            }
        }

    } elseif(isset($_GET['username'])){ 

        // Sanitize $_GET['emp_id'].
        $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

        // Build the parametrized SQL query using the filtered value.
        $query = "SELECT * FROM logins WHERE username = :username LIMIT 1";

        $statement = $db->prepare($query);

        // Bind the :id parameter in the query to the sanitized id value.
        // $id specifies an Integer binding-type.
        $statement->bindValue(':username', $username, PDO::PARAM_INT);

        // Execute the SELECT and fetch the single row returned.
        $statement->execute();

        // Only grabbing one row, so the fetch is here, (otherwise it would be looped through in the html)
        $login = $statement->fetch();

        if($_POST){
            echo "Update failed. Ensure both passwords match";
        }
    } else {
        // When not UPDATING or SELECTING
        $username = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $_GET['username'] ?></title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="blog.css" />
</head>
<body>
    <section>
        <h1><a href="index.php">VROAR Inc.</a> - Edit <?= $_GET['username'] ?>'s Account</h1>
        <h3><a href="login.php">Administration Home Page</a></h3>
    </section>

    <?php if(!isset($_GET['username'])): ?>
        <p>No account selected.</p>
    <?php else: ?>
        <form method="post" action="account.php?username=<?= $_GET['username'] ?>">
            <label for="username">Username: </label>
            <input id="username" name="username" value="<?= $login['username'] ?>">
            <label for="password1">New Password: </label>
            <input type="password" id="password1" name="password1">
            <label for="password2">Re-enter Password: </label>
            <input type="password" id="password2" name="password2">
            <input type="submit" class="submit" value="Update Account">
        </form>

        <form method="post" action="delete.php?username=<?= $_GET['username'] ?>">
            <input type="submit" class="submit" value="Delete Record">
        </form>
    
    <?php endif ?>
</body>
</html> 