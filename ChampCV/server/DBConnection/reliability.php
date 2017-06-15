<?php
set_time_limit(0);
require_once('../clientAPI/DBConnection.php');

class Reliability
{
    const rankReliabilityLimit = 0.2;
    const userReliabilityLimit = 0.2;
    const rankTimeLimit = 10;
    const W1 = 0.7; //penalty
    const W2 = 0.3;// time

    public static function calculate_time_penalty($cv_id, $user_id)
    {
        global $db;
        $query_text = "SELECT AVG(rank_time) AS avg, STD(rank_time) AS std, count(*) AS ranks_num
		FROM rankings
		WHERE rank_reliability>:rankReliabilityLimit AND rank_time>:rankTimeLimit";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->bindValue(':rankTimeLimit', Reliability::rankTimeLimit);
            $query_statement->execute();
            $time_stat = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($time_stat == false) {//there is at least one rank
                $toReturn = array('error_message' => "no match found in the DB for time_stat");
                return $toReturn;
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }
        $query_text = "
			SELECT rank_time
			FROM rankings
			WHERE ranking_person_id= :user_id AND cv_id= :cv_id
			";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->execute();
            $user_time = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($user_time == false) {
                $toReturn = array('error_message' => "no match found in the DB for user_time");
                return $toReturn;
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $user_time = array('error_message' => $error_message);
            return $user_time;
        }
        $ranks_num = intval($time_stat['ranks_num']);
        $rank_time = floatval($user_time['rank_time']);
        $avg = floatval($time_stat['avg']);
        $std = floatval($time_stat['std']);
        if ($rank_time < Reliability::rankTimeLimit) {
            return 1;
        }
        elseif ($ranks_num > 20) {
            if ($avg - $rank_time > $std * 3) { //0.26% of values
                return 1;
            }
            elseif ($avg - $rank_time > $std * 2.5) {
                return 0.5;
            }
            elseif ($avg - $rank_time > $std * 2) { // 4.5% of values
                return 0.25;
            }

        }
        else {
                return 0;
        }

    }

    public static function question_mean_ranks_penalty($user_id, $cv_id, $answer_question_i)
    {
        global $db;
        $query_text1 =  "
        SELECT AVG(answer_question_1) AS avg, STD(answer_question_1) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_1>0";

        $query_text2 =  "
        SELECT AVG(answer_question_2) AS avg, STD(answer_question_2) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_2>0";

        $query_text3 =  "
        SELECT AVG(answer_question_3) AS avg, STD(answer_question_3) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_3>0";

        $query_text4 =  "
        SELECT AVG(answer_question_4) AS avg, STD(answer_question_4) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_4>0";

        $query_text5 =  "
        SELECT AVG(answer_question_5) AS avg, STD(answer_question_5) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_5>0";

        $query_text6 =  "
        SELECT AVG(answer_question_6) AS avg, STD(answer_question_6) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_6>0";

        $query_text7 =  "
        SELECT AVG(answer_question_7) AS avg, STD(answer_question_7) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_7>0";

        $query_text8 =  "
        SELECT AVG(answer_question_8) AS avg, STD(answer_question_8) AS std, count(*) AS ranks_num
        FROM rankings
        WHERE rank_reliability> :rankReliabilityLimit AND answer_question_8>0";
        ////////////////////////////////////////////////////////
        $query_text1B = "
		SELECT  answer_question_1  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text2B = "
		SELECT  answer_question_2  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text3B = "
		SELECT  answer_question_3  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text4B = "
		SELECT  answer_question_4  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text5B = "
		SELECT  answer_question_5  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text6B = "
		SELECT  answer_question_6  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text7B = "
		SELECT  answer_question_7  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text8B = "
		SELECT  answer_question_8  as user_answer
        FROM rankings
        WHERE ranking_person_id= :user_id and cv_id= :cv_id";

        $query_text="";
        $query_textB="";
        if ($answer_question_i=="answer_question_1"){
            $query_text=$query_text1;
            $query_textB=$query_text1B;
        }
        if ($answer_question_i=="answer_question_2"){
            $query_text=$query_text2;
            $query_textB=$query_text2B;
        }
        if ($answer_question_i=="answer_question_3"){
            $query_text=$query_text3;
            $query_textB=$query_text3B;
        }
        if ($answer_question_i=="answer_question_4"){
            $query_text=$query_text4;
            $query_textB=$query_text4B;
        }
        if ($answer_question_i=="answer_question_5"){
            $query_text=$query_text5;
            $query_textB=$query_text5B;
        }
        if ($answer_question_i=="answer_question_6"){
            $query_text=$query_text6;
            $query_textB=$query_text6B;
        }
        if ($answer_question_i=="answer_question_7"){
            $query_text=$query_text7;
            $query_textB=$query_text7B;
        }
        if ($answer_question_i=="answer_question_8"){
            $query_text=$query_text8;
            $query_textB=$query_text8B;
        }
        try {
            $query_statement = $db->prepare($query_text);
           // $query_statement->bindValue(':answer_question_i', $answer_question_i);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {//can be zero results for that question
                $ranks_num = 0;
                $avg = 0;
                $std = 0;
            }
            else{
                $ranks_num = $q01['ranks_num'];
                $avg = $q01['avg'];
                $std = $q01['std'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }

        try {
            $query_statement = $db->prepare($query_textB);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->execute();
            $q02 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q02 == false) {//can be zero results for that question
                $user_answer = 0;
            }
            else{
                $user_answer = $q02['user_answer'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $user_time = array('error_message' => $error_message);
            return $user_time;
        }
        //<ToDo> liran addition:
        if ($user_answer >0 and $ranks_num > 8){
            if (abs($user_answer - $avg) > $std * 3){
                return 1;
            } elseif (abs($user_answer - $avg) > $std * 2){
                return 0.67;
            }
        }
        else{
                return 0;
            }
        //</ToDO>
        //if ($user_answer >0 and $ranks_num > 10 and abs($user_answer - $avg) > $std * 2) {
          //  return 1;
        //} else {
         //   return 0;
        //}
    }

    public static function mean_ranks_penalty($user_id, $cv_id)
    {
        $panelty_sum = 0;
        for ($i = 1; $i <= 8; $i++) {
            $answer_question = "answer_question_" . $i;
            $value = Reliability::question_mean_ranks_penalty($user_id, $cv_id, $answer_question);
            $panelty_sum = $panelty_sum + $value;
        }
        if ($panelty_sum > 4) {
            return 1;
        } else if ($panelty_sum > 2) {
            return 0.5;
        } else {
            return 0;
        }
    }

    public static function calculate_rank_reliability($user_id, $cv_id)
    {
        $w1 = Reliability::W1;
        $w2 = Reliability::W2;
        $T_uc = Reliability::calculate_time_penalty($cv_id, $user_id);
        $MR_uc = Reliability::mean_ranks_penalty($user_id, $cv_id);
        $rank_reliability = ((float) $w1 * (float) $T_uc + (float) $w2 * (float)$MR_uc) / ((float)$w1 + (float)$w2);
        return 1 - $rank_reliability;
    }

    //ToDo Validate[]
    public static function update_all_rank_reliability($user_id)
    {
        global $db;
        $query_text = "
        SELECT DISTINCT cv_id
        FROM rankings
        WHERE ranking_person_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->execute();
            $q01 = $query_statement->fetchAll();// ToDo shoud be fatch all
            if ($q01 == false) {// should be at least one
                $toReturn = array('error_message' => "no match found in the DB for update_all_rank_reliability");
                return $toReturn;
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }
        $max = sizeof($q01);
        for ($i = 0; $i < $max; $i++) {
            $rank_reliability = Reliability::calculate_rank_reliability($user_id, $q01[$i]['cv_id']);
            Reliability::update_rank_reliability($user_id, $q01[$i]['cv_id'], $rank_reliability);
        }
        return 1;
    }

    public static function cal_user_reliability($user_id)
    {
        global $db;
        $spu = Reliability::spam_user_ranks_penalty($user_id);
        $spcv = Reliability::spam_cv_ranks_penalty($user_id);
        $sprp = Reliability::spam_wrong_reports_penalty($user_id);
        $query_text = "
        SELECT AVG(rank_reliability) AS avg, count(*) as rank_num
        FROM rankings
        WHERE ranking_person_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {
                $avg=1;
            }
            else{
                $avg = $q01['avg'];
                $rank_num=$q01['rank_num'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }
        If (max($spu, $spcv, $sprp) == 1 ) {
            return 0;
        }
        else if($avg <= 0.2 and $rank_num>3) {
            return 0;
        }
        else {
            try{
                $user_reliability = ((float)$avg + (1 - (float)$spu) + (1 - (float)$spcv) + (1 - (float)$sprp)) / 4;
            }catch (RuntimeException $ex) {
                $error_message = $ex->getMessage();
                $err = array('error_message' => $error_message);
                return $err;
            }
            return $user_reliability;
        }
    }

    public static function cal_and_update_user_reliability($user_id)//ToDo validate[]
    {
        $user_reliability=Reliability::cal_user_reliability($user_id);
        global $db;
        $query_text = "
        SELECT *
        FROM users
        WHERE user_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {
                $is_regular_user=0;
            }
            else{
                $is_regular_user=1;
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }
        if ($is_regular_user==1){
            Reliability::update_regular_user_reliability($user_id, $user_reliability);
        }
        else{
            Reliability::update_recruiters_reliability($user_id, $user_reliability);
        }

    }

    public static function update_rank_reliability($user_id, $cv_id, $rank_reliability){
        global $db;
        $query_text = "
        Update rankings
        SET rank_reliability=:rank_reliability
        WHERE ranking_person_id= :user_id AND cv_id=:cv_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->bindValue(':rank_reliability', $rank_reliability);
            $query_statement->execute();
        }
        catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }

    }
    public static function update_regular_user_reliability($user_id, $rank_reliability){
        global $db;
        $query_text = "
        Update users
        SET user_reliability=:rank_reliability
        WHERE user_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->bindValue(':rank_reliability', $rank_reliability);
            $query_statement->execute();
        }
        catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }

    }
    public static function update_recruiters_reliability($user_id, $rank_reliability){
        global $db;
        $query_text = "
        Update recruiters
        SET recruiter_reliability=:rank_reliability
        WHERE recruiter_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->bindValue(':rank_reliability', $rank_reliability);
            $query_statement->execute();

        }
        catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }

    }
    public static function spam_user_ranks_penalty($input_user_id)
    {
        global $db;
        $query_text = "SELECT count(*) AS reported_num_user FROM reports, users WHERE reports.report_cv=0 AND
		 reports.reported_id=:input_user_id AND reports.member_id= users.user_id AND users.user_reliability>:userReliabilityLimit";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':input_user_id', $input_user_id);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->execute();
            $reported_num_user = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($reported_num_user == false) {
                $r_user_num=0;
            }
            else{
                $r_user_num=$reported_num_user['reported_num_user'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $reported_num_user = array('error_message' => $error_message);
            return $reported_num_user;
        }
        $query_text = "SELECT count(*) AS reported_num_recruiter FROM reports, recruiters WHERE reports.report_cv=0 AND
		 reports.reported_id=:input_user_id AND reports.member_id= recruiters.recruiter_id AND recruiters. recruiter_reliability>:userReliabilityLimit";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':input_user_id', $input_user_id);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->execute();
            $reported_num_recruiter = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($reported_num_recruiter == false) {
                $r_rec_num=0;
            }
            else{
                $r_rec_num=$reported_num_recruiter['reported_num_recruiter'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $reported_num_recruiter = array('error_message' => $error_message);
            return $reported_num_recruiter;
        }
        $totalNum = $r_user_num + $r_rec_num;
        if ($totalNum > 1) {
            return 1;
        } elseif ($totalNum == 1) {
            return 0.5;
        } else {
            return 0;
        }
    }

    //ToDo Validate[]
    public static function spam_cv_ranks_penalty($user_id)
    {
        global $db;
        $query_text = "
        SELECT cv_id
        FROM cvs
        WHERE user_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {// no cv uploaded by this user
                return 0;
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }
        $cv_id = $q01['cv_id'];

        $query_text = "
        SELECT count(*) AS reported_num_user
        FROM reports, users
        WHERE reports.report_cv= :cv_id AND reports.member_id= users.user_id AND users. user_reliability>:userReliabilityLimit ";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->execute();
            $q02 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q02 == false) {
                $reported_num_user =0;

            }
            else{
                $reported_num_user = $q02['reported_num_user'];
            }

        } catch (PDOException $ex) {
            $error_message = $ex->getMessage(); //ToDo we shouldn't reach here []
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }


        $query_text = "
        SELECT count(*) AS reported_num_recruiter
        FROM reports, recruiters
        WHERE reports.report_cv= :cv_id AND reports.member_id= recruiters. recruiter_id AND recruiters. recruiter_reliability >:userReliabilityLimit";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->execute();
            $q03 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q03 == false) {
                $reported_num_recruiter =0;

            }
            else{
                $reported_num_recruiter = $q03['reported_num_recruiter'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }

        $query_text = "
        SELECT count(*) AS rank_num
        FROM rankings
        WHERE cv_id= :cv_id AND rank_reliability > :rankReliabilityLimit";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->execute();
            $q04 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q04 == false) {
                $rank_num = 0;
            }
            else{
                $rank_num = $q04['rank_num'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $time_stat = array('error_message' => $error_message);
            return $time_stat;
        }
        $report_num = $reported_num_recruiter + $reported_num_user;

        If ($report_num == 0) {
            return 0;
        } else if ($report_num < 3) {
            If ($rank_num > 7) {
                return 0;
            } else if ($rank_num < 2) {
                if ($report_num == 1) {
                    return 0.5;
                }
                if ($report_num == 2) {
                }
                return 1;
            } else {
                if ($report_num == 1) {
                    return 0.25;
                }
                if ($report_num == 2) {
                    return 0.75;
                }
            }
        } else {
            Return 1;
        }
    }


    public static function spam_wrong_reports_penalty($user_id)
    {
        global $db;
        $query_text = "
        SELECT report_cv, reported_id
        FROM reports
        WHERE member_id= :user_id";
        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':user_id', $user_id);
            $query_statement->execute();
            $q01 = $query_statement->fetchAll();
            if ($q01 == false) {
                return 0;
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $reported_num_user = array('error_message' => $error_message);
            return $reported_num_user;
        }
        $num_wrong_report = 0;
        $num_single_reported_answers=0;
        $max = sizeof($q01);
        for ($i = 0; $i < $max; $i++) {
            $report_cv = $q01[$i]['report_cv'];
            $reported_id = $q01[$i]['reported_id'];
            $query_text1 = "
                SELECT count(*) AS report_num
                FROM reports
                WHERE report_cv= :report_cv OR reported_id= :reported_id";
            try {
                $query_statement = $db->prepare($query_text1);
                $query_statement->bindValue(':report_cv', $report_cv);
                $query_statement->bindValue(':reported_id', $reported_id);
                $query_statement->execute();
                $query_text1 = $query_statement->fetch(PDO::FETCH_ASSOC);
                if ($query_text1 == false) {// must be at least 1-> the user
                    $toReturn = array('error_message' => "no match found in the DB for spam_wrong_reports_penalty");
                    return $toReturn;
                }
            } catch (PDOException $ex) {
                $error_message = $ex->getMessage();
                $reported_num_user = array('error_message' => $error_message);
                return $reported_num_user;
            }
            $report_num = $query_text1['report_num'];

            $query_text2 = "
                SELECT count(*) AS rank_num
                FROM rankings
                WHERE cv_id =:report_cv AND rank_reliability> :rankReliabilityLimit";
            try {
                $query_statement = $db->prepare($query_text2);
                $query_statement->bindValue(':report_cv', $report_cv); //ToDo 11/06/2017 11:00  ($report_cv=0 is it legit)?
                $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
                $query_statement->execute();
                $query_text2 = $query_statement->fetch(PDO::FETCH_ASSOC);
                if ($query_text2 == false) {
                    $rank_num = 0;
                } else {
                    $rank_num = $query_text2['rank_num'];
                }
            } catch (PDOException $ex) {
                $error_message = $ex->getMessage();
                $reported_num_user = array('error_message' => $error_message);
                return $reported_num_user;
            }
            if ($report_num == 1) {
                if ($rank_num == 3 or $rank_num == 4) {
                    $num_wrong_report += 0.5;
                } else if ($rank_num > 4) {
                    $num_wrong_report += 1;
                }
            }
            else if (($report_num = 2) and ($rank_num > 10)) {
                $num_wrong_report += 1;
            }
            else if (is_null($report_cv)){
                $num_single_reported_answers+=1;
            }
        }//end of for loop
        if ($num_wrong_report>2){
            return 1;
        }
        else if ($num_single_reported_answers>3){
            return 1;
        }
        else{
            return 0;
        }
    }

    //ToDo Validate[]
    public static function get_ranks_results_array($cv_id)
    {
        $ret;
        $questions_res_arr=[];
        for ($i = 1; $i <= 8; $i++) {
            $answer_question = "answer_question_" . $i;
            $questions_res_arr [$i] = Reliability::get_rank_results_question($cv_id, $answer_question);
        }

        $users_ranks_num=Reliability::get_users_ranks_num($cv_id);
        $recruiters_ranks_num=Reliability::get_recruiters_ranks_num($cv_id);
        $ret = array('gradePerQuestion'=>$questions_res_arr,'numOfRankers'=>$users_ranks_num,'numOfRecruiters'=>$recruiters_ranks_num, 'isEnoughRanks'=>true);
        if ($users_ranks_num+$recruiters_ranks_num<5)
        {
            $ret['isEnoughRanks'] = false;
        }
        return $ret;
    }
    public static function get_users_ranks_num($cv_id)
    {
        global $db;
        $query_text = "
            SELECT count(*) AS u_ranks_num
            FROM rankings, users
            WHERE rankings.rank_reliability> :rankReliabilityLimit AND rankings.cv_id= :cv_id AND
            rankings. ranking_person_id= users. user_id
             AND users.user_reliability > :userReliabilityLimit";

        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {
                return 0;

            }
            else{
                return $q01['u_ranks_num'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $err = array('error_message' => $error_message);
            return $err;
        }
    }
    public static function get_recruiters_ranks_num($cv_id)
    {
        global $db;
        $query_text = "
            SELECT count(*) AS r_ranks_num
            FROM rankings, recruiters
            WHERE rank_reliability> :rankReliabilityLimit AND rankings.cv_id= :cv_id AND
            rankings. ranking_person_id= recruiters.recruiter_id
             AND recruiters.recruiter_reliability > :userReliabilityLimit";

        try {
            $query_statement = $db->prepare($query_text);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {
                return 0;

            }
            else{
                return $q01['r_ranks_num'];
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $err = array('error_message' => $error_message);
            return $err;
        }
    }
    //ToDo Validate[]
    public static function get_rank_results_question($cv_id, $answer_question_i)
    {
        global $db;
        switch ($answer_question_i) {
                case "answer_question_1":
                    $query_text1 = "
                        SELECT SUM(answer_question_1) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_1 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_1>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                    $query_text2 = "SELECT SUM(answer_question_1) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_1 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_1>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                case "answer_question_2":
                    $query_text1 = "
                        SELECT SUM(answer_question_2) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_2 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_2>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                    $query_text2 = "SELECT SUM(answer_question_2) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_2 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_2>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                case "answer_question_3":
                    $query_text1 = "
                        SELECT SUM(answer_question_3) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_3 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_3>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                    $query_text2 = "SELECT SUM(answer_question_3) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_3 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_3>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                  case "answer_question_4":
                      $query_text1 = "
                        SELECT SUM(answer_question_4) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_4 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_4>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                      $query_text2 = "SELECT SUM(answer_question_4) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_4 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_4>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                case "answer_question_5":
                    $query_text1 = "
                        SELECT SUM(answer_question_5) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_5 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_5>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                    $query_text2 = "SELECT SUM(answer_question_5) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_5 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_5>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                        break;
                case "answer_question_6":
                    $query_text1 = "
                        SELECT SUM(answer_question_6) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_6 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_6>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                    $query_text2 = "SELECT SUM(answer_question_6) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_6 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_6>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                case "answer_question_7":
                    $query_text1 = "
                        SELECT SUM(answer_question_7) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_7 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_7>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                    $query_text2 = "SELECT SUM(answer_question_7) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_7 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_7>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                  case "answer_question_8":
                      $query_text1 = "
                        SELECT SUM(answer_question_8) AS r_sum, count(*) AS r_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_r_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_8 ELSE 0 END) AS cv_r_sum
                        FROM rankings, recruiters
                        WHERE rank_reliability> :rankReliabilityLimit
                        AND answer_question_8>0 AND rankings. ranking_person_id= recruiters.recruiter_id
                        AND recruiters. recruiter_reliability> :userReliabilityLimit";


                      $query_text2 = "SELECT SUM(answer_question_8) AS u_sum, count(*) AS u_ranks_num, count(CASE WHEN cv_id=:cv_id THEN 1 ELSE NULL END) AS cv_u_ranks_num, 
		              sum(CASE WHEN cv_id=:cv_id THEN answer_question_8 ELSE 0 END) AS cv_u_sum
                      FROM rankings, users
                      WHERE rank_reliability> :rankReliabilityLimit AND
                         answer_question_8>0 AND rankings. ranking_person_id= users. user_id
                      AND users.user_reliability >:userReliabilityLimit";
                    break;
                default:
                    $query_text1 = "";
                    $query_text2 = "";
                    }

        try {
            $query_statement = $db->prepare($query_text1);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->execute();
            $q01 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q01 == false) {
                $r_sum = 0;
                $r_ranks_num = 0;
                $cv_r_ranks_num = 0;
                $cv_r_sum = 0;
            }
            else{
                $r_sum = $q01['r_sum'];
                $r_ranks_num = $q01['r_ranks_num'];
                if ($q01['cv_r_ranks_num']==null){
                    $cv_r_ranks_num=0;
                }else{
                    $cv_r_ranks_num= $q01['cv_r_ranks_num'];
                }
                if ($q01['cv_r_sum']==null){
                    $cv_r_sum=0;
                }else{
                    $cv_r_sum= $q01['cv_r_sum'];
                }
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $reported_num_user = array('error_message' => $error_message);
            return $reported_num_user;
        }

        global $db;


        try {
            $query_statement = $db->prepare($query_text2);
            $query_statement->bindValue(':rankReliabilityLimit', Reliability::rankReliabilityLimit);
            $query_statement->bindValue(':userReliabilityLimit', Reliability::userReliabilityLimit);
            $query_statement->bindValue(':cv_id', $cv_id);
            $query_statement->execute();
            $q02 = $query_statement->fetch(PDO::FETCH_ASSOC);
            if ($q02 == false) {
                $u_sum =0;
                $u_ranks_num = 0;
                $cv_u_ranks_num = 0;
                $cv_u_sum = 0;
            }
            else{
                $u_sum = $q02['u_sum'];
                $u_ranks_num = $q02['u_ranks_num'];
                if ($q02['cv_u_ranks_num']==null){
                    $cv_u_ranks_num=0;
                }else{
                    $cv_u_ranks_num= $q02['cv_u_ranks_num'];
                }
                if ($q02['cv_u_sum']==null){
                    $cv_u_sum=0;
                }else{
                    $cv_u_sum= $q02['cv_u_sum'];
                }
            }
        } catch (PDOException $ex) {
            $error_message = $ex->getMessage();
            $err = array('error_message' => $error_message);
            return $err;
        }
        $total_rank_num = $u_ranks_num + $r_ranks_num;
        
        
        $total_cv_rank_num = $cv_u_ranks_num + $cv_r_ranks_num;

        if ($total_rank_num==0){
            $total_avg_res=0;
        }
        else {
             try {
                 if ($u_ranks_num>0 and $r_ranks_num>0) {
                     $total_avg_res = (($r_sum * 2 / 3) + ($u_sum / 3)) / $total_rank_num;
                 }
                 else if($u_ranks_num>0){
                     $total_avg_res=$u_sum/ $total_rank_num;
                 }
                 else{
                     $total_avg_res=$r_sum/ $total_rank_num;
                 }
             }
             catch (PDOException $ex) {
                 $error_message = $ex->getMessage();
                 $err = array('error_message' => $error_message);
                 return $err;
             }
        }
        if ($total_cv_rank_num==0){
            $total_cv_avg_res=0;
        }
        else {
            try {
                if ($cv_u_ranks_num>0 and $cv_r_ranks_num>0) {
                    $total_cv_avg_res = (($cv_r_sum * 2 / 3) + ($cv_u_sum / 3)) / $total_cv_rank_num;
                }
                else if($cv_u_ranks_num>0){
                    $total_cv_avg_res=$cv_u_sum/ $total_cv_rank_num;
                }
                else{
                    $total_cv_avg_res=$cv_r_sum/ $total_cv_rank_num;
                }

      
            }
            catch (PDOException $ex) {
                $error_message = $ex->getMessage();
                $err = array('error_message' => $error_message);
                return $err;
            }
        }
        return array('userAvg'=>$total_cv_avg_res, 'crowdAvg'=>$total_avg_res);
    }
}


?>
