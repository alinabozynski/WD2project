<!-- Uses the GET superglobal to grab the blog post the user requests to view. --> 
<?php    
    require('connect.php');

    $emp_commments_query = "SELECT * from emp_comments";
    $dept_comments_query = "SELECT * from dept_comments";

    $emp_comment_statement = $db->prepare($emp_commments_query);
    $dept_comment_statement = $db->prepare($dept_comments_query);

    $emp_comment_statement->execute();
    $dept_comment_statement->execute(); 

    // Check if comment form was submitted and if a comment has been entered.
    if(isset($_POST['add_comment']) && !empty($_POST['comment'])){

        if(isset($_GET['emp_id'])){
            // Sanitize user input to filter out dangerous characters and make sure 
            //     they are valid to enter into the SQL.
            $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            // Build and prepare the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO emp_comments (emp_id, comment) VALUES (:emp_id, :comment)";
            $statement = $db->prepare($query); 
            $statement->bindValue(':emp_id', $emp_id);
            $statement->bindValue(':comment', $comment);

            // Execute the INSERT statement.
            $statement->execute(); 

            // Display message to show the user that the record addition was successful.
                
            header("Location: details.php?emp_id={$_GET['emp_id']}");

        } elseif(isset($_GET['department_id'])){
            // Sanitize user input to filter out dangerous characters and make sure 
            //     they are valid to enter into the SQL.
            $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            // Build and prepare the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO dept_comments (department_id, comment) VALUES (:department_id, :comment)";
            $statement = $db->prepare($query); 
            $statement->bindValue(':department_id', ucfirst($department_id));
            $statement->bindValue(':comment', $comment);

            // Execute the INSERT statement.
            $statement->execute(); 

            // Display message to show the user that the record addition was successful.
            
            header("Location: details.php?department_id={$_GET['department_id']}");
        }

    } elseif(isset($_POST['add_comment'])){

        echo "ATTENTION: Comment could not be added. Please ensure the comment is not blank.";
    }    

    if(isset($_GET['emp_id'])){
        // Sanitize the id GET parameter.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare SQL String with :id placeholder parameter.
        // LIMIT selects only 1 record 
        $query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);

        // Bind the :id parameter in the query to the sanitized id value.
        // $id specifies an Integer binding-type.
        $statement->bindValue('emp_id', $emp_id, PDO::PARAM_INT);

        // Execute the SELECT statement.
        $statement->execute();

        // Fetch the row selected by primary key id.
        $employee = $statement->fetch();

        // Retrieve the department name instead of the ID number (to display to users)
        $department_id = $employee['department_id'];

        // Build and prepare SQL String with :id placeholder parameter.
        // LIMIT selects only 1 record 
        $query2 = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $statement2 = $db->prepare($query2);

        // Bind the :id parameter in the query to the sanitized id value.
        // $id specifies an Integer binding-type.
        $statement2->bindValue('department_id', $department_id, PDO::PARAM_INT);

        // Execute the SELECT statement.
        $statement2->execute();

        // Fetch the row selected by primary key id.
        $department = $statement2->fetch();

    } else if(isset($_GET['department_id'])){
        // Sanitize the id GET parameter.
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare SQL String with :id placeholder parameter.
        // LIMIT selects only 1 record 
        $query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $query2 = "SELECT * FROM employees WHERE department_id = :department_id";

        $statement = $db->prepare($query);
        $statement2 = $db->prepare($query2);

        // Bind the :id parameter in the query to the sanitized id value.
        // $id specifies an Integer binding-type.
        $statement->bindValue('department_id', $department_id);
        $statement2->bindValue('department_id', $department_id);

        // Execute the SELECT statement.
        $statement->execute();
        $statement2->execute();

        // Fetch the row selected by primary key id.
        $department = $statement->fetch();
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php if(isset($_GET['emp_id'])): ?>
    <head>
        <title><?= $employee['first_name'] ?> <?= $employee['last_name'] ?></title>
        <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="blog.css" />
    </head>
    <body>
        <section>
            <h1><a href="index.php">VROAR Inc.</a> - <?= $employee['first_name'] ?> <?= $employee['last_name'] ?></h1>
            <h3><a href="index.php">Home</a></h3>
            <h3><a href="login.php">📝</a></h3>
        </section>
        <div>
            <p><b>Employee Name: </b><?= $employee['first_name'] ?> <?= $employee['last_name'] ?></p>
            <p><b>Department: </b><?= $department['department_name'] ?></p>
            <p><b>Phone: </b><?= $employee['tel_number'] ?></p>
            <p><b>Email: </b><?= $employee['email'] ?></p>
            <p><b>Record Updated On: </b><?= date_format(date_create($employee['updated']), "F d, Y, g:i a") ?></p>
            <?php if($employee['image_file'] != null || !empty($employee['image_file'])): ?>
                <p><img src="uploads/<?= $employee['image_file'] ?>" alt="<?= $employee['image_file'] ?>" title="<?= $employee['image_file'] ?>" height="300"></p>
            <?php endif ?>
        </div>

        <form method="POST" action="details.php?emp_id=<?= $_GET['emp_id'] ?>">
            <h3>Add comments about this employee</h3>
            <label for="comment">Comments</label>
            <input type="text" id="comment" name="comment">
            <input type="submit" class="submit" name="add_comment" value="Add Comment">
        </form>

        <?php while($comments = $emp_comment_statement->fetch()): ?>
            <?php if($comments['emp_id'] == $_GET['emp_id']): ?>
                <div>
                    <p><?= $comments['comment'] ?></p>
                    <p><?= $comments['created'] ?></p>
                </div>
            <?php endif ?>
        <?php endwhile ?>

    </body>
    
<?php elseif(isset($_GET['department_id'])): ?>

    <head>
        <title><?= $department['department_name'] ?></title>
        <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="blog.css" />
    </head>
    <body>
        <section>
            <h1><a href="index.php">VROAR Inc.</a></h1>
            <h1>- <?= $department['department_name'] ?></h1>
            <h3><a href="index.php">Home</a></h3>
        </section>
        <div>
            <p><b>Department Name: </b><?= $department['department_name'] ?></p>
            <p><b>Phone: </b><?= $department['tel_number'] ?></p>
            <p><b>Email: </b><?= $department['email'] ?></p>
            <p><b>Record Updated On: </b><?= date_format(date_create($department['updated']), "F d, Y, g:i a") ?></p>
            <?php if($department['image_file'] != null || !empty($department['image_file'])): ?>
                <p><img src="uploads/<?= $department['image_file'] ?>" alt="<?= $department['image_file'] ?>" title="<?= $department['image_file'] ?>" height="300"></p>
            <?php endif ?>
        </div>

        <h3>Employees in this department:</h3>
        <div>
            <?php while($employee_info = $statement2->fetch()): ?>
                <p><a href="details.php?emp_id=<?= $employee_info['emp_id'] ?>"><?= $employee_info['first_name'] ?> <?= $employee_info['last_name'] ?></a></p>
            <?php endwhile ?>
        </div>

        <form method="POST" action="details.php?department_id=<?= $_GET['department_id'] ?>">
            <h3>Add comments about this department</h3>
            <label for="comment">Comments</label>
            <input type="text" id="comment" name="comment">
            <input type="submit" class="submit" name="add_comment" value="Add Comment">
        </form>

        <?php while($comments = $dept_comment_statement->fetch()): ?>
            <?php if($comments['department_id'] == $_GET['department_id']): ?>
                <div>
                    <p><?= $comments['comment'] ?></p>
                    <p><?= $comments['created'] ?></p>
                </div>
            <?php endif ?>
        <?php endwhile ?>

    </body>
    
<?php endif ?>
</html> 