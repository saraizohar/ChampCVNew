<?php

require_once('./loginUpdateDB.php');

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    switch ($action){

        case "login":
            $username = $_POST["username"];
            $password = $_POST["password"];
			$result = loginUpdateDB::getDataExistingMemberLogin($username, $password);
			$temp = $result; 
			//check if an error occured or user entered wrong password
			if (key($result) == 'error_message'){
				break;
			}
			$isRecruiter = $temp['isRecruiter'] == 0 ? false : true; 
            $result = array('user' => array('name' => $temp['username'], 'cid' => 
			$temp['userid'], 'isRecruiter' => $isRecruiter, 'points' => $temp['points']));
            break;

    }

    echo (json_encode($result));
}
?>