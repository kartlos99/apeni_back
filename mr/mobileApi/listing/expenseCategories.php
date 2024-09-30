<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
// $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$categoriesSql = "SELECT
    *
FROM
    `expense_category`
WHERE
    `status` >= 0";
$categoriesList = $dbManager->getDataAsArray($categoriesSql);

echo json_encode($categoriesList);

$dbManager->closeConnection();