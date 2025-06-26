<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$result = [];

$beerListSql = "SELECT * FROM `beer` where `beer`.`status` > 0 order by sortValue";
$result["beers"] = $dbManager->getDataAsArray($beerListSql);

$bottleListSql = "SELECT * FROM `bottles` WHERE `status` > 0 ORDER BY `sortValue`";
$result["bottles"] = $dbManager->getDataAsArray($bottleListSql);

$barrelListSql =
    "SELECT `id`, `name`, `volume`, `actualVolume`, `status`, `sortValue`, `image`
FROM `barrels` 
WHERE barrels.status > 0
ORDER BY barrels.sortValue";
$result["barrels"] = $dbManager->getDataAsArray($barrelListSql);


echo json_encode($result);

$dbManager->closeConnection();