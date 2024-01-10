<?php
namespace Apeni\JWT;
// ---------- gadascem dRes, gibrunebs shekveTebs ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$response[DATA] = "0";


$orderComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $orderComment = "NULL";
}
$orderRegion = $postData->regionID == 0 ? $sessionData->regionID : $postData->regionID;

$sql_insert_order = "
INSERT INTO `orders`(`regionID`, `orderDate`, `orderStatusID`, `distributorID`, `clientID`, `comment`, `sortValue`, `modifyDate`, `modifyUserID`) 
VALUES (
'$orderRegion',        
'$postData->orderDate',
'$postData->orderStatus',
'$postData->distributorID',
'$postData->clientID',
$orderComment,
" . time() . ",
'$timeOnServer',
$postData->modifyUserID
)";

if (mysqli_query($con, $sql_insert_order)) {
    $orderID = mysqli_insert_id($con);

    // inserting order items

    if (count($postData->items) > 0) {
        $multiValue = "";
        for ($i = 0; $i < count($postData->items); $i++) {
            $orderItem = $postData->items[$i];

            $beerID = $orderItem->beerID;
            $canTypeID = $orderItem->canTypeID;
            $count = $orderItem->count;
            $check = $orderItem->check ? 1 : 0;

            if ($i > 0) {
                $multiValue .= ",";
            }
            $multiValue .= "('$orderID', '$beerID', '$canTypeID', '$count', $check, '$timeOnServer', '$sessionData->userID')";
        }

        $sql_insert_items = "
        INSERT INTO `order_items`(
        `orderID`,`beerID`,`canTypeID`,`count`,`chek`,`modifyDate`,`modifyUserID` )
        VALUES " . $multiValue;

        if (mysqli_query($con, $sql_insert_items)) {
            $response[DATA] = "შეკვეთა დაემატა!";
        } else {
            dieWithError(mysqli_errno($con), mysqli_error($con));
        }
    }
    if (count($postData->bottleItems) > 0) {
//        record bottle order items
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
            $response[DATA] = "შეკვეთა დაემატა!";
        } else {
            dieWithError(mysqli_errno($con), mysqli_error($con));
        }
    }

} else {
    dieWithError(mysqli_errno($con), mysqli_error($con));
}
//$response[DATA] = $sql;

echo json_encode($response);

// die json_encode($response);