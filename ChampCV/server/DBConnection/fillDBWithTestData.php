<?php
require_once('../clientAPI/fillDBWithNewUserInfo.php');
require_once('../clientAPI/CVUpdateDB.php');
require_once('../clientAPI/rankingUpdateDB.php');


$numOfRecruiters = 10;
$numOfUsers = 10;

//add new users
for($i = 1; $i <$numOfUsers + 1; $i++ )
{
    $fieldsToGrade = array('id_1' => 1, 'id_2' => 1, 'id_3' => 1, 'id_4' => 0, 'id_5' => 0, 'id_6' => 0,
	'id_7' => 0, 'id_8' => 0);
    $fieldsToGrade = json_decode(json_encode($fieldsToGrade));

    $res = fillDBWithNewUserInfo::getDataNewMember(0, "firstNameUser".$i, "lastNameUser".$i, "userUsername".$i, 
        "email".$i."@gmail.com", "pass".$i, "city".$i, "companyName".$i, $i, $fieldsToGrade);

    if (key($res) === 'error_message')
    {
        echo $res['error_message'];
        return false;
    }
    $fieldsInResume = $fieldsToGrade;

    $res = CVUpdateDB::newUserUploadsCV($i, 1, $fieldsInResume, "openQuestion?".$i, "tags".$i, "./server/resumes/".(($i % 6) * 111).".pdf");
    
    if ($res != null)
    {
        echo $res['error_message'];
        return false;
    }
}

//add new recruiters
for($i = $numOfUsers + 1; $i < $numOfUsers + $numOfRecruiters + 1 ; $i++ )
{
    $fieldsToGrade = array('id_1' => 1, 'id_2' => 1, 'id_3' => 1, 'id_4' => 1, 'id_5' => 1, 'id_6' => 1,
	'id_7' => 1, 'id_8' => 1);

    $fieldsToGrade = json_decode(json_encode($fieldsToGrade));

    $res = fillDBWithNewUserInfo::getDataNewMember(1, "firstNameRecruter".$i, "lastNameRecruter".$i, "recruterUsername".$i, 
        "email".$i."@gmail.com", $i , "city".$i, "companyName".$i, $i, $fieldsToGrade);
    if (key($res) === 'error_message')
    {
        echo $res['error_message'];
        return false;
    }
}

//add new rankings
for($i = 1; $i < $numOfRecruiters+$numOfUsers+1; $i++ )
{
    for($j = 1; $j < $numOfUsers + 1; $j++ )
    {    
        if ($i != $j)
        {
            $res = rankingUpdateDB::getDataNewRank($i, $j, 3, 1, 2, 0, 3, 4, 3, 5, "answer_open_question", "general_remarks", 22, 100);
            if (key($res) === 'error_message')
            {
                echo $res['error_message'];
                return false;
            }
        }
    }
}

//add 123 user fot test
$fieldsToGrade = array('id_1' => 1, 'id_2' => 1, 'id_3' => 1, 'id_4' => 1, 'id_5' => 1, 'id_6' => 1,
'id_7' => 1, 'id_8' => 1);

$fieldsToGrade = json_decode(json_encode($fieldsToGrade));

$res = fillDBWithNewUserInfo::getDataNewMember(1, "123",  "123",  "123", "123@gmail.com", 123 ,  "123", "123", "123", $fieldsToGrade);

if (key($res) === 'error_message')
{
    echo $res['error_message'];
    return false;
}//*/

echo "success";




?>