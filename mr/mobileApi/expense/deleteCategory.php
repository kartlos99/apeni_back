<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
// $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$id = $postData->id;
$userID = 1;

$checkUsageSql = "SELECT COUNT(`id`) AS useCount FROM `xarjebi` WHERE `category` = $id";

$usageResult = $dbManager->getDataAsArray($checkUsageSql);

if ($usageResult[0]["useCount"] > 0)
    $status = 0;
else
    $status = -1;
    
$sql = "UPDATE
    `expense_category`
SET
    `status` = '$status',
    `modifyDate` = '$dateOnServer',
    `modifyUserID` = $userID
WHERE
    `id` = $id";

$dbManager->baseInsert($sql);

$categoriesSql = "SELECT
    *
FROM
    `expense_category`
WHERE
    `status` >= 0";
$categoriesList = $dbManager->getDataAsArray($categoriesSql);


echo json_encode($categoriesList);

$dbManager->closeConnection();