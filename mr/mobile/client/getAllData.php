<?php

namespace Apeni\JWT;
use DataProvider;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
$clientID = $_GET["clientID"];
$dataProvider = new DataProvider($con);

$sqlGetInfo = "SELECT * FROM `customer` WHERE `id` = $clientID";
$sqlGetPrices = "SELECT * FROM `fasebi` WHERE `obj_id` = $clientID";


$intoResult = mysqli_query($con, $sqlGetInfo);
$priceResult = mysqli_query($con, $sqlGetPrices);


if ($intoResult && $priceResult && mysqli_num_rows($intoResult) == 1) {

    $prices = [];
    while ($rs = mysqli_fetch_assoc($priceResult)) {
        $prices[] = $rs;
    }

    $dataArr = mysqli_fetch_assoc($intoResult);
    $dataArr['prices'] = $prices;
    $response[DATA] = $dataArr;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get customer data!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);
