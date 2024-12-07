<?php

namespace Apeni\JWT;
use DbKey;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

const BEER_PRICES_KEY = 'beerPrices';
const BOTTLE_PRICES_KEY = 'bottlePrices';

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');

$dbManager = new \BaseDbManagerV2();

$customerListSql =
    "SELECT
        c.`id`,
        c.`dasaxeleba` AS name,
        c.`group`,
        c.`adress` AS address,
        c.`tel`,
        c.`comment`,
        c.`sk` AS identifyCode,
        c.`sakpiri` AS contactPerson,
        c.`active` AS `status`,
        c.`reg_date`,
        c.`chek`,
        c.`modifyDate`,
        c.`modifyUserID`
    FROM
        " . DbKey::$CUSTOMER_MAP_TB . " cm
        LEFT JOIN $CUSTOMER_TB c
        ON cm.customerID = c.id
    WHERE
    cm.regionID = {$sessionData->regionID} AND cm.active = 1  
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