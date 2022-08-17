<!-- Uses the GET superglobal to grab the blog post the user requests to view. --> 
<?php    
    require('connect.php');

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
            <?php if($employee['image_file'] != null): ?>
                <p><img src="uploads/<?= $employee['image_file'] ?>" alt="<?= $employee['image_file'] ?>" title="<?= $employee['image_file'] ?>" height="300"></p>
            <?php endif ?>
        </div>
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
            <?php if($department['image_file'] != null): ?>
                <p><img src="uploads/<?= $department['image_file'] ?>" alt="<?= $department['image_file'] ?>" title="<?= $department['image_file'] ?>" height="300"></p>
            <?php endif ?>
        </div>

        <h3>Employees in this department:</h3>
        <div>
            <?php while($employee_info = $statement2->fetch()): ?>
                <p><a href="details.php?emp_id=<?= $employee_info['emp_id'] ?>"><?= $employee_info['first_name'] ?> <?= $employee_info['last_name'] ?></a></p>
            <?php endwhile ?>
        </div>
    </body>
<?php endif ?>
</html> 