<?php

require_once('./rankingUpdateDB.php');
require_once('./fillDBWithReports.php');
require_once('../internal/PointsForRanking.php');
require_once('../DBconnection/reliability.php');

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    switch ($action){
        case "grade":
            $cid = $_POST["cid"];
            $dataJson = $_POST["data"];
            $data = json_decode($dataJson);

            $resumeId = $data->resumeId;

            $question1 = $data->closeQuestions[0];

            $question2 = $data->closeQuestions[1];
            $question2_grade = $question2->grade;
            $question2_isNotRelevant = $question2->isNotRelevant;

            $question3 = $data->closeQuestions[2];
            $question4 = $data->closeQuestions[3];
            $question5 = $data->closeQuestions[4];
            $question6 = $data->closeQuestions[5];
            $question7 = $data->closeQuestions[6];
            $question8 = $data->closeQuestions[7];

            $openQuestion = $data->openQuestion;
            $comments = $data->comments;
            // time took to grade the resume in miliseconds
            $duration = $data->duration;

            $isRecruiter = $data->isRecruiter;
            // This is relevant only if $isRecruiter = true
            $isBuyContactDetails = $data->isBuyContactDetails;

			$points_for_rank = PointsForRanking::getPoints($openQuestion, $comments);

			$result = rankingUpdateDB::getDataNewRank($cid, $resumeId, $question1->grade,
			$question2->grade, $question3->grade, $question4->grade, $question5->grade,
			$question6->grade, $question7->grade, $question8->grade, $openQuestion, $comments, $points_for_rank, $duration);

			//check if an error accured
			if (key($result) == 'error_message'){
				break;
			}

            $result = array('points'=>$result[points]);

            if($isBuyContactDetails){
                // decrease points from the recruiter bank and return contact details and new points
                /***added by Shiran 8.6***/
				$pointsToDecrease = $data->price;

                // decrease points from the recruiter bank and return contact details and new points
				$result = rankingUpdateDB::recruiterBuysCV($cid, $resumeId, $pointsToDecrease);
                //$result = array('points'=>46, 'contactDetails'=>array('firstName'=>'Sarai', 'lastName'=>'Zohar', 'email'=>'sarai.zohar@gmail.com', 'city'=>'Rishon Le-Zion', 'phoneNumber'=>'0545866635'));
            }

            //reliability::update_all_rank_reliability($cid);
            //reliability::cal_and_update_user_reliability($cid);

            break;
        case "report":
            $cid = $_POST["cid"];
            $resumeID = $_POST["resumeID"];

            // save report
            /***added by Shiran 8.6***/
			$result = fillDBWithReports::CVReport($cid, $resumeID);
			if ($result == null){
				$result = true;
			}
            break;
    }
    echo (json_encode($result));
}
?>
