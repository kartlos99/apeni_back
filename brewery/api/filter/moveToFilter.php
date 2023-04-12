<?php

namespace Apeni\JWT;

use FilterDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();
$userID = $sessionData->userID;

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new FilterDataManager();

$filterTankID = $postData->filteringTankModel->tank->ID;

$filtrationItem = $dbManager->getCurrentFilteringDataByTankId($filterTankID);

if (empty($filtrationItem)) {
    $insertResult = $dbManager->createFilteringItem(
        $postData->transferDate,
        $postData->fermentation->beerID,
        $filterTankID,
        $postData->comment,
        $userID
    );
    $filtrationItemID = $insertResult[RECORD_ID_KEY];
} else {
    $filtrationItemID = $filtrationItem["ID"];
}

$addMapResult = $dbManager->addPourToFilterMap(
    $postData->transferDate,
    $postData->transferAmount,
    $postData->fermentation->ID,
    $filtrationItemID,
    $postData->comment,
    $userID
);

echo json_encode($addMapResult);

$dbManager->closeConnection();