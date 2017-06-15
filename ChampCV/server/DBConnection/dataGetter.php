<?php

set_time_limit(0);
require_once('./DBConnection.php');
require_once('./getDataFromDB.php');
require_once('./pointsUpdateDB.php');

class dataGetter {
	
	const loginTimeLimitInDays = 30; 
	const reliabilityLimit = 0.5; 

    /*
     * @brif : get all CV's in the DB that are:
     *  1. nor ranked by $rakerID
     *  2. active (according to gita)
     *  3. rellible (according to gita)
     */
    
	/*Parameters: $maxNumOfCV, $rankerID.
    Description: function returns array with possible cv's for the user to rank. 
    Return: an array containing the cv's info. In case of an error,
    an array containing a description of the error*/
    public static function getAvailableCVs($maxNumOfCV, $rankerID){
		
		global $db; 
		$toReturn = array();

		$isRecruiter = getDataFromDB::isRecruiter($rankerID);
		
		//check if an error accured
		if ($isRecruiter != 0 and $isRecruiter != 1){
			return $isRecruiter;
		}
		//regular user
		else if ($isRecruiter == 0){
			//set query
			$query_text = "SELECT cvs.user_id, cvs.cv_id, cvs.fullstack, cvs.frontend, 
			cvs.backend, cvs.UX_UI, cvs.BI, cvs.QA, cvs.DBA, cvs.IT, cvs.amount_ranked FROM 
			cvs, users, login WHERE login.member_id = users.user_id AND
			users.user_id = cvs.user_id AND cvs.user_id != :rankerID AND users.user_reliability > :reliabilityLimit
			AND timediff(now(), login.last_login_time) <= :timeLimit AND cvs.cv_id NOT IN 
			(SELECT cv_id FROM rankings WHERE ranking_person_id = :rankerID)
			ORDER BY cvs.amount_ranked";
		}
		//recruiter
		else{
			//set query
			$query_text = "SELECT cvs.user_id, cvs.cv_id, cvs.fullstack, cvs.frontend, 
			cvs.backend, cvs.UX_UI, cvs.BI, cvs.QA, cvs.DBA, cvs.IT, cvs.amount_ranked FROM 
			cvs, users, login WHERE login.member_id = users.user_id AND
			users.user_id = cvs.user_id AND users.user_reliability > :reliabilityLimit 
			AND timediff(now(), login.last_login_time) <= :timeLimit AND cvs.cv_id NOT IN 
			(SELECT cv_id FROM rankings WHERE ranking_person_id = :rankerID)
			ORDER BY cvs.amount_ranked";
		}
		
		try {
			//prepare query
			$query_statement = $db->prepare($query_text);
			//bind
			$timeInHours = 24*dataGetter::loginTimeLimitInDays;
			$timeLimit = $timeInHours.":00:00"; 
			$query_statement->bindValue(':timeLimit', $timeLimit);
			$query_statement->bindValue(':rankerID', $rankerID);
			$query_statement->bindValue(':reliabilityLimit', dataGetter::reliabilityLimit);
			//execute query
			$query_statement->execute();
			
			$currentIndex = 0; 
			
			//fetch results
			$result = $query_statement->fetch(PDO::FETCH_ASSOC);
			
			//check if there is relevant match in the DB
			if ($result == false){
				$toReturn = array('error_message' => "no match found in the DB");
				return $toReturn;
			}
            
            $categories = dataGetter::processCategories($result);

			$toReturn[$currentIndex] = array('userID' => $result['user_id'], 'cvID' => $result['cv_id'],
			'numOfRanks' => $result['amount_ranked'], 'categories' => $categories);
			
			$currentIndex++; 
			
			while ($currentIndex < $maxNumOfCV){
				
				//fetch results
				$result = $query_statement->fetch(PDO::FETCH_ASSOC);
				
				//check if new data was added
				if ($result == false){
					break; 
				}
				else {

                    $categories = dataGetter::processCategories($result);

					$toReturn[$currentIndex] = array('userID' => $result['user_id'], 
					'cvID' => $result['cv_id'], 'numOfRanks' => $result['amount_ranked'], 
					'categories' =>$categories);
				}
				
				$currentIndex++;
                
			}
            
		}
        
		catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $result = array('error_message' => $error_message);
            return $result;
		}
		
		return $toReturn;  
    }
	
	/*Parameters: $rankerID.
    Description: function gets the user's ranking categories from the DB.
    Return: an array containing the user's categories. In case of an error,
    an array containing a description of the error*/
    public static function getCategoriesOfUser($rankerID) {

        $toReturn = array();

		$result = getDataFromDB::getMemberCategories($rankerID);
		
		//check if an error accured
		if (key($result) == 'error_message'){
			return $result; 
		}

        $toReturn = dataGetter::processCategories($result);
		
		return $toReturn; 
	}

    /*
     * @brif : 
     * @param= $uidArr = (1,23,4,5,7)
     * 
     */
	/*Parameters: $uidArr.
    Description: function gets the current amount of points from the DB for each user. 
    Return: an array containing the current amount of points for each user. 
    In case of an error, an array containing a description of the error*/
    public static function getPointsForCVs($uidArr) {
        $toReturn = array();

		for ($i=0; $i<count($uidArr); $i++){
			$result = getDataFromDB::getPointsMember($uidArr[$i], 0);
			if (key($result) == 'error_message'){
				return $result; 
			}
			$toReturn[$uidArr[$i]] = $result['current_amount'];
		}
		
		return $toReturn; 
		
	}
	

    /*
     * @brif : 
     * @param= $pointsToDecrese = uid=>points
     * 
     */
	/*Parameters: $pointsToDecrease.
    Description: function decreases user's points in the DB after promotion. 
    Return: null. In case of an error, an array containing a description of the error*/
    public static function payForprivileges($pointsToDecrease){
		
		for($i=0; $i<count($pointsToDecrease);$i++){
			
			$points = current($pointsToDecrease);
			$user_id = key($pointsToDecrease);
			
			$result = pointsUpdateDB::decreasePointsUser($user_id, $points, 0);
			
			//check if an error accured
			if ($result != null){
				return $result; 
			}
			
			next($pointsToDecrease);
            
		}
		
		//no errors, return null 
        return null;
    }

    /*
     * @brif :
     * @param= $CVsIds=array(1,23,4)
     * 
     */
    /*Parameters: $CVsIds.
    Description: function gets cv's relevant info for each of the cvs in $CVsIds.
    Return: an array containing all the cvs' info. In case of an error, 
    an array containing a description of the error*/
    public static function getCVsDataForUI($CVsIds){
        
        $toReturn = array();

		for($i=0; $i<count($CVsIds);$i++){
            
			$result = getDataFromDB::getCVInfoById($CVsIds[$i]);
            
			//check if an error accured 
			if (key($result) == 'error_message'){
				return $result; 
			}
            
			$keywords = explode(";", $result['tags_from_cv']);

			$fields = dataGetter::processCategories($result);
            
			$toReturn[$i] = array('id' => $CVsIds[$i], 'url' => $result['cv_url'],
			'keywords' => $keywords, 'question' => $result['open_question'], 'fields' => $fields);
            
		}
        
		return $toReturn;
    }
	
	/*Parameters: $fullstack, $frontend, $backend, $UX_UI, $BI, $QA, $DBA, $IT. 
    Description: function checks which of the fields are relevant(value 1 and not 0).
    Return: an array containing the serial numbers of the relevant categories.
    In case of an error, an array containing a description of the error*/
	public static function processCategories($catFromDB){

		$categories = array(); 

		$values = array(  $catFromDB['fullstack'], $catFromDB['frontend'],
        $catFromDB['backend'], $catFromDB['UX_UI'], $catFromDB['BI'], $catFromDB['QA'], 
        $catFromDB['DBA'], $catFromDB['IT']);
        
        assert (count($values) == 8);
        for ($index = 0; $index<8; $index++)
        {
            $name = 'id_'.($index+1);
            $categories[$name] = $values[$index] === "1" ? 1 : 0;
        }
        
        return $categories;		
	}
}

?>
