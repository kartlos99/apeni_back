<?php

namespace Apeni\JWT;
use DbKey;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

const BEER_PRICES_KEY = 'prices';
const BOTTLE_PRICES_KEY = 'bottlePrices';

require_once('../connection.php');
 $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');

$dbManager = new \BaseDbManagerV2();

$customerListSql =
    "SELECT
    c.*
FROM
    " . DbKey::$CUSTOMER_MAP_TB . " cm
    LEFT JOIN $CUSTOMER_TB c
    ON cm.customerID = c.id
WHERE
cm.regionID = {$sessionData->regionID} AND cm.active = 1 AND c.active = 1 
ORDER BY
    dasaxeleba ";

$customers = $dbManager->getDataAsArray($customerListSql);

$cr = [];
foreach ($customers as $customer) {

    $sqlGetPrices = "SELECT * FROM `fasebi` WHERE `obj_id` = " . $customer["id"];
    $sqlBottlePrices = "SELECT * FROM `bottle_prices` WHERE `clientID` = " . $customer["id"];

    $beerPrice = $dbManager->getDataAsArray($sqlGetPrices);
    $bottlePrice = $dbManager->getDataAsArray($sqlBottlePrices);

    $customer[BEER_PRICES_KEY] = $beerPrice;
    $customer[BOTTLE_PRICES_KEY] = $bottlePrice;

    $cr[] = $customer;
}


echo json_encode($cr);

$dbManager->closeConnection();