<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$sql = "SELECT ur.*, u.username, u.name as userDisplayName, r.name as regionName FROM `user_to_region_map` ur
LEFT JOIN users u ON u.id = ur.userID
LEFT JOIN regions r ON r.id = ur.regionID
ORDER BY u.username, r.name";

$arr = array();
$result = $con->query($sql);

while ($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);