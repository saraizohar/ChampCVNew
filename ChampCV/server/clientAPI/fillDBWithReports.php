<?php
set_time_limit(0);
require_once('./DBConnection.php');

class fillDBWithReports{
	
	/*Parameters: $member_id, $cv_id. 
	  Description: function inserts new report into reports TABLE.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function CVReport($member_id, $cv_id){
		
		global $db; 
		
		//set query
		$query_text = "INSERT INTO reports(member_id, report_cv, report_comments, 
		report_answer, reported_id) VALUES(:member_id, :report_cv, :report_comments, 
		:report_answer, :reported_id)";
		
		try{
			
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			$query_statement->bindValue(':report_cv', 1); 
			$query_statement->bindValue(':report_comments', 0); 
			$query_statement->bindValue(':report_answer', 0); 
			$query_statement->bindValue(':reported_id', $cv_id); 
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
	
	/*Parameters: $member_id, $comment_id. 
	  Description: function inserts new report into reports TABLE.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function commentReport($member_id, $comment_id){
		
		global $db; 
		
		//set query
		$query_text = "INSERT INTO reports(member_id, report_cv, report_comments, 
		report_answer, reported_id) VALUES(:member_id, :report_cv, :report_comments, 
		:report_answer, :reported_id)";
		
		try{
			
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			$query_statement->bindValue(':report_cv', 0); 
			$query_statement->bindValue(':report_comments', 1); 
			$query_statement->bindValue(':report_answer', 0); 
			$query_statement->bindValue(':reported_id', $comment_id); 
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
	
	/*Parameters: $member_id, $answer_id. 
	  Description: function inserts new report into reports TABLE.
	  Return: null. In case of an error, an array containing a description of the error*/
	public static function answerReport($member_id, $answer_id){
		
		global $db; 
		
		//set query
		$query_text = "INSERT INTO reports(member_id, report_cv, report_comments, 
		report_answer, reported_id) VALUES(:member_id, :report_cv, :report_comments, 
		:report_answer, :reported_id)";
		
		try{
			
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$query_statement->bindValue(':member_id', $member_id); 
			$query_statement->bindValue(':report_cv', 0); 
			$query_statement->bindValue(':report_comments', 0); 
			$query_statement->bindValue(':report_answer', 1); 
			$query_statement->bindValue(':reported_id', $answer_id); 
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