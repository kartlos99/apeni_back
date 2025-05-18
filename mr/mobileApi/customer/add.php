<?php

namespace Apeni\JWT;
use DbKey;
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

$customer = $postData;
$prices = $postData->beerPrices;
$bottlePrices = $postData->bottlePrices;

$sqlAddCustomer = "INSERT INTO $CUSTOMER_TB (
    `dasaxeleba`,
    `group`,
    `adress`,
    `tel`,
    `comment`,
    `sk`,
    `sakpiri`,
    `location`,
    `paymentType`,
    `active`,
    `reg_date`,
    `chek`,
    `modifyUserID`
)
    VALUES(
    '$customer->name',
    '$customer->group',
    '$customer->address',
    '$customer->tel',
    '$customer->comment',
    '$customer->identifyCode',
    '$customer->contactPerson',
    '$customer->location',
    '$customer->paymentType',
    '1',
    '$timeOnServer',
    '$customer->chek', "
    . $sessionData->userID
    . ")";


$insertResult = $dbManager->baseInsert($sqlAddCustomer);
$newCustomerId = $insertResult[RECORD_ID_KEY];

/** build sql for beer prices */
$multiValue = "";
for ($i = 0; $i < count($prices); $i++) {
    $priceItem = $prices[$i];

    $beerID = $priceItem->beerID;
    $price = $priceItem->price;

    if ($i > 0) {
        $multiValue .= ",";
    }
    $multiValue .= "('$newCustomerId', '$beerID', '$price', '$timeOnServer', '" . $sessionData->userID ."')";
}
$sqlInsertPrices = "INSERT INTO `beer_prices_2`(`clientID`, `beerID`, `price`, `modifyDate`, `modifyUserID`) VALUES " . $multiValue;

$dbManager->baseInsert($sqlInsertPrices);

/** build sql for bottle prices */
$values_to_insert = "";

for ($i = 0; $i < count($bottlePrices); $i++) {
    $item = $bottlePrices[$i];
    $bottleID = $item->bottleID;
    $price = $item->price;

    if ($i > 0) {
        $values_to_insert .= ", ";
    }
    $values_to_insert .= "('$newCustomerId', '$bottleID', '$price', '$timeOnServer', $sessionData->userID )";
}
$addBottlePriceMapSql = "INSERT INTO `bottle_prices_2`(`clientID`, `bottleID`, `price`, `modifyDate`, `modifyUserID`)"
    . " VALUES  $values_to_insert";

$dbManager->baseInsert($addBottlePriceMapSql);

/** automatic system cleaning for new customer. */
$sqlAddInitialSystemClear =
    "INSERT INTO `gawmenda` (`regionID`, `obieqtis_id`, `distributor_id`, `tarigi`) " .
    "VALUES ('$sessionData->regionID', '$newCustomerId', '$sessionData->userID', '$timeOnServer')";
$dbManager->baseInsert($sqlAddInitialSystemClear);

/** set new customer mapping to region */
$sqlInsertCustomerMap = "INSERT INTO " . DbKey::$CUSTOMER_MAP_TB
    . " (`customerID`, `regionID`, `active`) VALUES ('$newCustomerId', '$sessionData->regionID', 1);";
$dbManager->baseInsert($sqlInsertCustomerMap);


echo json_encode("DONE: id = $newCustomerId");

$dbManager->closeConnection();
