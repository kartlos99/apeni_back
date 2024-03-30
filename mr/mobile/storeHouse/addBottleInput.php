<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$comment = "'$postData->comment'";
if (empty($postData->comment)) {
    $comment = "NULL";
}

global $con;
global $timeOnServer;
$operationTime = $postData->operationTime;

function isUpdate($firstItem): bool {
    return $firstItem["id"] > 0;
}

if (isset($postData->items) && count($postData->items) > 0) {

//    if (isUpdate($postData->items[0])) {
//
//        $sql = "UPDATE
//    `storehouse_bottle_input`
//SET
//    `regionID` = $sessionData->regionID,
//    `groupID` = [
//VALUE
//    -3 ],
//    `inputDate` = [
//VALUE
//    -4 ],
//    `distributorID` = [
//VALUE
//    -5 ],
//    `bottleID` = [
//VALUE
//    -6 ],
//    `count` = [
//VALUE
//    -7 ],
//    `chek` = [
//VALUE
//    -8 ],
//    `comment` = [
//VALUE
//    -9 ],
//    `modifyDate` = [
//VALUE
//    -10 ],
//    `modifyUserID` = [
//VALUE
//    -11 ]
//WHERE
//    1";
//    }

    $multiValue = "";
    for ($i = 0; $i < count($postData->items); $i++) {
        $bottleItem = $postData->items[$i];

        $bottleID = $bottleItem->bottleID;
        $count = $bottleItem->count;

//        $response[DATA] = $receiveDate;
//        die(json_encode($response));
        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$sessionData->regionID', '$postData->groupID', '$operationTime', '$postData->actingPerson', 
        '$bottleID', '$count', $comment, '$timeOnServer', '$sessionData->userID')";
    }

    $sql = "INSERT INTO `storehouse_bottle_input`(
                `regionID`,
                `groupID`,
                `inputDate`,
                `distributorID`,
                `bottleID`,
                `count`,
                `comment`,
                `modifyDate`,
                `modifyUserID`
            )
            VALUES " . $multiValue;

    if (mysqli_query($con, $sql)) {
        $response[DATA] = "bottle input saved";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}

echo json_encode($response);