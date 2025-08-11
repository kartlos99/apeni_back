<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
//$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$json = file_get_contents('php://input');
$postData = json_decode($json);

const TOKEN_VALIDITY_PERIOD = 60 * 60;


$sql = "SELECT id, username, pass, name, type FROM `users` 
        WHERE 
            `active` = 1 AND
            `username` = '$postData->username' ";

$fetchedUsers = $dbManager->getDataAsArray($sql);

if (count($fetchedUsers) == 1) {
    $userData = $fetchedUsers[0];

    if ($userData['pass'] !== $postData->password)
        $dbManager->dieWithHttpError("incorrect password", ERROR_CODE_INCORRECT_PASSWORD);

    $payload = [
        'iat' => time(),
        'iss' => 'localhost',
        'exp' => time() + TOKEN_VALIDITY_PERIOD,
        'userID' => $userData['id'],
        'userType' => $userData['type'],
        'username' => $userData['username']
    ];

    $token = JWT::encode($payload, SECRET_KEY);
    $userData['token'] = $token;

    $sqlPermissions = "SELECT `permissionID` FROM `permission_mapping` WHERE `roleID` = " . $userData['type'];

    $permissions = $dbManager->getDataAsArray($sqlPermissions);
    if (count($permissions) > 0)
        foreach ($permissions as $permission) {
            $userData['permissions'][] = $permission['permissionID'];
        }
    else
        $userData['permissions'] = [];

    $sqlAllowedRegions =
        "SELECT
            reg.`ID` as `id`, 
            reg.`code`,
            reg.`name`,
            reg.`active` as `status`,
            reg.`ownStorage`
        FROM 
            `user_to_region_mapping` map, `regions` reg
        WHERE 
            `userID` = " . $userData['id'] . " AND map.`regionID` = reg.ID AND map.state = 1";

    $regions = $dbManager->getDataAsArray($sqlAllowedRegions);
    $userData['regions'] = $regions;

    if (count($regions) == 0)
        $dbManager->dieWithHttpError("no region attached", ERROR_CODE_NO_REGION_ATTACHED);

    $userData['pass'] = null;

    echo json_encode($userData);
} else {
    $dbManager->dieWithHttpError("can't identify user!", ERROR_CODE_CANT_IDENTIFY_USER);
}

$dbManager->closeConnection();
