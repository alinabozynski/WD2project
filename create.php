<!-- CREATES a blog post, inserting the title and content values the user 
     entered when submitting the form. -->
<?php
    require('connect.php');

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

            // Create a variable for errors with an inital value of 0
            $errors=0;

            if($_GET['type'] == "employee"){
                if(strlen($_POST['first_name']) <= 0){
                    $errors += 1;
                }
                if(strlen($_POST['last_name']) <= 0){
                    $errors += 1;
                }
                if(!preg_match('^1(\s)?\(?204\)?(\s|.|-)?\d{3}(\s|.|-)?\d{4}$^', $_POST['tel_number'])){
                    $errors += 1;
                }
                if(!preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email'])){
                    $errors += 1;
                }
                if($_POST['department_id'] == "Select a Department ID"){
                    $errors += 1;
                }

                return $errors;

            } elseif($_GET['type'] == "department"){
                if(strlen($_POST['department_name']) <= 0){
                    $errors += 1;
                }
                if(!preg_match('^1(\s)?\(?204\)?(\s|.|-)?\d{3}(\s|.|-)?\d{4}$^', $_POST['tel_number'])){
                    $errors += 1;
                }
                if(!preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email'])){
                    $errors += 1;
                }

                return $errors;
            }
        }
        
        if(filterinput() != 0){
            echo "ATTENTION: " . ucfirst($_GET['type']) . " record could not be added. Please ensure all data is valid. No fields should be left as their defaults or blank, the phone number must be 11 digits long and starting with 1(204), and the email should be a valid email address ending in '@VROAR.com'.";

        } else {
            if($_GET['type'] == "employee"){
                // Sanitize user input to filter out dangerous characters and make sure 
                //     they are valid to enter into the SQL.
                $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $department_id = filter_input(INPUT_POST, 'department_id');

                // Build and prepare the parameterized SQL query and bind to the above sanitized values.
                $query = "INSERT INTO employees (first_name, last_name, tel_number, email, department_id) VALUES (:first_name, :last_name, :tel_number, :email, :department_id)";
                $statement = $db->prepare($query); 
                $statement->bindValue(':first_name', ucfirst($first_name));
                $statement->bindValue(':last_name', ucfirst($last_name));
                $statement->bindValue(':tel_number', $tel_number);
                $statement->bindValue(':email', $email);
                $statement->bindValue(':department_id', $department_id);

                // Execute the INSERT statement.
                $statement->execute(); 

                // Display message to show the user that the record addition was successful.
                
                header("Location: login.php");

            } elseif($_GET['type'] == "department"){
                // Sanitize user input to filter out dangerous characters and make sure 
                //     they are valid to enter into the SQL.
                $department_name = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

                // Build and prepare the parameterized SQL query and bind to the above sanitized values.
                $query = "INSERT INTO departments (department_name, tel_number, email) VALUES (:department_name, :tel_number, :email)";
                $statement = $db->prepare($query); 
                $statement->bindValue(':department_name', ucfirst($department_name));
                $statement->bindValue(':tel_number', $tel_number);
                $statement->bindValue(':email', $email);

                // Execute the INSERT statement.
                $statement->execute(); 

                // Display message to show the user that the record addition was successful.
                
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
    <link rel="stylesheet" type="text/css" href="blog.css" />
</head>
<body>
    <section>
        <h1><a href="index.php">VROAR Inc.</a> - <a href="create.php?type=<?= $_GET['type'] ?>">New <?= ucfirst($_GET['type']) ?> Entry</a></h1>
        <h3><a href="login.php">Administration Home Page</a></h3>
    </section>

    <?php if($_GET['type'] == "employee"): ?>
        <ul>
            <li>List of Department IDs and Names for Employee Record</li>
            <?php while($row = $initial_statement->fetch()): ?>
                <li><?= $row['department_id'] ?>: <?= $row['department_name'] ?></li>
            <?php endwhile ?>
        </ul>
    <?php endif ?>

    <form method="POST" action="create.php?type=<?php echo $_GET['type']=="employee" ? 'employee' : 'department' ?>">

        <?php if($_GET['type'] == "employee"): ?>
            <label for="first_name">First Name: </label>
            <input id="first_name" name="first_name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>">
            <label for="last_name">Last Name: </label>
            <input id="last_name" name="last_name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>">
            <label for="tel_number">Phone Number: </label>
            <input id="tel_number" name="tel_number" value="<?php echo isset($_POST['tel_number']) ? $_POST['tel_number'] : '1(204)'; ?>">
            <label for="email">Email: </label>
            <input id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '@VROAR.com'; ?>" size=35>
            <label for="department_id">Department ID: </label>
            <select id="department_id" name="department_id">
                <option>Select a Department ID</option>
                <?php while($row = $initial_statement2->fetch()): ?>
                    <option value="<?= $row['department_id'] ?>"><?= $row['department_id'] ?></option>
                <?php endwhile ?>
            </select>
        <?php elseif($_GET['type'] == "department"): ?>
            <label for="department_name">Department Name: </label>
            <input id="department_name" name="department_name" value="<?php echo isset($_POST['department_name']) ? $_POST['department_name'] : ''; ?>">
            <label for="tel_number">Phone Number: </label>
            <input id="tel_number" name="tel_number" value="<?php echo isset($_POST['tel_number']) ? $_POST['tel_number'] : '1(204)'; ?>">
            <label for="email">Email: </label>
            <input id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '@VROAR.com'; ?>" size=35>
        <?php endif ?>

        <input type="submit" class="submit" value="Add Record">

    </form>
</body>
</html>