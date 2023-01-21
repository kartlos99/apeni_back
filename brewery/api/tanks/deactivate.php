<?php

namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

//echo json_encode($postData);
//die;

$myData = new MyData($dbLink);

echo json_encode($myData->deactivateTank(
    $postData,
    $sessionData->userID
));

mysqli_close($dbLink);