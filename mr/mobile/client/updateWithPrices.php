<?php

namespace Apeni\JWT;

use VersionControl;
use ChangesReporter;

require_once "../../ChangesReporter.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

//$response[DATA] = "0";

$client = $postData->customer;
$prices = $postData->beerPrices;
$bottlePrices = $postData->bottlePrices;

$reporter = new ChangesReporter($sessionData->userID);
$reporter->checkRecord($CUSTOMER_TB, $client->id);

$sqlUpdateClient = "UPDATE $CUSTOMER_TB SET " .
    "`dasaxeleba` = '$client->dasaxeleba'," .
    "`adress` = '$client->adress'," .
    "`tel` = '$client->tel'," .
    "`comment` = '$client->comment'," .
    "`sk` = '$client->sk'," .
    "`sakpiri` = '$client->sakpiri'," .
    "`active` = '1'," .
    "`chek` = '$client->chek'," .
    "`modifyDate` = CURRENT_TIMESTAMP," .
    "`modifyUserID` = " . $sessionData->userID .
    " WHERE id = $client->id ";


if (mysqli_query($con, $sqlUpdateClient)) {

    for ($i = 0; $i < count($prices); $i++) {
        $priceItem = $prices[$i];

        $beerID = $priceItem->beer_id;
        $price = $priceItem->fasi;
        $clientID = $priceItem->obj_id;

        $sqlUpdatePrice =
            "UPDATE fasebi " .
            "SET `fasi` = $price, `tarigi` = '$timeOnServer' " .
            "WHERE `obj_id`= $clientID AND `beer_id` = $beerID";

        if (!mysqli_query($con, $sqlUpdatePrice)) {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = mysqli_error($con);
            $response[ERROR_CODE] = mysqli_errno($con);
            die(json_encode($response));
        }
    }

    for ($i = 0; $i < count($bottlePrices); $i++) {
        $bottlePriceItem = $bottlePrices[$i];

        $bottleID = $bottlePriceItem->bottleID;
        $price = $bottlePriceItem->price;

        $sqlUpdatePrice =
            "UPDATE
                `bottle_prices`
            SET
                `price` = '$price',
                `modifyDate` = '$timeOnServer',
                `modifyUserID` = '$sessionData->userID'
            WHERE
                `clientID` = '$client->id' AND `bottleID` = '$bottleID'";

        if (!mysqli_query($con, $sqlUpdatePrice)) {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = mysqli_error($con);
            $response[ERROR_CODE] = mysqli_errno($con);
            die(json_encode($response));
        }
    }

    $response[DATA] = "done";

    $vc = new VersionControl($con);
    $vc->updateVersionFor(CLIENT_VCS);
    $vc->updateVersionFor(PRICE_VCS);

    $response[LOG_RECORD_ID_KEY] = $reporter->logAsNeed();

} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

$reporter->closeConnection();

echo json_encode($response);
mysqli_close($con);