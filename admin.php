<!-- Creates authentication to be used to access administrator actions on the CMS. -->
<?php 
	define('ADMIN_LOGIN','serveruser'); 
  	define('ADMIN_PASSWORD','gorgonzola7!'); 

  	if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) 
      || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN) 
      || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) { 
    	header('HTTP/1.1 401 Unauthorized'); 
    	header('WWW-Authenticate: Basic realm="VROAR Inc."'); 
    	exit("Access Denied: Username and password required."); 
  	}	 
?>