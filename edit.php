<!-- UPDATES a specified existing blog post, replacing the current title and content values
    with the new values that are altered by the user when submitting the form. -->
<?php
    require('connect.php');

    $emp_id = 0;
    $department_id = 0;

    $initial_query = "SELECT * FROM departments";
    $initial_query2 = "SELECT * FROM departments";
    $initial_statement = $db->prepare($initial_query);
    $initial_statement2 = $db->prepare($initial_query2);
    $initial_statement->execute();
    $initial_statement2->execute();
    
    // UPDATE blog if title, content and id are present in POST.
    // Used when user submits the form 
    if($_POST && isset($_GET['emp_id']) && $_POST['first_name'] != "" && $_POST['last_name'] != "" && preg_match('^1(\s)?\(?204\)?(\s|.|-)?\d{3}(\s|.|-)?\d{4}$^', $_POST['tel_number']) && preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email']) && $_POST['department_id'] != "Select a Department ID"){
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $department_id = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);

        // Build the parameterized SQL query and bind to the above sanitized values.
        $query = "UPDATE employees SET first_name = :first_name, last_name = :last_name, tel_number = :tel_number, email = :email, department_id = :department_id WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':first_name', $first_name);        
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':tel_number', $tel_number);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':department_id', $department_id);
        $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);

        // Execute the UPDATE statement.
        $statement->execute();

        // Redirect after update.
        header("Location: details.php?emp_id={$_GET['emp_id']}");

    } elseif($_POST && isset($_GET['department_id']) && $_POST['department_name'] != "" && preg_match('^1(\s)?\(?204\)?(\s|.|-)?\d{3}(\s|.|-)?\d{4}$^', $_POST['tel_number']) && preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email'])){
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
        $department_name = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        // Build the parameterized SQL query and bind to the above sanitized values.
        $query = "UPDATE departments SET department_name = :department_name, tel_number = :tel_number, email = :email WHERE department_id = :department_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':department_name', $department_name);        
        $statement->bindValue(':tel_number', $tel_number);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);

        // Execute the UPDATE statement.
        $statement->execute();

        // Redirect after update.
        header("Location: details.php?department_id={$_GET['department_id']}");

        //header("Location: edit.php?id={$id}");

        // Grab record to be updated (if id GET parameter exists in the URL).
    } elseif(isset($_GET['emp_id'])){ 

        // Sanitize $_GET['emp_id'].
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

        // Build the parametrized SQL query using the filtered value.
        $query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";

        $statement = $db->prepare($query);

        // Bind the :id parameter in the query to the sanitized id value.
        // $id specifies an Integer binding-type.
        $statement->bindValue('emp_id', $emp_id, PDO::PARAM_INT);

        // Execute the SELECT and fetch the single row returned.
        $statement->execute();

        // Only grabbing one row, so the fetch is here, (otherwise it would be looped through in the html)
        $employee = $statement->fetch();

        if($_POST){
            echo "Update failed. Ensure all content is valid. No fields should be left blank, the phone number must be 11 digits long and starting with 1(204), and the email should be a valid email address ending in '@VROAR.com'.";
        }
    } elseif(isset($_GET['department_id'])){
        // Sanitize $_GET['department_name'].
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

        // Build the parametrized SQL query using the filtered value.
        $query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $statement = $db->prepare($query);

        $statement->bindValue('department_id', $department_id);

        // Execute the SELECT and fetch the single row returned.
        $statement->execute();

        $department = $statement->fetch();

        if($_POST){
            echo "Update failed. Ensure all content is valid. No fields should be left blank, the phone number must be 11 digits long and starting with 1(204), and the email should be a valid email address ending in '@VROAR.com'.";
        }

    } else {
        // When not UPDATING or SELECTING
        $emp_id = false;
        $department_id = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo isset($_GET['emp_id']) ? "Employee" : "Department" ?> Editting</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="blog.css" />
</head>
<body>
    <section>
        <h1><a href="index.php">VROAR Inc.</a> - Edit <?php echo isset($_GET['emp_id']) ? "Employee" : "Department" ?> Record</h1>
        <h3><a href="login.php">Administration Home Page</a></h3>
        <h3><a href="upload.php?<?php echo isset($_GET['emp_id']) ? "emp_id=".$_GET['emp_id'] : "department_id=".$_GET['department_id'] ?>">Upload image to the record for <i><?php echo isset($_GET['emp_id']) ? $employee['first_name']." ".$employee['last_name'] : $department['department_name'] ?></i></a></h3>
    </section>

    <?php if(isset($_GET['emp_id'])): ?>
        <ul>
            <li>List of Department IDs and Names for Employee Record</li>
            <?php while($row = $initial_statement->fetch()): ?>
                <li><?= $row['department_id'] ?>: <?= $row['department_name'] ?></li>
            <?php endwhile ?>
        </ul>
        <h4><?= $employee['first_name']?> <?= $employee['last_name'] ?>'s Current Department: <?= $employee['department_id']?></h4>
    <?php endif ?>

    <?php if(!isset($_GET['emp_id']) && !isset($_GET['department_id'])): ?>
        <p>No record selected.</p>
    <?php else: ?>
        <form method="post" action="edit.php?<?php echo isset($_GET['emp_id']) ? "emp_id=".$_GET['emp_id'] : "department_id=".$_GET['department_id'] ?>">

            <?php if(isset($_GET['emp_id'])): ?>
                <label for="first_name">First Name: </label>
                <input id="first_name" name="first_name" value="<?= $employee['first_name'] ?>">
                <label for="last_name">Last Name: </label>
                <input id="last_name" name="last_name" value="<?= $employee['last_name'] ?>">
                <label for="tel_number">Phone Number: </label>
                <input id="tel_number" name="tel_number" value="<?= $employee['tel_number'] ?>">
                <label for="email">Email: </label>
                <input id="email" name="email" value="<?= $employee['email'] ?>" size=35>
                <label for="department_id">Department ID: </label>
                <select id="department_id" name="department_id">
                    <option>Select a Department ID</option>
                    <?php while($department = $initial_statement2->fetch()): ?>
                        <option value="<?= $department['department_id'] ?>"><?= $department['department_id'] ?></option>
                    <?php endwhile ?>
                </select>
                <input type="submit" class="submit" value="Update Record">
                <?php if($employee['image_file'] != null): ?>
                    <p><img src="uploads/<?= $employee['image_file'] ?>" alt="<?= $employee['image_file'] ?>" title="<?= $employee['image_file'] ?>" height="300"></p>
                    <p><a href="image_removal.php?emp_id=<?= $employee['emp_id'] ?>">Remove image from this record</a></p>
                <?php endif ?>
            <?php elseif(isset($_GET['department_id'])): ?>
                <label for="department_name">Department Name: </label>
                <input id="department_name" name="department_name" value="<?= $department['department_name'] ?>">
                <label for="tel_number">Phone Number: </label>
                <input id="tel_number" name="tel_number" value="<?= $department['tel_number'] ?>">
                <label for="email">Email: </label>
                <input id="email" name="email" value="<?= $department['email'] ?>" size=35>
                <input type="submit" class="submit" value="Update Record">
                <?php if($department['image_file'] != null): ?>
                    <p><img src="uploads/<?= $department['image_file'] ?>" alt="<?= $department['image_file'] ?>" title="<?= $department['image_file'] ?>" height="300"></p>
                    <p><a href="image_removal.php?department_id=<?= $department['department_id'] ?>">Remove image from this record</a></p>
                <?php endif ?>
            <?php endif ?>
        </form>

        <form method="post" action="delete.php?<?php echo isset($_GET['emp_id']) ? "emp_id=".$_GET['emp_id'] : "department_id=".$_GET['department_id'] ?>">
            <input type="submit" class="submit" value="Delete Record">
        </form>
    
    <?php endif ?>
</body>
</html> 