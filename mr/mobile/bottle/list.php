<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
require_once('../../BaseDbManager.php');
//$sessionData = checkToken();

$bottlesListSql = "
SELECT * FROM `bottles` 
WHERE `status` > 0
ORDER BY `sortValue`";

$dbManager = new \BaseDbManager();

echo json_encode($dbManager->getDataAsArray($bottlesListSql));

$dbManager->closeConnection();