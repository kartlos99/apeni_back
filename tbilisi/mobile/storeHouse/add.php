<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$comment = "'$postData->comment'";
if (empty($postData->comment)) {
    $comment = "NULL";
}

$operTime = $timeOnServer;

// check for valid barrels values
if (isset($postData->outputBarrels) && count($postData->outputBarrels) > 0) {
    $actualdate = $postData->operationTime ;
    $balanceMap = getEmptyBarrelsBalanceMap($con, $actualdate, $postData->groupID);

    foreach ($postData->outputBarrels as $barrelOutput) {
        if (!isset($balanceMap[$barrelOutput->canTypeID]) || $balanceMap[$barrelOutput->canTypeID]['balance'] < $barrelOutput->count) {
            dieWithError(
                ER_CODE_EXTRA_BARREL_OUTPUT_STORE,
                sprintf(ER_TEXT_EXTRA_BARREL_OUTPUT_STORE,
                    $actualdate,
                    $balanceMap[$barrelOutput->canTypeID]['dasaxeleba'],
                    $barrelOutput->count)
            );
        }
    }
}

//if ($postData->operationTime != "") {

    $operTime = $postData->operationTime;

    $sqlDeleteBeerInput = "DELETE FROM `storehousebeerinpit` WHERE `groupID` = '$postData->groupID'";
    $sqlDeleteBarrelOutput = "DELETE FROM `storehousebarreloutput` WHERE `groupID` = '$postData->groupID'";
    mysqli_query($con, $sqlDeleteBarrelOutput);
    mysqli_query($con, $sqlDeleteBeerInput);
    $response[DATA] = "items Removed!";
//}

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
        $multiValue .= "('$postData->groupID', '$operTime', '$sessionData->userID', '$beerID',
        '$canTypeID', '$count', '$postData->chek', $comment, '$timeOnServer', '$sessionData->userID')";
    }

    $sql = "INSERT INTO `storehousebeerinpit`(
        `groupID`,                                  
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
        $multiValue .= "('$postData->groupID', '$operTime', '$sessionData->userID', '$canTypeID', '$count', 
        '$postData->chek', $comment, '$timeOnServer', '$sessionData->userID')";
    }

    $sql = "INSERT INTO `storehousebarreloutput`(
        `groupID`,
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

function getEmptyBarrelsBalanceMap($dbConn, $date, $exceptGroupID)
{
    $sqlQuery = "CALL getEmptyBarrelsInStore('$date', '$exceptGroupID');";
    $mMap = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        $mMap[$rs['canTypeID']] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $mMap;
}