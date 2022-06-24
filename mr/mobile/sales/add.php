<?php

namespace Apeni\JWT;
// ---------- gadascem dRes, gibrunebs shekveTebs ----------

use OrderHelper;
use DataProvider;
use QueryHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$orderHelper = new OrderHelper($con);
$dataProvider = new DataProvider($con);
$queryHelper = new QueryHelper();

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
WHERE di.code = 'order_active' AND o.`regionID` = {$sessionData->regionID} AND o.`clientID` = " . $postData->clientID;

$result = mysqli_query($con, $getOrderSql);
// obieqtze bolo aqtiuri Sekvetis ID
$orderID = mysqli_fetch_assoc($result)['orderID'];

// check for valid barrels values : except 'zugdidi' id=64
if (isset($postData->barrels) && $postData->clientID != 64) {
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

    if (hasOwnStorage($con, $sessionData->regionID)) {
        // Check for balance in Storehouse
        $storeBalanceArr = getFullBarrelsBalanceInStore($con, $sessionData->regionID);
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
    } else {
        // check balance in global StoreHouse
        $globalStorageDada = $dataProvider->sqlToArray($queryHelper->queryGlobalStoreBalance($postData->sales[0]->saleDate));
        $amountByBarrelMap = [];
        foreach ($globalStorageDada as $row) {
            $amountByBarrelMap[$row['id']] = $row['initialAmount'] + $row['globalIncome'] - $row['globalOutput'];
        }
        foreach ($postData->sales as $saleItm) {
            if ($saleItm->count > $amountByBarrelMap[$saleItm->canTypeID])
                dieWithError(COMMON_ERROR_CODE, ER_TEXT_EXTRA_BARREL_SALE);
        }
    }


    if ($orderID == 0) {
        // if no order make it
        $shouldChangeStatus = false;

        $sql_insert_order = "
    INSERT INTO `orders`(`regionID`, `orderDate`, `orderStatusID`, `distributorID`, `clientID`, `comment`, `modifyDate`, `modifyUserID`) 
    VALUES (
    '$sessionData->regionID',
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
        $multiValue .= "('$sessionData->regionID', '$saleDate', '$postData->clientID', '$postData->distributorID', '$beerID', '$price',
        '$canTypeID', '$count', '$orderID', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $salesInsertSql = "
    INSERT INTO `sales`(
        `regionID`, 
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

        if ($postData->isReplace == 1) {

            $multiValue = "";
            for ($i = 0; $i < count($postData->sales); $i++) {
                $saleItem = $postData->sales[$i];

                $outputDate = $saleItem->saleDate;
                $canTypeID = $saleItem->canTypeID;
                $count = $saleItem->count;

                if ($i > 0) {
                    $multiValue .= ",";
                }
                $multiValue .= "('$sessionData->regionID', '$outputDate', '$postData->clientID', '$postData->distributorID', '$canTypeID', '$count', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
            }

            $barrelsInsertSql = "
                INSERT INTO `barrel_output`(
                    `regionID`, 
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

//            echo $barrelsInsertSql . " ";
            if (mysqli_query($con, $barrelsInsertSql))
                $response[DATA] .= "-replace-";
            else {
                $response[SUCCESS] = false;
                $response[ERROR_TEXT] = mysqli_errno($con) . " " . mysqli_error($con);
                $response[ERROR_CODE] = ER_CODE_BARREL_OUTPUT;
            }
        }
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_errno($con) . " " . mysqli_error($con);
        $response[ERROR_CODE] = ER_CODE_ADD_SALES;
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
        $multiValue .= "('$sessionData->regionID', '$outputDate', '$postData->clientID', '$postData->distributorID', 
        '$canTypeID', '$count', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $barrelsInsertSql = "
    INSERT INTO `barrel_output`(
        `regionID`, 
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
        $response[ERROR_TEXT] = mysqli_errno($con) . " " . mysqli_error($con);
        $response[ERROR_CODE] = ER_CODE_BARREL_OUTPUT;
    }
}

if (isset($postData->money) && count($postData->money) > 0) {

    $multiValue = "";
    for ($i = 0; $i < count($postData->money); $i++) {
        $moneyItm = $postData->money[$i];

        $takeMoneyDate = $moneyItm->takeMoneyDate;
        $amount = round($moneyItm->amount, 2, PHP_ROUND_HALF_UP );
        $paymentType = $moneyItm->paymentType;

        if ($i > 0) $multiValue .= ",";

        $multiValue .= "('$sessionData->regionID', '$takeMoneyDate', '$postData->clientID', '$postData->distributorID', 
        '$amount', '$paymentType', $saleComment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $moneyInsertSql = "
    INSERT INTO `moneyoutput`(
        `regionID`,
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
        $response[ERROR_TEXT] = mysqli_errno($con) . " " . mysqli_error($con);
        $response[ERROR_CODE] = ER_CODE_MONEY_OUTPUT;
    }
}


echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));

function getBalanceMap($dbConn, $clientID = 0)
{
    $sqlQuery = "CALL getBarrelBalanceByID($clientID, 0);";
    $mMap = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        $mMap[$rs['canTypeID']] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $mMap;
}

function getFullBarrelsBalanceInStore($dbConn, $regionID)
{
    $sql = "call getFullBarrelsBalanceInStore(0, 0, $regionID);";
    $fArr = [];
    $result = mysqli_query($dbConn, $sql);
    while ($rs = mysqli_fetch_assoc($result)) {
        $fArr[] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $fArr;
}
