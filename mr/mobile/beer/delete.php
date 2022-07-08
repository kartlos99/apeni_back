<?php

namespace Apeni\JWT;

// use DataProvider;
use QueryHelper;
use DbKey;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$id   	= $postData->beerId;

if($id > 0){
    
    $sql = "UPDATE `ludi` SET `active` = 0 WHERE `ludi`.`id` = $id ";
    
    if(mysqli_query($con, $sql)){
        $response[DATA] = $id;
    } else {
        dieWithError(mysqli_errno($con), "ERROR: Could not able to execute $sql" . mysqli_error($con) );
    }
    
}else{
    dieWithError(999, "invaili beer id");
}

$vc = new VersionControl($con);
$vc->updateVersionFor(BEER_VCS);
$vc->updateVersionFor(PRICE_VCS);

echo json_encode($response);