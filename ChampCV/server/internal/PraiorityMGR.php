<?php

/**
 * PraiorityMGR short summary.
 *
 * PraiorityMGR description.
 *
 * @version 1.0
 * @author haimsa
 */

class PraiorityMGR
{
    /*
     * modify $avalableTasks in place
     */
    public static function calcPrioritys(&$avalableTasks, $numOfTasks)
    {
        $bill = array();
        $uids = array();
        foreach ($avalableTasks as $task)
        {
            $uids[] = $task["userID"];
            $bill[$task["userID"]] = 0;
        }

        $userIdToPoints = dataGetter::getPointsForCVs($uids);
        if (key($userIdToPoints) === 'error_message')
            return $userIdToPoints;

        $toalTasks = count($avalableTasks);
        $price = basePrice;
        $numOfPlacesToAdvance = $numOfTasks;

        assert($numOfPlacesToAdvance <= $numOfTasks);

        do
        {
            for ($index = $numOfTasks; $index < $toalTasks; $index++)
            {
                $buyerTask = &$avalableTasks[$index];
                $buyerID = $buyerTask["userID"];

                if (PraiorityMGR::shouldBuy($userIdToPoints[$buyerID] - $bill[$buyerID], $buyerTask["totalGrade"], $toalTasks - $index, $price, $numOfPlacesToAdvance) )
                    PraiorityMGR::executeBuy($avalableTasks, $bill, $index, $index - $numOfPlacesToAdvance, $price );
            }

        } while (false); //TODO - in case we decide to ask everyone on the list again

        return $bill;
    }

    /*
     * change $avalableTasks and $bill in place
     */
    private static function executeBuy(&$avalableTasks, &$bill, $buyerIndex, $newIndex, $price)
    {
        $byerTask = $avalableTasks[$buyerIndex];

        //remove $byerTask from $avalableTasks 
        array_splice($avalableTasks, $buyerIndex, 1);

        //insert $byerTask to $avalableTasks in new index and push other elements
        array_splice($avalableTasks, $newIndex, 0, array($byerTask));
        
        if (array_key_exists($buyerIndex, $bill))
            $bill[$buyerIndex] += $price;
        else
            $bill[$buyerIndex] = $price;

        //echo "buy <br/>";
    }

    /*
     * $location = count($tasks) - $index
     */
    private static function shouldBuy($numOfPoints, $grade, $location, $price, $numOfPlaces)
    {
        $currentUtility = PraiorityMGR::calcUtility($numOfPoints, $grade, $location);
        $potentialUtility = PraiorityMGR::calcUtility($numOfPoints  - $price, $grade, $location + $numOfPlaces);
        return $potentialUtility > $currentUtility;
    }

    private static function calcUtility($numOfPoints, $grade, $location)
    {
        if ($numOfPoints < 0)
            return PHP_INT_MIN;

        return $numOfPoints + 50 * $grade * sqrt($location);
    }
}

?>