<?php

include '../internal/TasksGetter.php';


if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    switch ($action){
        case "getTasksList":
            $cid = $_POST["cid"];
            /*
            $task1 = array('id'=>456, 'url'=>'./server/resumes/111.pdf', "keywords"=>array('Python', '.Net', 'C++'), "question"=>'What do you think about my last job?', 'fields'=>array(1,2,3), 'price'=>5);
            $task2 = array('id'=>789, 'url'=>'./server/resumes/222.pdf', "keywords"=>array('JS', 'Java', 'PHP'), "question"=>'What do you think about my last job?', 'fields'=>array(3,4,5), 'price'=>15);
            $task3 = array('id'=>963, 'url'=>'./server/resumes/333.pdf', "keywords"=>array('Oracle', 'MySQL'), "question"=>'What do you think about my last job?', 'fields'=>array(2,3,4), 'price'=>30);
            $task4 = array('id'=>753, 'url'=>'./server/resumes/444.pdf', "keywords"=>array('Oracle', 'MySQL'), "question"=>'What do you think about my last job?', 'fields'=>array(1,3,5), 'price'=>45);
            $task5 = array('id'=>159, 'url'=>'./server/resumes/555.pdf', "keywords"=>array('Oracle', 'MySQL'), "question"=>'What do you think about my last job?', 'fields'=>array(6,7,8), 'price'=>2);
            $task6 = array('id'=>794, 'url'=>'./server/resumes/666.pdf', "keywords"=>array('Oracle', 'MySQL'), "question"=>'What do you think about my last job?', 'fields'=>array(8,7,6), 'price'=>90);
            $result = array('tasksList'=>array($task1, $task2, $task3, $task4, $task5, $task6));
            //*/

            $result2 = TasksGetter::getTasks($cid, 10);
            
            break;
    }
    echo (json_encode($result2));
}
?>