<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

const MODIFY_REGION_PERMISSION_CODE = "permManageRegion";

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$user = $postData->user;

$addMode = $user->id == "";

$regionIDsString = implode(",", $postData->regionIDs);

$hasPermissionToModifyRegion = $dbManager->hasPermission(MODIFY_REGION_PERMISSION_CODE, $sessionData->userID);

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

    $regionMappingSql = "INSERT INTO %s (`userId`, `regionId`, `state`) SELECT %s, regions.id, regions.id IN ($regionIDsString) FROM regions;";

    if (!$hasPermissionToModifyRegion && $sessionData->regionID != $regionIDsString) {
        dieWithDefaultHttpError("no permission to modify region!", 1480);
    }
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

    $regionMappingSql = "UPDATE %s map
        SET `state`= map.regionId IN ($regionIDsString)
        WHERE map.userId = %s ";
}

$insertResult = $dbManager->baseInsert($sql);
$newUserId = $addMode ? $insertResult[RECORD_ID_KEY] : $user->id;

if ($hasPermissionToModifyRegion || $addMode) {
    $dbManager->baseInsert(sprintf($regionMappingSql, DbKey::$USER_TO_REGION_MAP_TB, $newUserId));
}

echo json_encode($insertResult);

$dbManager->closeConnection();