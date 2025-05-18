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

$pt = "null";
if (isset($customer->paymentType))
    $pt = "'$customer->paymentType'";

$sqlUpdateCustomer = "UPDATE $CUSTOMER_TB SET " .
    "`dasaxeleba` = '$customer->name'," .
    "`group` = '$customer->group'," .
    "`adress` = '$customer->address'," .
    "`tel` = '$customer->tel'," .
    "`comment` = '$customer->comment'," .
    "`sk` = '$customer->identifyCode'," .
    "`sakpiri` = '$customer->contactPerson'," .
    "`location` = '$customer->location'," .
    "`paymentType` = $pt," .
    "`chek` = '$customer->chek'," .
    "`modifyDate` = CURRENT_TIMESTAMP," .
    "`modifyUserID` = " . $sessionData->userID .
    " WHERE id = $customer->id ";

$updateResult = $dbManager->baseInsert($sqlUpdateCustomer);

/** build sql for beer prices */
/**
 * ************* MOVE beer prices from old table to new **********
 *
 * INSERT INTO `beer_prices_2`(`clientID`, `beerID`, `price`, `modifyDate`, `modifyUserID`)
 * SELECT fasebi.obj_id, fasebi.beer_id, fasebi.fasi, fasebi.tarigi, fasebi.user_id FROM fasebi
 * ON DUPLICATE KEY UPDATE
 * `price`=VALUES(`price`),
 * `modifyDate`=CURRENT_TIMESTAMP,
 * `modifyUserID`=VALUES(`modifyUserID`)
 *
 * *******  UPDATE/INSERT beer price mapping **************
 *
 * INSERT INTO `beer_prices_2`(`clientID`, `beerID`, `price`, `modifyUserID`)
 * VALUES
 * ('27','2','222','12')
 * ON DUPLICATE KEY UPDATE
 * `modifyDate`= IF( beer_prices_2.price = VALUES(`price`), beer_prices_2.modifyDate, CURRENT_TIMESTAMP),
 * `modifyUserID`= IF( beer_prices_2.price = VALUES(`price`), beer_prices_2.modifyUserID, VALUES(`modifyUserID`)),
 * `price` = VALUES(`price`)
 */
if (!empty($prices)) {
    $values = "";
    foreach ($prices as $beerPrice) {
        $values .= "('$beerPrice->clientID','$beerPrice->beerID','$beerPrice->price','$sessionData->userID'),";
    }
    $updateBeerPricesSql = "INSERT INTO `beer_prices_2`(`clientID`, `beerID`, `price`, `modifyUserID`)
VALUES " . trim($values, ",") . "
 ON DUPLICATE KEY UPDATE
`modifyDate`= IF( beer_prices_2.price = VALUES(`price`), beer_prices_2.modifyDate, CURRENT_TIMESTAMP),
`modifyUserID`= IF( beer_prices_2.price = VALUES(`price`), beer_prices_2.modifyUserID, VALUES(`modifyUserID`)),
`price` = VALUES(`price`)";

    $dbManager->baseInsert($updateBeerPricesSql);
}

/** build sql for bottle prices */
if (!empty($bottlePrices)) {
    /**
     * Script for MOVING existing bottle prices to new table
     *
     * INSERT INTO `bottle_prices_2`(`clientID`, `bottleID`, `price`, `modifyDate`, `modifyUserID`)
     * SELECT `clientID`, `bottleID`, `price`, `modifyDate`, `modifyUserID` FROM bottle_prices
     * ON DUPLICATE KEY UPDATE
     * `price`=VALUES(`price`),
     * `modifyDate`=CURRENT_TIMESTAMP,
     * `modifyUserID`=VALUES(`modifyUserID`)
     *
     * INSERT INTO `bottle_prices_2`(`clientID`, `bottleID`, `price`, `modifyUserID`)
     * VALUES
     * ('27','6','606','16')
     * ON DUPLICATE KEY UPDATE
     * `modifyDate`= IF( bottle_prices_2.price = VALUES(`price`), bottle_prices_2.modifyDate, CURRENT_TIMESTAMP),
     * `modifyUserID`= IF( bottle_prices_2.price = VALUES(`price`), bottle_prices_2.modifyUserID, VALUES(`modifyUserID`)),
     * `price` = VALUES(`price`)
     */
    $values = "";
    foreach ($bottlePrices as $bottlePrice) {
        $values .= "('$bottlePrice->clientID','$bottlePrice->bottleID','$bottlePrice->price','$sessionData->userID'),";
    }
    $updateBottlePricesSql = "INSERT INTO `bottle_prices_2`(`clientID`, `bottleID`, `price`, `modifyUserID`)
    VALUES " . trim($values, ",") . " 
    ON DUPLICATE KEY UPDATE
    `modifyDate`= IF( bottle_prices_2.price = VALUES(`price`), bottle_prices_2.modifyDate, CURRENT_TIMESTAMP),
    `modifyUserID`= IF( bottle_prices_2.price = VALUES(`price`), bottle_prices_2.modifyUserID, VALUES(`modifyUserID`)),
    `price` = VALUES(`price`)";

    $dbManager->baseInsert($updateBottlePricesSql);
}

echo json_encode("DONE update: id = $customer->id");

$dbManager->closeConnection();
