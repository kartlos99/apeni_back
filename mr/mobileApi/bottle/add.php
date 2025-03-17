<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);


$bottleID = $postData->id;
$bottleName = $postData->name;
$volume = $postData->volume;
$beerID = $postData->beerID;
$price = $postData->price;
$status = $postData->status;

$imageName = "base_bottle.jpg";

if ($bottleID == 0) { // adding

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

    $insertResult = $dbManager->baseInsert($insertSql);
    $newBottleId = $insertResult[RECORD_ID_KEY];

    $customerIdsSql = "SELECT id FROM $CUSTOMER_TB ";

    $customerIds = array_map(
        fn($value): int => $value["id"],
        $dbManager->getDataAsArray($customerIdsSql)
    );

    $values_to_insert = "(";

    for ($i = 0; $i < count($customerIds); $i++) {
        $values_to_insert = $values_to_insert . "'$customerIds[$i]', '$newBottleId', '$price', '$timeOnServer', '$sessionData->userID' )";

        if ($i < (count($customerIds) - 1)) {
            $values_to_insert = $values_to_insert . ", (";
        }
    }

    $addPriceMapSql = "INSERT INTO `bottle_prices_2`(`clientID`, `bottleID`, `price`, `modifyDate`, `modifyUserID`)"
        . " VALUES  $values_to_insert";

    $dbManager->baseInsert($addPriceMapSql);

} else { // update

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

    $dbManager->baseInsert($updateSql);
}

$bottlesListSql = "SELECT * FROM `bottles` WHERE `status` > 0 ORDER BY `sortValue`";

echo json_encode($dbManager->getDataAsArray($bottlesListSql));

$dbManager->closeConnection();
