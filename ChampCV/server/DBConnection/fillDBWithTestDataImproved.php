<?php
require_once('../clientAPI/fillDBWithNewUserInfo.php');
require_once('../clientAPI/CVUpdateDB.php');
require_once('../clientAPI/rankingUpdateDB.php');
require_once('../libs/Gaussian.php');


$numOfRecruiters = 10;
$numOfUsers = 23;

//add new users
for($i = 1; $i <$numOfUsers + 1; $i++ )
{
    $fieldsToGrade = array('id_1' => rand(0,1), 'id_2' => rand(0,1), 'id_3' => rand(0,1), 'id_4' => rand(0,1), 'id_5' => rand(0,1), 'id_6' => rand(0,1),
	'id_7' => rand(0,1), 'id_8' => rand(0,1));
    $fieldsToGrade = json_decode(json_encode($fieldsToGrade));

    $res = fillDBWithNewUserInfo::getDataNewMember(0, "firstNameUser".$i, "lastNameUser".$i, "userUsername".$i, 
        "email".$i."@gmail.com", "pass".$i, "city".$i, "companyName".$i, $i, $fieldsToGrade);

    if (key($res) === 'error_message')
    {
        echo $res['error_message'];
        return false;
    }
    $fieldsInResume = array('id_1' => rand(0,1), 'id_2' => rand(0,1), 'id_3' => rand(0,1), 'id_4' => rand(0,1), 'id_5' => rand(0,1), 'id_6' => rand(0,1),
	'id_7' => rand(0,1), 'id_8' => rand(0,1));
    $fieldsInResume = json_decode(json_encode($fieldsInResume));

    $res = CVUpdateDB::newUserUploadsCV($i, 1, $fieldsInResume, "openQuestion?".$i, "tags".$i, "./server/resumes/".$i.".pdf");
    
    if ($res != null)
    {
        echo $res['error_message'];
        return false;
    }
}

//add new recruiters
for($i = $numOfUsers + 1; $i < $numOfUsers + $numOfRecruiters + 1 ; $i++ )
{
    $fieldsToGrade = array('id_1' => rand(0,1), 'id_2' => rand(0,1), 'id_3' => rand(0,1), 'id_4' => rand(0,1), 'id_5' => rand(0,1), 'id_6' => rand(0,1),
	'id_7' => rand(0,1), 'id_8' => rand(0,1));

    $fieldsToGrade = json_decode(json_encode($fieldsToGrade));

    $res = fillDBWithNewUserInfo::getDataNewMember(1, "firstNameRecruter".$i, "lastNameRecruter".$i, "recruterUsername".$i, 
        "email".$i."@gmail.com", $i , "city".$i, "companyName".$i, $i, $fieldsToGrade);
    if (key($res) === 'error_message')
    {
        echo $res['error_message'];
        return false;
    }
}


echo '<table><tr><th>CV id</th><th>Q1</th><th>Q2</th><th>Q3</th><th>Q4</th><th>Q5</th><th>Q6</th><th>Q7</th><th>Q8</th></tr>';
$answers = array();
for ($i = 1; $i <= $numOfUsers; $i++ ){
    echo '<tr>';
    $randomQ1 = rand(1,5);
    $randomQ2 = rand(0,5);
    $randomQ3 = rand(0,5);
    $randomQ4 = rand(0,5);
    $randomQ5 = rand(0,5);
    $randomQ6 = rand(1,5);
    $randomQ7 = rand(1,5);
    $randomQ8 = rand(1,5);
    array_push($answers, array($randomQ1,$randomQ2,$randomQ3,$randomQ4,$randomQ5,$randomQ6,$randomQ7,$randomQ8));
    echo '<td>'.$i.'</td><td>'.$randomQ1.'</td><td>'.$randomQ2.'</td><td>'.$randomQ3.'</td><td>'.$randomQ4.'</td><td>'.$randomQ5.'</td><td>'.$randomQ6.'</td><td>'.$randomQ7.'</td><td>'.$randomQ8.'</td></tr>';
}
echo '</table>';

$gauss = new Gaussian;

//add new rankings
for($i = 1; $i < $numOfRecruiters+$numOfUsers+1; $i++ )
{
    for($j = 1; $j < $numOfUsers + 1; $j++ )
    {
        if ($i != $j)
        {
            $baseAnswers = $answers[$j-1];
            $res = rankingUpdateDB::getDataNewRank($i, $j, round($gauss->generateFunction($answers[$j-1][0],1, 0, 5)), round($gauss->generateFunction($answers[$j-1][1],1, 0, 5)), round($gauss->generateFunction($answers[$j-1][2],1, 0, 5)), round($gauss->generateFunction($answers[$j-1][3],1, 0, 5)), round($gauss->generateFunction($answers[$j-1][4],1, 0, 5)), round($gauss->generateFunction($answers[$j-1][5],1, 0, 5)), round($gauss->generateFunction($answers[$j-1][6],1, 0, 5)),round($gauss->generateFunction($answers[$j-1][7],1, 0, 5)), "answer_open_question", "general_remarks", 22, $gauss->generateFunction(10,1,5,25));
            if (key($res) === 'error_message')
            {
                echo $res['error_message'];
                return false;
            }
        }
    }
}

//add 123 user fot test
$fieldsToGrade = array('id_1' => rand(0,1), 'id_2' => rand(0,1), 'id_3' => rand(0,1), 'id_4' => rand(0,1), 'id_5' => rand(0,1), 'id_6' => rand(0,1),
	'id_7' => rand(0,1), 'id_8' => rand(0,1));

$fieldsToGrade = json_decode(json_encode($fieldsToGrade));

$res = fillDBWithNewUserInfo::getDataNewMember(1, "123",  "123",  "123", "123@gmail.com", 123 ,  "123", "123", "123", $fieldsToGrade);

if (key($res) === 'error_message')
{
    echo $res['error_message'];
    return false;
}

echo "success";




?>