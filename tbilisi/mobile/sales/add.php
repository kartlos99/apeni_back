<?php

namespace Apeni\JWT;
// ---------- gadascem dRes, gibrunebs shekveTebs ----------

use OrderHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$orderHelper = new OrderHelper($con);

// **********************************************************************************

$response[DATA] = '';
$shouldChangeStatus = true;

$saleComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $saleComment = "NULL";
}

$getOrderSql = "
SELECT ifnull(max(o.ID), 0) AS orderID FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
WHERE di.code = 'order_active' AND o.`clientID` = " . $postData->clientID;

$result = mysqli_query($con, $getOrderSql);
// obieqtze bolo aqtiuri Sekvetis ID
$orderID = mysqli_fetch_assoc($result)['orderID'];

// check for valid barrels values
if (isset($postData->barrels)) {
    $balanceMap = getBalanceMap($con, $postData->clientID);
    foreach ($postData->barrels as $barrelOutput) {
        if (!isset($balanceMap[$barrelOutput->canTypeID]) || $barrelOutput->count > $balanceMap[$barrelOutput->canTypeID]['balance']) {
            dieWithError(
                ER_CODE_EXTRA_BARREL_OUTPUT,
                sprintf(ER_TEXT_EXTRA_BARREL_OUTPUT, $balanceMap[$barrelOutput->canTypeID]['dasaxeleba'], $barrelOutput->count)
            );
        }
    }
}


if (isset($postData->sales) && count($postData->sales) > 0) {

    // Check for balance in Storehouse
    $storeBalanceArr = getFullBarrelsBalanceInStore($con);
    foreach ($postData->sales as $saleItm) {
        $stRow = [];
        array_filter($storeBalanceArr, function ($stItem) {
            global $stRow;
            global $saleItm;
            if ($stItem['beerID'] == $saleItm->beerID && $stItem['barrelID'] == $saleItm->canTypeID) {
                $stRow = $stItem;
                return true;
            }
            return false;
        });
        if (!isset($stRow['balance']) || $stRow['balance'] < $saleItm->count)
            dieWithError(COMMON_ERROR_CODE, ER_TEXT_EXTRA_BARREL_SALE);
    }

    if ($orderID == 0) {
        // if no order make it
        $shouldChangeStatus = false;

        $sql_insert_order = "
    INSERT INTO `orders`(`orderDate`, `orderStatusID`, `distributorID`, `clientID`, `comment`, `modifyDate`, `modifyUserID`) 
    VALUES (
    '$dateOnServer',
    " . ORDER_STATUS_AUTO_CREATED . ",
    $postData->distributorID,
    $postData->clientID,
    $saleComment,
    '$timeOnServer',
    $postData->modifyUserID
    )";

        if (mysqli_query($con, $sql_insert_order)) {
            $orderID = mysqli_insert_id($con);
        }
    }

    $multiValue = "";
    for ($i = 0; $i < count($postData->sales); $i++) {
        $saleItem = $postData->sales[$i];

        $saleDate = $saleItem->saleDate;
        $beerID = $saleItem->beerID;
        $price = $saleItem->price;
        $canTypeID = $saleItem->canTypeID;
        $count = $saleItem->count;
//        $orderID = $saleItem->orderID;

        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$saleDate', '$postData->clientID', '$postData->distributorID', '$beerID', '$price',
        '$canTypeID', '$count', '$orderID', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $salesInsertSql = "
    INSERT INTO `sales`(
        `saleDate`,
        `clientID`,
        `distributorID`,
        `beerID`,
        `unitPrice`,
        `canTypeID`,
        `count`,
        `orderID`,
        `comment`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES " . $multiValue;

    if (mysqli_query($con, $salesInsertSql)) {
        $response[DATA] = "sale-done";
        if ($shouldChangeStatus)
            $orderHelper->checkOrderCompletion($orderID);
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}


if (isset($postData->barrels)) {

    $multiValue = "";
    for ($i = 0; $i < count($postData->barrels); $i++) {
        $barrelItem = $postData->barrels[$i];

        $outputDate = $barrelItem->outputDate;
        $canTypeID = $barrelItem->canTypeID;
        $count = $barrelItem->count;

        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$outputDate', '$postData->clientID', '$postData->distributorID', 
        '$canTypeID', '$count', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $barrelsInsertSql = "
    INSERT INTO `barrel_output`(
        `outputDate`,
        `clientID`,
        `distributorID`,
        `canTypeID`,
        `count`,
        `comment`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES " . $multiValue;

    if (mysqli_query($con, $barrelsInsertSql)) {
        $response[DATA] .= " barrel-done";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}

if (isset($postData->money)) {

    $multiValue = "";
    for ($i = 0; $i < count($postData->money); $i++) {
        $moneyItm = $postData->money[$i];

        $takeMoneyDate = $moneyItm->takeMoneyDate;
        $amount = $moneyItm->amount;
        $paymentType = $moneyItm->paymentType;

        if ($i > 0) $multiValue .= ",";

        $multiValue .= "('$takeMoneyDate', '$postData->clientID', '$postData->distributorID', 
        '$amount', '$paymentType', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $moneyInsertSql = "
    INSERT INTO `moneyoutput`(
        `tarigi`,
        `obieqtis_id`,
        `distributor_id`,
        `tanxa`,
        `paymentType`,
        `comment`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES " . $multiValue;

    if (mysqli_query($con, $moneyInsertSql)) {
        $response[DATA] .= " money-done";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}


echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));

function getBalanceMap($dbConn, $clientID = 0)
{
    $sqlQuery = "CALL getBarrelBalanceByID($clientID);";
    $mMap = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        $mMap[$rs['canTypeID']] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $mMap;
}

function getFullBarrelsBalanceInStore($dbConn)
{
    $sql = "call getFullBarrelsBalanceInStore(0);";
    $fArr = [];
    $result = mysqli_query($dbConn, $sql);
    while ($rs = mysqli_fetch_assoc($result)) {
        $fArr[] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $fArr;
}
