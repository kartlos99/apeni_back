<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$id = $postData->id ?? null;
$regionID = $sessionData->regionID;
$date = $postData->date;
$distributorID = $postData->distributorID;
$category = $postData->category;
$amount = $postData->amount;
$comment = $postData->comment;
$userID = 1;

//die(json_encode($sessionData));

$checkCategoryStatusSql = "SELECT `status` FROM `expense_category` WHERE `id`=$category";
$checkResult = $dbManager->getDataAsArray($checkCategoryStatusSql);
if ($checkResult[0]["status"] != 1)
    $dbManager->dieWithHttpError("selected category is not active!", 1030);

if (is_null($id)) {
    $sql = "INSERT INTO `xarjebi`(
        `regionID`,
        `tarigi`,
        `distributor_id`,
        `tanxa`,
        `category`,
        `comment`
    )
    VALUES(
        '$regionID',
        '$date',
        $distributorID,
        $amount,
        $category,
        '$comment'
    )";
} else {
    $sql = "UPDATE
    `xarjebi`
SET
    `distributor_id` = $distributorID,
    `tanxa` = $amount,
    `category` = $category,
    `comment` = '$comment'
WHERE
    `id` = $id";
}


echo json_encode($dbManager->baseInsert($sql));

$dbManager->closeConnection();