<?php
    if(!@require('connect.php')){
        require('connect.php');
    }

    if(isset($_POST['sort_request'])){
        $sort = $_POST['sort'];

        if($sort == "last_name"){
            $e_order = "last_name";
            $d_order = "department_name";

        } elseif($sort == "first_name"){
            $e_order = "first_name";
            $d_order = "department_name";

        } elseif($sort == "created_date"){
            $e_order = "created DESC";
            $d_order = "created DESC";

        } elseif($sort == "updated_date"){
            $e_order = "updated DESC";
            $d_order = "updated DESC";

        }

    // Default order by value
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
    <link rel="stylesheet" type="text/css" href="home.css" />
</head>
<body>
    <header>
        <h1><a href="index.php">VROAR Inc.</a> - <a href="login.php">Administration Home Page</a></h1>

        <form method="POST" action="login.php">
            <input type="submit" class="submit" name="logout" value="Logout">
        </form>

        <h3><a href="login_data.php">View Login Data</a></h3>
        <h3>Data Management</h3>
    </header>
    <div>
        <ul>
            <li><b>Create Data</b></li>
            <li><a href="create.php?type=employee">Create a new employee record</a></li>
            <li><a href="create.php?type=department">Create a new department record</a></li>
        </ul>
    </div>
    <form method="POST" action="locked.php">
        <label for="sort"></label>
        <select id="sort" name="sort">
            <option value="last_name">Order by last name</option>
            <option value="first_name">Order by first name</option>
            <option value="created_date">Order by most recent creation date</option>
            <option value="updated_date">Order by most recently updated</option>
        </select>
        <input type="submit" class="submit" value="Sort" name="sort_request">
    </form>
    <div>
        <?php if(isset($_POST['sort_request'])): ?>
            <p>Current sort applied: <?= $_POST['sort'] ?></p>
        <?php endif ?>
        <p><i>*Note that ordering by employee first or last name orders the departments by name as well.</i></p>
    </div>
    <div>
        <ul>
            <li><b>Edit or Delete a current employee record</b></li>
            <?php while($row = $statement->fetch()): ?>
                <li><a href="edit.php?emp_id=<?= $row['emp_id'] ?>"><?= $row['last_name'] ?>, <?= $row['first_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </div>
    <div>
        <ul>
            <li><b>Edit or Delete a current department record</b></li>
            <?php while($row = $statement2->fetch()): ?>
                <li><a href="edit.php?department_id=<?= $row['department_id'] ?>"><?= $row['department_name'] ?></a></li>
            <?php endwhile ?>
        </ul>
    </div>
</body>
</html> 