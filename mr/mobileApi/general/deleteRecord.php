<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

const TILDA = '`';

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

switch ($postData->table) {
    case trim(DbKey::$USERS_TB, TILDA):
        // states: 0 - deleted, 2 - inActive
        $sql = sprintf("UPDATE %s SET users.active = 2 WHERE users.id = %s ", DbKey::$USERS_TB, $postData->recordID);
        break;

    default:
        $sql = sprintf("DELETE FROM %s WHERE id = %s ", $postData->table, $postData->recordID);
        break;
}

$dbManager->baseInsert($sql);
$dbManager->closeConnection();