<?php
set_time_limit(0);
require_once('DBConnection.php');

class changeDetailsUpdateDB{
	
	/*Parameters: $isRecruiter, $member_id, $email, $phone, $city, $company_name, $share_info.
	  Description: function is called when a regular user or a recruiter updates his/hers 
	  personal details in the personal zone. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateMemberDetails($isRecruiter, $member_id, $email, 
	$phone, $city, $company_name, $share_info){
		
		if ($isRecruiter == 1){
			//recruiter
			return changeDetailsUpdateDB::updateRecruiterDetails($member_id, $email, $phone, $company_name);
		}
		else{
			//regular user
			return changeDetailsUpdateDB::updateUserDetails($member_id, $email, $phone, $city, $share_info);
		}
		
	}
	
	/*Parameters: $user_id, $email, $phone, $city, $share_info.
	  Description: function updates user's personal details in users TABLE.  
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateUserDetails($user_id, $email, $phone, $city, $share_info){
		
		global $db;
		
		//set query
		$query_text = "UPDATE users SET city = :city, email = :email, phone = :phone, 
		share_info = :share_info WHERE user_id = :user_id";
			
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':city', $city);
			$query_statement->bindValue(':email', $email);
			$query_statement->bindValue(':phone', $phone);
			$query_statement->bindValue(':share_info', $share_info);
			$query_statement->bindValue(':user_id', $user_id);
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
	
	/*Parameters: $recruiter_id, $email, $phone, $company_name.
	  Description: function updates recruiter's personal details in recruiters TABLE.  
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateRecruiterDetails($recruiter_id, $email, $phone, $company_name){
		
		global $db;
		
		//set query
		$query_text = "UPDATE recruiters SET company_name = :company_name, email = :email, 
		phone = :phone WHERE recruiter_id = :recruiter_id";
			
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':company_name', $company_name);
			$query_statement->bindValue(':email', $email);
			$query_statement->bindValue(':phone', $phone);
			$query_statement->bindValue(':recruiter_id', $recruiter_id);
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
	
	/*Parameters: $user_id, $share_info. 
	  Description: function updates $share_info field in users TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateShareInfoInUsersTable($user_id, $share_info){
		
		global $db;
		
		//set query
		$query_text = "UPDATE users SET share_info = :share_info WHERE user_id = :user_id";
		
		try {
			//prepare query			
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':share_info', $share_info); 
			$query_statement->bindValue(':user_id', $user_id); 
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
	
	/*Parameters: $fieldsToGrade, $member_id, $isRecruiter. 
	  Description: function is called when a regular user or a recruiter updates 
	  at least one of fieldsToGrade in their personal zone. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateFieldsToGrade($fieldsToGrade, $member_id, $isRecruiter){
		
		global $db; 
		
		//regular user
		if ($isRecruiter == 0){
			//set query
			$query_text = "UPDATE users SET fullstack = :fullstack, frontend = :frontend, 
			backend = :backend, UX_UI = :UX_UI, BI = :BI, QA = :QA, DBA = :DBA, IT = :IT
			WHERE user_id = :member_id";
		
		}
		//recruiter
		else{
			//set query
			$query_text = "UPDATE recruiters SET fullstack = :fullstack, frontend = :frontend, 
			backend = :backend, UX_UI = :UX_UI, BI = :BI, QA = :QA, DBA = :DBA, IT = :IT
			WHERE recruiter_id = :member_id";
		}
		
		try {
			//prepare query			
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':fullstack', $fieldsToGrade->id_1);
			$query_statement->bindValue(':frontend', $fieldsToGrade->id_2);
			$query_statement->bindValue(':backend', $fieldsToGrade->id_3);
			$query_statement->bindValue(':UX_UI', $fieldsToGrade->id_4);
			$query_statement->bindValue(':BI', $fieldsToGrade->id_5);
			$query_statement->bindValue(':QA', $fieldsToGrade->id_6);
			$query_statement->bindValue(':DBA', $fieldsToGrade->id_7);
			$query_statement->bindValue(':IT', $fieldsToGrade->id_8); 
			$query_statement->bindValue(':member_id', $member_id); 
			
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