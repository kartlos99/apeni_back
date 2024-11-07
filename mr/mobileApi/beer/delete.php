<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
//$sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

/**
 * Beer deletion for as means it to become inactive,
 * so we're just updating beer status to INACTIVE
 */

$beerId = json_decode($json);

if ($beerId > 0) {

    $deleteBeerSql = "UPDATE
            `beer`
        SET
            `status` = '2'
        WHERE
            `id` = '$beerId'";
//die($updateBeerSql);
    $dbManager->baseInsert($deleteBeerSql);
} else {
    dieWithDefaultHttpError("invalid beer id", 999);
}

//$vc = new VersionControl($con);
//$vc->updateVersionFor(BEER_VCS);
//$vc->updateVersionFor(PRICE_VCS);

$beerListSql = "SELECT * FROM `beer` where `beer`.`status` > 0 order by sortValue";

$beerList = $dbManager->getDataAsArray($beerListSql);

echo json_encode($beerList);

$dbManager->closeConnection();
