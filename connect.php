<!-- Makes a connection to the database 'project'. -->
<?php
    define('DB_DSN','mysql:host=localhost;dbname=project;charset=utf8');
    define('DB_USER','serveruser');
    define('DB_PASS','gorgonzola7!');    
     
    try {

        // Try creating new PDO connection to MySQL.
        $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        
        // Force execution to stop on errors.
        die(); 
    }
?>