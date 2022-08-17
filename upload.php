<?php
    require('connect.php');

    if(isset($_GET['emp_id'])){
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
        $initial_query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $initial_statement = $db->prepare($initial_query);
        $initial_statement->bindValue('emp_id', $emp_id);
        $initial_statement->execute();
        $employee = $initial_statement->fetch();

    } elseif(isset($_GET['department_id'])){
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
        $initial_query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $initial_statement = $db->prepare($initial_query);
        $initial_statement->bindValue('department_id', $department_id);
        $initial_statement->execute();
        $department = $initial_statement->fetch();
    }

    // file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
    // Default upload path is an 'uploads' sub-folder in the current folder.
    function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
       $current_folder = dirname(__FILE__);
       
       // Build an array of paths segment names to be joined using OS specific slashes.
       $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
       
       // The DIRECTORY_SEPARATOR constant is OS specific.
       return join(DIRECTORY_SEPARATOR, $path_segments);
    }

    // file_is_an_image() - Checks the mime-type & extension of the uploaded file for specific types
    function file_is_an_image($temporary_path, $new_path) {
        $allowed_mime_types      = ['image/jpeg', 'image/png', 'image/gif', 'iamge/apng', 'image/avif', 'image/svg+xml', 'image/webp'];
        $allowed_file_extensions = ['jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif', 'pdf', 'apng', 'avif', 'svg', 'webp'];
        
        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = mime_content_type($temporary_path);
        
        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
        
        return $file_extension_is_valid && $mime_type_is_valid;
    }
    
    $upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
    $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

    if ($upload_detected) { 
        $image_name          = $_FILES['image']['name'];
        $temporary_file_path = $_FILES['image']['tmp_name'];
        $new_file_path       = file_upload_path($image_name);

        if (file_is_an_image($temporary_file_path, $new_file_path)) {
            move_uploaded_file($temporary_file_path, $new_file_path);

            // Add image filename to the data for the appropriate record
            if(isset($_GET['emp_id'])){
                // Sanitize user input to escape HTML entities and filter out dangerous characters.
                $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
                $image_file = $image_name;

                // Build the parameterized SQL query and bind to the above sanitized values.
                $query = "UPDATE employees SET image_file = :image_file WHERE emp_id = :emp_id LIMIT 1";
                $statement = $db->prepare($query);
                $statement->bindValue(':image_file', $image_file);
                $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);

                // Execute the UPDATE statement.
                $statement->execute();

                // Redirect after update.
                header("Location: details.php?emp_id={$_GET['emp_id']}");

            } elseif(isset($_GET['department_id'])){
                $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
                $image_file = $image_name;

                // Build the parameterized SQL query and bind to the above sanitized values.
                $query = "UPDATE departments SET image_file = :image_file WHERE department_id = :department_id LIMIT 1";
                $statement = $db->prepare($query);
                $statement->bindValue(':image_file', $image_file);
                $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);

                // Execute the UPDATE statement.
                $statement->execute();

                // Redirect after update.
                header("Location: details.php?department_id={$_GET['department_id']}");

                // Grab record to be uploaded to (if id GET parameter exists in the URL).
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
            }
        } else{
            echo "Only image files can be uploaded. The accepted file extensions are: 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif', 'pdf', 'apng', 'avif', 'svg', 'webp'.";
        }
    }
?>
 <!DOCTYPE html>
 <html lang="en">
<head>
    <title>Image Upload</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="home.css" />
</head>
<body>
    <section>
        <h1><a href="index.php">VROAR Inc.</a> - Image Upload for <?php echo isset($_GET['emp_id']) ? $employee['first_name']." ".$employee['last_name']  : $department['department_name'] ?></h1>
        <h3><a href="login.php">Administration Home Page</a></h3>
    </section>

    <form method='post' enctype='multipart/form-data'>
        <label for='image'>Filename:</label>
        <input type='file' name='image' id='image'>
        <input type='submit' name='submit' value='Upload Image'>
    </form>
     
    <?php if ($upload_error_detected): ?>
        <p>Error Number: <?= $_FILES['file']['error'] ?></p>
    <?php endif ?>
</body>
</html>