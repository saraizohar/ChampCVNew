<?php

//require_once('./DBMusiciansDataRetrival.php');

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $file = $_FILES['file'];
    //Use this CID to save the URL in the DB. Maybe get the serial number of the DB to save the file with the serial number as name
    $cid = $_POST['cid'];
    $uploads_dir = "./resumes";
    $isSuccess = move_uploaded_file ( $file['tmp_name'] , "$uploads_dir/$cid.pdf");
    $result = true;

    echo (json_encode($result));
}
?>