<?php

namespace Apeni\JWT;

use UserDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new UserDataManager();

$user = $dbManager->findUser($sessionData->userID);

if (count($user) != 1)
    dieWithDefaultHttpError(ERROR_TEXT_CANT_IDENTIFY_USER, ERROR_CODE_NO_USER_FOUND);

$user = $user[0];

if ($user['pass'] != $postData->oldPassword)
    dieWithDefaultHttpError(ERROR_TEXT_PASSWORD_DONT_MATCH, ERROR_CODE_PASSWORD_DONT_MATCH);

$result = $dbManager->updatePassword(
    $sessionData->userID,
    $postData->newPassword
);

echo json_encode($result);

$dbManager->closeConnection();
