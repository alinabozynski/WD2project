<!-- Uses SQL to display a list of the employees and departments. 
     Provides the necessary links for the user to view the details of each. 
     Users also have the option to search results by a keyword. --> 
<?php  
    // Require the connection to the database for this page
    require('connect.php');

    // Grab the department info to display the departments list in the search bar category option
    $initial_query = "SELECT * FROM departments";
    $initial_statement = $db->prepare($initial_query);
    $initial_statement->execute(); 

    // If the user has entered a keyword to search the database for 
    if(isset($_POST['search_request'])){
        if(str_contains(trim($_POST['search']), ' ')){
            $search_results = false;

            echo "Sorry, the keyword search bar only accepts one word searches.";

            // Build SQL String and prepare PDO::Statement from the query.
            $query = "SELECT * FROM employees ORDER BY last_name";
            $query2 = "SELECT * FROM departments ORDER BY department_name";

            $statement = $db->prepare($query);
            $statement2 = $db->prepare($query2);

            // Execute() on the DB server.
            $statement->execute(); 
            $statement2->execute();

        } else {
                // Retrieve user submitted keyword from the search form 
                $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                $query = "SELECT * FROM employees WHERE first_name LIKE '%$keyword%' OR last_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement = $db->prepare($query);
                $statement->execute();

                $query2 = "SELECT * FROM departments WHERE department_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement2 = $db->prepare($query2);
                $statement2->execute();

                // Check if the query had results 
                $results1 = [];
                while($employee = $statement->fetch()){
                    $results1 = $employee['emp_id'];
                }

                // Check if the query had results 
                $results2 = [];
                while($department = $statement2->fetch()){
                    $results2 = $department['department_id'];
                }

                if(empty($results1) && !empty($results2)){
                    echo "No employee records found for your search.";
                } else {

                    $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                    $query = "SELECT * FROM employees WHERE first_name LIKE '%$keyword%' OR last_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                    $statement = $db->prepare($query);
                    $statement->execute();
                }

                if(empty($results2) && !empty($results1)){
                    echo "No department records found for your search.";
                } else {

                    $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                    $query2 = "SELECT * FROM departments WHERE department_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";;
                    $statement2 = $db->prepare($query2);
                    $statement2->execute();
                }

                if(empty($results1) && empty($results2)){
                    echo "No records match your search."; 
                    $search_results = false;

                    // Build SQL String and prepare PDO::Statement from the query.
                    $query = "SELECT * FROM employees ORDER BY last_name";
                    $query2 = "SELECT * FROM departments ORDER BY department_name";

                    $statement = $db->prepare($query);
                    $statement2 = $db->prepare($query2);

                    // Execute() on the DB server.
                    $statement->execute(); 
                    $statement2->execute();

                }

        }

    // If no keyword has been searched for, set and use a default ORDER BY value
    } else {

        // Build SQL String and prepare PDO::Statement from the query.
        $query = "SELECT * FROM employees ORDER BY last_name";
        $query2 = "SELECT * FROM departments ORDER BY department_name";

        $statement = $db->prepare($query);
        $statement2 = $db->prepare($query2);

        // Execute() on the DB server.
        $statement->execute(); 
        $statement2->execute();
    }

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

    <form method="POST" action="index.php">
        <label for="search">Search by keyword</label>
        <input type="text" id="search" name="search">
        <select name="category" id="category">
            <option value="no_category">OPTIONAL: Select a category to search in</option>
            <?php while($row = $initial_statement->fetch()): ?>
                <option><?= $row['department_name'] ?></option>
            <?php endwhile ?>
        </select>
        <input type="submit" class="submit" name="search_request" value="Search">
    </form>

    <section>
        <?php if(!empty($results1)): ?>
            <h3>Seach for contact information by Employee name:</h3>
        <?php endif ?>
        <ul>
            <?php while($row = $statement->fetch()): ?>
                <li><a href="details.php?emp_id=<?= $row['emp_id'] ?>"><?= $row['last_name'] ?>, <?= $row['first_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>

    <section>
        <?php if(!empty($results2)): ?>
            <h3>Seach for contact information by category with a department name:</h3>
        <?php endif ?>
        <ul>
            <?php while($row = $statement2->fetch()): ?>
                <li><a href="details.php?department_id=<?= $row['department_id'] ?>"><?= $row['department_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>
</body>
</html> 