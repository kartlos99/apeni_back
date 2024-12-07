<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
$dbKey = new DbKey();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

if ($sessionData->userType != USERTYPE_ADMIN)
    throwHttpError(ER_CODE_NO_PERMISSION, ER_TEXT_NO_PERMISSION);

$debtSql = "SELECT dbt.*, ifnull(cr.needCleaning, 0) AS needCleaning FROM `clients_debt` dbt
LEFT JOIN cleaningreport cr
ON dbt.`clientID` = cr.clientID 
WHERE dbt.`clientID` = " . $postData->customerID;

$debtResult = $dbManager->getDataAsArray($debtSql)[0];

if ($debtResult['barrel'] - $debtResult['barrelTakenBack'] > 0 || $debtResult['price'] - $debtResult['payed'] > 0.5) {
    throwHttpError(ER_CODE_DEBT_ON_CLIENT, ER_TEXT_DEBT_ON_CLIENT);
}

if ($postData->allRegions)
    $deactivateSql = "UPDATE " . $CUSTOMER_TB . " SET
     `active` = 0,
     `modifyDate` = CURRENT_TIMESTAMP,
     `modifyUserID` = " . $sessionData->userID . " 
     WHERE `id` = " . $postData->clientID;
else
    $deactivateSql = "UPDATE " . $dbKey::$CUSTOMER_MAP_TB . " SET `active` = 0 
    WHERE `customerID` = " . $postData->customerID . " AND `regionID` = '$sessionData->regionID'";

$dbManager->baseInsert($deactivateSql);

echo json_encode("customer with ID = $postData->customerID is deactivated!");

$dbManager->closeConnection();