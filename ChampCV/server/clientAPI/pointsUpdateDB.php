<?php
set_time_limit(0);
require_once('DBConnection.php');

class pointsUpdateDB{
	
	/*Parameters: $isRecruiter, $member_id, $points_for_rank. 
	  Description: function updates ranker's points after rank in the DB. 
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function updatePointsAfterRank($isRecruiter, $member_id, $points_for_rank){
		
		global $db; 
		
		//regular user
		if ($isRecruiter == 0){
			//set query
			$query_text = "UPDATE points_users SET current_amount = current_amount + :points_for_rank, 
			total_amount_ever = total_amount_ever + :points_for_rank WHERE user_id = :member_id";
		}
		//recruiter
		else {
			//set query
			$query_text = "UPDATE points_recruiters SET current_amount = current_amount + :points_for_rank, 
			total_amount_ever = total_amount_ever + :points_for_rank WHERE recruiter_id = :member_id";
		}
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':points_for_rank', $points_for_rank);
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
	
	/*Parameters: $user_id, $pointsToDecrease, $isRecruiter.
	  Description: function updates user's/recruiter's points in the DB.  
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function decreasePointsUser($member_id, $pointsToDecrease, $isRecruiter){
		
		global $db; 
		
		if ($isRecruiter == 0) {
			//set query
			$query_text = "UPDATE points_users SET current_amount = current_amount - :pointsToDecrease 
			WHERE user_id = :member_id";
		}
		else {
			//set query
			$query_text = "UPDATE points_recruiters SET current_amount = current_amount - :pointsToDecrease 
			WHERE recruiter_id = :member_id";
		}
		
			
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':pointsToDecrease', $pointsToDecrease);
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

}

?>
			