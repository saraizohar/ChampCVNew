<?php

require_once('./fillDBWithReports.php');
require_once('./getDataFromDB.php');
require_once('../DBconnection/reliability.php');

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    switch ($action){

        case "getAnalyze":
            $cid = $_POST["cid"];

            $cv_result = getDataFromDB::getCvIdByUserID($cid);
            if (key($cv_result) == 'error_message'){
				$result = $cv_result;
				break;
			}

            $cv_id = $cv_result['id'];
            $isUploadedResume = $cv_id != -1;

            $statistic = array();
            $answers = array();
            $comments = array();

            if($isUploadedResume){
                $answers = getDataFromDB::getAnswersForDisplay($cid);

                //check if an error accured
                if (key($answers) == 'error_message'){
                    $result = $answers;
                    break;
                }

                $temp = getDataFromDB::getCommentsForDisplay($cid);

                //check if an error accured
                if (key($temp) == 'error_message'){
                    $result = $temp;
                    break;
                }
                else{
                    $comments = $temp['comments'];
                }

                $statistic= reliability::get_ranks_results_array($cv_result['id']);
            }

            // If the user don't have a resume, we don't have data to show, therefore 'generalStatistic', 'grades', 'answers' and 'comments'
            // are NULL. send isUploadedResume=false
            $result = array('isUploadedResume'=>$isUploadedResume,
                            'statistic'=>$statistic,
                            'answers'=>$answers, 
                            'comments'=>$comments);
            break;
        case 'reportAnswer':
            $cid = $_POST["cid"];
            $answerID = $_POST["id"];

            /***added by Shiran 8.6***/
			$result = fillDBWithReports::answerReport($cid, $answerID);
			if ($result == null){
				$result = true;
			}

            reliability::update_all_rank_reliability($cid);
            reliability::cal_and_update_user_reliability($cid);
            break;
        case 'reportComment':
            $cid = $_POST["cid"];
            $commentID = $_POST["id"];

            /***added by Shiran 8.6***/
			$result = fillDBWithReports::commentReport($cid, $commentID);
			if ($result == null){
                $result = true;
			}

            reliability::update_all_rank_reliability($cid);
            reliability::cal_and_update_user_reliability($cid);
            break;

    }

    echo (json_encode($result));
}
?>