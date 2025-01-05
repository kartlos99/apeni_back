<?php

namespace Apeni\JWT;
use DbKey;
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

$user = $postData->user;

$addMode = $user->id == "";

if ($addMode) {
    $sql =
        "INSERT INTO `users` " .
        "(`username`, `pass`, `name`, `type`, `maker`, `tel`, `adress`, `comment`, `reg_date`) " .
        "VALUES " .
        "(
         '$user->username',
         '$postData->password', 
         '$user->name', 
         '$user->type',
         '$user->maker',
         '$user->tel',
         '$user->address',
         '$user->comment',
         '$timeOnServer'
         )";
} else {

    $setPass = $postData->changePass ? "`pass`='$postData->password', " : "";

    $sql =
        "UPDATE `users` SET " .
        "`username`= '$user->username', " .
        $setPass .
        "`type`=$user->type, " .
        "`maker`=$user->maker, " .
        "`name` = '$user->name', " .
        "`adress`= '$user->address', " .
        "`tel`='$user->tel', " .
        "`comment`='$user->comment' " .
        "WHERE" .
        "  `users`.`id` = $user->id ";
}

$insertResult = $dbManager->baseInsert($sql);
$newUserId = $insertResult[RECORD_ID_KEY];

if ($addMode) {
    $sqlInsertCustomerMap =
        "INSERT INTO " . DbKey::$USER_MAP_TB . " (`userID`, `regionID`) VALUES ('$newUserId', '$sessionData->regionID');";
    $dbManager->baseInsert($sqlInsertCustomerMap);
}

echo json_encode($insertResult);

$dbManager->closeConnection();