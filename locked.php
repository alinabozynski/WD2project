<!-- The Admin landing page after logging in. --> 
<?php
    // Require the connection to the database if it does not exist already (while exploring the website and coming back to this page)
    if(!@require('connect.php')){
        require('connect.php');
    }

    // If the user has entered a sort to be used for the data display
    if(isset($_POST['sort_request'])){
        $sort = $_POST['sort'];

        // Sort by last_name
        if($sort == "last_name"){
            $e_order = "last_name";
            $d_order = "department_name";

        // Sort by first_name
        } elseif($sort == "first_name"){
            $e_order = "first_name";
            $d_order = "department_name";

        // Sort by the creation date of the records
        } elseif($sort == "created_date"){
            $e_order = "created DESC";
            $d_order = "created DESC";

        // Sort by the last updated date of the records
        } elseif($sort == "updated_date"){
            $e_order = "updated DESC";
            $d_order = "updated DESC";

        }

    // Specify the default ORDER BY value
    } elseif(!isset($_POST['sort_request'])) {
        $e_order = "last_name";
        $d_order = "department_name";
    }

        // Build SQL String and prepare PDO::Statement from the query.
        $query = "SELECT * FROM employees ORDER BY $e_order";
        $query2 = "SELECT * FROM departments ORDER BY $d_order";

        $statement = $db->prepare($query);
        $statement2 = $db->prepare($query2);

        // Execute() on the DB server.
        $statement->execute(); 
        $statement2->execute();
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin</title>
    <link href='https://fonts.googleapis.com/css2?family=Rubik+Moonrocks&display=swap&family=Shadows+Into+Light&family=Space+Mono' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="locked.css" />
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a></h1>
        <h1><a href="login.php">Admin Home Page</a></h1>
        <h3><a href="login_data.php">View Login Data</a></h3>
        <form method="POST" action="login.php">
            <input type="submit" class="submit" name="logout" value="Logout">
        </form>
    </header>

    <h2>Data Management</h2>
    <div>
        <h3><b>Enter Data</b></h3>
        <ul>
            <li><a href="create.php?type=employee">Create a new employee record</a></li>
            <li><a href="create.php?type=department">Create a new department record</a></li>
        </ul>
    </div>

    <h2>Editting Data</h2>
    <form method="POST" action="locked.php" id="sort_form">
        <select id="sort" name="sort">
            <option value="last_name">Order by last name</option>
            <option value="first_name">Order by first name</option>
            <option value="created_date">Order by most recent creation date</option>
            <option value="updated_date">Order by most recently updated</option>
        </select>
        <input type="submit" class="submit" value="Sort" name="sort_request" id="sort_submit">
    </form>
    <div>
        <?php if(isset($_POST['sort_request'])): ?>
            <p>Current sort applied: <?= $_POST['sort'] ?></p>
        <?php endif ?>
        <p><i>*Note that ordering by employee first or last name orders the departments by name as well.</i></p>
    </div>
    <section>
        <h3><b>Edit or Delete a current employee record and comments</b></h3>
        <ul>
            <?php while($row = $statement->fetch()): ?>
                <li><a href="edit.php?emp_id=<?= $row['emp_id'] ?>"><?= $row['last_name'] ?>, <?= $row['first_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>
    <section>
        <h3><b>Edit or Delete a current department record and comments</b></h3>
        <ul>
            <?php while($row = $statement2->fetch()): ?>
                <li><a href="edit.php?department_id=<?= $row['department_id'] ?>"><?= $row['department_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </section>
</body>
</html> 