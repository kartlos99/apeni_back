<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
// $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$sqlQuery = "SELECT * FROM `bottles` WHERE `bottles`.`status` > 0 ORDER BY `sortValue`";

echo json_encode($dbManager->getDataAsArray($sqlQuery));

$dbManager->closeConnection();