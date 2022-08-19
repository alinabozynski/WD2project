<!-- Uplaods an image to the correct record and redirects the user back to the details.php page for that record to see the change. -->
<?php
    // Require the connection to the database for this page
    require('connect.php');

    // Grab a name to specify where the image is getting uploaded to 
    if(isset($_GET['emp_id'])){
        // Sanitize the emp_id retrieved with GET to use in the query 
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $initial_query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $initial_statement = $db->prepare($initial_query);
        $initial_statement->bindValue('emp_id', $emp_id, PDO::PARAM_INT);

        // Perform the SELECT
        $initial_statement->execute();

        // Fetch the result
        $employee = $initial_statement->fetch();

    } elseif(isset($_GET['department_id'])){
        // Sanitize the department_id retrieved with GET to use in the query 
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $initial_query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $initial_statement = $db->prepare($initial_query);
        $initial_statement->bindValue('department_id', $department_id, PDO::PARAM_INT);

        // Perform the SELECT
        $initial_statement->execute();

        // Fetch the result
        $department = $initial_statement->fetch();
    }

    // Builds a path String that uses appropriate separators for the OS.
    // Default upload path is set to an 'uploads' sub-folder in the current folder.
    function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
        // Retrieve the current folder 
        $current_folder = dirname(__FILE__);
       
        // Build an array of paths segments to be joined using OS specific separators.
        $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
       
        return join(DIRECTORY_SEPARATOR, $path_segments);
    }

    // Checks the mime-type & extension of the uploaded file to see if it is appropriate
    function file_is_an_image($temporary_path, $new_path) {
        // Specify appropriate mime types and file extensions 
        $allowed_mime_types      = ['image/jpeg', 'image/png', 'image/gif', 'iamge/apng', 'image/avif', 'image/svg+xml', 'image/webp'];
        $allowed_file_extensions = ['jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif', 'pdf', 'apng', 'avif', 'svg', 'webp'];
        
        // Retrieve the actual file extension and mime type
        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = mime_content_type($temporary_path);
        
        // Check is actual file extension and mime type exist in the allowed types and extensions arrays
        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
        
        // Return a Boolean value to determine if the uploaded file is appropriate
        return $file_extension_is_valid && $mime_type_is_valid;
    }
    
    $upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
    $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

    if ($upload_detected) { 
        $image_name          = $_FILES['image']['name'];
        $temporary_file_path = $_FILES['image']['tmp_name'];
        $new_file_path       = file_upload_path($image_name);

        // If the uploaded file is an image, move it to the folder specified in the file_upload_path function
        if (file_is_an_image($temporary_file_path, $new_file_path)) {
            move_uploaded_file($temporary_file_path, $new_file_path);

            // Add image filename to the data for the appropriate record
            if(isset($_GET['emp_id'])){
                // Sanitize user input to escape HTML entities and filter out dangerous characters.
                $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

                // Build the parameterized SQL query and bind to the above sanitized values.
                $query = "UPDATE employees SET image_file = :image_name WHERE emp_id = :emp_id LIMIT 1";
                $statement = $db->prepare($query);
                $statement->bindValue(':image_name', $image_name,PDO::PARAM_STR);
                $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);

                // Execute the UPDATE statement.
                $statement->execute();

                // Redirect after update.
                header("Location: details.php?emp_id={$_GET['emp_id']}");

            } elseif(isset($_GET['department_id'])){
                // Sanitize user input to escape HTML entities and filter out dangerous characters.
                $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

                // Build the parameterized SQL query and bind to the above sanitized values.
                $query = "UPDATE departments SET image_file = :image_name WHERE department_id = :department_id LIMIT 1";
                $statement = $db->prepare($query);
                $statement->bindValue(':image_name', $image_name, PDO::PARAM_STR);
                $statement->bindValue(':department_id', $department_id, PDO::PARAM_INT);

                // Execute the UPDATE statement.
                $statement->execute();

                // Redirect after update.
                header("Location: details.php?department_id={$_GET['department_id']}");

            }

        // If a file other than an image was uploaded, display an error message.
        } else {
            echo "Only image files can be uploaded. The accepted file extensions are: 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif', 'pdf', 'apng', 'avif', 'svg', 'webp'.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Image Upload</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="upload.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1> 
        <h1 id="middle">Image Upload for <?php echo isset($_GET['emp_id']) ? $employee['first_name']." ".$employee['last_name']  : $department['department_name'] ?></h1>
        <h1><a href="login.php">📝</a></h1>
    </header>

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