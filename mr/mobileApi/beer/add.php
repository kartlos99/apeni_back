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

$beerId = $postData->id;
$name = $postData->name;
$price = $postData->price;
$color = $postData->color;

if ($beerId == 0) { // axali ludis chawera

    $addBeerSql = "INSERT INTO `beer`(`name`, `price`, `status`, `color`, `sortValue`)  
                    VALUES ( '$name', '$price', '1', '$color', UNIX_TIMESTAMP())";

    $insertResult = $dbManager->baseInsert($addBeerSql);
    $newBeerId = $insertResult[RECORD_ID_KEY];

    $customerIdsSql = "SELECT id FROM $CUSTOMER_TB ";

    $customerIds = array_map(
        fn($value): int => $value["id"],
        $dbManager->getDataAsArray($customerIdsSql)
    );

    $values_to_insert = "(";

    for ($i = 0; $i < count($customerIds); $i++) {
        $values_to_insert = $values_to_insert . "'$customerIds[$i]', '$newBeerId', '$price', '$timeOnServer')";

        if ($i < (count($customerIds) - 1)) {
            $values_to_insert = $values_to_insert . ", (";
        }
    }

    $addPricesSql = "INSERT INTO `fasebi` 
	                    (`obj_id`, `beer_id`, `fasi`, `tarigi`) 
	                VALUES
	                    $values_to_insert";

    $dbManager->baseInsert($addPricesSql);

} else { // redaqtireba

    $updateBeerSql = "UPDATE
            `beer`
        SET
            `name` = '$name',
            `price` = '$price',
            `color` = '$color'
        WHERE
            `id` = '$beerId'";
//die($updateBeerSql);
    $dbManager->baseInsert($updateBeerSql);
}

//$vc = new VersionControl($con);
//$vc->updateVersionFor(BEER_VCS);
//$vc->updateVersionFor(PRICE_VCS);

$beerListSql = "SELECT * FROM `beer` where `beer`.`status` > 0 order by sortValue";

$beerList = $dbManager->getDataAsArray($beerListSql);

echo json_encode($beerList);

$dbManager->closeConnection();
