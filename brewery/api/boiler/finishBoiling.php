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

$fermentationStartResult = $myData->insertFermentation(
    $postData->code,
    $postData->density,
    $postData->comment,
    $startDate,
    $sessionData->userID
);

if ($fermentationStartResult[RECORD_ID_KEY] > 0) {
    $myData->mapBoilingToFermentation(
        $postData->boilingID,
        $fermentationStartResult[RECORD_ID_KEY],
        $postData->amount
    );
}

echo json_encode($fermentationStartResult);

mysqli_close($dbLink);