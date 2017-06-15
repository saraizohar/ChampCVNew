<?php
set_time_limit(0);
require_once('DBConnection.php');

class getDataFromDB{
	
	/*Parameters: $member_name.
	  Description: function finds the member's id by the member_name.  
	  Return: an array containing member_id. In case of an error,
	  an array containing a description of the error*/
	public static function getMemberIdByName($member_name){
		
		global $db; 
		
		//set query
		$query_text = "SELECT member_id FROM login WHERE member_name = :member_name";
				
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_name', $member_name); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		} 
		
		return $result; 
	}
	
	/*Parameters: $cv_id.
	  Description: function finds the user's id by his/hers cv_id. 
	  Return: an array containing member_id. In case of an error,
	  an array containing a description of the error*/
	public static function getUserIdByCVId($cv_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT user_id FROM cvs WHERE cv_id = :cv_id";
				
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':cv_id', $cv_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		} 
		
		return $result; 
	}
	
	/*Parameters: $member_name, $password.
	  Description: function confirms the password entered by the user.
	  Return: an array containing member_id. In case of an error or incurrect password,
	  an array containing a description of the error*/
	public static function confirmPassword($member_name, $password){
		
		global $db;
		
		//set query
		$query_text = "SELECT member_id, password FROM login WHERE member_name = :member_name";
		
		try{
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_name', $member_name); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		} 
		
		//user password incurrect
		if ($result['password'] != $password){
			$error_message = "incorrect password"; 
			$toReturn = array('error_message' => $error_message);
			return $toReturn;
		}
		
		//password currect
		$toReturn = array('member_id' => $result['member_id']);
		
		//password currect 
		return $toReturn; 
		
	}
	
	/*Parameters: $member_id.
	  Description: function checks whether the member is a regular user or a recruiter.   
	  Return: 1 if the member is a recruiter, 0 if the member is a regular user. 
	  In case of an error, an array containing a description of the error*/
	public static function isRecruiter($member_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT recruiter_id FROM recruiters WHERE recruiter_id = :recruiter_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':recruiter_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		if ($result['recruiter_id'] == null){
			//member is a regular user and not a recruiter
			return 0; 
		}
		
		//member is a recruiter
		return 1; 
	}
	
	/*Parameters: $isRecruiter, $member_id.
	  Description: function is called when user enters his/hers settings' zone.  
	  Return: an array containing all the relevant details. In case of an error, an array containing
	  a description of the error*/
	public static function getDataForSettings($isRecruiter, $member_id){
		
		//check if member is a regular user or a recruiter
		if ($isRecruiter == 0){
			//regular user
			return getDataFromDB::getDataForSettingsUser($member_id);
		}
		//recruiter
		return getDataFromDB::getDataForSettingsRecruiter($member_id);
		
	}
	
	/*Parameters: $member_id.
	  Description: function is called when a regular user enters his/hers settings' zone.  
	  Return: an array containing all the relevant details. In case of an error, an array containing
	  a description of the error*/
	public static function getDataForSettingsUser($member_id){
		
		//get data from users TABLE
		$resultUsers = getDataFromDB::getDataFromUsersTable($member_id);
			
		//check if an error occured
		if (key($resultUsers) == 'error_message'){
			return $resultUsers; 
		}
			
		//get data from cvs TABLE
		$resultCVs = getDataFromDB::getDataFromCVsTable($member_id);
			
		//check if an error occured
		if (key($resultCVs) == 'error_message'){
			return $resultCVs; 
		}
			
		$result = array('contactDetails' => array('email' => $resultUsers['email'], 
		'city' => $resultUsers['city'], 'phoneNumber' => $resultUsers['phone']), 
		'fieldsToGrade' => array('id_1' => $resultUsers['fullstack'],
		'id_2' => $resultUsers['frontend'], 'id_3' => $resultUsers['backend'],
		'id_4' => $resultUsers['UX_UI'], 'id_5' => $resultUsers['BI'], 'id_6' => $resultUsers['QA'],
		'id_7' => $resultUsers['DBA'], 'id_8' => $resultUsers['IT']), 'resume' => 
		array('id' => $resultCVs['cv_id'], 'url' => $resultCVs['cv_url'],
		'fieldsInResume' => array('id_1' => $resultCVs['fullstack'],
		'id_2' => $resultCVs['frontend'], 'id_3' => $resultCVs['backend'], 'id_4' => $resultCVs['UX_UI'],
		'id_5' => $resultCVs['BI'], 'id_6' => $resultCVs['QA'], 'id_7' => $resultCVs['DBA'],
		'id_8' => $resultCVs['IT']), 'isSendContactDetails'=>$resultUsers['share_info'],
		'openQuestion'=>$resultCVs['open_question']));
			
		return $result; 
	}
	
	/*Parameters: $member_id.
	  Description: function gets all relevant data from users TABLE.  
	  Return: an array containing the user's fields - city, email, phone, fullstack, 
	  frontend, backend, UX_UI, BI, QA, DBA, IT, share_info. In case of an error, an array containing
	  a description of the error*/
	public static function getDataFromUsersTable($member_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT city, email, phone, fullstack, frontend, backend,
		UX_UI, BI, QA, DBA, IT, share_info FROM users WHERE user_id = :user_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':user_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
		
	}
	
	/*Parameters: $member_id.
	  Description: function gets all relevant data from cvs TABLE.  
	  Return: an array containing the cv's fields - cv_id, cv_url, open_question, fullstack, 
	  frontend, backend, UX_UI, BI, QA, DBA, IT. In case of an error, an array containing
	  a description of the error*/
	public static function getDataFromCVsTable($member_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT cv_id, cv_url, open_question, fullstack, frontend, backend,
		UX_UI, BI, QA, DBA, IT  FROM cvs WHERE user_id = :user_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':user_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
	}
	
	/*Parameters: $member_id.
	  Description: function is called when a recruiter enters his/hers settings' zone.  
	  Return: an array containing all the relevant details. In case of an error, an array containing
	  a description of the error*/
	public static function getDataForSettingsRecruiter($member_id){
		
		//get data from recruiters TABLE
		$resultRecruiters = getDataFromDB::getDataFromRecruitersTable($member_id);
			
		//check if an error occured
		if (key($resultRecruiters) == 'error_message'){
			return $resultRecruiters; 
		}
			
		$result = array('contactDetails' => array('email' => $resultRecruiters['email'],
		'companyName' => $resultRecruiters['company_name'], 'phoneNumber' => $resultRecruiters['phone']),
		'fieldsToGrade' => array('id_1' => $resultRecruiters['fullstack'],
		'id_2' => $resultRecruiters['frontend'], 'id_3' => $resultRecruiters['backend'],
		'id_4' => $resultRecruiters['UX_UI'], 'id_5' => $resultRecruiters['BI'], 'id_6' => $resultRecruiters['QA'],
		'id_7' => $resultRecruiters['DBA'], 'id_8' => $resultRecruiters['IT']));
			
		return $result; 
	}
	
	/*Parameters: $member_id.
	  Description: function gets all relevant data from recruiters TABLE.  
	  Return: an array containing the recruiter's fields - company_name, email, phone, 
	  fullstack, frontend, backend, UX_UI, BI, QA, DBA, IT. In case of an error, 
	  an array containing a description of the error*/
	public static function getDataFromRecruitersTable($member_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT company_name, email, phone, fullstack, frontend, backend,
		UX_UI, BI, QA, DBA, IT FROM recruiters WHERE recruiter_id = :recruiter_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':recruiter_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
	}
	
	/*Parameters: $member_id, $isRecruiter.
	  Description: function gets user's/recruiter's current amount of points from the DB.
	  TABLE.
	  Return: an array containing the user's/recruiter's current_amount of points.  
	  In case of an error, an array containing a description of the error*/
	public static function getPointsMember($member_id, $isRecruiter){
		
		global $db; 
		
		//regular user
		if($isRecruiter == 0){
			//set query
			$query_text = "SELECT current_amount FROM points_users WHERE 
			user_id = :member_id";
		}
		//recruiter
		else{
			//set query
			$query_text = "SELECT current_amount FROM points_recruiters WHERE 
			recruiter_id = :member_id";
		}
			
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
	}
	
	/*Parameters: $username.
	  Description: function checks if $username already exists in the DB. 
	  Return: 1 if $username exists in the DB, 0 if not. 
	  In case of an error, an array containing a description of the error*/
	public static function existUserName($username){
		
		global $db; 
		
		//set query
		$query_text = "SELECT member_name FROM login WHERE member_name = :member_name";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_name', $username); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
			if ($result == false){
				return 0;
			}
			else{
				return 1;
			}
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
	}
	
	/*Parameters: $member_name.
	  Description: function gets the member's reliability from the DB. 
	  Return: an array containing the member's reliability. In case of an error,
	  an array containing a description of the error*/
	public static function getMemberReliability($member_name){
		
		global $db; 
		
		$result = getDataFromDB::getMemberIdByName($member_name);
		//check if an error accured
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		$member_id = $result['member_id'];
		
		$isRecruiter = getDataFromDB::isRecruiter($member_id);
		
		//check if an error accured
		if ($isRecruiter != 0 and $isRecruiter != 1){
			return $isRecruiter; 
		}
		//regular user
		else if ($isRecruiter == 0){ 
			//set query
			$query_text = "SELECT user_reliability FROM users WHERE user_id = :member_id";
		}
		//recruiter
		else{
			//set query
			$query_text = "SELECT recruiter_reliability FROM recruiters WHERE recruiter_id = :member_id";
		}
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		if ($isRecruiter == 0){
			$toReturn = array('reliability' => $result['user_reliability']);
		}
		else{
			$toReturn = array('reliability' => $result['recruiter_reliability']);
		}
		
		return $toReturn; 
		
	}
	
	/*Parameters: $cv_id. 
	  Description: function gets cv's info from the DB by the cv_id. 
	  Return: an array containing the cv's info. In case of an error,
	  an array containing a description of the error*/
	public static function getCVInfoById($cv_id){
		
		global $db; 
					
		//set query
		$query_text = "SELECT cv_url, tags_from_cv, open_question, fullstack, frontend,
        backend, UX_UI, BI, QA, DBA, IT	FROM cvs WHERE cv_id = :cv_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':cv_id', $cv_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
	}
	
	/*Parameters: $member_id. 
	  Description: function gets user's/recruiter's fields to grade from the DB. 
	  Return: an array containing the user's/recruiter's fields to grade info. 
	  In case of an error, an array containing a description of the error*/
	public static function getMemberCategories($member_id){
		
		global $db; 
		
		$isRecruiter = getDataFromDB::isRecruiter($member_id);
		
		//check if an error accured
		if ($isRecruiter != 0 and $isRecruiter != 1){
			return $isRecruiter;
		}
		//regular user
		else if ($isRecruiter == 0){
			//set query
			$query_text = "SELECT fullstack, frontend, backend, UX_UI, BI, QA, DBA, IT
			FROM users WHERE user_id = :member_id";
		}
		//recruiter
		else{
			//set query
			$query_text = "SELECT fullstack, frontend, backend, UX_UI, BI, QA, DBA, IT
			FROM recruiters WHERE recruiter_id = :member_id";
		}
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
		
	}
	
	/*Parameters: $cv_id. 
	  Description: function gets the user's, which cv_id belongs to him/her, contact details.
	  Return: an array containing the user's contact details. In case of an error,
	  an array containing a description of the error*/
	public static function getUserDetailsForRecruiter($cv_id){
		
		global $db; 
		
		$result = getDataFromDB::getUserIdByCVId($cv_id);
		
		//check if an error accured
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		$user_id = $result['user_id'];
		
		//set query
		$query_text = "SELECT first_name, last_name, city, email, phone FROM users
		WHERE user_id = :user_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':user_id', $user_id); 
			//execute query
			$query_statement->execute();
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return $result; 
		
	}
	
	/*Parameters: $user_id. 
	  Description: function gets all the answers for user's open_question. 
	  Return: an array containing all the relevant data. In case of an error,
	  an array containing a description of the error*/
	public static function getAnswersForDisplay($user_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT open_question, answer_open_question, ranking_person_id
		FROM rankings, cvs WHERE user_id = ranked_person_id and user_id = :user_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':user_id', $user_id); 
			//execute query
			$query_statement->execute();
			$num_rows = $query_statement->rowCount();
			
			$current = $query_statement->fetch(PDO::FETCH_ASSOC);
			
			//check if the user wrote an open question
			if ($current['open_question'] == null){
				$questionArray = array(); 
				$toReturn = array('isHaveQuestion' => false, 'question' => '', 'list'=>array());
				return $toReturn; 
			}
			
			$index = 0; 
			
			//enter relevant data only
			if ($current['answer_open_question'] != null){
				$temp[$index] = array('id' => $current['ranking_person_id'], 'text' => 
				$current['answer_open_question']);
				$index++;
			}
			
			for ($i=1; $i<$num_rows; $i++){
				//fetch results
				$current = $query_statement->fetch(PDO::FETCH_ASSOC);
				//enter relevant data only
				if ($current['answer_open_question'] != null){
					$temp[$index] = array('id' => $current['ranking_person_id'], 'text' => 
					$current['answer_open_question']);
					$index++;
				}
			}
			
			//no answers to display
			if ($index == 0){
				$questionArray = array();
				$toReturn = array('isHaveQuestion' => true, 'question' => $current['open_question'], 'list'=>array());
				return $toReturn; 
			}
						
			$toReturn = array('isHaveQuestion' => true, 'question' => $current['open_question'],
			'list' => $temp);
			
			return $toReturn; 
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
	}
	
	/*Parameters: $user_id. 
	  Description: function gets all the comments the user recieved. 
	  Return: an array containing all the relevant data. In case of an error,
	  an array containing a description of the error*/
	public static function getCommentsForDisplay($user_id){
		
		global $db; 
		
		//set query
		$query_text = "SELECT general_remarks, ranking_person_id FROM rankings
		WHERE ranked_person_id = :user_id"; 
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':user_id', $user_id); 
			//execute query
			$query_statement->execute();
			$num_rows = $query_statement->rowCount();
			
			$index = 0; 
					
			for ($i=0; $i<$num_rows; $i++){
				//fetch results
				$temp = $query_statement->fetch(PDO::FETCH_ASSOC);
				//enter relevant data only
				if ($temp['general_remarks'] != null){
					$toReturn[$index] = array('id' => $temp['ranking_person_id'],
					'text' => $temp['general_remarks']);
					$index++;
				}
			}
			
			//no comments to display
			if ($index == 0){
				$toReturn = array('comments' => array()); 
				return $toReturn; 
			}
			
			$toReturnFinal = array('comments' => $toReturn); 
			return $toReturnFinal; 
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
	}
	
	/*Parameters: $user_id. 
	  Description: function gets checks cv_id by the user_id. 
	  Return: an array containing the cv_id. In case of an error,
	  an array containing a description of the error*/
    public static function getCvIdByUserID($user_id){
		
        global $db;

		//set query
		$query_text = "SELECT cv_id FROM cvs
		WHERE user_id = :user_id";

		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':user_id', $user_id);
			//execute query
			$query_statement->execute();
            $result = $query_statement->fetch(PDO::FETCH_ASSOC);

            if($result == false){
                return array('id'=>-1);
            } else {
                return array('id'=>$result['cv_id']);
            }
		}

		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
    }
	
}


?>