# Web Development 2 Project - Content Management System
Used the basic AMP stack - Apache as the web server, MySQL/MariaDB for the database, and PHP for the programming language. \
A proposal was written for this project, including a name and description of a fictional business and why they require a CMS, as well as a description of the database with names of tables, why each table is required, and descriptions of associations between the tables. 

## Main Project Features
* Validations when creating/updating pages
* Sanitized strings retrieved from GET or POST parameters to prevent HTML injection attacks
* Images may be added/removed to pages when editting by way of form upload
* Only admins can perform CUD (create, update, delete) tasks
* Passwords are stored encrypted (hashed and salted)
* Admin services can be accessed by creating an account (or existing admin credentials to use to login are example / password)

## Configuring Xampp 
Download Xampp for Windows [here](https://www.apachefriends.org/download.html).

By default, Apache is configured to communicate using TCP/IP port 80, but if you already have an existing web server running on that port, change the port used by Apache.

To do this, click on the "config" button in the Xampp Control Panel, beside Apache under Actions.

Locate the line that reads 'Listen 80'. Change it to use an open port, such as 'Listen 31337'.

If you also already have something running on port 443, like VMWare, you will also need to locate the httpd-ssl.conf file (within the xampp install) and change it to listen on port 500 instead.

Now, in Xampp, click the start buttons next to Apache and MySQL. Here, Windows may trigger a firewall security prompt. If so, choose 'unblock'. This may require going into your firewall to verify the new rules, or make your own if you still have connection issues. BUT DO NOT SHUT OFF THE FIREWALL!

When Apache and MySQL are running without errors, load up the loopback address via your web-browser (http://localhost:31337/ or http://127.0.0.1:31337/) to access the Xampp home page.

Place projects in the XAMPP htdocs folder. They will be accessed through http://locahost:31337/folder_name or http://127.0.0.1:31337/folder_name.

## Run this application
1. Start up Xampp with the instructions above and enter PHPMyAdmin.
2. Navigate to the SQL tab and execute:
```
CREATE DATABASE serverside;
CREATE USER 'serveruser'@'localhost' IDENTIFIED BY 'gorgonzola7!';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER
  ON serverside.* TO 'serveruser'@'localhost';
```
3. Clone this repository into the htdocs folder of Xampp.
4. Execute the project SQL File that is within this repository.
5. In SQL again, execute:
```
grant all privileges on DATABASE_NAME.* to USERNAME@localhost identified by 'PASSWORD';
flush privileges;
```
7. Navigate to localhost/WD2project.

## Troubleshooting Help
1. If you receive the error message, 'Error: SQLSTATE[HY000] [1044] Access denied for user 'serveruser'@'localhost' to database ...' when navigating to the project, in the PHPMyAdmin Xampp page SQL, execute:
```
drop user'serveruser'@'localhost';
CREATE USER 'serveruser'@'localhost' IDENTIFIED BY 'gorgonzola7!';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER
  ON serverside.* TO 'serveruser'@'localhost';
grant all privileges on project.* to 'serveruser'@'localhost' IDENTIFIED BY 'gorgonzola7!';
flush privileges;
```
2. If you receive the error, "'Read page with wrong checksum' from storage engine Aria" within SQL at any point, try the following:
* Select 'mysql' database from the list of databases on PHPMyAdmin.
* Select all tables, then 'Repair Table' within the combobox at the bottom, and click 'Go' if necesssary.
* Retry SQL command. 
