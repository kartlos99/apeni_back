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
$name = $postData->name;
$status = $postData->status;
$color = $postData->color;
$userID = $sessionData->userID;


if (is_null($id)) {
    $sql = "INSERT INTO `expense_category`(
        `name`,
        `status`,
        `color`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES(
        '$name',
        '$status',
        '$color',
        '$dateOnServer',
        $userID
    )";
} else {
    $sql = "UPDATE
        `expense_category`
    SET
        `name` = '$name',
        `status` = '$status',
        `color` = '$color',
        `modifyDate` = '$dateOnServer',
        `modifyUserID` = $userID
    WHERE
        `id` = $id";
}

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