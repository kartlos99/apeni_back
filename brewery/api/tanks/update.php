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

echo json_encode($myData->updateTank(
    $postData->ID,
    $postData->number,
    $postData->title,
    $postData->volume,
    $postData->tankType,
    $postData->comment,
    $postData->status,
    $postData->sortValue,
    $sessionData->userID
));

mysqli_close($dbLink);