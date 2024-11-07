<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
// $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$newValue = $postData->sortValue;
$beerId = $postData->beerId;

$setSortValueSql = "UPDATE `beer` SET 
`sortValue` = '$newValue'
WHERE `id` = '$beerId'";

$dbManager->baseInsert($setSortValueSql);

$beerListSql = "SELECT * FROM `beer` where `beer`.`status` > 0 order by sortValue";

$beerList = $dbManager->getDataAsArray($beerListSql);

echo json_encode($beerList);

$dbManager->closeConnection();

