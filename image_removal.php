<!-- Removes an image from a specific record as per the user's request. -->
<?php
    // Require the connection to the database for this page
    require('connect.php');
    
    // Remove image from the uploads folder and the selected record once user selects the image removal link on edit.php
    if (isset($_GET['emp_id'])) {
        // Sanitize $_GET['id'] to filter out dangerous characters.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

        // Fetch the row of the selected record
        $initial_query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $initial_statement = $db->prepare($initial_query);
        $initial_statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        $initial_statement->execute();
        $temp_employee = $initial_statement->fetch();

        // Remove image from the uploads folder 
        $path = "uploads/".$temp_employee['image_file'];
        unlink($path);

        // Remove image from database
        // Set new image_file value
        $image_file = null;
        
        // Build and prepare the parameterized SQL query, then bind to the above sanitized values.
        $query = "UPDATE employees SET image_file = :image_file WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':image_file', $image_file);
        $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        
        // Execute the UPDATE.
        $statement->execute();
        
        // Redirect after update.
        header("Location: details.php?emp_id={$_GET['emp_id']}");

        exit;
        
    } elseif(isset($_GET['department_id'])) {
        // Sanitize $_GET['id'] to filter out dangerous characters.
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

        // Fetch the row of the selected record
        $initial_query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $initial_statement = $db->prepare($initial_query);
        $initial_statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);
        $initial_statement->execute();
        $temp_department = $initial_statement->fetch();

        // Remove image from the uploads folder 
        $path = "uploads/".$temp_department['image_file'];
        unlink($path);

        // Remove image from database
        // Set new image_file value
        $image_file = null;
        
        // Build and prepare the parameterized SQL query, then bind to the above sanitized values.
        $query = "UPDATE departments SET image_file = :image_file WHERE department_id = :department_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':image_file', $image_file);
        $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);
        
        // Execute the INSERT.
        $statement->execute();
        
        // Redirect after update.
        header("Location: details.php?department_id={$_GET['department_id']}");

        exit;

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Record</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="blog.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <section>
        <h1><a href="index.php">VROAR Inc.</a> - <?php echo isset($_GET['emp_id']) ? "Employee" : "Department" ?> Record Image Removed</h1>
        <h3><a href="login.php">Administration Home Page</a></h3>
    </section>

    <h2>The image for the selected record has been removed successfully.</h2>
</body>
</html> 