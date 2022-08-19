<!-- Uses SQL to display a list of the employees and departments. 
     Provides the necessary links for the user to view the details of each. 
     Users also have the option to search results by a keyword, with or 
     without a category applied to the search. --> 
<?php  
    // Require the connection to the database for this page
    require('connect.php');

    // Grab the department info to display the departments list in the search bar category option
    $initial_query = "SELECT * FROM departments";
    $initial_statement = $db->prepare($initial_query);
    $initial_statement->execute(); 

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

    // If the user has entered a keyword to search the database for 
    if(isset($_POST['search_request'])){

        if(empty($_POST['search'])){
            $any_search_results = false;

            echo "Could not search. Ensure the search bar is not empty.";

            // Build SQL String and prepare PDO::Statement from the query.
            $query = "SELECT * FROM employees ORDER BY last_name";
            $query2 = "SELECT * FROM departments ORDER BY department_name";

            $statement = $db->prepare($query);
            $statement2 = $db->prepare($query2);

            // Execute() on the DB server.
            $statement->execute(); 
            $statement2->execute();

        } else {
            $any_search_results = true;

            if($_POST['category'] == "no_category"){
                // Retrieve user submitted keyword from the search form 
                $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                $query = "SELECT * FROM employees WHERE first_name LIKE '%$keyword%' OR last_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement = $db->prepare($query);
                $statement->execute();

                $query2 = "SELECT * FROM departments WHERE department_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement2 = $db->prepare($query2);
                $statement2->execute(); 

            } else {
                // Grab the user selected category (department name)
                // No sanitization necessary for a select option value
                $department_name = $_POST['category'];

                $dept_query = "SELECT * FROM departments WHERE department_name = :department_name LIMIT 1";
                $dept_statement = $db->prepare($dept_query);
                $dept_statement->bindValue(':department_name', $department_name, PDO::PARAM_STR);
                $dept_statement->execute();
                $dept = $dept_statement->fetch();
                $department_id = $dept['department_id'];

                // Retrieve user submitted keyword from the search form 
                $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                $query = "SELECT * FROM employees WHERE department_id = :department_id AND first_name LIKE '%$keyword%' OR last_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement = $db->prepare($query);
                $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);
                $statement->execute();

                $query2 = "SELECT * FROM departments WHERE department_id = :department_id AND department_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement2 = $db->prepare($query2);
                $statement2->bindValue(':department_id', $department_id, PDO::PARAM_INT);
                $statement2->execute();
            }

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

                $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                $query2 = "SELECT * FROM departments WHERE department_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";;
                $statement2 = $db->prepare($query2);
                $statement2->execute();
            } elseif(!empty($results1) && empty($results2)) {
                echo "No department records found for your search.";

                $keyword = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
                $query = "SELECT * FROM employees WHERE first_name LIKE '%$keyword%' OR last_name LIKE '%$keyword%' OR tel_number LIKE '%$keyword%' OR email LIKE '%$keyword%' OR image_file LIKE '%$keyword%'";
                $statement = $db->prepare($query);
                $statement->execute();
            } elseif(empty($results1) && empty($results2)){
                echo "No records match your search."; 
                $any_search_results = false;

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
        $any_search_results = false;

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
    <link rel="stylesheet" type="text/css" href="index.css" />
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1>
        <h1 id="middle"> The <i>highest-rated</i> dealership in town!</h1>
        <h1><a href="login.php" id="admin" title="Admin Access">📝</a></h1>
    </header>

    <form method="POST" action="index.php">
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
        <label for="search">Search by keyword (employees within a department): </label>
        <input type="text" id="search" name="search" autofocus>
        <select name="category" id="category">
            <option value="no_category">OPTIONAL: Select a category to search in</option>
            <?php while($row = $initial_statement->fetch()): ?>
                <option><?= $row['department_name'] ?></option>
            <?php endwhile ?>
        </select>
        <input type="submit" class="submit" name="search_request" value="Search">
    </form>

    <section>
        <?php if(!empty($results1) || !$any_search_results): ?>
            <h3>Seach for contact information by Employee name:</h3>
        <?php endif ?>
        <ul>
            <?php while($row = $statement->fetch()): ?>
                <li><a href="details.php?emp_id=<?= $row['emp_id'] ?>"><?= $row['last_name'] ?>, <?= $row['first_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>

    <section>
        <?php if(!empty($results2) || !$any_search_results): ?>
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