<?php
namespace Apeni\JWT;
use OrderHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');
$postData = json_decode($json);

$orderHelper = new OrderHelper($con);

$response[DATA] = "0";

$orderComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $orderComment = "NULL";
}


$orderUpdateSql = "
UPDATE
    `orders`
SET
    `orderDate` = '$postData->orderDate',
    `orderStatusID` = $postData->orderStatus,
    `distributorID` = $postData->distributorID,
    `clientID` = $postData->clientID,
    `comment` = $orderComment,
    `modifyDate` = '$timeOnServer',
    `modifyUserID` = $postData->modifyUserID
WHERE
    ID = " . $postData->ID;

if (mysqli_query($con, $orderUpdateSql)) {
    $response[DATA] = "update-done ";

    $deleteOldItemsSql = "DELETE FROM `order_items` WHERE `orderID` = " . $postData->ID;
    mysqli_query($con, $deleteOldItemsSql);

    $multiValue = "";
    for ($i = 0; $i < count($postData->items); $i++){
        $orderItem = $postData->items[$i];

        $beerID = $orderItem->beerID;
        $canTypeID = $orderItem->canTypeID;
        $count = $orderItem->count;
        $check = $orderItem->check ? 1 : 0;
        $modifyUserID = $orderItem->modifyUserID;

        if ($i > 0) { $multiValue .= ","; }
        $multiValue .= "('$postData->ID', '$beerID', '$canTypeID', '$count', $check, '$timeOnServer', '$modifyUserID')";
    }

    $sql_insert_items = "
        INSERT INTO `order_items`(
        `orderID`,`beerID`,`canTypeID`,`count`,`chek`,`modifyDate`,`modifyUserID` )
        VALUES " . $multiValue;

    if (mysqli_query($con, $sql_insert_items)) {
        $response[DATA] = "შეკვეთა განახლებულია!";
        $orderHelper->checkOrderCompletion($postData->ID);
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }

} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));