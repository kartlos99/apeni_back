<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

session_start();
require_once "../../../mr/_webLoad.php";
include_once('../../../jwt/JWT.php');
include_once('../../../jwt/extension.php');

$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$prices = $_POST["prices"];
$customerID = $_POST["customerID"];
$product = $_POST["product"];

if (!empty($prices) && $product == "beer") {
    $values = "";
    foreach ($prices as $itemPrice) {
        $beerID = $itemPrice["itemID"];
        $price = $itemPrice["price"];
        $values .= "('$customerID','$beerID','$price','$sessionData->userID'),";
    }
    $updateBeerPricesSql = "INSERT INTO `beer_prices_2`(`clientID`, `beerID`, `price`, `modifyUserID`)
VALUES " . trim($values, ",") . "
 ON DUPLICATE KEY UPDATE
`modifyDate`= IF( beer_prices_2.price = VALUES(`price`), beer_prices_2.modifyDate, CURRENT_TIMESTAMP),
`modifyUserID`= IF( beer_prices_2.price = VALUES(`price`), beer_prices_2.modifyUserID, VALUES(`modifyUserID`)),
`price` = VALUES(`price`)";

    $dbManager->baseInsert($updateBeerPricesSql);
}

if (!empty($prices) && $product == "bottle") {
    $values = "";
    foreach ($prices as $itemPrice) {
        $bottleID = $itemPrice["itemID"];
        $price = $itemPrice["price"];
        $values .= "('$customerID','$bottleID','$price','$sessionData->userID'),";
    }
    $updateBottlePricesSql = "INSERT INTO `bottle_prices_2`(`clientID`, `bottleID`, `price`, `modifyUserID`)
    VALUES " . trim($values, ",") . " 
    ON DUPLICATE KEY UPDATE
    `modifyDate`= IF( bottle_prices_2.price = VALUES(`price`), bottle_prices_2.modifyDate, CURRENT_TIMESTAMP),
    `modifyUserID`= IF( bottle_prices_2.price = VALUES(`price`), bottle_prices_2.modifyUserID, VALUES(`modifyUserID`)),
    `price` = VALUES(`price`)";

    $dbManager->baseInsert($updateBottlePricesSql);
}


echo json_encode([
    "pData" => $_POST,
    "sesss" => $sessionData
]);

$dbManager->closeConnection();