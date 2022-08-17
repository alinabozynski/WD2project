<!-- Uses SQL to display a list of the employees and departments. 
     Provides the necessary links for the user to view the details of each. --> 
<?php  
    require('connect.php');

    // Build SQL String and prepare PDO::Statement from the query.
    $query = "SELECT * FROM employees ORDER BY last_name";
    $query2 = "SELECT * FROM departments ORDER BY department_name";

    $statement = $db->prepare($query);
    $statement2 = $db->prepare($query2);

    // Execute() on the DB server.
    $statement->execute(); 
    $statement2->execute();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>VROAR Inc.</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="home.css" />
</head>
<body>
    <header>
        <h1>VROAR Inc.<i> - The highest-rated dealership in town!</i><a href="login.php" id="admin">📝</a></h1>
    </header>

    <form method="POST" action="login_data.php">
        <h3>Wish to create an account? Fill out the form below :)</h3>
        <label for="username">Username: </label>
        <input type="text" id="username" name="username">
        <label for="password1">Password: </label>
        <input type="password" id="password1" name="password1">
        <label for="password2">Re-enter Password: </label>
        <input type="password" id="password2" name="password2">
        <input type="submit" class="submit" value="Create User" name="new_user">
    </form>
    
    <section>
        <h3>Seach for contact information by Employee name:</h3>
        <ul>
            <?php while($row = $statement->fetch()): ?>
                <li><a href="details.php?emp_id=<?= $row['emp_id'] ?>"><?= $row['last_name'] ?>, <?= $row['first_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>

    <section>
        <h3>Seach for contact information by category with Department names:</h3>
        <ul>
            <?php while($row = $statement2->fetch()): ?>
                <li><a href="details.php?department_id=<?= $row['department_id'] ?>"><?= $row['department_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>
</body>
</html> 