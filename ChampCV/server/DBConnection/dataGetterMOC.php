<?php

/*
 * for testing
 */
class dataGetter
{
    public static function getAvailableCVs($maxNumOfCV, $rakerID)
    {
        $res = array();
        $numOfCV = 10;

        for ($i = 0; $i<$numOfCV ; $i++)
        {
            $tmp =  array(
                        "userID"=>$i,
                        "cvID"=>$i,
                        //"numOfRanks"=> 5,
                        //"numOfRanks"=> ($numOfCV-$i) * 5,
                        "numOfRanks"=> $i * 5,
                        //"numOfRanks"=> rand(0,25),
                        "categories" => array('id_1'=>1, 'id_2'=>1, 'id_3'=>3),
                        //"categories" => array(1=>1, 2=>1, 3=>$i>10 ? 1:0),
                        //"categories" => array(1=>1, 2=>1, 3=>$i==10 ? 1:0)
                          );

            array_push($res, $tmp);
        }

        return $res;
    }

    public static function getCategoriesOfUser($rankerID)
    {
        $res = array('id_1'=>1, 'id_2'=>1, 'id_3'=>3); //sholde be 8 cat
        return $res;
    }

    public static function getPointsForCVs($uidArr)
    {
        //{userID->numOfPoints}

        $res = array();
        foreach ($uidArr as $uid)
        {
            //$res[$uid] = i%2 == 0? 10000 : 0;
            $res[$uid] = 10000000;
        }
        $res[5] = 100000000;
        return $res;
    }

    public static function payForprivileges($pointsToDecrese)
    {
        return null;
    }

    public static function getCVsDataForUI($CVsIds)
    {
        $res = array();
        foreach($CVsIds as $cvID)
        {
            $tmp = array('id'=>$cvID, 'url'=>'./server/resumes/111.pdf', "keywords"=>array('Python', '.Net', 'C++'), "question"=>'What do you think about my last job?', 'fields'=>array('id_1','id_2','id_3'), 'price'=>15);
            $res[]  = $tmp;
        }

        return $res;
    }

}


?>