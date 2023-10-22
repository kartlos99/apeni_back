<?php

namespace Apeni\JWT;

use OrderHelper;
use DataProvider;
use QueryHelper;
use ChangesReporter;

require_once "../../ChangesReporter.php";

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

$saleComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $saleComment = "NULL";
}

if (isset($postData->sales) && count($postData->sales) > 0) {
    $saleItem = $postData->sales[0];

    if (hasOwnStorage($con, $sessionData->regionID)) {
        // Check for balance in Storehouse
        $storeBalanceArr = getFullBarrelsBalanceInStore($con, $saleItem->ID, $sessionData->regionID);
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
    } else {
        // check balance in global StoreHouse
        $globalStorageDada = $dataProvider->sqlToArray(
            $queryHelper->queryGlobalStoreBalance($postData->sales[0]->saleDate, $saleItem->ID)
        );
        $amountByBarrelMap = [];
        foreach ($globalStorageDada as $row) {
            $amountByBarrelMap[$row['id']] = $row['initialAmount'] + $row['globalIncome'] - $row['globalOutput'];
        }
        foreach ($postData->sales as $saleItm) {
            if ($saleItm->count > $amountByBarrelMap[$saleItm->canTypeID])
                dieWithError(COMMON_ERROR_CODE, ER_TEXT_EXTRA_BARREL_SALE);
        }
    }

    $response[DATA] = $saleItem->ID;
    if ($saleItem->ID > 0) {

        $saleDate = $saleItem->saleDate;
        $beerID = $saleItem->beerID;
        $price = $saleItem->price;
        $canTypeID = $saleItem->canTypeID;
        $count = $saleItem->count;

        $updateSql = "
        UPDATE `$SALES_TB` SET 
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

        $reporter = new ChangesReporter($sessionData->userID);
        $reporter->checkRecord($SALES_TB, $saleItem->ID);

        if (mysqli_query($con, $updateSql)) {
            $response[DATA] = "sale-updated";
            $response[LOG_RECORD_ID_KEY] = $reporter->logAsNeed();
            $orderID = $orderHelper->getActiveOrderIDForClient($postData->clientID, $sessionData->regionID);
            if ($orderID > 0)
                $orderHelper->checkOrderCompletion($orderID);
        } else {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = mysqli_error($con);
            $response[ERROR_CODE] = mysqli_errno($con);
        }
        $reporter->closeConnection();
    }
}

if (isset($postData->barrels) && count($postData->barrels) > 0) {
    $barrelItem = $postData->barrels[0];

    // check for valid empty barrel amount : except 'zugdidi' id=64
    if ($postData->clientID != 64) {
        $balanceMap = getBalanceMap($con, $postData->clientID, $barrelItem->ID);
        if (!isset($balanceMap[$barrelItem->canTypeID]) || $barrelItem->count > $balanceMap[$barrelItem->canTypeID]['balance']) {
            dieWithError(
                ER_CODE_EXTRA_BARREL_OUTPUT,
                sprintf(ER_TEXT_EXTRA_BARREL_OUTPUT, $balanceMap[$barrelItem->canTypeID]['dasaxeleba'], $barrelItem->count)
            );
        }
    }

    $response[DATA] = $barrelItem->ID;
    if ($barrelItem->ID > 0) {

        $barrelsUpdateSql = "
        UPDATE $BARREL_OUTPUT_TB SET
            `outputDate` = '$barrelItem->outputDate',
            `canTypeID` = '$barrelItem->canTypeID',
            `count` = '$barrelItem->count',
            `comment` = $saleComment,
            `modifyDate` = '$timeOnServer',
            `modifyUserID` = $postData->modifyUserID
        WHERE 
            `ID` = $barrelItem->ID";

        $reporter = new ChangesReporter($sessionData->userID);
        $reporter->checkRecord($BARREL_OUTPUT_TB, $barrelItem->ID);

        if (mysqli_query($con, $barrelsUpdateSql)) {
            $response[DATA] = "barrel-updated";
            $response[LOG_RECORD_ID_KEY] = $reporter->logAsNeed();
        } else {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = mysqli_errno($con) . " $barrelsUpdateSql " . mysqli_error($con);
            $response[ERROR_CODE] = ER_CODE_BARREL_OUTPUT;
        }
        $reporter->closeConnection();
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

    $reporter = new ChangesReporter($sessionData->userID);
    $reporter->checkRecord($MONEY_OUTPUT_TB, $moneyItm->ID);

    if (mysqli_query($con, $moneyUpdateSql)) {
        $response[DATA] = "money-updated";
        $response[LOG_RECORD_ID_KEY] = $reporter->logAsNeed();
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
    $reporter->closeConnection();
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

function getFullBarrelsBalanceInStore($dbConn, $exceptRecID, $regionID)
{
    $sql = "call getFullBarrelsBalanceInStore(0, $exceptRecID, $regionID);";
    $fArr = [];
    $result = mysqli_query($dbConn, $sql);
    while ($rs = mysqli_fetch_assoc($result)) {
        $fArr[] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $fArr;
}