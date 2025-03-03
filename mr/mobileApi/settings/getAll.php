<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$sqlQuery = "SELECT `id`, `code`, `valueInt` as paramValue
FROM `dictionary_items` 
WHERE `isActive` = 1 AND `dictionaryID` = (SELECT dictionary.id FROM dictionary WHERE dictionary.code = 'commonSettings')
ORDER BY dictionary_items.sortID";

echo json_encode($dbManager->getDataAsArray($sqlQuery));

$dbManager->closeConnection();