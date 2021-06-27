<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

//checkToken();

$userID = $_GET['userID'];

$sqlAllowedRegions =
    "SELECT `regionID`, `name` FROM `user_to_region_map` map, `regions` reg
         WHERE `userID` = " . $userID . " AND map.`regionID` = reg.ID
         ORDER BY `name`";
$regionsResult = mysqli_query($con, $sqlAllowedRegions);
$regions = [];
while ($rs = mysqli_fetch_assoc($regionsResult)) {
    $regions[] = $rs;
}
$response[DATA] = $regions;


echo json_encode($response);

mysqli_close($con);