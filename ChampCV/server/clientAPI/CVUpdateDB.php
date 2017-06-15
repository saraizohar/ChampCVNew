<?php
set_time_limit(0);
require_once('DBConnection.php');
require_once('getDataFromDB.php');
require_once('changeDetailsUpdateDB.php');

class CVUpdateDB{

	/*Parameters: $user_id, $isSendContactDetails, $fieldsInResume, $openQuestion,
   	  $tags_from_cv, $cv_url.
	  Description: function is called when a new user uploads a CV.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function newUserUploadsCV($user_id, $isSendContactDetails, $fieldsInResume, 
	$openQuestion, $tags_from_cv, $cv_url){

		$fullstack = $fieldsInResume->id_1;
		$frontend = $fieldsInResume->id_2;
		$backend = $fieldsInResume->id_3;
		$UX_UI = $fieldsInResume->id_4;
		$BI = $fieldsInResume->id_5;
		$QA = $fieldsInResume->id_6;
		$DBA = $fieldsInResume->id_7;
		$IT = $fieldsInResume->id_8;
		
		//insert new CV info into cvs TABLE
		$flag = CVUpdateDB::fillDBWithCvsInfo($user_id, $cv_url, $tags_from_cv, 
		$openQuestion, $fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT);
		
		//check if an error accured
		if ($flag != null){
			return $flag;
		}
		
		//update CV_uploaded_amount in DB
		$flag = CVUpdateDB::updateCVUploadedAmountInUsersTable($user_id);
		
		//check if an error accured
		if ($flag != null){
			return $flag;
		}
		
		if ($isSendContactDetails == 1){
			//update share_info in DB
			$flag = changeDetailsUpdateDB::updateShareInfoInUsersTable($user_id, 1);
			//check if an error accured
			if ($flag != null){
			return $flag;
			}
		}
		
		//no error, no error_message array to return
		return null;
		
	}

	/*Parameters: $user_id, $cv_id, $fieldInResume, $open_question, $tags_from_cv, $cv_url.
	  Description: function is called when an existing user uploads a new CV. 
	  Return: an array containing cv_id and cv_url. In case of an error, an array containing a description of the error*/
	public static function existingUserUploadsCV($user_id, $cv_id, $fieldsInResume, $open_question,
	$tags_from_cv, $cv_url){
		
		$fullstack = $fieldsInResume->id_1;
		$frontend = $fieldsInResume->id_2;
		$backend = $fieldsInResume->id_3;
		$UX_UI = $fieldsInResume->id_4;
		$BI = $fieldsInResume->id_5;
		$QA = $fieldsInResume->id_6;
		$DBA = $fieldsInResume->id_7;
		$IT = $fieldsInResume->id_8;
				
		return CVUpdateDB::handleNewCV($user_id, $cv_id, $cv_url, $tags_from_cv, $open_question,
		$fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT);
		
	}
	
	
	/*Parameters: $user_id, $cv_id, $cv_url, $tags_from_cv, $open_question, $fullstack,
   	  $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT. 
	  Description: function is called in order to handle new CV situation - delete old CV's
	  info and insert new CV's info. 
	  Return: an array containing cv_id and cv_url. In case of an error, an array containing a description of the error*/
	public static function handleNewCV($user_id, $cv_id, $cv_url, $tags_from_cv, $open_question,
	$fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT){
		
		if ($cv_id != null){
			//delete old CV data from DB
			$flag = CVUpdateDB::deleteDataOldCV($cv_id);
			//check if an error accured
			if ($flag != null){
				return $flag;
			}
		}
		
		//insert new CV info into cvs TABLE
		$flag = CVUpdateDB::fillDBWithCvsInfo($user_id, $cv_url, $tags_from_cv, 
		$open_question, $fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT);
		
		//check if an error accured
		if ($flag != null){
			return $flag;
		}
		
		//get new CV url and id
		$result = getDataFromDB::getDataFromCVsTable($user_id);
		
		//check if an error occured
		if (key($result) == 'error_message'){
			return $result; 
		}

		$toReturn = array('resume'=>array('id' => $result['cv_id'], 'url' => $result['cv_url']));
			
		//update cv_uploaded_amount in users TABLE
		$flag = CVUpdateDB::updateCVUploadedAmountInUsersTable($user_id);
		
		//check if an error accured
		if ($flag != null){
			return $flag;
		}
		
		return $toReturn; 

		
	}
	
	/*Parameters: $cv_id.
	  Description: function deletes old CV data from cvs and rankings TABLES in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function deleteDataOldCV($cv_id){
		
		global $db; 
		
		//delete old CV info from cvs TABLE
		
		//set query
		$query_text = "DELETE FROM cvs WHERE cv_id = :cv_id";
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':cv_id', $cv_id); 
			//execute query
			$query_statement->execute();
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		//delete old CV info from rankings TABLE
		
		//set query 
		$query_text = "DELETE FROM rankings WHERE cv_id = :cv_id";
		
		try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':cv_id', $cv_id); 
			//execute query
			$query_statement->execute();
		}
		
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		return null; 
	}
	
	/*Parameters: $user_id, $cv_url, $tags_from_cv, $open_question, $fullstack, $frontend, 
	  $backend, $UX_UI, $BI, $QA, $DBA, $IT, $can_be_ranked, $amount_ranked.
	  Description: function inserts data into cvs TABLE in the DB. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithCvsInfo($user_id, $cv_url, $tags_from_cv, 
	$open_question, $fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT){
		
		global $db; 
		
		//set query 
		$query_text = "INSERT INTO cvs(user_id, cv_url, tags_from_cv, 
			open_question, fullstack, frontend, backend, UX_UI, BI, QA, DBA, IT,  
			amount_ranked) VALUES(:user_id, :cv_url, :tags_from_cv, 
			:open_question, :fullstack, :frontend, :backend, :UX_UI, :BI, :QA, :DBA, :IT,
			:amount_ranked)";
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":user_id",$user_id);
			$query_statement->bindValue(":cv_url",$cv_url);
			$query_statement->bindValue(":tags_from_cv",$tags_from_cv);
			$query_statement->bindValue(":open_question",$open_question);
			$query_statement->bindValue(":fullstack",$fullstack);
			$query_statement->bindValue(":frontend",$frontend);
			$query_statement->bindValue(":backend",$backend);
			$query_statement->bindValue(":UX_UI",$UX_UI);
			$query_statement->bindValue(":BI",$BI);
			$query_statement->bindValue(":QA",$QA);
			$query_statement->bindValue(":DBA",$DBA);
			$query_statement->bindValue(":IT",$IT);
			$query_statement->bindValue(":amount_ranked",0);
			
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
	  Description: function updates cv_uploaded_amount field in users TABLE in the DB.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateCVUploadedAmountInUsersTable($user_id){
		
		global $db; 
		
		//set query
		$query_text = "UPDATE users SET cv_uploaded_amount = cv_uploaded_amount+1 WHERE user_id = :user_id";
		
		try {
			//prepare query			
			$query_statement = $db->prepare($query_text);
			//bind
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
	
	/*Parameters: $cv_id.
	  Description: funtion is called when a user wants to remove his/hers CV. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function removeCV($cv_id){
		
		return cvUpdateDB::deleteDataOldCV($cv_id);
		
	}
	
	/*Parameters: $cv_id, $fieldsInResume, $open_question. 
	  Description: funtion is called when a user wants to update his/hers open_question 
	  and/or his fieldsInResume in the existing CV. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateSameResumeDetails($cv_id, $fieldsInResume, $open_question){
		
		global $db; 
		
		//check if the user asked a new question 
		$flag = CVUpdateDB::isNewQuestion($cv_id, $open_question);
		
		//check for errors
		if ($flag != 0 and $flag != 1){
			return $flag; 
		}
		//new question - delete old's question's answers data from rankings TABLE
		else if ($flag == 1){
			
			//set query 
			$query_text = "update rankings SET answer_open_question = null WHERE cv_id = :cv_id";
		
			try {
			//prepare query		
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':cv_id', $cv_id); 
			//execute query
			$query_statement->execute();
			}
		
			catch (PDOException $ex) {
				$error_message = $ex->getMessage();
				$result = array('error_message' => $error_message);
				return $result;
			}
		
		}
		
		//update DB with new Info
		
		//set query
		$query_text = "UPDATE cvs SET open_question = :open_question, fullstack = :fullstack,
		frontend = :frontend, backend = :backend, UX_UI = :UX_UI, BI = :BI, QA = :QA, 
		DBA = :DBA, IT = :IT WHERE cv_id = :cv_id";
		
		try {
			//prepare query			
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':open_question', $open_question); 
			$query_statement->bindValue(':fullstack', $fieldsInResume->id_1); 
			$query_statement->bindValue(':frontend', $fieldsInResume->id_2); 
			$query_statement->bindValue(':backend', $fieldsInResume->id_3); 
			$query_statement->bindValue(':UX_UI', $fieldsInResume->id_4); 
			$query_statement->bindValue(':BI', $fieldsInResume->id_5); 
			$query_statement->bindValue(':QA', $fieldsInResume->id_6); 
			$query_statement->bindValue(':DBA', $fieldsInResume->id_7); 
			$query_statement->bindValue(':IT', $fieldsInResume->id_8); 
			$query_statement->bindValue(':cv_id', $cv_id); 
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
	
	/*Parameters: $cv_id, $question. 
	  Description: function checks if user wrote a new question.  
	  Return: 1 if yes, 0 if not. In case of an error, an array containing a description of the error*/
	public static function isNewQuestion($cv_id, $question){
		
		global $db; 
		
		//set query
		$query_text = "SELECT open_question FROM cvs WHERE cv_id = :cv_id";
		
		try {
			//prepare query			
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':cv_id', $cv_id); 
			//execute query
			$query_statement->execute();
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
			if ($result['open_question'] == $question){
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
	
}

?>