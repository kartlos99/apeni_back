<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

const YEAR = "year";
const MONTH = "month";
const BARREL_INPUT_DETAIL = "detail";
const BARREL_INPUT = "BarrelInput";


$sessionData = checkToken();

$year = $_GET['year'] ?? 2020;


$sql = [
    BARREL_INPUT => "
SELECT 
concat(YEAR(s.`inputDate`), MONTH(s.`inputDate`), s.beerID) AS ID,
YEAR(s.`inputDate`) AS `year`,
MONTH(s.`inputDate`) AS `month`,
`beerID`,
b.name AS beer,
`barrelID`,
SUM(`count`) AS amount,
SUM(`count` * k.volume) AS liter
FROM `storehousebeerinpit` s

LEFT JOIN `beer` AS b
ON  s.beerID = b.id

LEFT JOIN `barrels` AS k
ON  s.`barrelID` = k.id

WHERE year(s.`inputDate`) = $year
AND regionID = {$sessionData->regionID}

GROUP BY
	YEAR(s.`inputDate`), 
    MONTH(s.`inputDate`),    
    s.beerID
    
ORDER BY YEAR(s.`inputDate`), MONTH(s.`inputDate`), b.sortValue",

    BARREL_INPUT_DETAIL => "SELECT 
concat(YEAR(s.`inputDate`), MONTH(s.`inputDate`), s.beerID) AS ID,
YEAR(s.`inputDate`) AS `year`,
MONTH(s.`inputDate`) AS `month`,
`beerID`,
b.name AS beer,
s.`barrelID`,
k.name AS canType,
SUM(`count`) AS amount,
SUM(`count` * k.volume) AS liter
FROM `storehousebeerinpit` s

LEFT JOIN beer AS b
ON  s.beerID = b.id

LEFT JOIN barrels AS k
ON  s.`barrelID` = k.id

WHERE year(s.`inputDate`) = $year
AND regionID = {$sessionData->regionID}

GROUP BY
	YEAR(s.`inputDate`), 
    MONTH(s.`inputDate`),    
    s.beerID,
    s.`barrelID`
    
ORDER BY YEAR(s.`inputDate`), MONTH(s.`inputDate`), b.sortValue, k.sortValue"
];

$queryResult = [];
$result = mysqli_query($con, $sql[BARREL_INPUT]);
while ($rs = mysqli_fetch_assoc($result)) {
    $queryResult[] = $rs;
}

$detailResult = [];
$result = mysqli_query($con, $sql[BARREL_INPUT_DETAIL]);
while ($rs = mysqli_fetch_assoc($result)) {
    $detailResult[] = $rs;
}


$arr = [];

foreach ($queryResult as $key => $item) {
    $arr[$item[MONTH]][] = $item;
}

foreach ($arr as $mKey => $monthItem) {
    foreach ($monthItem as $bKey => $beerItem) {
        foreach ($detailResult as $detail) {
            if ($beerItem['ID'] == $detail['ID'])
                $arr[$mKey][$bKey]['barrels'][] = $detail;
        }
    }
}

$response[DATA] = $arr;

echo json_encode($response);