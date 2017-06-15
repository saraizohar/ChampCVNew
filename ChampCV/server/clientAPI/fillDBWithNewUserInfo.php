<?php
set_time_limit(0);
require_once('DBConnection.php');
require_once('getDataFromDB.php');
date_default_timezone_set("Asia/Jerusalem");

class fillDBWithNewUserInfo{
	
	/*Parameters: $isRecruiter, $firstName, $lastName, $username, $email, $phoneNumber,
	  $city, $companyName, $password, $fieldsToGrade.
	  Description: function is called when a new user signs up.
	  Return: an array containing isRecruiter, userid, username. In case of an error,
	  an array containing a description of the error*/
	public static function getDataNewMember($isRecruiter, $firstName, $lastName, $username, 
	$email, $phoneNumber, $city, $companyName, $password, $fieldsToGrade){
		
		//check if this user name in the DB
		$result = getDataFromDB::existUserName($username);
		//check if an error accured
		if ($result != 0 and $result != 1){
			return $result; 
		}
		else if ($result == 1){
			$toReturn = array('error_message' => "username already exists in the DB");
			return $toReturn;
		}
		
		$last_login_time = date("Y-m-d H:i:s");  
		
		//INSERT INTO login TABLE
		$result = fillDBWithNewUserInfo::fillDBWithLoginInfo($username, $password, $last_login_time);
		
		//check if an error occured
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		$member_id = $result['member_id'];
		
		//check if member is user or recruiter
		if ($isRecruiter == 0){
			//member is a user
			$flag = fillDBWithNewUserInfo::fillDBWithUserDetails($member_id, $username, $firstName, $lastName, 
			$email, $phoneNumber, $city, $fieldsToGrade, $last_login_time);
			
			//check if an error occured
			if ($flag != null){
				return $flag; 
			}
		}
		else{
			//member is a recruiter
			$flag = fillDBWithNewUserInfo::fillDBWithRecruiterDetails($member_id, $username, $firstName, 
			$lastName, $email, $phoneNumber, $companyName, $fieldsToGrade, $last_login_time);
					
			//check if an error occured
			if ($flag != null){
				return $flag; 
			}
			
		}
		
		$toReturn = array('isRecruiter' => $isRecruiter, 'userid' => $result['member_id'], 
		'username' => $username, 'points' => 0);
			
		return $toReturn; 
		
	}
	
	/*Parameters: $member_id, $member_name, $first_name, $last_name, $email, $phone, 
	  $city, $fieldsToGrade, $last_login_time.
	  Description: function is called when the new member is a regular user.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithUserDetails($member_id, $member_name, $first_name, 
	$last_name, $email, $phone, $city, $fieldsToGrade, $last_login_time){
		
		$fullstack = $fieldsToGrade->id_1;
		$frontend = $fieldsToGrade->id_2;
		$backend = $fieldsToGrade->id_3;
		$UX_UI = $fieldsToGrade->id_4;
		$BI = $fieldsToGrade->id_5;
		$QA = $fieldsToGrade->id_6;
		$DBA = $fieldsToGrade->id_7;
		$IT = $fieldsToGrade->id_8;
		
		//INSERT INTO users TABLE
		$flag = fillDBWithNewUserInfo::fillDBWithUsersInfo($member_id, $member_name, $first_name,
		$last_name, $city, $email, $phone, $last_login_time, $fullstack, 
		$frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT);
		
		//check if an error occured
		if ($flag != null){
			return $flag; 
		}
			
		//INSERT INTO points_users TABLE
		$flag = fillDBWithNewUserInfo::fillDBWithPointsUsersInfo($member_id); 
			
		//check if an error occured
		if ($flag != null){
			return $flag; 
		}
		
		//no error, no error_message array to return
		return null; 
		
	}
	
	/*Parameters: $member_id, $member_name, $first_name, $last_name, $email, $phone, 
	  $company_name, $fieldsToGrade, $last_login_time.
	  Description: function is called when the new member is a recruiter.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithRecruiterDetails($member_id, $member_name, $first_name, 
	$last_name, $email, $phone, $company_name, $fieldsToGrade, $last_login_time){

		$fullstack = $fieldsToGrade->id_1;
		$frontend = $fieldsToGrade->id_2;
		$backend = $fieldsToGrade->id_3;
		$UX_UI = $fieldsToGrade->id_4;
		$BI = $fieldsToGrade->id_5;
		$QA = $fieldsToGrade->id_6;
		$DBA = $fieldsToGrade->id_7;
		$IT = $fieldsToGrade->id_8;
		
		//INSERT INTO recruiters TABLE
		$flag = fillDBWithNewUserInfo::fillDBWithRecruitersInfo($member_id, $member_name, $first_name,
		$last_name, $company_name, $email, $phone, $last_login_time, $fullstack, $frontend,
		$backend, $UX_UI, $BI, $QA, $DBA, $IT);
		
		//check if an error occured
		if ($flag != null){
			return $flag; 
		}
		
		//INSERT INTO points_recruiters TABLE
		$flag = fillDBWithNewUserInfo::fillDBWithPointsRecruitersInfo($member_id); 
			
		//check if an error occured
		if ($flag != null){
			return $flag; 
		}
		
		//no error, no error_message array to return
		return null; 
		
	}
	
	/*Parameters: $member_name, $password, $last_login_time. 
	  Description: function inserts data into login TABLE in the DB.
	  Return: an array containing member_id. In case of an error,
	  an array containing a description of the error*/
	public static function fillDBWithLoginInfo($member_name, $password, $last_login_time){
		
		global $db;
		
		//set query
		$query_text = "INSERT INTO login(member_name, password, last_login_time)
		VALUES(:member_name, :password, :last_login_time)";
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":member_name",$member_name);
			$query_statement->bindValue(":password",$password);
			$query_statement->bindValue(":last_login_time",$last_login_time);
			//execute query
			$query_statement->execute();
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		} 
		
		//get member_id 
		return getDataFromDB::getMemberIdByName($member_name);
	}
	
	/*Parameters: $user_id, $user_name, $first_name, $last_name, $city, $email, $phone,
	  $sign_up_time, $fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT.
	  Description: function inserts data into users TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithUsersInfo($user_id, $user_name, $first_name, $last_name,
	$city, $email, $phone, $sign_up_time, $fullstack, $frontend, $backend, $UX_UI, $BI,
	$QA, $DBA, $IT){
		
		global $db;
		
		//set query
		$query_text = "INSERT INTO users(user_id, user_name, first_name, last_name,
			city, email, phone, sign_up_time, fullstack, frontend, backend, UX_UI, 
			BI, QA, DBA, IT, share_info, amount_ranked, CV_uploaded_amount, 
			user_reliability) VALUES(:user_id, :user_name, :first_name, 
			:last_name, :city, :email, :phone, :sign_up_time, :fullstack, :frontend,
			:backend, :UX_UI, :BI, :QA, :DBA, :IT, :share_info, :amount_ranked,
			:CV_uploaded_amount, :user_reliability)";
		
		try {
			//prepare query			
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":user_id",$user_id);
			$query_statement->bindValue(":user_name",$user_name);
			$query_statement->bindValue(":first_name",$first_name);
			$query_statement->bindValue(":last_name",$last_name);
			$query_statement->bindValue(":city",$city);
			$query_statement->bindValue(":email",$email);
			$query_statement->bindValue(":phone",$phone);
			$query_statement->bindValue(":sign_up_time",$sign_up_time);
			$query_statement->bindValue(":fullstack",$fullstack);
			$query_statement->bindValue(":frontend",$frontend);
			$query_statement->bindValue(":backend",$backend);
			$query_statement->bindValue(":UX_UI",$UX_UI);
			$query_statement->bindValue(":BI",$BI);
			$query_statement->bindValue(":QA",$QA);
			$query_statement->bindValue(":DBA",$DBA);
			$query_statement->bindValue(":IT",$IT);
			$query_statement->bindValue(":share_info",0);
			$query_statement->bindValue(":amount_ranked",0);
			$query_statement->bindValue(":CV_uploaded_amount",0);
			$query_statement->bindValue(":user_reliability",1);
			//execute query
			$query_statement->execute();
		}
		
		catch(PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		} 
		
		//no error, no error_message array to return
		return null;
	}

	/*Parameters: $recruiter_id, $recruiter_name, $first_name, $last_name, $company_name,
	  $email, $phone, $sign_up_time, $fullstack, $frontend, $backend, $UX_UI, $BI, $QA,
	  $DBA, $IT.
	  Description: function inserts data into recruiters TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithRecruitersInfo($recruiter_id, $recruiter_name, $first_name,
	$last_name, $company_name, $email, $phone, $sign_up_time, $fullstack, $frontend, 
	$backend, $UX_UI, $BI, $QA, $DBA, $IT){
		
		global $db; 
		
		//set query
		$query_text = "INSERT INTO recruiters(recruiter_id, recruiter_name, 
		first_name, last_name, company_name, email, phone, sign_up_time, fullstack, 
		frontend, backend, UX_UI, BI, QA, DBA, IT, amount_ranked, recruiter_reliability)
		VALUES(:recruiter_id, :recruiter_name, :first_name, :last_name, :company_name, 
		:email, :phone, :sign_up_time, :fullstack, :frontend, :backend,	:UX_UI, :BI, 
		:QA, :DBA, :IT, :amount_ranked, :recruiter_reliability)";
		
		try {
			//prepare
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":recruiter_id",$recruiter_id);
			$query_statement->bindValue(":recruiter_name",$recruiter_name);
			$query_statement->bindValue(":first_name",$first_name);
			$query_statement->bindValue(":last_name",$last_name);
			$query_statement->bindValue(":company_name",$company_name);
			$query_statement->bindValue(":email",$email);
			$query_statement->bindValue(":phone",$phone);
			$query_statement->bindValue(":sign_up_time",$sign_up_time);
			$query_statement->bindValue(":fullstack",$fullstack);
			$query_statement->bindValue(":frontend",$frontend);
			$query_statement->bindValue(":backend",$backend);
			$query_statement->bindValue(":UX_UI",$UX_UI);
			$query_statement->bindValue(":BI",$BI);
			$query_statement->bindValue(":QA",$QA);
			$query_statement->bindValue(":DBA",$DBA);
			$query_statement->bindValue(":IT",$IT);
			$query_statement->bindValue(":amount_ranked",0);
			$query_statement->bindValue(":recruiter_reliability",1);
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

	/*Parameters: $recruiter_id.
	  Description: function inserts data into points_recruiters TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithPointsRecruitersInfo($recruiter_id){
		
		global $db; 
		
		//set query
		$query_text = "INSERT INTO points_recruiters(recruiter_id, current_amount,
		total_amount_ever) VALUES(:recruiter_id, :current_amount, :total_amount_ever)";
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":recruiter_id",$recruiter_id);
			$query_statement->bindValue(":current_amount",0);
			$query_statement->bindValue(":total_amount_ever",0);
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
	
	/*Parameters: $user_id.
	  Description: function inserts data into points_users TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithPointsUsersInfo($user_id){
		
		global $db;

		//set query
		$query_text = "INSERT INTO points_users(user_id, current_amount,
		total_amount_ever) VALUES(:user_id, :current_amount, :total_amount_ever)";
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":user_id",$user_id);
			$query_statement->bindValue(":current_amount",0);
			$query_statement->bindValue(":total_amount_ever",0);
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