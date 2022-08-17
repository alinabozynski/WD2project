<!-- DELETES a specified record as per the user's request. -->
<?php
    require('connect.php');
    
    // DELETE record once user selects the delete button.
    if ($_POST && isset($_GET['emp_id'])) {
        // Sanitize $_GET['id'] to filter out dangerous characters.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Build and prepare the parameterized SQL query, then bind to the above sanitized values.
        $query = "DELETE FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        
        // Execute the INSERT.
        $statement->execute();
        
        // Redirect after update.
        header("Location: delete.php?emp_id={$emp_id}");
        exit;
        
    } elseif($_POST && isset($_GET['department_id'])) {
        // Sanitize $_GET['id'] to filter out dangerous characters.
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Build and prepare the parameterized SQL query, then bind to the above sanitized values.
        $query = "DELETE FROM departments WHERE department_id = :department_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);
        
        // Execute the INSERT.
        $statement->execute();
        
        // Redirect after update.
        header("Location: delete.php?department_id={$department_id}");
        exit;

    } elseif(isset($_GET['username'])){
        $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

        $query = "DELETE FROM logins WHERE username = :username LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();

        header("Location: delete.php?account=deleted");
        exit();

    // grab record to be deleted
    } elseif(isset($_GET['emp_id'])){
        // Sanitize $_GET['id'] to filter out dangerous characters.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Build and prepare the parameterized SQL query, then bind to the above sanitized values.
        $query = "DELETE FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        
        // Execute the INSERT.
        $statement->execute();

    } elseif(isset($_GET['department_id'])){
        // Sanitize $_GET['id'] to filter out dangerous characters.
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Build and prepare the parameterized SQL query, then bind to the above sanitized values.
        $query = "DELETE FROM departments WHERE department_id = :department_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);
        
        // Execute the INSERT.
        $statement->execute();
    } else{
        // When not DELETING
        $emp_id = false; 
        $department_id = false;
        $username = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Record</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="blog.css" />
</head>
<body>
    <section>
        <h1><a href="index.php">VROAR Inc.</a> - <?php if(isset($_GET['account'])){ echo "Account";} else {echo isset($_GET['emp_id']) ? "Employee Record" : "Department Record";} ?> Deleted</h1>
        <h3><a href="login.php">Administration Home Page</a></h3>
    </section>

    <h2>The selected record has been deleted successfully.</h2>
</body>
</html> 