<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$response[DATA] = null;


$sql = "SELECT id, username, name, type FROM `users` 
        WHERE 
            `active` = 1 AND
            `username` = '$postData->username' AND 
            `pass` = '$postData->password'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) == 1) {
    $userData = mysqli_fetch_assoc($result);

    $payload = [
        'iat' => time(),
        'iss' => 'localhost',
        'exp' => time() + 60 * 60,
        'userID' => $userData['id'],
        'userType' => $userData['type'],
        'username' => $userData['username']
    ];

    $token = JWT::encode($payload, SECRET_KEY);
    $userData['token'] = $token;

    $sqlPermissions = "SELECT `permissionID` FROM `permission_mapping` WHERE `roleID` = " . $userData['type'];
    $permResult = mysqli_query($con, $sqlPermissions);
    $permissions = [];
    while ($rs = mysqli_fetch_assoc($permResult)) {
        $permissions[] = $rs['permissionID'];
    }
    $userData['permissions'] = $permissions;

    $sqlAllowedRegions =
        "SELECT `regionID`, `name`, `ownStorage` FROM `user_to_region_map` map, `regions` reg
         WHERE `userID` = " . $userData['id'] . " AND map.`regionID` = reg.ID";
    $regionsResult = mysqli_query($con, $sqlAllowedRegions);
    $regions = [];
    while ($rs = mysqli_fetch_assoc($regionsResult)) {
        $regions[] = $rs;
    }
    $userData['regions'] = $regions;

    $response[DATA] = $userData;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't identify user!";
    $response[ERROR_CODE] = 401;
}

echo json_encode($response);

mysqli_close($con);