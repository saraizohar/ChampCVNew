<?php

require_once('./fillDBWithNewUserInfo.php');
require_once('./CVUpdateDB.php');

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    switch ($action){

        case "register":
            $dataJson = $_POST["data"];
            // all registration data
            $data = json_decode($dataJson);

            $isRecruiter = $data->isRecruiter;
            $is_recruiter = $isRecruiter == false ? 0 : 1;

            $firstName = $data->firstName;
            $lastName = $data->lastName;
            $username = $data->username;
            $email = $data->email;
            $phoneNumber = $data->phoneNumber;
            $city = $data->city;
            $companyName = $data->companyName;
            $password = $data->password;
            $fieldsToGrade = $data->fieldsToGrade;

            $result = fillDBWithNewUserInfo::getDataNewMember($is_recruiter, $firstName, $lastName, $username, $email, $phoneNumber, $city, $companyName, $password, $fieldsToGrade);

			//check if an error occured
			if (key($result) == 'error_message'){
				break;
			}
			$temp = $result;
			$cid = $temp['userid'];

            $isFileChosen = $data->isFileChosen;
            if($isFileChosen){
                $isSendContactDetails = $data->isSendContactDetails;
                $fieldsInResume = $data->fieldsInResume;
                $openQuestion = $data->openQuestion;

                $file = $_FILES['file'];
                //Use the CID to save the URL in the DB. Maybe get the serial number of the DB to save the file with the serial number as name
                $uploads_dir = "../resumes";
                $isSuccess = move_uploaded_file ( $file['tmp_name'] , "$uploads_dir/$cid.pdf");
                if (!$isSuccess){
					$result = array('error_message' => "failed to save file");
					break;
				}
                // This is the path you should save in the DB
                $pathToSave = "./server/resumes/$cid.pdf";

				//get tags_from_cv from Gita
				$tags_from_cv = "";
				$result = CVUpdateDB::newUserUploadsCV($cid, $isSendContactDetails, $fieldsInResume, $openQuestion, $tags_from_cv, $pathToSave);
				//check if an error accured
				if ($result != null){
					break;
                }

            }
           
            //return user object

            // if it's not a resuirter, the points will be 0 (or you can send me the regular use points and I will not use it)
            $result = array('user' => array('name' => $temp['username'], 'cid' => $cid, 'isRecruiter' => $isRecruiter, 'points' => $temp['points']));
            break;

    }

    echo (json_encode($result));
}
?>