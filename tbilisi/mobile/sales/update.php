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

$saleComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $saleComment = "NULL";
}

if (isset($postData->sales) && count($postData->sales) > 0) {
    $saleItem = $postData->sales[0];

    // Check for balance in Storehouse
    $storeBalanceArr = getFullBarrelsBalanceInStore($con, $saleItem->ID);
    $stRow = [];
    array_filter($storeBalanceArr, function ($stItem) {
        global $stRow;
        global $saleItem;
        if ($stItem['beerID'] == $saleItem->beerID && $stItem['barrelID'] == $saleItem->canTypeID) {
            $stRow = $stItem;
            return true;
        }
        return false;
    });
    if (!isset($stRow['balance']) || $stRow['balance'] < $saleItem->count)
        dieWithError(COMMON_ERROR_CODE, ER_TEXT_EXTRA_BARREL_SALE);


    $response[DATA] = $saleItem->ID;
    if ($saleItem->ID > 0) {

        $saleDate = $saleItem->saleDate;
        $beerID = $saleItem->beerID;
        $price = $saleItem->price;
        $canTypeID = $saleItem->canTypeID;
        $count = $saleItem->count;

        $updateSql = "
        UPDATE `sales` SET 
            `saleDate` = '$saleDate',
            `beerID` = $beerID,
            `unitPrice` = '$price',
            `canTypeID` = $canTypeID,
            `count` = $count,
            `comment` = $saleComment,
            `modifyDate` = '$timeOnServer',
            `modifyUserID` = $postData->modifyUserID
        WHERE 
            `ID` = $saleItem->ID
        ";

        if (mysqli_query($con, $updateSql)) {
            $response[DATA] = "sale-updated";
            $orderHelper->checkOrderCompletion($saleItem->orderID);
        } else {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = mysqli_error($con);
            $response[ERROR_CODE] = mysqli_errno($con);
        }

    }
}

if (isset($postData->barrels) && count($postData->barrels) > 0) {
    $barrelItem = $postData->barrels[0];

    // check for valid empty barrel amount
    $balanceMap = getBalanceMap($con, $postData->clientID, $barrelItem->ID);
    if (!isset($balanceMap[$barrelItem->canTypeID]) || $barrelItem->count > $balanceMap[$barrelItem->canTypeID]['balance']) {
        dieWithError(
            ER_CODE_EXTRA_BARREL_OUTPUT,
            sprintf(ER_TEXT_EXTRA_BARREL_OUTPUT, $balanceMap[$barrelItem->canTypeID]['dasaxeleba'], $barrelItem->count)
        );
    }

    $response[DATA] = $barrelItem->ID;
    if ($barrelItem->ID > 0) {

        $barrelsUpdateSql = "
        UPDATE `barrel_output` SET
            `outputDate` = '$barrelItem->outputDate',
            `canTypeID` = '$barrelItem->canTypeID',
            `count` = '$barrelItem->count',
            `comment` = $saleComment,
            `modifyDate` = '$timeOnServer',
            `modifyUserID` = $postData->modifyUserID
        WHERE 
            `ID` = $barrelItem->ID";

        if (mysqli_query($con, $barrelsUpdateSql)) {
            $response[DATA] = "barrel-updated";
        } else {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = mysqli_errno($con) . " $barrelsUpdateSql " . mysqli_error($con);
            $response[ERROR_CODE] = ER_CODE_BARREL_OUTPUT;
        }
    }
}

if (isset($postData->money) && count($postData->money) > 0) {
    $moneyItm = $postData->money[0];

    $takeMoneyDate = $moneyItm->takeMoneyDate;
    $amount = $moneyItm->amount;
    $paymentType = $moneyItm->paymentType;
    $id = $moneyItm->ID;

    $moneyUpdateSql = "
    UPDATE `moneyoutput` SET
        `tarigi` = '$takeMoneyDate',
        `tanxa` = '$amount',
        `paymentType` = '$paymentType',
        `comment` = $saleComment,
        `modifyDate` = '$timeOnServer',
        `modifyUserID` = $postData->modifyUserID
    WHERE
        `ID` = $id";

    if (mysqli_query($con, $moneyUpdateSql)) {
        $response[DATA] = "money-updated";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}

echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));

function getBalanceMap($dbConn, $clientID, $exceptRecID)
{
    $sqlQuery = "CALL getBarrelBalanceByID($clientID, $exceptRecID);";
    $mMap = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        $mMap[$rs['canTypeID']] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $mMap;
}

function getFullBarrelsBalanceInStore($dbConn, $exceptRecID)
{
    $sql = "call getFullBarrelsBalanceInStore(0, $exceptRecID);";
    $fArr = [];
    $result = mysqli_query($dbConn, $sql);
    while ($rs = mysqli_fetch_assoc($result)) {
        $fArr[] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $fArr;
}