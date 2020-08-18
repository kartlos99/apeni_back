<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$comment = "'$postData->comment'";
if (empty($postData->comment)) {
    $comment = "NULL";
}

$operTime = $timeOnServer;

if ($postData->operationTime != "") {

    $operTime = $postData->operationTime;

    $sqlDeleteBeerInput = "DELETE FROM `storehousebeerinpit` WHERE `inputDate` = '$postData->operationTime'";
    $sqlDeleteBarrelOutput = "DELETE FROM `storehousebarreloutput` WHERE `outputDate` = '$postData->operationTime'";
    mysqli_query($con, $sqlDeleteBarrelOutput);
    mysqli_query($con, $sqlDeleteBeerInput);
    $response[DATA] = "items Removed!";
}

if (isset($postData->inputBeer) && count($postData->inputBeer) > 0) {

    $multiValue = "";
    for ($i = 0; $i < count($postData->inputBeer); $i++) {
        $receiveItem = $postData->inputBeer[$i];

//        $receiveDate = $receiveItem->receiveDate;
        $beerID = $receiveItem->beerID;
        $canTypeID = $receiveItem->canTypeID;
        $count = $receiveItem->count;

//        $response[DATA] = $receiveDate;
//        die(json_encode($response));

        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$operTime', '$postData->modifyUserID', '$beerID',
        '$canTypeID', '$count', '$postData->chek', $comment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $sql = "INSERT INTO `storehousebeerinpit`(
        `inputDate`,
        `distributorID`,
        `beerID`,
        `barrelID`,
        `count`,
        `chek`,
        `comment`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES " . $multiValue;



    if (mysqli_query($con, $sql)) {
        $response[DATA] = "inputBeerToStore-done";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}



if (isset($postData->outputBarrels) && count($postData->outputBarrels) > 0) {

    $multiValue = "";
    for ($i = 0; $i < count($postData->outputBarrels); $i++) {
        $barrelItem = $postData->outputBarrels[$i];

//        $outputDate = $barrelItem->outputDate;
        $canTypeID = $barrelItem->canTypeID;
        $count = $barrelItem->count;

        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$operTime', '$postData->modifyUserID', '$canTypeID', '$count', 
        '$postData->chek', $comment, '$timeOnServer', '$postData->modifyUserID')";
    }

    $sql = "INSERT INTO `storehousebarreloutput`(
        `outputDate`,
        `distributorID`,
        `barrelID`,
        `count`,
        `chek`,
        `comment`,
        `modifyDate`,
        `modifyUserID`
    )
    VALUES " . $multiValue;

// $response[DATA] = $sql;
// die(json_encode($response));
    if (mysqli_query($con, $sql)) {
        $response[DATA] = "outputBarrelFromStore-done";
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }
}




echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));