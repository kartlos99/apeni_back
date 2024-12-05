<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$customer = $postData;
$prices = $postData->beerPrices;
$bottlePrices = $postData->bottlePrices;

$sqlUpdateCustomer = "UPDATE $CUSTOMER_TB SET " .
    "`dasaxeleba` = '$customer->name'," .
    "`group` = '$customer->group'," .
    "`adress` = '$customer->address'," .
    "`tel` = '$customer->tel'," .
    "`comment` = '$customer->comment'," .
    "`sk` = '$customer->identifyCode'," .
    "`sakpiri` = '$customer->contactPerson'," .
    "`chek` = '$customer->chek'," .
    "`modifyDate` = CURRENT_TIMESTAMP," .
    "`modifyUserID` = " . $sessionData->userID .
    " WHERE id = $customer->id ";

$updateResult = $dbManager->baseInsert($sqlUpdateCustomer);

/** build sql for beer prices */
for ($i = 0; $i < count($prices); $i++) {
    $priceItem = $prices[$i];

    $beerID = $priceItem->beer_id;
    $price = $priceItem->fasi;
    $clientID = $priceItem->obj_id;

    $sqlUpdatePrice =
        "UPDATE
            fasebi 
        SET 
            `fasi` = $price,
            `tarigi` = '$timeOnServer' 
        WHERE
            `obj_id`= $clientID AND `beer_id` = $beerID";

    $dbManager->baseInsert($sqlUpdatePrice);
}

/** build sql for bottle prices */
for ($i = 0; $i < count($bottlePrices); $i++) {
    $bottlePriceItem = $bottlePrices[$i];

    $bottleID = $bottlePriceItem->bottleID;
    $price = $bottlePriceItem->price;
    $clientId = $bottlePriceItem->clientID;

    $sqlUpdatePrice =
        "UPDATE
                `bottle_prices`
            SET
                `price` = '$price',
                `modifyDate` = '$timeOnServer',
                `modifyUserID` = '$sessionData->userID'
            WHERE
                `clientID` = '$clientId' AND `bottleID` = '$bottleID'";

    $dbManager->baseInsert($sqlUpdatePrice);
}

echo json_encode("DONE update: id = $customer->id");

$dbManager->closeConnection();
