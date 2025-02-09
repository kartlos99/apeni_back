<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

const USERS = "users";
const REGIONS = "regions";

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$userListSql = "SELECT
    a.`id`,
    a.`username`,
    a.`name`,
    a.`type`,
    a.`tel`,
    a.`adress` as `address`,
    IFNULL(b.username, 'x') AS maker,
    a.active AS userStatus,
    a.`comment`
FROM
    `users` a
LEFT JOIN `users` b ON
    `a`.`maker` = `b`.`id`
LEFT JOIN user_to_region_mapping um ON
    um.userID = a.id
WHERE
    um.regionID = {$sessionData->regionID} AND a.active >= 1 AND um.state = 1
ORDER BY a.`username`
";

$users = $dbManager->getDataAsArray($userListSql);

$attachedRegionsIdSql = "SELECT `regionID` 
FROM `user_to_region_mapping`
WHERE `userID` = %s AND state = 1";

$regionsSql = "SELECT
    `ID` as `id`, `code`, `name`, `active` as `status`, `ownStorage`
FROM
    `regions`
WHERE
    `active` > 0";

$result = [
    USERS => [],
    REGIONS => $dbManager->getDataAsArray($regionsSql)
];

foreach ($users as $user) {
    $regions = $dbManager->getDataAsArray(sprintf($attachedRegionsIdSql, $user['id']));
    $user[REGIONS] = array_map(
        fn($value): int => $value["regionID"],
        $regions
    );
    $result[USERS][] = $user;
}

echo json_encode($result);

$dbManager->closeConnection();