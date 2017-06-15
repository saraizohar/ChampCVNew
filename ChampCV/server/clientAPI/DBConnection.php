<?php

// Define DB connection parameters
$dsn = 'mysql:host=localhost;dbname=champcv';
$username = 'root';
$password = '';

try {
	// Set new connection and make it persistent
    $db = new PDO($dsn, $username, $password, array(PDO::ATTR_PERSISTENT => true));
	// Force PDO to throw exeptions in case of error
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Force PDO to convert empty string values into null
	$db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
	// Define default fetch mode
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} 
catch (PDOException $e) {
    echo json_encode(array("error_message"=>"Unable to connect: ".$e->getMessage()."<br>"));
    exit();
}
?>