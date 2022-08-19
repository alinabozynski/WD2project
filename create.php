<!-- CREATES a new employee or department record post with user input. -->
<?php
    // Require the connection to the database for this page
    require('connect.php');

    // Display the departments (twice) when an employee record is being added 
    // Once to display a list of the department names and department ids (to choose
    //  an ID in the form), and a second query to display the deparment IDs in the form
    $initial_query = "SELECT * FROM departments";
    $initial_query2 = "SELECT * FROM departments";
    $initial_statement = $db->prepare($initial_query);
    $initial_statement2 = $db->prepare($initial_query2);
    $initial_statement->execute();
    $initial_statement2->execute();
    
    // Check if form was submitted
    if($_POST){

        // Validate user input
        function filterinput(){

            // Create a variable for errors defaulted to false
            $errors = false;

            if($_GET['type'] == "employee"){

                // Ensure first and last name fields are not empty
                if(empty($_POST['first_name'])){
                    $errors = true;
                }
                if(empty($_POST['last_name'])){
                    $errors = true;
                }

                // Validate the phone number, which must start with some form of 1204
                if(!preg_match('^(?:\+?1[-.●]?)?\(?([0-9]{3})\)?[-.●]?([0-9]{3})[-.●]?([0-9]{4})$^', $_POST['tel_number'])){
                    $errors = true;
                }

                // Ensure the email is a company email
                if(!preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email'])){
                    $errors = true;
                }

                // Ensure a department was selected
                if($_POST['department_id'] == "Select a Department ID"){
                    $errors = true;
                }

                // Returns true is the form has errors.
                return $errors;

            } elseif($_GET['type'] == "department"){
                // Ensure department name field is not empty
                if(empty($_POST['department_name'])){
                    $errors = true;
                }

                // Validate the phone number, which must start with some form of 1204
                if(!preg_match('^(?:\+?1[-.●]?)?\(?([0-9]{3})\)?[-.●]?([0-9]{3})[-.●]?([0-9]{4})$^', $_POST['tel_number'])){
                    $errors = true;
                }

                // Ensure the email is a company email
                if(!preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email'])){
                    $errors = true;
                }

                // Returns true is the form has errors.
                return $errors;
            }
        }
        
        // If the form has errors, display an error message
        if(filterinput()){
            echo "ATTENTION: " . ucfirst($_GET['type']) . " record could not be added. Please ensure all data is valid. No fields should be left as their defaults or blank, the phone number should be valid, and the email should be a valid email address ending in '@VROAR.com'.";

        // If there were no errors, create the new employee or department record.
        } else {
            if($_GET['type'] == "employee"){
                // Sanitize user input to filter out dangerous characters and make sure 
                //     they are valid to enter into the SQL.
                $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
                $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
                $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $department_id = filter_input(INPUT_POST, 'department_id');

                // Build and prepare the parameterized SQL query and bind to the above sanitized values.
                $query = "INSERT INTO employees (first_name, last_name, tel_number, email, department_id) VALUES (:first_name, :last_name, :tel_number, :email, :department_id)";
                $statement = $db->prepare($query); 
                $statement->bindValue(':first_name', ucfirst($first_name), PDO::PARAM_STR);
                $statement->bindValue(':last_name', ucfirst($last_name), PDO::PARAM_STR);
                $statement->bindValue(':tel_number', $tel_number,PDO::PARAM_STR);
                $statement->bindValue(':email', $email, PDO::PARAM_STR);
                $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);

                // Execute the INSERT statement.
                $statement->execute(); 
                
                // Redirect after insert. 
                header("Location: login.php");

            } elseif($_GET['type'] == "department"){
                // Sanitize user input to filter out dangerous characters and make sure 
                //     they are valid to enter into the SQL.
                $department_name = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_STRING);
                $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

                // Build and prepare the parameterized SQL query and bind to the above sanitized values.
                $query = "INSERT INTO departments (department_name, tel_number, email) VALUES (:department_name, :tel_number, :email)";
                $statement = $db->prepare($query); 
                $statement->bindValue(':department_name', ucfirst($department_name), PDO::PARAM_STR);
                $statement->bindValue(':tel_number', $tel_number, PDO::PARAM_STR);
                $statement->bindValue(':email', $email, PDO::PARAM_STR);

                // Execute the INSERT statement.
                $statement->execute(); 
                
                // Redirect after insert. 
                header("Location: login.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Blog Post</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="edit.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1>
        <h1 id="middle"><a href="create.php?type=<?= $_GET['type'] ?>">New <?= ucfirst($_GET['type']) ?> Entry</a></h1>
        <h1><a href="login.php">📝</a></h1>
    </header>

    <?php if($_GET['type'] == "employee"): ?>
        <h3>List of Department IDs and Names for Employee Record</h3>
        <ul>
            <?php while($row = $initial_statement->fetch()): ?>
                <li><?= $row['department_id'] ?>: <?= $row['department_name'] ?></li>
            <?php endwhile ?>
        </ul>
    <?php endif ?>

    <form method="POST" action="create.php?type=<?php echo $_GET['type']=="employee" ? 'employee' : 'department' ?>">

        <?php if($_GET['type'] == "employee"): ?>
            <label for="first_name">First Name: </label>
            <input id="first_name" name="first_name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>"  size=35 autofocus>
            <label for="last_name">Last Name: </label>
            <input id="last_name" name="last_name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>" size=35>
            <label for="tel_number">Phone Number: </label>
            <input type="tel" id="tel_number" name="tel_number" value="<?php echo isset($_POST['tel_number']) ? $_POST['tel_number'] : '1(204)'; ?>" size=35>
            <label for="email">Email: </label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '@VROAR.com'; ?>" size=35>
            <label for="department_id">Department ID: </label>
            <select id="department_id" name="department_id">
                <option>Select a Department ID</option>
                <?php while($row = $initial_statement2->fetch()): ?>
                    <option value="<?= $row['department_id'] ?>"><?= $row['department_id'] ?></option>
                <?php endwhile ?>
            </select>
        <?php elseif($_GET['type'] == "department"): ?>
            <label for="department_name">Department Name: </label>
            <input id="department_name" name="department_name" value="<?php echo isset($_POST['department_name']) ? $_POST['department_name'] : ''; ?>"  size=35 autofocus>
            <label for="tel_number">Phone Number: </label>
            <input type="tel" id="tel_number" name="tel_number" value="<?php echo isset($_POST['tel_number']) ? $_POST['tel_number'] : '1(204)'; ?>" size=35>
            <label for="email">Email: </label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '@VROAR.com'; ?>" size=35>
        <?php endif ?>

        <input type="submit" class="submit" value="Add Record">

    </form>
</body>
</html>