<?php

require_once( '../DBConnection/dataGetter.php'); //TODO HAIM should not be MOC
require_once( 'TaskMatchingAlg.php');
require_once( 'PraiorityMGR.php');
require_once( 'PricingAlg.php');

define("basePrice", 66);;

class TasksGetter
{
    public static function getTasks($rankerID, $numOfTasks)
    {
        $tasks = TaskMatchingAlg::getTasks($rankerID, $numOfTasks);
        if (key($tasks) === 'error_message')
            return $tasks;

        $bill = PraiorityMGR::calcPrioritys($tasks, ceil(count($tasks)/2)); //halfe of $tasks get the chance to buy

        if (key($bill) === 'error_message')
            return $bill;

        $ret = dataGetter::payForprivileges($bill);
        if ($ret != null)
            return $ret;

        $tasksForUI = TasksGetter::getTasksForUI($tasks, $numOfTasks);
        if (key($tasksForUI) === 'error_message')
            return $tasksForUI;

        PricingAlg::calcPriceForRecruter($tasksForUI);
        assert(count($tasksForUI) <= $numOfTasks);

        $tasksForUI = array('tasksList'=>$tasksForUI);
        return $tasksForUI;
    }

    private static function getTasksForUI(&$tasks,$numOfTasks)
    {
        $taskCVid =array();

        array_splice($tasks, $numOfTasks);
        foreach ($tasks as $task)
        {
            $taskCVid[] = $task["cvID"];
        }

        $tasksForUI = dataGetter::getCVsDataForUI($taskCVid);
        return $tasksForUI;        
    }
}


?>