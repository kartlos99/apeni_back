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
$postData = json_decode($json);

$code = $postData->code;
$value = $postData->paramValue;

$sqlUpdateSettings = "
UPDATE `dictionary_items` SET 
    `valueInt` = '$value',
    `modifyDate` = '$timeOnServer',
    `modifyUserID` = '$sessionData->userID'
WHERE `code` = '$code' ";

$dbManager->baseInsert($sqlUpdateSettings);
echo json_encode($postData);

$dbManager->closeConnection();