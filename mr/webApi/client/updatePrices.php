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


echo json_encode([
    "pData" => $_POST,
    "sesss" => $sessionData
]);

$dbManager->closeConnection();