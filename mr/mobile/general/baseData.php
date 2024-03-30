<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
require_once('../../BaseDbManager.php');
$sessionData = checkToken();

$bottlesListSql = "
SELECT * FROM `bottles` 
WHERE `status` > 0
ORDER BY `sortValue`";

$beerListSql = "SELECT * FROM ludi where ludi.`active` > 0 order by sortValue";

$dbManager = new \BaseDbManager();

$response[DATA] = [
    "beers" => $dbManager->getDataAsArray($beerListSql),
    "bottles" => $dbManager->getDataAsArray($bottlesListSql)
];

echo json_encode($response);

$dbManager->closeConnection();