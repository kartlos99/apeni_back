<?php
namespace Apeni\JWT;

use UserDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new UserDataManager();

$userResult = $dbManager->identifyUser($postData->username, $postData->password);

if (count($userResult) == 1) {
    $user = $userResult[0];

    $payload = [
        'iat' => time(),
        'iss' => 'localhost',
        'exp' => time() + 60 * 60,
        'userID' => $user['id'],
        'userType' => $user['type'],
        'username' => $user['username']
    ];

    $token = JWT::encode($payload, SECRET_KEY);
    $user['token'] = $token;

/*    $sqlPermissions = "SELECT `permissionID` FROM `permission_mapping` WHERE `roleID` = " . $userData['type'];
    $permResult = mysqli_query($con, $sqlPermissions);
    $permissions = [];
    while ($rs = mysqli_fetch_assoc($permResult)) {
        $permissions[] = $rs['permissionID'];
    }
    $user['permissions'] = $permissions;*/

    echo json_encode($user);
} else {
    $dbManager->closeConnection();
    dieWithError(400, ERROR_TEXT_CANT_IDENTIFY_USER);
}

$dbManager->closeConnection();