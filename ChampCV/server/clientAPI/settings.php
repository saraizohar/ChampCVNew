<?php

require_once('./getDataFromDB.php');
require_once('./changeDetailsUpdateDB.php');
require_once('./CVUpdateDB.php');

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    switch ($action){
        case "getUserDetails":
            $cid = $_POST["cid"];
            $isRecruiter = $_POST["isRecruiter"] == 'true';

            $is_recruiter =  $isRecruiter == false ? 0 : 1;
            $result = getDataFromDB::getDataForSettings($is_recruiter, $cid);
            break;
        case "updateContactDetails":
            
            $dataStr = $_POST["data"];
            $data = json_decode($dataStr);

            $cid = $data->cid;
            $isRecruiter = $data->isRecruiter;
            $is_recruiter = $isRecruiter == false ? 0 : 1;
            $email = $data->email;
            $phoneNumber = $data->phoneNumber;
            $city = $data->city;
            $companyName = $data->companyName;
            // Not relevant for recruiters - will be NULL
            $isSendContactDetails = $data->isSendContactDetails ? 1 : 0;

            $result = changeDetailsUpdateDB::updateMemberDetails($is_recruiter, $cid, $email, $phoneNumber, $city,
	    	$companyName, $isSendContactDetails);
            if ($result == null){
	   		    $result = true;
            }

            break;
        case "updateFieldsToGrade":
            $dataStr = $_POST["data"];
            $data = json_decode($dataStr);

            $cid = $data->cid;
            $isRecruiter = $data->isRecruiter;
            $is_recruiter = $isRecruiter == false ? 0 : 1;
            $fieldsToGrade = $data->fieldsToGrade;

            $result = changeDetailsUpdateDB::updateFieldsToGrade($fieldsToGrade, $cid, $is_recruiter);
            if ($result == null){
                $result = true;
			}
            break;
        case "updateResumeDetails":
            $dataStr = $_POST["data"];
            $data = json_decode($dataStr);

            $cid = $data->cid;
            $isFileChosen = $data->isFileChosen;
            // at least one field was chosen
            $fieldsInResume = $data->fieldsInResume;
            // if the user didn't type a question (or removed the prev question), it will be NULL
            $openQuestion = $data->openQuestion;
            $isRemoveResume = $data->isRemoveResume;

            $result = true;
            $uploads_dir = "../resumes";

            // if it's the first time the user upload a resume, the ID will be NULL.
            $resumeId = $data->resumeId;
            if($isFileChosen){
                $file = $_FILES['file'];
                //Use the CID to save the URL in the DB. Maybe get the serial number of the DB to save the file with the serial number as name
               
                $isSuccess = move_uploaded_file ( $file['tmp_name'] , "$uploads_dir/$cid.pdf");
                // This is the path you should save in the DB
                $pathToSave = "./server/resumes/$cid.pdf";

                $tags_from_cv = "Python:Java";
				if ($isSuccess){
					$result = CVUpdateDB::existingUserUploadsCV($cid, $resumeId, $fieldsInResume,
					$openQuestion, $tags_from_cv, $pathToSave);
				}
				else {
					$result = array('error_message' => "failed to save file");
				}

            } else if($isRemoveResume){
                // Remove the user's resume
                $isSuccess = unlink("$uploads_dir/$cid.pdf");
				if ($isSuccess){
                    $result = CVUpdateDB::removeCV($resumeId);
					if ($result == null){
						$result = true;
					}
				}
				else {
					$result = array('error_message' => "failed to delete file");
				}
            } else {
				$result = CVUpdateDB::updateSameResumeDetails($resumeId, $fieldsInResume, $openQuestion);
				if ($result == null){
					$result = true;
				}
			}

            
            break;
    }
    echo (json_encode($result));
}
?>
