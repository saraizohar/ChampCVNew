<?php

class TaskMatchingAlg
{
    private static function comperByGrade($a, $b)
    {
        if ($a["totalGrade"] === $b["totalGrade"])
            return 0;

        return $a["totalGrade"] < $b["totalGrade"] ? 1 : -1;
    }

    private static function getTotalRanks($avalableTasks)
    {  
        $totalNumOfRanks = 0;
        foreach ($avalableTasks as $task)
        {
            $totalNumOfRanks += $task["numOfRanks"];
        }
        return $totalNumOfRanks;
    }

    private static function calcMatchGrade($cvCat, $rankerCat)
    {
        $rankerSize = 0;
        $cvSize = 0;
        $intersection = 0;
        $union = 0;

        foreach ($cvCat as $key => $value)
        {
            $intersection += ($value & $rankerCat[$key]);
            $union += ($value | $rankerCat[$key]);
            $rankerSize +=  $rankerCat[$key];
            $cvSize += $value;
        }

        assert ($rankerSize != 0 and $cvSize != 0);
        
        return ($intersection/$rankerSize + $intersection/$cvSize + $intersection/$union)/3;
    }

    private static function calculateGrade(&$avalableTasks, $rankerCat)
    {
        $totalNumOfRanks = TaskMatchingAlg::getTotalRanks($avalableTasks);
        assert($totalNumOfRanks != 0 or count($avalableTasks) == 0);

        foreach ($avalableTasks as &$task) 
        {
            $rankingGrade = 1 - ($task["numOfRanks"] / $totalNumOfRanks);
            $catGrade = TaskMatchingAlg::calcMatchGrade($task["categories"], $rankerCat);

            $totalGrade = 0.6 * $rankingGrade + 0.4 * $catGrade;
            $task["totalGrade"] = $totalGrade;
        }
    }

    public static function getTasks($rankerID, $numOfTasks)
    {
        $avalableTasks = dataGetter::getAvailableCVs(100, $rankerID);
        if (key($avalableTasks) === 'error_message')
        {
            if ($avalableTasks['error_message'] === "no match found in the DB")
                $avalableTasks = array();
            else
                return $avalableTasks;
        }

        $rankerCat = dataGetter::getCategoriesOfUser($rankerID); //TODO haim test this
        if (key($rankerCat) === 'error_message')
            return $avalableTasks;

        TaskMatchingAlg::calculateGrade($avalableTasks, $rankerCat);

        usort($avalableTasks, "TaskMatchingAlg::comperByGrade");

        array_splice($avalableTasks, 2 * $numOfTasks);

        return $avalableTasks;
    }
}

?>