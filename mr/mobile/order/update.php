<?php

namespace Apeni\JWT;

use OrderHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');
$postData = json_decode($json);

const TEXT_ORDER_IS_UPDATED = "შეკვეთა განახლებულია!";

$orderHelper = new OrderHelper($con);

$response[DATA] = "0";

$orderComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $orderComment = "NULL";
}
$orderRegion = $postData->regionID == 0 ? $sessionData->regionID : $postData->regionID;

$orderID = $postData->ID;

$orderUpdateSql = "
UPDATE
    `orders`
SET
    `regionID` = '$orderRegion',
    `orderDate` = '$postData->orderDate',
    `orderStatusID` = $postData->orderStatus,
    `distributorID` = $postData->distributorID,
    `clientID` = $postData->clientID,
    `comment` = $orderComment,
    `modifyDate` = '$timeOnServer',
    `modifyUserID` = $sessionData->userID
WHERE
    ID = " . $orderID;

if (mysqli_query($con, $orderUpdateSql)) {
    $response[DATA] = "update-done ";

    $deleteOldItemsSql = "DELETE FROM `order_items` WHERE `orderID` = " . $orderID;
    mysqli_query($con, $deleteOldItemsSql);

    $deleteOldBottleItemsSql = "DELETE FROM `order_items_bottle` WHERE `orderID` = " . $orderID;
    mysqli_query($con, $deleteOldBottleItemsSql);

    if (count($postData->items) > 0) {
        $multiValue = "";
        for ($i = 0; $i < count($postData->items); $i++) {
            $orderItem = $postData->items[$i];

            $beerID = $orderItem->beerID;
            $canTypeID = $orderItem->canTypeID;
            $count = $orderItem->count;
            $check = $orderItem->check ? 1 : 0;
            $modifyUserID = $sessionData->userID;

            if ($i > 0) {
                $multiValue .= ",";
            }
            $multiValue .= "('$orderID', '$beerID', '$canTypeID', '$count', $check, '$timeOnServer', '$modifyUserID')";
        }

        $sql_insert_items = "
        INSERT INTO `order_items`(
        `orderID`,`beerID`,`canTypeID`,`count`,`chek`,`modifyDate`,`modifyUserID` )
        VALUES " . $multiValue;

        if (mysqli_query($con, $sql_insert_items)) {
            $response[DATA] = TEXT_ORDER_IS_UPDATED;
        } else {
            dieWithError(mysqli_errno($con), mysqli_error($con));
        }
    }

    if (count($postData->bottleItems) > 0) {
        $multiValue = "";
        for ($i = 0; $i < count($postData->bottleItems); $i++) {
            $orderItem = $postData->bottleItems[$i];

            if ($i > 0)
                $multiValue .= ",";

            $multiValue .= "('$orderID', '$orderItem->bottleID', '$orderItem->count', '$sessionData->userID')";
        }

        $sql_insert_items = "
        INSERT INTO `order_items_bottle`(
            `orderID`,
            `bottleID`,
            `count`,
            `modifyUserID`
        )
        VALUES " . $multiValue;

        if (mysqli_query($con, $sql_insert_items)) {
            $response[DATA] = TEXT_ORDER_IS_UPDATED;
        } else {
            dieWithError(mysqli_errno($con), mysqli_error($con));
        }
    }

    $orderHelper->checkOrderCompletion($orderID);

} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));