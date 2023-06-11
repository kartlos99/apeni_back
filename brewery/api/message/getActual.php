<?php

namespace Apeni\JWT;

use MessageDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

const DEFAULT_MESSAGES_COUNT = 50;

$dbManager = new MessageDataManager();

$limit = $_GET['limit'] ?? DEFAULT_MESSAGES_COUNT;

echo json_encode(
    $dbManager->getMessages($limit)
);

$dbManager->closeConnection();