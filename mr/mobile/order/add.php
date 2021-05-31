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


$sql_insert_order = "
INSERT INTO `orders`(`regionID`, `orderDate`, `orderStatusID`, `distributorID`, `clientID`, `comment`, `sortValue`, `modifyDate`, `modifyUserID`) 
VALUES (
'$sessionData->regionID',        
'$postData->orderDate',
$postData->orderStatus,
$postData->distributorID,
$postData->clientID,
$orderComment,
" . time() . ",
'$timeOnServer',
$postData->modifyUserID
)";

if (mysqli_query($con, $sql_insert_order)) {
    $orderID = mysqli_insert_id($con);

    // inserting order items

    $multiValue = "";
    for ($i = 0; $i < count($postData->items); $i++){
        $orderItem = $postData->items[$i];

        $beerID = $orderItem->beerID;
        $canTypeID = $orderItem->canTypeID;
        $count = $orderItem->count;
        $check = $orderItem->check ? 1 : 0;
        $modifyUserID = $orderItem->modifyUserID;

        if ($i > 0) { $multiValue .= ","; }
        $multiValue .= "('$orderID', '$beerID', '$canTypeID', '$count', $check, '$timeOnServer', '$modifyUserID')";
    }

    $sql_insert_items = "
        INSERT INTO `order_items`(
        `orderID`,`beerID`,`canTypeID`,`count`,`chek`,`modifyDate`,`modifyUserID` )
        VALUES " . $multiValue;

    if (mysqli_query($con, $sql_insert_items)) {
            $response[DATA] = "შეკვეთა დაემატა!";
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
//$response[DATA] = $sql;

echo json_encode($response);

// die json_encode($response);