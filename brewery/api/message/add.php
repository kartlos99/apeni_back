<?php

namespace Apeni\JWT;

use MessageDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new MessageDataManager();

echo json_encode(
    $dbManager->addMessage(
        $postData->remoteMessageType,
        $postData->message,
        $sessionData->userID
    )
);

$dbManager->closeConnection();