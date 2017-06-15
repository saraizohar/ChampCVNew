<?php
set_time_limit(0);
require_once('DBConnection.php');
require_once('getDataFromDB.php');
require_once('pointsUpdateDB.php');

class rankingUpdateDB{

	/*Parameters: $ranking_person_id, $cv_id, $rank_reliability, $answer_question_1,
	  $answer_question_2, $answer_question_3, $answer_question_4, $answer_question_5,
	  $answer_question_6, $answer_question_7, $answer_question_8, $answer_open_question,
	  $general_remarks, $points_for_rank, $rank_time.
	  Description: function is called when a new rank is made. 
	  Return: an array containing ranking user updated amount of points. 
	  In case of an error, an array containing a description of the error*/
	public static function getDataNewRank($ranking_person_id, $cv_id, $answer_question_1,
	$answer_question_2, $answer_question_3, $answer_question_4, $answer_question_5,
	$answer_question_6, $answer_question_7, $answer_question_8, $answer_open_question, 
	$general_remarks, $points_for_rank, $rank_time){
		
		global $db; 
		
		//get ranked_person_id
		$result = getDataFromDB::getUserIdByCVId($cv_id);
	   
	   //check if an error occured
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		$ranked_person_id = $result['user_id'];
		
		$result = rankingUpdateDB::fillDBWithRankingsInfo($cv_id, $ranked_person_id, $ranking_person_id,
		$answer_question_1, $answer_question_2, $answer_question_3, $answer_question_4, 
		$answer_question_5, $answer_question_6, $answer_question_7, $answer_question_8, 
		$answer_open_question, 	$general_remarks, $points_for_rank, $rank_time);
		
		//check if an error accured 
		if ($result != null){
			return $result;
		}
		
		$result = rankingUpdateDB::updateTablesAfterRank($cv_id, $ranking_person_id, $points_for_rank);
		
		//check if an error accured 
		if ($result != null){
			return $result;
		}
		
		$isRecruiter = getDataFromDB::isRecruiter($ranking_person_id);
		
		//check if an error accured
		if ($isRecruiter != 0 and $isRecruiter != 1){
			return $isRecruiter; 
		}
		if ($isRecruiter == 1){
			$result = getDataFromDB::getPointsMember($ranking_person_id, $isRecruiter);
			 //check if an error occured
			if (key($result) == 'error_message'){
				return $result; 
			}
			$toReturn = array('points' => $result['current_amount']);
			return $toReturn; 
		}
		
		//regular user
		$toReturn = array('points' => 0);
		
		return $toReturn;
	
	}
	
	/*Parameters: $cv_id, $ranked_person_id, $ranking_person_id, $rank_reliability, 
	  $answer_question_1, $answer_question_2, $answer_question_3, $answer_question_4,
	  $answer_question_5, $answer_question_6, $answer_open_question, $general_remarks,
	  $points_for_rank, $rank_time.
	  Description: function inserts data into rankings TABLE. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function fillDBWithRankingsInfo($cv_id, $ranked_person_id, $ranking_person_id,
		$answer_question_1, $answer_question_2, $answer_question_3, $answer_question_4, 
		$answer_question_5, $answer_question_6, $answer_question_7, $answer_question_8, 
		$answer_open_question, $general_remarks, $points_for_rank, $rank_time){
		
		global $db; 
		
		//set query
		$query_text = "INSERT INTO rankings(cv_id, ranked_person_id, ranking_person_id,
			rank_reliability, answer_question_1, answer_question_2, answer_question_3, 
			answer_question_4, answer_question_5, answer_question_6, answer_question_7, 
			answer_question_8, answer_open_question, general_remarks, points_for_rank, rank_time) 
			VALUES(:cv_id, :ranked_person_id, :ranking_person_id, :rank_reliability, 
			:answer_question_1, :answer_question_2, :answer_question_3, :answer_question_4,
			:answer_question_5, :answer_question_6, :answer_question_7, :answer_question_8,
			:answer_open_question, :general_remarks, :points_for_rank, :rank_time)";
			
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(":cv_id",$cv_id);
			$query_statement->bindValue(":ranked_person_id",$ranked_person_id);
			$query_statement->bindValue(":ranking_person_id",$ranking_person_id);
			$query_statement->bindValue(":rank_reliability",1);
			$query_statement->bindValue(":answer_question_1",$answer_question_1);
			$query_statement->bindValue(":answer_question_2",$answer_question_2);
			$query_statement->bindValue(":answer_question_3",$answer_question_3);
			$query_statement->bindValue(":answer_question_4",$answer_question_4);
			$query_statement->bindValue(":answer_question_5",$answer_question_5);
			$query_statement->bindValue(":answer_question_6",$answer_question_6);
			$query_statement->bindValue(":answer_question_7",$answer_question_7);
			$query_statement->bindValue(":answer_question_8",$answer_question_8);
			$query_statement->bindValue(":answer_open_question",$answer_open_question);
			$query_statement->bindValue(":general_remarks",$general_remarks);
			$query_statement->bindValue(":points_for_rank",$points_for_rank);
			$query_statement->bindValue(":rank_time",$rank_time);
			//execute query
			$query_statement->execute();
		}
				
		catch (PDOException $ex) {
			$error_message = $ex->getMessage();
			$result = array('error_message' => $error_message);
			return $result;
		}
		
		//no errors, return null 
		return null;
		
	}
	
	/*Parameters: $cv_id, $ranking_person_id, $rank_reliability, $points_for_rank.
	  Description: function updates TABLES after rank. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateTablesAfterRank($cv_id, $ranking_person_id, $points_for_rank){
		
		global $db; 
		
		$result = rankingUpdateDB::updateAmountRankedInCVsTable($cv_id);
		
		//check if an error accured
		if ($result != null){
			return $result; 
		}
		
		$isRecruiter = getDataFromDB::isRecruiter($ranking_person_id);
		
		//check if an error accured
		if ($isRecruiter != 0 and $isRecruiter != 1){
			return $isRecruiter;
		}
		
		//update points in the DB
		$result = pointsUpdateDB::updatePointsAfterRank($isRecruiter, $ranking_person_id, 
		$points_for_rank);
		
		//check if an error accured
		if ($result != null){
			return $result; 
		}
		
		$result = rankingUpdateDB::updateAmountRankedForRanker($isRecruiter, $ranking_person_id);
		
		//check if an error accured
		if ($result != null){
			return $result; 
		}
		
		//no errors, return null
		return null;
	
	}
	
	/*Parameters: $isRecruiter, $member_id.
	  Description: function updates amount_ranked in the recruiters/users TABLE after rank. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateAmountRankedForRanker($isRecruiter, $member_id){
		
		global $db; 
		
		//recruiter
		if ($isRecruiter == 1){
			//set query
			$query_text = "UPDATE recruiters SET amount_ranked = amount_ranked+1 WHERE recruiter_id = :member_id";
		}
		//regular user
		else{
			//set query
			$query_text = "UPDATE users SET amount_ranked = amount_ranked+1 WHERE user_id = :member_id";
		}
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			//execute query
			$query_statement->execute();
		}
			
		catch (PDOException $ex) {
		$error_message = $ex->getMessage();
		$result = array('error_message' => $error_message);
		return $result;
		}
		
		//no errors, return null
		return null; 
	}
	
	/*Parameters: $cv_id.
	  Description: function updates amount_ranked in cvs TABLE. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updateAmountRankedInCVsTable($cv_id){
		
		global $db; 
		
		//set query
		$query_text = "UPDATE cvs SET amount_ranked = amount_ranked+1 WHERE cv_id = :cv_id";
			
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
		
		//no errors, return null
		return null; 
		
	}
	
	/*Parameters: $recruiter_id, cv_id, $pointsToDecrease. 
	  Description: function is called when a recruiter buys CV. 
	  Return: an array containing the relevant user's contact details.
	  In case of an error, an array containing a description of the error*/
	public static function recruiterBuysCV($recruiter_id, $cv_id, $pointsToDecrease){
		
		$result = getDataFromDB::getUserDetailsForRecruiter($cv_id);
		
		//check if an error accured
		if (key($result) == 'error_message'){
			return $result; 
		}
		
		$temp = pointsUpdateDB::decreasePointsUser($recruiter_id, $pointsToDecrease, 1);
		
		//check if an error accured
		if ($temp != null){
			return $temp; 
		}
		
		$points = getDataFromDB::getPointsMember($recruiter_id, 1);
		
		//check if an error accured
		if (key($points) == 'error_message'){
			return $points; 
		}
		
		$toReturn = array('points' => $points['current_amount'], 'contactDetails' => 
		array('firstName' => $result['first_name'], 'lastName' => $result['last_name'], 
		'email' => $result['email'], 'city' => $result['city'], 'phoneNumber' => 
		$result['phone']));
		
		return $toReturn; 
	
	}
	
}

?>