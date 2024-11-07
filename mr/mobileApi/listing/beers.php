<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
// $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$beerListSql = "SELECT * FROM `beer` where `beer`.`status` > 0 order by sortValue";

$beerList = $dbManager->getDataAsArray($beerListSql);

echo json_encode($beerList);

$dbManager->closeConnection();