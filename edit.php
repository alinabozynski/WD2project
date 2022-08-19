<!-- UPDATES a specified existing employee or department record, replacing the current 
        values with user input. -->
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

    // Retrieve both comments table data to display any matches for the selected record
    $emp_commments_query = "SELECT * from emp_comments ORDER BY created DESC";
    $dept_comments_query = "SELECT * from dept_comments ORDER BY created DESC";
    $emp_comment_statement = $db->prepare($emp_commments_query);
    $dept_comment_statement = $db->prepare($dept_comments_query);
    $emp_comment_statement->execute();
    $dept_comment_statement->execute();
    
    // UPDATE record if user input is valid.
    // Used when user submits the form 
    if($_POST && isset($_GET['emp_id']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && preg_match('^(?:\+?1[-.●]?)?\(?([0-9]{3})\)?[-.●]?([0-9]{3})[-.●]?([0-9]{4})$^', $_POST['tel_number']) && preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email']) && $_POST['department_id'] != "Select a Department ID"){
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $department_id = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $query = "UPDATE employees SET first_name = :first_name, last_name = :last_name, tel_number = :tel_number, email = :email, department_id = :department_id WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':first_name', $first_name, PDO::PARAM_STR);        
        $statement->bindValue(':last_name', $last_name, PDO::PARAM_STR);
        $statement->bindValue(':tel_number', $tel_number, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);
        $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);

        // Execute the UPDATE statement.
        $statement->execute();

        // Redirect after update.
        header("Location: details.php?emp_id={$_GET['emp_id']}");

    } elseif($_POST && isset($_GET['department_id']) && !empty($_POST['department_name']) && preg_match('^(?:\+?1[-.●]?)?\(?([0-9]{3})\)?[-.●]?([0-9]{3})[-.●]?([0-9]{4})$^', $_POST['tel_number']) && preg_match('/\A[a-zA-Z0-9+_.-]+@VROAR.com/', $_POST['email'])){
        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
        $department_name = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_STRING);
        $tel_number = filter_input(INPUT_POST, 'tel_number', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $query = "UPDATE departments SET department_name = :department_name, tel_number = :tel_number, email = :email WHERE department_id = :department_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':department_name', $department_name, PDO::PARAM_STR);        
        $statement->bindValue(':tel_number', $tel_number, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);

        // Execute the UPDATE statement.
        $statement->execute();

        // Redirect after update.
        header("Location: details.php?department_id={$_GET['department_id']}");

    // Grab record to be updated if id in the URL.
    } elseif(isset($_GET['emp_id'])){ 

        // Sanitize $_GET['emp_id'].
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parametrized SQL query and bind to the above sanitized values.
        $query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";

        $statement = $db->prepare($query);

        $statement->bindValue('emp_id', $emp_id, PDO::PARAM_INT);

        // Execute the SELECT and fetch the single row returned.
        $statement->execute();

        $employee = $statement->fetch();

        if($_POST){
            echo "Update failed. Please ensure all data is valid. No fields should be left as their defaults or blank, the phone number should be valid, and the email should be a valid email address ending in '@VROAR.com'.";
        }
    } elseif(isset($_GET['department_id'])){
        // Sanitize $_GET['department_id'].
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";

        $statement = $db->prepare($query);

        $statement->bindValue('department_id', $department_id, PDO::PARAM_INT);

        // Execute the SELECT and fetch the single row returned.
        $statement->execute();

        $department = $statement->fetch();

        if($_POST){
            echo "Update failed. Please ensure all data is valid. No fields should be left as their defaults or blank, the phone number should be valid, and the email should be a valid email address ending in '@VROAR.com'.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo isset($_GET['emp_id']) ? "Employee" : "Department" ?> Editting</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="edit.css" />
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1>
        <h1 id="middle">Edit <i><?php echo isset($_GET['emp_id']) ? $employee['first_name'] . ' ' . $employee['last_name'] . "'s" : $department['department_name'] ?></i> Record</h1>
        <h1><a href="login.php" title="Admin Access">📝</a></h1>
    </header>

    <div>
        <h3><a href="upload.php?<?php echo isset($_GET['emp_id']) ? "emp_id=".$_GET['emp_id'] : "department_id=".$_GET['department_id'] ?>">Upload an image to the record for <i><?php echo isset($_GET['emp_id']) ? $employee['first_name']." ".$employee['last_name'] : $department['department_name'] ?></i></a></h3>
        <p><i>*Note that this action replaces the current image, if there is one.</i></p>
    </div>

    <?php if(isset($_GET['emp_id'])): ?>
        <h3>List of Department IDs with Names for Employee Record Form</h3>
        <ul>
            <?php while($row = $initial_statement->fetch()): ?>
                <li><?= $row['department_id'] ?>: <?= $row['department_name'] ?></li>
            <?php endwhile ?>
        </ul>
        <h3>Update Record</h3>
        <h4 id="current"><?= $employee['first_name']?> <?= $employee['last_name'] ?>'s Current Department: <?= $employee['department_id']?></h4>
    <?php endif ?>

    <?php if(!isset($_GET['emp_id']) && !isset($_GET['department_id'])): ?>
        <p>No record selected.</p>
    <?php else: ?>
            
        <?php if(isset($_GET['emp_id'])): ?>
            <form method="post" action="edit.php?emp_id=<?= $_GET['emp_id'] ?>">
                <label for="first_name">First Name: </label>
                <input id="first_name" name="first_name" value="<?= $employee['first_name'] ?>" size=35>
                <label for="last_name">Last Name: </label>
                <input id="last_name" name="last_name" value="<?= $employee['last_name'] ?>" size=35>
                <label for="tel_number">Phone Number: </label>
                <input type="tel" id="tel_number" name="tel_number" value="<?= $employee['tel_number'] ?>" size=35>
                <label for="email">Email: </label>
                <input type="email" id="email" name="email" value="<?= $employee['email'] ?>" size=35>
                <label for="department_id">Department ID: </label>
                <select id="department_id" name="department_id">
                    <option>Select a Department ID</option>
                    <?php while($department = $initial_statement2->fetch()): ?>
                        <option value="<?= $department['department_id'] ?>"><?= $department['department_id'] ?></option>
                    <?php endwhile ?>
                </select>
                <input type="submit" class="submit" value="Update Record">
            </form>

                <?php if($employee['image_file'] != null || !empty($employee['image_file'])): ?>
                    <h3>Current attached image</h3>
                    <p><img src="uploads/<?= $employee['image_file'] ?>" alt="<?= $employee['image_file'] ?>" title="<?= $employee['image_file'] ?>" height="300"></p>
                    <p><a href="image_removal.php?emp_id=<?= $employee['emp_id'] ?>">Remove image from this record</a></p>
                <?php endif ?>

            <form method="post" action="delete.php?emp_id=<?= $_GET['emp_id'] ?>" class="delete_record">
                <input type="submit" class="submit" value="Delete Record">
            </form>

            <h3>Comments</h3>
            <?php while($comments = $emp_comment_statement->fetch()): ?>
                <?php if($comments['emp_id'] == $_GET['emp_id']): ?>
                    <div class="comments">
                        <p><?= $comments['user'] ?></p>
                        <p><?= $comments['created'] ?></p>
                        <p><?= $comments['comment'] ?></p>
                        <form method="POST" action="details.php?emp_id=<?= $_GET['emp_id'] ?>&comm_id=<?= $comments['comm_id'] ?>">
                            <input type="submit" class="submit" name="delete_comment" value="Delete Comment">
                        </form>
                        <form method="POST" action="details.php?emp_id=<?= $_GET['emp_id'] ?>&comm_id=<?= $comments['comm_id'] ?>">
                            <input type="submit" class="submit" name="disemvowel_comment" value="Disemvowel Comment">
                        </form>
                    </div>
                <?php else: ?>
                    <p>No Comments for this record.</p>
                <?php endif ?>
            <?php endwhile ?>

        <?php elseif(isset($_GET['department_id'])): ?>
            <h3>Update Record</h3>
            <form method="post" action="edit.php?department_id=<?= $_GET['department_id'] ?>">
                <label for="department_name">Department Name: </label>
                <input id="department_name" name="department_name" value="<?= $department['department_name'] ?>" size=35>
                <label for="tel_number">Phone Number: </label>
                <input type="tel" id="tel_number" name="tel_number" value="<?= $department['tel_number'] ?>" size=35>
                <label for="email">Email: </label>
                <input type="email" id="email" name="email" value="<?= $department['email'] ?>" size=35>
                <input type="submit" class="submit" value="Update Record">
            </form>

                <?php if($department['image_file'] != null || !empty($department['image_file'])): ?>
                    <p><img src="uploads/<?= $department['image_file'] ?>" alt="<?= $department['image_file'] ?>" title="<?= $department['image_file'] ?>" height="300"></p>
                    <p><a href="image_removal.php?department_id=<?= $department['department_id'] ?>">Remove image from this record</a></p>
                <?php endif ?>

            <form method="post" action="delete.php?department_id=<?= $_GET['department_id'] ?>" class="delete_record">
                <input type="submit" class="submit" value="Delete Record">
            </form>

            <h3>Comments</h3>
            <?php while($comments = $dept_comment_statement->fetch()): ?>
                <?php if($comments['department_id'] == $_GET['department_id']): ?>
                    <div class="comments">
                        <p><?= $comments['user'] ?></p>
                        <p><?= $comments['created'] ?></p>
                        <p><?= $comments['comment'] ?></p>
                        <form method="POST" action="details.php?department_id=<?= $_GET['department_id'] ?>&comm_id=<?= $comments['comm_id'] ?>">
                            <input type="submit" class="submit" name="delete_comment" value="Delete Comment">
                        </form>
                        <form method="POST" action="details.php?department_id=<?= $_GET['department_id'] ?>&comm_id=<?= $comments['comm_id'] ?>">
                            <input type="submit" class="submit" name="disemvowel_comment" value="Disemvowel Comment">
                        </form>
                    </div>
                <?php else: ?>
                    <p>No Comments for this record.</p>
                <?php endif ?>
            <?php endwhile ?>
        <?php endif ?>
    <?php endif ?>
</body>
</html> 