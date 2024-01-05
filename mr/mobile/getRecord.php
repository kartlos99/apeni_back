<?php

namespace Apeni\JWT;
// ---------- chanaweris amogeba (redaqtirebistvis) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

const OPERATION_SALE_BY_BARREL = "mitana";
const OPERATION_SALE_BY_BOTTLE = "mitana_bottle";
const OPERATION_BARREL_OUT = "kout";
const OPERATION_MONEY_OUT = "mout";

$json = file_get_contents('php://input');
$postData = json_decode($json);

$tableIdentifier = $postData->table;
$id = $postData->id;

$respModel = [
    OPERATION_SALE_BY_BARREL => null,
    OPERATION_SALE_BY_BOTTLE => null,
    OPERATION_BARREL_OUT => null,
    OPERATION_MONEY_OUT => null
];

if ($id != 0) {
    if ($tableIdentifier == OPERATION_SALE_BY_BARREL) {
        $sql = "
        SELECT
            `ID`,
            `saleDate`,
            `clientID`,
            `distributorID`,
            `beerID`,
            `unitPrice`,
            `canTypeID`,
            `count`,
            `orderID`,
            `comment`
        FROM
            `sales`
        WHERE
            id = $id";
    }

    if ($tableIdentifier == OPERATION_SALE_BY_BOTTLE) {
        $sql = "SELECT `ID`, `saleDate`, `clientID`, `distributorID`, `bottleID`, `price`, `count`, `orderID`, `comment` 
        FROM 
             `bottle_sales`
        WHERE 
              ID = $id";
    }

    if ($tableIdentifier == OPERATION_BARREL_OUT) {
        $sql = "
        SELECT
            `ID`,
            `outputDate`,
            `clientID`,
            `distributorID`,
            `canTypeID`,
            `count`,
            `comment`
        FROM
            `barrel_output`
        WHERE
            id = $id";
    }

    if ($tableIdentifier == OPERATION_MONEY_OUT)
        $sql = "
        SELECT
            `id` as ID,
            `tarigi` AS takeMoneyDate,
            `obieqtis_id` AS clientID,
            `distributor_id` AS distributorID,
            `tanxa` AS amount,
            `paymentType`,
            `comment`
        FROM
            `moneyoutput`
        WHERE
            id = $id";

    $result = mysqli_query($con, $sql);
    if ($result) {
        $respModel[$tableIdentifier] = mysqli_fetch_assoc($result);
//        $response[DATA] = $respModel;
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = mysqli_error($con);
        $response[ERROR_CODE] = mysqli_errno($con);
    }

}

$response[DATA] = $respModel;

echo json_encode($response);

mysqli_close($con);
