<?php
namespace Apeni\JWT;
// ---------- gadascem dRes, gibrunebs shekveTebs ----------

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
            $response[ERROR_TEXT] = mysqli_error($con);
            $response[ERROR_CODE] = mysqli_errno($con);
        }
    }
}

if (isset($postData->money)) {

    $takeMoneyDate = $postData->money->takeMoneyDate;
    $amount = $postData->money->amount;
    $id = $postData->money->ID;

    $moneyUpdateSql = "
    UPDATE `moneyoutput` SET
        `tarigi` = '$takeMoneyDate',
        `tanxa` = '$amount',
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