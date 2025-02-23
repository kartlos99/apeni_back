<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$bottleId = json_decode($json);

if ($bottleId > 0) {

    $usageCheckSql = "
        SELECT id FROM `bottle_sales` 
        WHERE `bottleID` = $bottleId 
        UNION
        SELECT id FROM `order_items_bottle` 
        WHERE `bottleID` = $bottleId ";


    $usageResult = $dbManager->getDataAsArray($usageCheckSql);
    if (count($usageResult) > 0)
        $bottleStatus = BOTTLE_STATUS_DELETE_FROM_USAGE;
    else
        $bottleStatus = BOTTLE_STATUS_DELETE_FULL;

    $deleteBottleSql = "UPDATE `bottles` 
            SET
                `status` = $bottleStatus,
                `modifyUserID` = $sessionData->userID,
                `modifyDate` = '$timeOnServer'
            WHERE `id`= $bottleId ";

//die($updateBeerSql);
    $dbManager->baseInsert($deleteBottleSql);
} else {
    throwHttpError("invalid bottle id", 999);
}

echo json_encode($dbManager->getDataAsArray(AllBottleSqlQuery));

$dbManager->closeConnection();
