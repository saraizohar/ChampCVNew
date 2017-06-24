<?php

include '../internal/TasksGetter.php';


if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $action = $_POST["action"];
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    switch ($action){
        case "getTasksList":
            $cid = $_POST["cid"];
            $isRecruiter = $_POST["isRecruiter"] == 'true';

            $result2 = TasksGetter::getTasks($cid, 10);
            
            break;
    }
    echo (json_encode($result2));
}
?>
