<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
$userID = 0;
if (isset( $_GET["userID"]))
    $userID = $_GET["userID"];
else dieWithError(COMMON_ERROR_CODE, "user is Not set!");

$sql =
    "SELECT
    r.ID,
    r.name,
    IFNULL(uMap.id, 0) AS attached
FROM
    `regions` r
LEFT JOIN(
    SELECT * FROM
        `user_to_region_map`
    WHERE
        user_to_region_map.userID = $userID
) uMap
ON
    uMap.`regionID` = r.ID";

$arr = array();
$result = $con->query($sql);

while ($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);