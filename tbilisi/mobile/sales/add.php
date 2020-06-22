<?php

// ---------- gadascem dRes, gibrunebs shekveTebs ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

// **********************************************************************************

$saleComment = "'$postData->comment'";
if (empty($postData->comment)) {
    $saleComment = "NULL";
}


if (isset($postData->sales) && count($postData->sales) > 0) {

    $multiValue = "";
    for ($i = 0; $i < count($postData->sales); $i++) {
        $saleItem = $postData->sales[$i];

        $saleDate = $saleItem->saleDate;
        $beerID = $saleItem->beerID;
        $price = $saleItem->price;
        $canTypeID = $saleItem->canTypeID;
        $count = $saleItem->count;
        $orderID = $saleItem->orderID;

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
        $response[DATA] = "sale-done ";
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
        $response[DATA] .= "barrel-done";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}

if (isset($postData->money)) {

    $takeMoneyDate = $postData->money->takeMoneyDate;
    $amount = $postData->money->amount;

    $multiValue = "('$takeMoneyDate', '$postData->clientID', '$postData->distributorID', 
        '$amount', $saleComment, '$timeOnServer', '$postData->modifyUserID')";

    $moneyInsertSql = "
    INSERT INTO `moneyoutput`(
        `tarigi`,
        `obieqtis_id`,
        `distributor_id`,
        `tanxa`,
        `comment`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES " . $multiValue;

    if (mysqli_query($con, $moneyInsertSql)) {
        $response[DATA] .= "money-done";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}


//$response[DATA] = $sql;

echo json_encode($response);

// die(json_encode($response));