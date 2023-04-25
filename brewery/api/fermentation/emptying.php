<?php

namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$myData = new MyData($dbLink);

$comment = isset($postData->comment) ? "'$postData->comment'" : 'null';

$resp = $myData->emptyingFermentation(
    $postData->tankID,
    $postData->fermentationID,
    $postData->date,
    $postData->amount,
    $comment,
    $sessionData->userID
);


echo json_encode($resp);

mysqli_close($dbLink);