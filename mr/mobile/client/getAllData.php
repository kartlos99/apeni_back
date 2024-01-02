<?php

namespace Apeni\JWT;

use DataProvider;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
require_once('../../BaseDbManager.php');
checkToken();

const BEER_PRICES_KEY = 'prices';
const BOTTLE_PRICES_KEY = 'bottlePrices';

const GET_KEY_CLIENT_ID = 'clientID';

$clientID = $_GET[GET_KEY_CLIENT_ID] ?? dieWithError(ERROR_CODE_MISSED_PARAM, "need clientID!");

$sqlCustomerInfo = "SELECT * FROM `customer` WHERE `id` = $clientID";
$sqlGetPrices = "SELECT * FROM `fasebi` WHERE `obj_id` = $clientID";
$sqlBottlePrices = "SELECT * FROM `bottle_prices` WHERE `clientID` = $clientID";

$dbManager = new \BaseDbManager();

$customers = $dbManager->getDataAsArray($sqlCustomerInfo);

if (count($customers) == 1) {
    $customer = $customers[0];
    $beerPrice = $dbManager->getDataAsArray($sqlGetPrices);
    $bottlePrice = $dbManager->getDataAsArray($sqlBottlePrices);

    $customer[BEER_PRICES_KEY] = $beerPrice;
    $customer[BOTTLE_PRICES_KEY] = $bottlePrice;

    $response[DATA] = $customer;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get customer data!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

$dbManager->closeConnection();
