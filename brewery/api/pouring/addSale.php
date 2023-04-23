<?php

namespace Apeni\JWT;

use PourDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();
$userID = $sessionData->userID;

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new PourDataManager();

$comment = isset($postData->comment) ? "'$postData->comment'" : 'null';

$addResult = $dbManager->addSale(
    $postData->saleDate,
    $postData->clientID,
    $postData->producedBeerID,
    $postData->unitPrice,
    $postData->barrelID,
    $postData->count,
    $postData->tankID,
    $postData->beerOriginID,
    $comment,
    $userID
);

echo json_encode($addResult);

$dbManager->closeConnection();