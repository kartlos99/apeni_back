<?php

// ---------- chanaweris amogeba (redaqtirebistvis) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$json = file_get_contents('php://input');
$postData = json_decode($json);

$tableIdentifier = $postData->table;
$id = $postData->id;

$respModel = ["mitana" => null, "kout" => null, "mout" => null];

if ($id != 0) {
    if ($tableIdentifier == "mitana") {
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

    if ($tableIdentifier == "kout") {
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

    if ($tableIdentifier == "mout")
        $sql = "
        SELECT
            `id` as ID,
            `tarigi` AS takeMoneyDate,
            `obieqtis_id` AS clientID,
            `distributor_id` AS distributorID,
            `tanxa` AS amount,
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
