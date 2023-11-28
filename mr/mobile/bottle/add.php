<?php

namespace Apeni\JWT;

// use DataProvider;
use QueryHelper;
use DbKey;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$bottleID = $postData->bottleID;
$bottleName = $postData->name;
$volume = $postData->volume;
$beerID = $postData->beerID;
$price = $postData->price;
$status = $postData->status;

$imageName = "base_bottle.jpg";

if ($bottleID == "0") { // adding

    $insertSql = "INSERT INTO `bottles`(
    `name`,
    `volume`,
    `actualVolume`,
    `beerID`,
    `price`,
    `status`,
    `sortValue`,
    `image`,
    `modifyUserID`
)
VALUES(
    '$bottleName',
       $volume,
       $volume,
       $beerID,
       $price,
       $status,
       UNIX_TIMESTAMP(),
       '$imageName', "
        . $sessionData->userID
        . ")";

    if (mysqli_query($con, $insertSql)) {
        $newBottleID = mysqli_insert_id($con);

        $clientIds = [];
        $clientIdsSql = "SELECT id FROM $CUSTOMER_TB ";

        $clientIdsResult = mysqli_query($con, $clientIdsSql);

        while ($rs = mysqli_fetch_assoc($clientIdsResult)) {
            $clientIds[] = $rs['id'];
        }

        $values_to_insert = "(";

        for ($i = 0; $i < count($clientIds); $i++) {

            $values_to_insert = $values_to_insert . "'$clientIds[$i]', '$newBottleID', '$price', '$timeOnServer', $sessionData->userID )";

            if ($i < (count($clientIds) - 1)) {
                $values_to_insert = $values_to_insert . ", (";
            }
        }

        $addPriceMapSql = "INSERT INTO `bottle_prices`(`clientID`, `bottleID`, `price`, `modifyDate`, `modifyUserID`)"
            . " VALUES  $values_to_insert";

        if (!mysqli_query($con, $addPriceMapSql)) {
            echo "ERROR: Could not able to execute $addPriceMapSql. " . mysqli_error($con);
        }
        $response[DATA] = "$newBottleID";
    } else {
        dieWithError(mysqli_errno($con), mysqli_error($con));
    }
} else { // editing

    $updateSql = "UPDATE
    `bottles`
SET
    `name` = '$bottleName',
    `volume` = $volume,
    `actualVolume` = $volume,
    `beerID` = $beerID,
    `price` = $price,
    `status` = $status,
    `image` = '$imageName',
    `modifyUserID` = $sessionData->userID,
    `modifyDate` = CURRENT_TIMESTAMP
WHERE
    id = $bottleID";

    if (!mysqli_query($con, $updateSql)) {
        dieWithError(mysqli_errno($con), mysqli_error($con));
    } else {
        $response[DATA] = "";
    }
}

//$vc = new VersionControl($con);
//$vc->updateVersionFor(BEER_VCS);
//$vc->updateVersionFor(PRICE_VCS);

echo json_encode($response);
