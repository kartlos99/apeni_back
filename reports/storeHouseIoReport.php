<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


require_once('../mr/mobile/connection.php');
//$sessionData = checkToken();
include_once 'baseReport.php';



$sql = "

";
$columns = [

];

exportToExcel($conn, $sql, $columns, "storeHouseIO" . $today);

/*{
    "SELECT
    `ID`,
    `groupID`,
    `inputDate` AS ioDate,
    `distributorID`,
    `beerID`,
    `barrelID`,
    `count`,
    `chek`,
    `comment`
FROM
    `storehousebeerinpit`
WHERE
    `regionID` = 1
UNION
SELECT
    `ID`,
    `groupID`,
    `outputDate` AS ioDate,
    `distributorID`,
    0 AS `beerID`,
    `barrelID`,
    `count`,
    `chek`,
    `comment`
FROM
    `storehousebarreloutput`
WHERE
    `regionID` = 1
ORDER BY
    ioDate DESC, beerID
LIMIT 50"
}*/