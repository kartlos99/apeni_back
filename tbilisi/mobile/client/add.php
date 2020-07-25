<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

//$response[DATA] = "0";


$client = $postData->obieqti;

$prices = $postData->prices;



$sqlAddClient = "INSERT INTO `obieqtebi`(
    `dasaxeleba`,
    `adress`,
    `tel`,
    `comment`,
    `sk`,
    `sakpiri`,
    `active`,
    `reg_date`,
    `chek`
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
    '$client->chek'
    )";

if (mysqli_query($con, $sqlAddClient)) {
    $clientID = mysqli_insert_id($con);

    $multiValue = "";
    for ($i = 0; $i < count($prices); $i++){
        $priceItem = $prices[$i];

        $beerID = $priceItem->beer_id;
        $price = $priceItem->fasi;

        if ($i > 0) { $multiValue .= ","; }
        $multiValue .= "('$clientID', '$beerID', '$price', '$timeOnServer', '0')";
    }

    $sqlInsertPrices = "INSERT INTO `fasebi`(`obj_id`, `beer_id`, `fasi`, `tarigi`, `user_id`) VALUES " . $multiValue;

    if (mysqli_query($con, $sqlInsertPrices)) {
        $response[DATA] = $clientID;
        $vc = new VersionControl($con);
        $vc->updateVersionFor(CLIENT_VCS);
        $vc->updateVersionFor(PRICE_VCS);
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

//$response[DATA] = $sql;
// die json_encode($response);