<?php

namespace Apeni\JWT;

use FilterDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new FilterDataManager();

$comment = isset($postData->comment) ? "'$postData->comment'" : 'null';

$resp = $dbManager->emptyingFilterTank(
    $postData->tankID,
    $postData->beerOriginEntityID,
    $postData->date,
    $postData->amount,
    $comment,
    $sessionData->userID
);


echo json_encode($resp);

mysqli_close($dbLink);