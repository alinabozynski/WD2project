<!-- Uses the GET superglobal to retrieve the record the user requests to view. --> 
<?php    
    // Require the connection to the database for this page
    require('connect.php');

    // Retrieve both comments table data to display any matches for the selected record
    $emp_commments_query = "SELECT * from emp_comments";
    $dept_comments_query = "SELECT * from dept_comments";
    $emp_comment_statement = $db->prepare($emp_commments_query);
    $dept_comment_statement = $db->prepare($dept_comments_query);
    $emp_comment_statement->execute();
    $dept_comment_statement->execute(); 

    // Upon request from the edit page, delete a comment from a specific record (page) to display an updated record
    if(isset($_POST['delete_comment']) && isset($_GET['comm_id']) && isset($_GET['emp_id'])){
        // Sanitize $_GET['comm_id'] to use in a query
        $comm_id = filter_input(INPUT_GET, 'comm_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $query = "DELETE FROM emp_comments WHERE comm_id = :comm_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);

        // Perform the DELETE
        $statement->execute();

        // Redirect after delete. 
        header("Location: details.php?emp_id={$_GET['emp_id']}");

        exit();

    } elseif(isset($_POST['delete_comment']) && isset($_GET['comm_id']) && isset($_GET['department_id'])){
        // Sanitize $_GET['comm_id'] to use in a query
        $comm_id = filter_input(INPUT_GET, 'comm_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $query = "DELETE FROM dept_comments WHERE comm_id = :comm_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);

        // Perform the DELETE
        $statement->execute();

        // Redirect after delete. 
        header("Location: details.php?department_id={$_GET['department_id']}");

        exit();
    } 

    // Upon request from the edit page, disemvowel a comment from a specific record (page) to display an updated record
    if(isset($_POST['disemvowel_comment']) && isset($_GET['comm_id']) && isset($_GET['emp_id'])){
        // Sanitize $_GET['comm_id'] to use in a query
        $comm_id = filter_input(INPUT_GET, 'comm_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized value to SELECT the comment to disemvowel.
        $query = "SELECT * FROM emp_comments WHERE comm_id = :comm_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);
        $statement->execute();
        $info = $statement->fetch();
        $comment = $info['comment'];

        // Disemvowel the retrieved comment
        $disemvoweled = str_replace(array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'), '', $comment);

        // UPDATE the current comment value to the disemvoweled version 
        $new_query = "UPDATE emp_comments SET comment = :disemvoweled WHERE comm_id = :comm_id LIMIT 1";
        $new_statement = $db->prepare($new_query);
        $new_statement->bindValue(':disemvoweled', $disemvoweled, PDO::PARAM_STR);
        $new_statement->bindValue(':comm_id', $comm_id);
        $new_statement->execute();

        // Redirect after update. 
        header("Location: details.php?emp_id={$_GET['emp_id']}");

        exit();

    } elseif(isset($_POST['disemvowel_comment']) && isset($_GET['comm_id']) && isset($_GET['department_id'])) {
        // Sanitize $_GET['comm_id'] to use in a query
        $comm_id = filter_input(INPUT_GET, 'comm_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare the parameterized SQL query and bind to the above sanitized values.
        $query = "SELECT * FROM dept_comments WHERE comm_id = :comm_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);
        $statement->execute();
        $info = $statement->fetch();
        $comment = $info['comment'];

        // Disemvowel the retrieved comment
        $disemvoweled = str_replace(array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'), '', $comment);

        // UPDATE the current comment value to the disemvoweled version 
        $new_query = "UPDATE dept_comments SET comment = :disemvoweled WHERE comm_id = :comm_id LIMIT 1";
        $new_statement = $db->prepare($new_query);
        $new_statement->bindValue(':disemvoweled', $disemvoweled, PDO::PARAM_STR);
        $new_statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);
        $new_statement->execute();

        // Redirect after update. 
        header("Location: details.php?department_id={$_GET['department_id']}");

        exit();

    // Grab the comment record to be editted / deleted
    } elseif(isset($_GET['comm_id']) && isset($_GET['emp_id'])){
        $comm_id = filter_input(INPUT_GET, 'comm_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "SELECT * FROM emp_comments WHERE comm_id = :comm_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);
        $statement->execute();
    } elseif(isset($_GET['comm_id']) && isset($_GET['department_id'])){
        $comm_id = filter_input(INPUT_GET, 'comm_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "SELECT * FROM dept_comments WHERE comm_id = :comm_id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':comm_id', $comm_id, PDO::PARAM_INT);
        $statement->execute();
    }

    // Check if a new comment was submitted and if a comment has been entered.
    if(isset($_POST['add_comment']) && !empty($_POST['comment'])){

        // If the comment is for an employee record
        if(isset($_GET['emp_id'])){
            // Sanitize user input to filter out dangerous characters and make sure 
            //     they are valid to enter into the SQL.
            $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            // Build and prepare the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO emp_comments (emp_id, comment) VALUES (:emp_id, :comment)";
            $statement = $db->prepare($query); 
            $statement->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
            $statement->bindValue(':comment', $comment, PDO::PARAM_STR);

            // Execute the INSERT statement.
            $statement->execute(); 

            // Redirect after insert.                    
            header("Location: details.php?emp_id={$_GET['emp_id']}");

        // If the comment is for a department record
        } elseif(isset($_GET['department_id'])){
            // Sanitize user input to filter out dangerous characters and make sure 
            //     they are valid to enter into the SQL.
            $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            // Build and prepare the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO dept_comments (department_id, comment) VALUES (:department_id, :comment)";
            $statement = $db->prepare($query); 
            $statement->bindValue(':department_id', ucfirst($department_id),PDO::PARAM_INT);
            $statement->bindValue(':comment', $comment, PDO::PARAM_STR);

            // Execute the INSERT statement.
            $statement->execute(); 

            // Redirect after insert. 
            header("Location: details.php?department_id={$_GET['department_id']}");
        }

        // If comment field is empty upon submit, display an error message.
    } elseif(isset($_POST['add_comment']) && empty($_POST['comment'])){
        echo "ATTENTION: Comment could not be added. Please ensure the comment is not blank.";
    } 

    // Grab the employee or department record to be displayed
    if(isset($_GET['emp_id'])){
        // Sanitize the emp_id GET parameter.
        $emp_id = filter_input(INPUT_GET, 'emp_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare SQL String with :emp_id placeholder parameter.
        // LIMIT selects only 1 record 
        $query = "SELECT * FROM employees WHERE emp_id = :emp_id LIMIT 1";
        $statement = $db->prepare($query);

        // Bind the :emp_id parameter in the query to the sanitized emp_id value.
        $statement->bindValue('emp_id', $emp_id, PDO::PARAM_INT);

        // Execute the SELECT statement.
        $statement->execute();

        // Fetch the row selected by primary key id.
        $employee = $statement->fetch();

        // Retrieve the department name instead of the ID number (to display to users)
        $department_id = $employee['department_id'];
        $query2 = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";
        $statement2 = $db->prepare($query2);
        $statement2->bindValue('department_id', $department_id, PDO::PARAM_INT);
        $statement2->execute();
        $department = $statement2->fetch();

    } else if(isset($_GET['department_id'])){
        // Sanitize the id GET parameter.
        $department_id = filter_input(INPUT_GET, 'department_id', FILTER_SANITIZE_NUMBER_INT);

        // Build and prepare SQL String with :department_id placeholder parameter.
        $query = "SELECT * FROM departments WHERE department_id = :department_id LIMIT 1";

        // Retrieve the employees that work in the selected department
        $query2 = "SELECT * FROM employees WHERE department_id = :department_id";

        $statement = $db->prepare($query);

        // This will be looped through in the HTML
        $statement2 = $db->prepare($query2);

        // Bind the :department_id parameter in the query to the sanitized department_id value.
        $statement->bindValue('department_id', $department_id, PDO::PARAM_INT);
        $statement2->bindValue('department_id', $department_id, PDO::PARAM_INT);

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
        <link rel="stylesheet" type="text/css" href="details.css" />
    </head>
    <body>
        <header>
            <h1><a href="index.php">VROAR Inc.</a></h1>
            <h1 id="middle"><?= $employee['first_name'] ?> <?= $employee['last_name'] ?></h1>
            <h1><a href="login.php">📝</a></h1>
        </header>

        <h3>Employee Information</h3>

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

        <h3>Comments</h3>

        <form method="POST" action="details.php?emp_id=<?= $_GET['emp_id'] ?>">
            <h3>Add a comment about this employee</h3>
            <textarea rows=3 cols=110 id="comment" name="comment" value="<?php echo isset($_POST['comment']) ? $_POST['comment'] : ''; ?>"></textarea>
            <input type="submit" class="submit" name="add_comment" value="Add Comment">
        </form>

        <?php while($comments = $emp_comment_statement->fetch()): ?>
            <?php if($comments['emp_id'] == $_GET['emp_id']): ?>
                <div class="comments">
                    <p>Posted on: <?= $comments['created'] ?></p>
                    <p><?= $comments['comment'] ?></p>
                </div>
            <?php endif ?>
        <?php endwhile ?>
    </body>
    
<?php elseif(isset($_GET['department_id'])): ?>

    <head>
        <title><?= $department['department_name'] ?></title>
        <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="details.css" />
    </head>
    <body>
        <header>
            <h1><a href="index.php">VROAR Inc.</a></h1>
            <h1 id="middle"><?= $department['department_name'] ?></h1>
            <h1><a href="login.php">📝</a></h1>
        </header>

        <h3>Department Information</h3>

        <div>
            <p><b>Department Name: </b><?= $department['department_name'] ?></p>
            <p><b>Phone: </b><?= $department['tel_number'] ?></p>
            <p><b>Email: </b><?= $department['email'] ?></p>
            <p><b>Record Updated On: </b><?= date_format(date_create($department['updated']), "F d, Y, g:i a") ?></p>
            <?php if($department['image_file'] != null || !empty($department['image_file'])): ?>
                <p><img src="uploads/<?= $department['image_file'] ?>" alt="<?= $department['image_file'] ?>" title="<?= $department['image_file'] ?>" height="300"></p>
            <?php endif ?>
        </div>

        <h3 id="emp_title">Employees in this department:</h3>

        <div id="employees">
            <?php while($employee_info = $statement2->fetch()): ?>
                <p><a href="details.php?emp_id=<?= $employee_info['emp_id'] ?>"><?= $employee_info['first_name'] ?> <?= $employee_info['last_name'] ?></a></p>
            <?php endwhile ?>
        </div>

        <h3>Comments</h3>

        <form method="POST" action="details.php?department_id=<?= $_GET['department_id'] ?>">
            <h3>Add a comment about this department</h3>
            <textarea rows=3 cols=110 id="comment" name="comment" value="<?php echo isset($_POST['comment']) ? $_POST['comment'] : ''; ?>"></textarea>
            <input type="submit" class="submit" name="add_comment" value="Add Comment">
        </form>

        <?php while($comments = $dept_comment_statement->fetch()): ?>
            <?php if($comments['department_id'] == $_GET['department_id']): ?>
                <div class="comments">
                    <p>Posted on: <?= $comments['created'] ?></p>
                    <p><?= $comments['comment'] ?></p>
                </div>
            <?php endif ?>
        <?php endwhile ?>
    </body>
    
<?php endif ?>
</html> 