<?php
set_time_limit(0);
require_once('./DBConnection.php');
require_once('./getDataFromDB.php');
date_default_timezone_set("Asia/Jerusalem");

class loginUpdateDB{
	
	const reliabilityLimit = 0.2; 
	
	/*Parameters: $username, $password.
	  Description: function is called when an existing user logs in.
	  Return: an array containing isRecruiter, userid and username. In case of an error,
	  an array containing a description of the error*/
	public static function getDataExistingMemberLogin($username, $password){
		
		$last_login_time = date("Y-m-d H:i:s"); 
		
		//check if this username exists in the DB
		$result = getDataFromDB::existUserName($username);
		if ($result != 0 and $result != 1){
			return $result; 
		}
		else if ($result == 0){
			$toReturn = array('error_message' => "username doesn't exist");
			return $toReturn; 
		}
		
		//check if user should be blocked
		$result = getDataFromDB::getMemberReliability($username);
		//check if an error accured 
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		$reliability = $result['reliability'];
		if ($reliability < loginUpdateDB::reliabilityLimit){
			$toReturn = array('error_message' => "user blocked");
			return $toReturn; 
		}
		
		$result = getDataFromDB::confirmPassword($username, $password); 
		
		//check if an error occured or user entered wrong password
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		//update last_login_time 
		$flag = loginUpdateDB::updateLoginTime($username, $last_login_time);
		
		//check if an error occured
		if ($flag != null){
			return $flag; 
		}
		
		$userid = $result['member_id'];
		$isRecruiter = getDataFromDB::isRecruiter($userid);
		
		//check if an error accured
		if ($isRecruiter != 1 and $isRecruiter != 0){
			return $isRecruiter; 
		}
		
		if ($isRecruiter == 0){
			//regular user
			$points = 0; 
		}
		else {
			//recruiter
			$flag = getDataFromDB::getPointsMember($userid, 1);
			//check if an error occured
			if (key($result) == 'error_message'){
				return $result; 
			}
			$points = $flag['current_amount'];
		}
			
		$toReturn = array('isRecruiter' => $isRecruiter, 'userid' => $userid,
		'username' => $username, 'points' => $points);
		
		//return an array with user id and name from
		return $toReturn;
		
	}
	
	/*Parameters: $member_name, $last_login_time.
	  Description: function updates login time in login TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateLoginTime($member_name, $last_login_time){
		
		global $db; 
		
		//set query
		$query_text = "UPDATE login SET last_login_time = :last_login_time WHERE
		member_name = :member_name";
		
		try{
			
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':last_login_time', $last_login_time); 
			$query_statement->bindValue(':member_name', $member_name); 
			//execute query
			$query_statement->execute();
		
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		} 
		
		//no error, no error_message array to return
		return null; 
		
	}
}


?>