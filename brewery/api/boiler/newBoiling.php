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

$startDate = $postData->startDate;
if (empty($startDate))
    $startDate = $timeOnServer;

echo json_encode($myData->insertBoiling(
    $postData->code,
    $startDate,
    $postData->density,
    $postData->amount,
    $postData->tankID,
    $postData->beerID,
    $postData->comment,
    $sessionData->userID
));

mysqli_close($dbLink);