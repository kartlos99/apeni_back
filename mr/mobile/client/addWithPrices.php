<?php

namespace Apeni\JWT;

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

$client = $postData->customer;
$prices = $postData->beerPrices;
$bottlePrices = $postData->bottlePrices;


$sqlAddClient = "INSERT INTO $CUSTOMER_TB (
    `dasaxeleba`,
    `adress`,
    `tel`,
    `comment`,
    `sk`,
    `sakpiri`,
    `active`,
    `reg_date`,
    `chek`,
    `modifyUserID`
)
    VALUES(
    '$client->dasaxeleba',
    '$client->adress',
    '$client->tel',
    '$client->comment',
    '$client->sk',
    '$client->sakpiri',
    '1',
    '$timeOnServer',
    '$client->chek', "
    . $sessionData->userID
    . ")";

if (mysqli_query($con, $sqlAddClient)) {
    $clientID = mysqli_insert_id($con);

    $multiValue = "";
    for ($i = 0; $i < count($prices); $i++) {
        $priceItem = $prices[$i];

        $beerID = $priceItem->beer_id;
        $price = $priceItem->fasi;

        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$clientID', '$beerID', '$price', '$timeOnServer', '0')";
    }
    $sqlInsertPrices = "INSERT INTO `fasebi`(`obj_id`, `beer_id`, `fasi`, `tarigi`, `user_id`) VALUES " . $multiValue;

    $values_to_insert = "";

    for ($i = 0; $i < count($bottlePrices); $i++) {
        $item = $bottlePrices[$i];
        $bottleID = $item->bottleID;
        $price = $item->price;

        if ($i > 0) {
            $values_to_insert .= ", ";
        }
        $values_to_insert .= "('$clientID', '$bottleID', '$price', '$timeOnServer', $sessionData->userID )";
    }
    $addBottlePriceMapSql = "INSERT INTO `bottle_prices`(`clientID`, `bottleID`, `price`, `modifyDate`, `modifyUserID`)"
        . " VALUES  $values_to_insert";

    if (mysqli_query($con, $sqlInsertPrices) && mysqli_query($con, $addBottlePriceMapSql)) {
        $response[DATA] = $clientID;
        $vc = new VersionControl($con);
        $vc->updateVersionFor(CLIENT_VCS);
        $vc->updateVersionFor(PRICE_VCS);

        $sqlAddInitialSystemClear =
            "INSERT INTO `gawmenda` (`regionID`, `obieqtis_id`, `distributor_id`, `tarigi`) " .
            "VALUES ('$sessionData->regionID', '$clientID', '$sessionData->userID', '$timeOnServer')";
        mysqli_query($con, $sqlAddInitialSystemClear);

        $sqlInsertCustomerMap =
            "INSERT INTO " . DbKey::$CUSTOMER_MAP_TB . " (`customerID`, `regionID`, `active`) VALUES ('$clientID', '$sessionData->regionID', 1);";
        mysqli_query($con, $sqlInsertCustomerMap);

    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }

} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}


echo json_encode($response);

mysqli_close($con);