<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

const TILDA = '`';
const TABLE_DELIVERY = "SALE_BEER";
const TABLE_DELIVERY_BOTTLE = "SALE_BOTTLE";
const TABLE_BARREL_OUTPUT = "kout";
const TABLE_MONEY_OUTPUT = "TAKE_MONEY";

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$userID = $sessionData->userID;
$recordId = $postData->recordID;

switch ($postData->table) {
    case trim(DbKey::$USERS_TB, TILDA):
        // states: 0 - deleted, 2 - inActive
        $sql = sprintf("UPDATE %s SET users.active = 2 WHERE users.id = %s ", DbKey::$USERS_TB, $postData->recordID);
        break;

    case TABLE_DELIVERY:
        $sql = "SELECT remove_sale($recordId, $userID)";
        break;
    case TABLE_DELIVERY_BOTTLE:
        $sql = "SELECT remove_bottle_sale($recordId, $userID)";
        break;
    case TABLE_BARREL_OUTPUT:
        $sql = "SELECT remove_barrel($recordId, $userID) ";
        break;
    case TABLE_MONEY_OUTPUT:
        $sql = "SELECT remove_money($recordId, $userID) ";
        break;

    default:
        $sql = sprintf("DELETE FROM %s WHERE id = %s ", $postData->table, $postData->recordID);
        break;
}

$dbManager->baseInsert($sql);
$dbManager->closeConnection();